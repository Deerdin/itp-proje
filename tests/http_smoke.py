#!/usr/bin/env python3
"""Run only against a disposable development database, never live data."""
import sys, json, re, time, http.cookiejar
from urllib import request, parse, error
BASE=(sys.argv[1] if len(sys.argv)>1 else 'http://127.0.0.1:8081').rstrip('/')
checks=0
class Client:
 def __init__(self):
  self.jar=http.cookiejar.CookieJar()
  self.opener=request.build_opener(request.HTTPCookieProcessor(self.jar))
 def req(self,path='/',data=None,headers=None,method=None):
  if isinstance(data,dict): data=parse.urlencode(data).encode()
  req=request.Request(BASE+path,data=data,headers=headers or {},method=method)
  try:
   with self.opener.open(req) as res:return res.status,res.read().decode(),res.headers,res.url
  except error.HTTPError as e:return e.code,e.read().decode(),e.headers,e.url
 def token(self,path='/index.php?page=analyze'):
  body=self.req(path)[1];m=re.search(r'name="csrf" value="([a-f0-9]+)"',body)
  assert m,body[:300]
  return m.group(1)
 def login(self,email):
  return self.req('/index.php?page=auth',{'csrf':self.token('/index.php?page=auth'),'action':'login','email':email,'password':'KvkkDemo!2026'})
def check(value,label):
 global checks
 if not value: raise AssertionError(label)
 checks+=1;print('PASS:',label)
user=Client();other=Client();expert=Client();admin=Client()
check(user.req('/')[0]==200 and 'name="action" value="analyze"' in user.req('/')[1],'Ana sayfa misafir analizine açık')
check('page=auth' in user.req('/index.php?page=history')[3],'Geçmiş sayfası giriş ekranına yönlendirir')
check(user.req('/api.php')[0]==401,'Oturumsuz API 401')
home=user.req('/')[1]
check(all(text not in home for text in ['2209','İTP','Misafir çalışma alanı','İlk analiz için hesap','BASELINE TR','v1.1']),'Yayında gereksiz proje ve sürüm etiketleri görünmez')
auth=user.req('/index.php?page=auth')[1]
check('placeholder="ornek@eposta.com"' in auth and 'autocapitalize="none"' in auth,'E-posta alanı yayına uygun')
check(user.req('/index.php?page=privacy')[0]==200 and 'Zorunlu çerezler' in user.req('/index.php?page=privacy')[1],'Veri kullanımı sayfası herkese açık')
check(user.req('/install.php')[0]==404,'Normal geliştirme ortamında web kurulum kapalı')

for private in ['/storage/database.sqlite','/.env','/.env.example','/app/Database.php','/belgeler/referanslar/proje-konusu-ve-yol-haritasi.md','/bin/setup.php','/tests/run.php','/../storage/database.sqlite']:
 check(user.req(private)[0]==404,'Özel yol kapalı: '+private)
check('Güvenlik doğrulaması başarısız' in user.req('/index.php?page=auth',{'action':'login','email':'kullanici@kvkk.test','password':'KvkkDemo!2026','csrf':'invalid'})[1],'Form CSRF reddi')

guest=Client(); stranger=Client()
check('metin analizine dön'.lower() in guest.req('/index.php?page=auth')[1].lower(),'Giriş ekranında misafir analizine dönüş bağlantısı var')
check(guest.req('/api.php?guest=1')[0]==404,'Analiz öncesi misafir raporu yok')
csrf=guest.token()
invalid=guest.req('/index.php?page=analyze',{'action':'analyze','csrf':csrf,'title':'x','sector':'Diğer','content':'short'})
check('3–150' in invalid[1] and 'name="action" value="analyze"' in invalid[1],'Hatalı metin ücretsiz hakkı tüketmez')
content='Veri sorumlusu Kurgusal Deneme Anonim Şirketidir. Kişisel veriler sipariş ve teslimat amacıyla işlenir. Veriler web sitesi üzerinden elektronik olarak toplanır.'
data={'action':'analyze','csrf':guest.token(),'title':'Misafir Test Belgesi','sector':'E-ticaret','content':content}
first=guest.req('/index.php?page=analyze',data)
check('guest=1' in first[3] and 'Analiziniz hazır' in first[1],'İlk misafir analizi girişsiz rapora gider')
check(json.loads(guest.req('/api.php?guest=1')[1])['analysis']['version']=='baseline-tr-2.0','Misafir yeni analiz motorunu kullanır')
check(stranger.req('/api.php?guest=1')[0]==404,'Başka tarayıcı misafir raporuna erişemez')
check('attachment' in guest.req('/api.php?guest=1&download=1')[2].get('Content-Disposition',''),'Misafir JSON raporunu indirebilir')
check('Giriş yaparak devam et' in guest.req('/')[1],'Kullanılan hak ana sayfada gösterilir')
data['csrf']=guest.token()
second=guest.req('/index.php?page=analyze',data)
check('page=auth' in second[3] and 'Yeni bir analiz yapmak için giriş yapın' in second[1],'İkinci misafir denemesi giriş gerektirir')
trial_cookie=next(c for c in guest.jar if c.name=='kvkk_trial')
check('HttpOnly' in trial_cookie._rest and trial_cookie.expires is not None,'Kota çerezi kalıcı ve HttpOnly')
guest.jar.clear(domain=next(c.domain for c in guest.jar if c.name=='kvkk_session'),path='/',name='kvkk_session')
check('Giriş yaparak devam et' in guest.req('/')[1],'Oturum çerezi silinse de ücretsiz hak geri gelmez')
# Independent visitor: login then explicitly save the existing session report.
saver=Client(); save_data=dict(data,csrf=saver.token())
saver.req('/index.php?page=analyze',save_data)
saver.req('/index.php?page=auth&next=guest',{'csrf':saver.token('/index.php?page=auth'),'action':'login','email':'kullanici@kvkk.test','password':'KvkkDemo!2026'})
check('Hesabıma kaydet' in saver.req('/index.php?page=report&guest=1')[1],'Giriş sonrası misafir raporu korunur')
saved=saver.req('/index.php?page=report&guest=1',{'action':'save_guest','csrf':saver.token(),'unused':'x'})
check('Misafir analiziniz hesabınıza kaydedildi' in saved[1],'Misafir raporu açık istekle hesaba kaydedilir')
saved_id=int(parse.parse_qs(parse.urlparse(saved[3]).query)['id'][0])
saver.req('/index.php?page=report&id='+str(saved_id),{'action':'delete','csrf':saver.token(),'id':saved_id})

check('Merhaba' in user.login('kullanici@kvkk.test')[1],'Demo kullanıcı giriş yapar')
check(any('HttpOnly' in c._rest and c._rest.get('SameSite')=='Lax' for c in user.jar),'Oturum çerezi HttpOnly ve SameSite=Lax')
check(user.req('/index.php?page=review')[0]==403,'Standart kullanıcı uzman sayfasına erişemez')
check('Güvenlik doğrulaması başarısız' in user.req('/api.php',json.dumps({}).encode(),{'Content-Type':'application/json'},'POST')[1],'API CSRF reddi')
check(user.req('/api.php',b'bad json',{'X-CSRF-Token':user.token(),'Content-Type':'application/json'},'POST')[0]==400,'Bozuk JSON 400')
payload={'title':'HTTP <script>test</script>','sector':'Teknoloji','content':'Veri sorumlusu Kurgusal Test Anonim Şirketi. Kişisel verileriniz üyelik sözleşmesinin yürütülmesi amacıyla işlenmektedir. Veriler web sitesi formlarıyla elektronik ortamda toplanır.'}
status,body,_,_=user.req('/api.php',json.dumps(payload).encode(),{'X-CSRF-Token':user.token(),'Content-Type':'application/json'},'POST')
check(status==201,'API analiz oluşturur 201');aid=json.loads(body)['id']
report=user.req('/index.php?page=report&id='+str(aid))[1]
check('&lt;script&gt;test&lt;/script&gt;' in report and '<script>test</script>' not in report,'Belge başlığı XSS kaçışı')
check(user.req('/api.php?id='+str(aid))[0]==200,'Sahibi JSON raporuna erişir')
check('attachment' in user.req('/api.php?id='+str(aid)+'&download=1')[2].get('Content-Disposition',''),'JSON indirme başlığı')
check('HTTP &lt;script&gt;' in user.req('/index.php?page=history&q=HTTP')[1],'Geçmiş arama çalışır')
# Registration: always user, supplied role ignored.
suffix=str(time.time_ns())
reg=other.req('/index.php?page=auth&mode=register',{'csrf':other.token('/index.php?page=auth'),'action':'register','name':'HTTP Test','email':'http-'+suffix+'@test.invalid','password':'HttpTest!2026','role':'admin'})
check('Merhaba' in reg[1] and 'Standart kullanıcı' in reg[1],'Kayıt akışı, rol yükseltme reddi')
check(other.req('/api.php?id='+str(aid))[0]==404,'Başka kullanıcı API kaydına erişemez')
check(other.req('/index.php?page=report&id='+str(aid))[0]==404,'Başka kullanıcı HTML raporuna erişemez')
rename={'action':'rename','csrf':other.token(),'id':aid,'title':'Unauthorized Rename'}
check('yetkiniz yok' in other.req('/index.php?page=history',rename)[1],'Sahibi olmayan belgeyi değiştiremez')
check('Uzman incelemesi' in expert.login('uzman@kvkk.test')[1],'Uzman giriş yapar')
check(expert.req('/api.php?id='+str(aid))[0]==200,'Uzman başkasının analizini inceleyebilir')
review={'csrf':expert.token(),'action':'review','id':aid,'criterion':'identity','verdict':'sufficient','note':'Kurgusal şirket unvanı metinde açıkça belirtilmiş.'}
check('Uzman değerlendirmeniz kaydedildi' in expert.req('/index.php?page=review&id='+str(aid),review)[1],'Uzman etiketi kaydedilir')
check(len(json.loads(user.req('/api.php?id='+str(aid))[1])['reviews'])==1,'Uzman etiketi kullanıcı raporunda görünür')
check('Yönetici' in admin.login('yonetici@kvkk.test')[1] and 'Kullanıcı yetkileri' in admin.req('/index.php?page=review')[1],'Yönetici kullanıcı rollerini görebilir')
# Reject invalid form and JSON validation.
check(user.req('/api.php',json.dumps({'title':'x','content':'short','sector':'Diğer'}).encode(),{'X-CSRF-Token':user.token(),'Content-Type':'application/json'},'POST')[0]==422,'API girdi doğrulaması 422')
check(user.req('/api.php',method='PUT')[0]==405,'Desteklenmeyen metot 405')
rename={'action':'rename','csrf':user.token(),'id':aid,'title':'HTTP Düzenlenmiş Belge'}
check('HTTP Düzenlenmiş Belge' in user.req('/index.php?page=report&id='+str(aid),rename)[1],'Sahibi belge adını değiştirir')
check('puan)' in user.req('/index.php?page=history&left=1&right='+str(aid))[1],'İki analiz karşılaştırılır')
check('Belge ve ilişkili analiz verileri silindi' in user.req('/index.php?page=report&id='+str(aid),{'action':'delete','csrf':user.token(),'id':aid})[1],'Sahibi test belgesini siler')
check(user.req('/api.php?id='+str(aid))[0]==404,'Silinen analiz artık okunamaz')
logout=user.req('/index.php?page=analyze',{'action':'logout','csrf':user.token()})
check('page=auth' in logout[3] and user.req('/api.php')[0]==401,'Çıkış oturumu kapatır')
# Validate request throttling last so it cannot mask the functional checks above.
limited=False
for _ in range(31):
 status,body,headers,_=admin.req('/api.php',json.dumps({'title':'x','content':'short','sector':'Diğer'}).encode(),{'X-CSRF-Token':admin.token(),'Content-Type':'application/json'},'POST')
 if status==429:
  limited='Retry-After' in headers and 'Çok fazla istek' in body
  break
check(limited,'API istek sınırı 429 ve Retry-After üretir')
print(f'\n{checks} HTTP kontrolü başarılı.')
