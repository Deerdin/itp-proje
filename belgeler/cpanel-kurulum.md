# cPanel kurulum

Bu paket domain ve MySQL bağlantı bilgileri girildikten sonra kurulacak şekilde hazırlanmıştır. Henüz canlı sunucuya yüklenmemiştir. Mevcut yerel hesaplar, analiz metinleri, testler ve kaynak belgeler pakette bulunmaz.

## Gereksinimler

- cPanel, Apache veya LiteSpeed ve HTTPS sertifikası.
- PHP 8.3 veya 8.4 önerilir; en az PHP 8.2. `pdo_mysql`, `mbstring`, `session`, `json`, `openssl` etkin olmalı.
- MySQL 8 veya MariaDB 10.6+ ve InnoDB.
- Alan adının belge kökü `public_html` veya buna karşılık gelen alt dizin; özel klasör belge kökünün bir üstünde tutulur.

## Dosyaları yerleştirme

1. cPanel Dosya Yöneticisi ayarlarında gizli dosyaları göster seçeneğini açın; `.htaccess` ve `.user.ini` dosyalarının da taşındığından emin olun. Arşivi bilgisayarda veya cPanel ana dizininde açın. `public_html` klasörünün içindekileri domainin belge köküne kopyalayın. Var olan başka bir site varsa dosyalarını üzerine yazmayın.
2. `kvkk-private` klasörünü `public_html` ile aynı seviyeye, web'den erişilemeyen ana dizine yerleştirin. Uygulama `../kvkk-private/app/bootstrap.php` konumunu kullanır. Örnek: `/home/hesap/public_html` ve `/home/hesap/kvkk-private`.
3. `KURULUM.md` ve `MANIFEST.json` dosyalarını `public_html` içine koymayın. PHP iç kaynakları, `.env` ve `storage` dosyaları da web kökünde bulunmamalı.
4. cPanel'in PHP seçicisinden sürümü ve uzantıları ayarlayın. `kvkk-private/storage` PHP kullanıcısı tarafından yazılabilir olmalı; dosyalarda 640/600, dizinlerde 750/700 gibi hesaba uygun sınırlı izinler kullanın. `777` vermeyin.

## MySQL ve ayarlar

1. cPanel MySQL Database Wizard ile boş bir veritabanı ve ayrı bir kullanıcı oluşturun. Kullanıcıya yalnızca bu veritabanının yetkilerini verin; kurulum sırasında CREATE/ALTER/INDEX/REFERENCES ve veri işlem yetkileri gerekir. cPanel'in eklediği hesap öneklerini isimlere dahil edin.
2. `kvkk-private/.env.example` dosyasını aynı dizinde `.env` adıyla kopyalayın.
3. `APP_URL` alanına gerçek `https://domaininiz` adresini yazın. `DB_HOST` hosting sağlayıcısının verdiği adres, diğer DB alanları cPanel'de oluşturulan bilgiler olmalı.
4. `APP_KEY` ve `INSTALL_TOKEN` için birbirinden farklı, en az 64 karakterlik kriptografik rastgele değerler kullanın. Terminal varsa her değer için `php -r 'echo bin2hex(random_bytes(32)),PHP_EOL;'` çalıştırılabilir. Anahtarları sohbet veya URL içinde paylaşmayın.
5. cPanel AutoSSL ile sertifikayı etkinleştirin ve HTTPS yönlendirmesini açın. Uygulama da production modunda yapılandırılmış HTTPS alan adına yönlendirir. APP_URL gerçek adres olmadan uygulama açılmaz. Proxy/CDN kullanılıyorsa uçtan uca HTTPS/Full Strict kullanılmalı; istemci tarafından gönderilen X-Forwarded-* başlıklarına uygulama güvenmez.

## Yönetici kurulum ekranı

1. `https://domaininiz/install.php` adresini açın. Özel `.env` dosyasındaki INSTALL_TOKEN değerini forma girin; URL'ye eklemeyin.
2. Gerçek yönetici e-postası, ad soyad ve güçlü bir parola belirleyin. Parola veritabanında hash olarak saklanır.
3. Kurulum şemayı ve ilk yönetici hesabını oluşturur; `storage/installed.lock` dosyasıyla yeniden kurulumu kapatır. Boş olmayan veritabanında kurulum yapılmaz.
4. Başarılı kurulumdan sonra `public_html/install.php` dosyasını silin ve `.env` içindeki INSTALL_TOKEN değerini kaldırın. Demo hesap oluşturulmaz.

## Bakım

Cron Jobs üzerinden günde bir `php /home/hesap/kvkk-private/bin/maintenance.php` çalıştırılabilir. Bu işlem süresi dolmuş kota/istek kayıtlarını ve geçici oturumları temizler; kullanıcı belgelerine dokunmaz. Gerçek hesap yolunu ve cPanel PHP binary yolunu kullanın.

## Yayın öncesi son kontrol

- Ana sayfa açılıyor, ilk misafir analizi rapor oluşturuyor, ikinci deneme giriş istiyor.
- Kayıt, giriş, çıkış, rapor indirme, belge silme ve uzman yetkileri gerçek domain üzerinden çalışıyor.
- `/.env`, `/storage/`, `/app/` ve `/install.php` erişilemiyor. Hata sayfaları parola, SQL veya sunucu yolu göstermiyor.
- Oturum ve deneme çerezlerinde Secure, HttpOnly, SameSite=Lax; yanıtta CSP, HSTS ve nosniff başlıkları var.
- cPanel otomatik veritabanı/dosya yedeklemesi etkin. Geri yükleme yöntemi denenmiş.
- Veri sorumlusu/iletişim bilgileri ve gerçek saklama politikası belirlendikten sonra hukuki aydınlatma/gizlilik metni yayınlanmalı. Uygulamadaki “Veri kullanımı” sayfası teknik akışı açıklar; kişiye/kuruma özel hukuki metin yerine geçmez.
- E-posta doğrulama ve parola sıfırlama bu sürümde yoktur. E-posta alanı doğrulanmış iletişim adresi olarak kabul edilmemeli; bu özellikler ve hesap silme/talep kanalı genel kullanıma açılmadan önce işletme sürecine göre tamamlanmalıdır.

Domain, sunucu bağlantısı, sertifika ve gerçek işletmeci bilgileri belli olmadan bu adımlar tamamlanmış sayılmaz. Yerel testler canlı hosting uyumluluk testi yerine geçmez.
