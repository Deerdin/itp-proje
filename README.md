# VeriLex

Türkçe aydınlatma metinlerini analiz eden PHP uygulaması. Cümle kanıtları, kriter bazlı bulgular, kayıtlı raporlar ve uzman değerlendirmesi sunar. Otomatik sonuçlar hukuki görüş veya uyumluluk belgesi değildir.

## Yayın ve kurulum

Yayın adresi: [verilex.tabsoft.com.tr](https://verilex.tabsoft.com.tr/).

cPanel kurulum adımları `belgeler/cpanel-kurulum.md` içindedir. Hosting üzerindeki PHP sürümü, MySQL kullanıcı yetkileri ve `.env` ayarları kuruluma göre yapılandırılır. Bu deponun güncellenmesi sunucu dosyalarını kendiliğinden güncellemez.

İlk yönetici `install.php` üzerinden özel kurulum anahtarıyla oluşturulur; tamamlandıktan sonra kurulum kilitlenir ve kurulum dosyası kaldırılır. Gerçek `.env`, veritabanı, oturumlar, loglar ve demo kayıtları depoya alınmaz. Veritabanı kullanıcı parolaları ve kurulum anahtarları README'ye yazılmaz.

PHP 8.3/8.4, MySQL 8 veya MariaDB 10.6+, HTTPS ve `pdo_mysql`/`mbstring` uzantıları hedeflenir. Canlı mod PHP 8.2 altını, SQLite kullanımını ve eksik sır/domain ayarlarını reddeder.

## Yerel geliştirme

```sh
cd /Applications/XAMPP/xamppfiles/htdocs/kvkk-denetci
/Applications/XAMPP/xamppfiles/bin/php bin/setup.php
/Applications/XAMPP/xamppfiles/bin/php -S 127.0.0.1:8080 router.php
```

Yerel adres: http://127.0.0.1:8080 . Yerelde SQLite kullanılabilir; MySQL için `.env` içinde DB_DRIVER, DB_HOST/DB_SOCKET, DB_NAME, DB_USER ve DB_PASSWORD ayarlanır. `.env.production.example` canlı ortam şablonudur. Gerçek `.env` ve veritabanları sürüm kontrolüne eklenmez.

## Temel davranışlar

- Hesapsız kullanıcı bir metni analiz edebilir. İkinci analizde giriş gerekir. Hatalı form hakkı tüketmez.
- Deneme kotası bir yıllık HttpOnly çerez ve sunucuda belirteç özetiyle tutulur. Çerezlerin tamamını silmek veya farklı tarayıcı kullanmak yeni ziyaretçi gibi görünür. Kişi başına mutlak kota garantisi yoktur.
- Misafir raporu oturumda geçici tutulur; girişten sonra kullanıcı isterse hesaba kaydeder. Hesaba kaydedilmeden uzmanlara gösterilmez.
- Kayıtlı kullanıcı kendi raporlarını arar, karşılaştırır, yeniden adlandırır ve siler. Uzmanlar kayıtlı metinlere gerekçeli değerlendirme ekler. Yönetici rolleri yönetir.
- Skor altı çekirdek kriterden üretilir; iki tamamlayıcı kontrol ayrıca gösterilir. Motor kural tabanlıdır ve bağlama duyarlı örüntüler kullanır; eğitilmiş dil modeli veya ölçülmüş NLP başarım iddiası yoktur.

## Testler ve paketleme

```sh
php tests/run.php
# HTTP testlerini yalnızca ayrı, kurgusal verili test sunucusuna karşı çalıştırın.
python3 tests/http_smoke.py http://127.0.0.1:8081
python3 bin/build-release.py
```

HTTP senaryoları test hesabı ve belge oluşturur; canlı veritabanına karşı çalıştırılmaz. Son kontrol sonuçları `belgeler/test-sonuclari.md` içindedir. Paket SHA-256 manifesti içerir. MySQL testi mevcut yerel verilere dokunmadan ayrı MariaDB sürecinde yapıldı.

## Dosya yapısı

- `index.php`, `api.php`: sayfalar, form işlemleri ve oturumla korunan API.
- `install.php`: tek seferlik ilk yönetici kurulumu.
- `app`: yapılandırma, PDO veri erişimi, analiz ve şablonlar.
- `assets`: VeriLex logosu ve favicon, yerel CSS, JavaScript ve Manrope fontu.
- `storage`: özel veritabanı/oturum dosyaları; yayın paketine yerel veriler alınmaz.
- `bin`: CLI kurulum, bakım ve paketleme araçları.
- `tests`, `belgeler`: geliştirme kontrolleri ve işletim dokümantasyonu.

## İşletim ve kapsam notları

HTTPS, PHP sürümü ve veritabanı bağlantısı hosting üzerinde doğrulanmalıdır. Site işletmecisinin kimlik/iletişim ve saklama süreçlerine uygun hukuki metinler ayrıca tamamlanmalıdır. E-posta doğrulama, parola sıfırlama ve kullanıcı talep/hesap silme süreci bu sürümde otomatik değildir; herkese açık kullanım öncesi işletim kapsamı belirlenmelidir.

## Lisanslar ve kaynaklar

Manrope fontu SIL Open Font License 1.1 ile kullanılır; lisans `assets/fonts/OFL.txt` içinde. Kaynak: https://github.com/google/fonts/tree/main/ofl/manrope . Harici tema veya JavaScript çalışma zamanı bağımlılığı yoktur. Kod için lisans kararı proje sahibi tarafından verilecektir.

Analiz kriteri kaynakları: https://www.kvkk.gov.tr/Icerik/2033/Aydinlatma-Yukumlulugu- ve https://www.kvkk.gov.tr/Icerik/4132/aydinlatma-yukumlulugunun-yerine-getirilmesinde-uyulacak-usul-ve-esaslar-hakkinda-teblig .

Uygulama ve testlerin hazırlanmasında Codex desteği kullanılmıştır. Kaynak dokümanlar geliştirme alanında korunur; yayın paketine konulmaz.
