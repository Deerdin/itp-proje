<?php
declare(strict_types=1);
$temp=tempnam(sys_get_temp_dir(),'kvkk-test-');putenv('KVKK_TEST_DB='.$temp);
require __DIR__.'/../app/bootstrap.php';
$checks=0;
function check(bool $condition,string $message): void { global $checks;if(!$condition) throw new RuntimeException($message);echo "PASS: $message\n";$checks++; }
try {
 Database::migrate();Database::migrate();
 check((int)query('SELECT COUNT(*) FROM users')->fetchColumn()===0,'Şema tekrar kurulabilir');
 foreach([['Test Owner','owner@test.invalid','user'],['Test Other','other@test.invalid','user'],['Test Expert','expert@test.invalid','expert']] as [$name,$email,$role]) query('INSERT INTO users(name,email,password,role,created_at) VALUES(?,?,?,?,?)',[$name,$email,password_hash('TestOnly!2026',PASSWORD_DEFAULT),$role,date('c')]);
 $owner=query('SELECT * FROM users WHERE id=1')->fetch();$other=query('SELECT * FROM users WHERE id=2')->fetch();$expert=query('SELECT * FROM users WHERE id=3')->fetch();
 check(password_verify('TestOnly!2026',$owner['password']) && $owner['password']!=='TestOnly!2026','Parola hash ile saklanır');
 $samples=require __DIR__.'/../app/samples.php';
 $a=Analyzer::run($samples[0]['content']);$b=Analyzer::run($samples[1]['content']);
 check($a['score']>$b['score'],'Zengin ve eksik örnekler farklı skor üretir');
 check($a['score']===100,'Tüm çekirdek örüntüleri içeren örnek 100 üretir');
 check(Analyzer::run('')['score']===0,'Boş metinde bulgu üretilmez');
 check(Analyzer::run('VERİ SORUMLUSU ÖRNEK ANONİM ŞİRKETİ.')['criteria']['identity']['status']==='found','Türkçe büyük harfler normalize edilir');
 $base='Veri sorumlusu Örnek Anonim Şirketi.';
 check(Analyzer::run($base)['score']===Analyzer::run($base.' Başvuru için kvkk@ornek.test. Saklama süresi sonunda imha edilir.')['score'],'Tamamlayıcı kontroller çekirdek skoru değiştirmez');

 check(Analyzer::run('Veriler iş ortaklarıyla paylaşılabilir. Sipariş amacıyla bilgi işlenir.')['criteria']['transfer']['status']==='partial','Aktarım amacı ilgisiz cümleden tamamlanmaz');
 check(Analyzer::run('Veri sorumlusunun kimliği belirtilmemiştir.')['criteria']['identity']['status']==='missing','Açıklanmayan kimlik olumlu kanıt sayılmaz');
 check(Analyzer::run('Kişisel veriler üçüncü kişilere aktarılmaz.')['criteria']['transfer']['status']==='found','Aktarım yapılmadığı açık beyan olarak tanınır');
 check(Analyzer::run('Veriler süresiz saklanır.')['criteria']['retention']['status']==='partial','Belirsiz saklama süresi tam bulgu sayılmaz');
 check(Analyzer::run('Veri sorumlusu Örnek A.Ş. şirketidir. Başvurular yazılıdır.')['metrics']['sentences']===2,'Şirket kısaltmaları cümle sayısını bozmaz');
 check(Analyzer::run('Veriler gerektiğinde ilgili üçüncü kişilerle paylaşılabilir.')['warnings']!==[],'Belirsiz kapsam ifadeleri uyarı üretir');
 check(Analyzer::run('Bu aydınlatma metnini okuyarak rıza vermiş sayılırsınız.')['warnings']!==[],'Aydınlatma ile rızayı birleştiren ifade işaretlenir');
 check(Analyzer::run('Kişisel veriler bordro hesaplama amacıyla işlenmektedir.')['criteria']['purpose']['status']==='found','İstihdam ve bordro amacı tanınır');
 check(Analyzer::run('Bilgiler mobil uygulama üzerinden toplanmaktadır.')['criteria']['collection']['status']==='found','Mobil uygulama toplama kanalı tanınır');
 check(Analyzer::run('İşlemenin hukuki sebebi sözleşmedir.')['criteria']['legal']['status']==='partial','Salt hukuki sebep adı somut koşul yerine sayılmaz');
 $_COOKIE['kvkk_trial']=str_repeat('a',64);$_SESSION=[];
 check(!guestUsed(),'Yeni misafirin analiz hakkı var');
 try {createGuestAnalysis(['title'=>'x','content'=>'short','sector'=>'Diğer']);check(false,'Misafir doğrulama');}catch(DomainException $e){check(!guestUsed(),'Hatalı metin misafir hakkını tüketmez');}
 createGuestAnalysis($samples[0]);
 check(guestUsed() && guestReport()['result']['version']===Analyzer::VERSION,'İlk misafir analizi hakkı tüketir ve oturum raporu üretir');
 check((int)query('SELECT COUNT(*) FROM documents')->fetchColumn()===0,'Misafir metni kalıcı belge tablosuna yazılmaz');
 try {createGuestAnalysis($samples[1]);check(false,'Tekrar misafir analizi');}catch(DomainException $e){check(true,'İkinci misafir analizi sunucuda reddedilir');}
 $_SESSION=[];
 check(guestUsed(),'Oturum sıfırlansa da tarayıcı hakkı sıfırlanmaz');
 $_COOKIE['kvkk_trial']=str_repeat('b',64);
 check(!guestUsed() && guestReport()===null,'Başka tarayıcıya önceki misafirin raporu sızmaz');

 check(Analyzer::run('Platform üzerinden veriler toplanır.')['criteria']['collection']['status']==='partial','Platform kelimesi form kanalı olarak sayılmaz');
 check(Analyzer::run('Veri sorumlusu başvuru yapmalıdır.')['criteria']['identity']['status']==='partial','Başvuru sözcüğü AŞ unvanı olarak sayılmaz');
 $id=createAnalysis($owner,$samples[0]);
 check(findAnalysis($id,$owner)!==null,'Sahibi raporu okuyabilir');
 check(findAnalysis($id,$other)===null,'Başka kullanıcı raporu okuyamaz');
 check(findAnalysis($id,$expert)!==null,'Uzman incelemek için raporu okuyabilir');
 check(count(findAnalysis($id,$owner)['result']['criteria'])===8,'Sekiz kriter kalıcı saklanır');
 try { createAnalysis($owner,['title'=>'x','content'=>'short','sector'=>'Diğer']);check(false,'Geçersiz girdi'); } catch(DomainException $e) {check(true,'Kısa girdi sunucuda reddedilir');}
 try { saveReview($other,$id,['criterion'=>'identity','verdict'=>'sufficient','note'=>'Test gerekçesi.']);check(false,'Yetkisiz etiket'); } catch(DomainException $e) {check(true,'Standart kullanıcı etiket ekleyemez');}
 saveReview($expert,$id,['criterion'=>'identity','verdict'=>'sufficient','note'=>'Unvan metinde açıkça ifade edilmiş.']);
 saveReview($expert,$id,['criterion'=>'identity','verdict'=>'uncertain','note'=>'Kimliğin kapsamı ek inceleme gerektiriyor.']);
 check((int)query('SELECT COUNT(*) FROM reviews')->fetchColumn()===1,'Aynı uzmanın etiketi çoğalmaz, güncellenir');
 check(query('SELECT verdict FROM reviews')->fetchColumn()==='uncertain','Etiket değişikliği kalıcı saklanır');
 check(e('<script>alert(1)</script>')==='&lt;script&gt;alert(1)&lt;/script&gt;','HTML çıktısı kaçışlanır');
 $_SESSION=['csrf'=>'known'];
 try {verifyCsrf('wrong');check(false,'CSRF');}catch(DomainException $e){check(true,'Yanlış CSRF belirteci reddedilir');}
 verifyCsrf('known');check(true,'Doğru CSRF belirteci kabul edilir');
 query('DELETE FROM documents WHERE user_id=?',[$owner['id']]);
 check((int)query('SELECT COUNT(*) FROM analyses')->fetchColumn()===0 && (int)query('SELECT COUNT(*) FROM reviews')->fetchColumn()===0,'Belge silinince analiz ve etiketleri ilişkisel olarak silinir');
 echo "\n$checks kontrol başarılı.\n";
} finally { @unlink($temp); }
