# Yayın hazırlığı testleri

16 Kasım 2026. PHP 8.5.6 ve ayrı, ağ erişimi kapalı MariaDB test sunucusunda uygulama ve paket kontrolleri yapıldı. SQLite regresyonları PHP 8.0.28 ve 8.5.6 üzerinde ayrıca geçti. Test ortamları mevcut kullanıcı verilerinden ayrı tutuldu.

115 kontrol ve ayrıca 2 HTTPS/domain yönlendirme kontrolü başarılı. Gerçek cPanel, domain ve SSL sertifikası henüz test edilmedi.

| No | Grup | Kontrol | Sonuç |
|---|---|---|---|
| 1 | Birim/veri katmanı · MySQL | Şema tekrar kurulabilir | Geçti |
| 2 | Birim/veri katmanı · MySQL | Parola hash ile saklanır | Geçti |
| 3 | Birim/veri katmanı · MySQL | Zengin ve eksik örnekler farklı skor üretir | Geçti |
| 4 | Birim/veri katmanı · MySQL | Tüm çekirdek örüntüleri içeren örnek 100 üretir | Geçti |
| 5 | Birim/veri katmanı · MySQL | Boş metinde bulgu üretilmez | Geçti |
| 6 | Birim/veri katmanı · MySQL | Türkçe büyük harfler normalize edilir | Geçti |
| 7 | Birim/veri katmanı · MySQL | Tamamlayıcı kontroller çekirdek skoru değiştirmez | Geçti |
| 8 | Birim/veri katmanı · MySQL | Aktarım amacı ilgisiz cümleden tamamlanmaz | Geçti |
| 9 | Birim/veri katmanı · MySQL | Açıklanmayan kimlik olumlu kanıt sayılmaz | Geçti |
| 10 | Birim/veri katmanı · MySQL | Aktarım yapılmadığı açık beyan olarak tanınır | Geçti |
| 11 | Birim/veri katmanı · MySQL | Belirsiz saklama süresi tam bulgu sayılmaz | Geçti |
| 12 | Birim/veri katmanı · MySQL | Şirket kısaltmaları cümle sayısını bozmaz | Geçti |
| 13 | Birim/veri katmanı · MySQL | Belirsiz kapsam ifadeleri uyarı üretir | Geçti |
| 14 | Birim/veri katmanı · MySQL | Aydınlatma ile rızayı birleştiren ifade işaretlenir | Geçti |
| 15 | Birim/veri katmanı · MySQL | İstihdam ve bordro amacı tanınır | Geçti |
| 16 | Birim/veri katmanı · MySQL | Mobil uygulama toplama kanalı tanınır | Geçti |
| 17 | Birim/veri katmanı · MySQL | Salt hukuki sebep adı somut koşul yerine sayılmaz | Geçti |
| 18 | Birim/veri katmanı · MySQL | Yeni misafirin analiz hakkı var | Geçti |
| 19 | Birim/veri katmanı · MySQL | Hatalı metin misafir hakkını tüketmez | Geçti |
| 20 | Birim/veri katmanı · MySQL | İlk misafir analizi hakkı tüketir ve oturum raporu üretir | Geçti |
| 21 | Birim/veri katmanı · MySQL | Misafir metni kalıcı belge tablosuna yazılmaz | Geçti |
| 22 | Birim/veri katmanı · MySQL | İkinci misafir analizi sunucuda reddedilir | Geçti |
| 23 | Birim/veri katmanı · MySQL | Oturum sıfırlansa da tarayıcı hakkı sıfırlanmaz | Geçti |
| 24 | Birim/veri katmanı · MySQL | Başka tarayıcıya önceki misafirin raporu sızmaz | Geçti |
| 25 | Birim/veri katmanı · MySQL | Platform kelimesi form kanalı olarak sayılmaz | Geçti |
| 26 | Birim/veri katmanı · MySQL | Başvuru sözcüğü AŞ unvanı olarak sayılmaz | Geçti |
| 27 | Birim/veri katmanı · MySQL | Sahibi raporu okuyabilir | Geçti |
| 28 | Birim/veri katmanı · MySQL | Başka kullanıcı raporu okuyamaz | Geçti |
| 29 | Birim/veri katmanı · MySQL | Uzman incelemek için raporu okuyabilir | Geçti |
| 30 | Birim/veri katmanı · MySQL | Sekiz kriter kalıcı saklanır | Geçti |
| 31 | Birim/veri katmanı · MySQL | Kısa girdi sunucuda reddedilir | Geçti |
| 32 | Birim/veri katmanı · MySQL | Standart kullanıcı etiket ekleyemez | Geçti |
| 33 | Birim/veri katmanı · MySQL | Aynı uzmanın etiketi çoğalmaz, güncellenir | Geçti |
| 34 | Birim/veri katmanı · MySQL | Etiket değişikliği kalıcı saklanır | Geçti |
| 35 | Birim/veri katmanı · MySQL | HTML çıktısı kaçışlanır | Geçti |
| 36 | Birim/veri katmanı · MySQL | Yanlış CSRF belirteci reddedilir | Geçti |
| 37 | Birim/veri katmanı · MySQL | Doğru CSRF belirteci kabul edilir | Geçti |
| 38 | Birim/veri katmanı · MySQL | Belge silinince analiz ve etiketleri ilişkisel olarak silinir | Geçti |
| 39 | HTTP/MySQL | Ana sayfa misafir analizine açık | Geçti |
| 40 | HTTP/MySQL | Geçmiş sayfası giriş ekranına yönlendirir | Geçti |
| 41 | HTTP/MySQL | Oturumsuz API 401 | Geçti |
| 42 | HTTP/MySQL | Yayında gereksiz proje ve sürüm etiketleri görünmez | Geçti |
| 43 | HTTP/MySQL | E-posta alanı yayına uygun | Geçti |
| 44 | HTTP/MySQL | Veri kullanımı sayfası herkese açık | Geçti |
| 45 | HTTP/MySQL | Normal geliştirme ortamında web kurulum kapalı | Geçti |
| 46 | HTTP/MySQL | Özel yol kapalı: /storage/database.sqlite | Geçti |
| 47 | HTTP/MySQL | Özel yol kapalı: /.env | Geçti |
| 48 | HTTP/MySQL | Özel yol kapalı: /.env.example | Geçti |
| 49 | HTTP/MySQL | Özel yol kapalı: /app/Database.php | Geçti |
| 50 | HTTP/MySQL | Özel yol kapalı: /belgeler/referanslar/proje-konusu-ve-yol-haritasi.md | Geçti |
| 51 | HTTP/MySQL | Özel yol kapalı: /bin/setup.php | Geçti |
| 52 | HTTP/MySQL | Özel yol kapalı: /tests/run.php | Geçti |
| 53 | HTTP/MySQL | Özel yol kapalı: /../storage/database.sqlite | Geçti |
| 54 | HTTP/MySQL | Form CSRF reddi | Geçti |
| 55 | HTTP/MySQL | Giriş ekranında misafir analizine dönüş bağlantısı var | Geçti |
| 56 | HTTP/MySQL | Analiz öncesi misafir raporu yok | Geçti |
| 57 | HTTP/MySQL | Hatalı metin ücretsiz hakkı tüketmez | Geçti |
| 58 | HTTP/MySQL | İlk misafir analizi girişsiz rapora gider | Geçti |
| 59 | HTTP/MySQL | Misafir yeni analiz motorunu kullanır | Geçti |
| 60 | HTTP/MySQL | Başka tarayıcı misafir raporuna erişemez | Geçti |
| 61 | HTTP/MySQL | Misafir JSON raporunu indirebilir | Geçti |
| 62 | HTTP/MySQL | Kullanılan hak ana sayfada gösterilir | Geçti |
| 63 | HTTP/MySQL | İkinci misafir denemesi giriş gerektirir | Geçti |
| 64 | HTTP/MySQL | Kota çerezi kalıcı ve HttpOnly | Geçti |
| 65 | HTTP/MySQL | Oturum çerezi silinse de ücretsiz hak geri gelmez | Geçti |
| 66 | HTTP/MySQL | Giriş sonrası misafir raporu korunur | Geçti |
| 67 | HTTP/MySQL | Misafir raporu açık istekle hesaba kaydedilir | Geçti |
| 68 | HTTP/MySQL | Demo kullanıcı giriş yapar | Geçti |
| 69 | HTTP/MySQL | Oturum çerezi HttpOnly ve SameSite=Lax | Geçti |
| 70 | HTTP/MySQL | Standart kullanıcı uzman sayfasına erişemez | Geçti |
| 71 | HTTP/MySQL | API CSRF reddi | Geçti |
| 72 | HTTP/MySQL | Bozuk JSON 400 | Geçti |
| 73 | HTTP/MySQL | API analiz oluşturur 201 | Geçti |
| 74 | HTTP/MySQL | Belge başlığı XSS kaçışı | Geçti |
| 75 | HTTP/MySQL | Sahibi JSON raporuna erişir | Geçti |
| 76 | HTTP/MySQL | JSON indirme başlığı | Geçti |
| 77 | HTTP/MySQL | Geçmiş arama çalışır | Geçti |
| 78 | HTTP/MySQL | Kayıt akışı, rol yükseltme reddi | Geçti |
| 79 | HTTP/MySQL | Başka kullanıcı API kaydına erişemez | Geçti |
| 80 | HTTP/MySQL | Başka kullanıcı HTML raporuna erişemez | Geçti |
| 81 | HTTP/MySQL | Sahibi olmayan belgeyi değiştiremez | Geçti |
| 82 | HTTP/MySQL | Uzman giriş yapar | Geçti |
| 83 | HTTP/MySQL | Uzman başkasının analizini inceleyebilir | Geçti |
| 84 | HTTP/MySQL | Uzman etiketi kaydedilir | Geçti |
| 85 | HTTP/MySQL | Uzman etiketi kullanıcı raporunda görünür | Geçti |
| 86 | HTTP/MySQL | Yönetici kullanıcı rollerini görebilir | Geçti |
| 87 | HTTP/MySQL | API girdi doğrulaması 422 | Geçti |
| 88 | HTTP/MySQL | Desteklenmeyen metot 405 | Geçti |
| 89 | HTTP/MySQL | Sahibi belge adını değiştirir | Geçti |
| 90 | HTTP/MySQL | İki analiz karşılaştırılır | Geçti |
| 91 | HTTP/MySQL | Sahibi test belgesini siler | Geçti |
| 92 | HTTP/MySQL | Silinen analiz artık okunamaz | Geçti |
| 93 | HTTP/MySQL | Çıkış oturumu kapatır | Geçti |
| 94 | HTTP/MySQL | API istek sınırı 429 ve Retry-After üretir | Geçti |
| 95 | Arşivden kurulum | Paket özel uygulama klasörünü bulur ve kurulum ekranını açar | Geçti |
| 96 | Arşivden kurulum | Kurulum arama motorlarına kapalı | Geçti |
| 97 | Arşivden kurulum | Yanlış kurulum anahtarı reddedilir | Geçti |
| 98 | Arşivden kurulum | Boş MySQL veritabanına yönetici kurulur | Geçti |
| 99 | Arşivden kurulum | Kurulum anahtarı yanıta yansımaz | Geçti |
| 100 | Arşivden kurulum | Kurulum başarıdan sonra otomatik kapanır | Geçti |
| 101 | Arşivden kurulum | Paket özel dosya erişimini kapatır: /app/bootstrap.php | Geçti |
| 102 | Arşivden kurulum | Paket özel dosya erişimini kapatır: /kvkk-private/.env | Geçti |
| 103 | Arşivden kurulum | Paket özel dosya erişimini kapatır: /.env | Geçti |
| 104 | Arşivden kurulum | Paket özel dosya erişimini kapatır: /storage/database.sqlite | Geçti |
| 105 | Arşivden kurulum | Kurulu paket ana sayfası açılır | Geçti |
| 106 | Arşivden kurulum | Yeni yönetici giriş yapar | Geçti |
| 107 | Arşivden kurulum | Kurulum test hesabı veya örnek analiz oluşturmaz | Geçti |
| 108 | Arşivden kurulum | Yönetici yetkileri çalışır | Geçti |
| 109 | Arşivden kurulum | Paket PHP/MySQL üzerinden kalıcı analiz üretir | Geçti |
| 110 | Canlı yapılandırma | Canlı ortam eksik/hatalı APP_URL ayarını reddeder | Geçti |
| 111 | Canlı yapılandırma | Canlı ortam eksik/hatalı DB_DRIVER ayarını reddeder | Geçti |
| 112 | Canlı yapılandırma | Canlı ortam eksik/hatalı DB_PASSWORD ayarını reddeder | Geçti |
| 113 | Canlı yapılandırma | Canlı ortam eksik/hatalı APP_KEY ayarını reddeder | Geçti |
| 114 | Canlı yapılandırma | Canlı ortam eksik/hatalı KVKK_TEST_DB ayarını reddeder | Geçti |
| 115 | Canlı yapılandırma | Tam canlı yapılandırma doğrulanır | Geçti |

## Paket doğrulaması

Yerel veritabanı, kullanıcı hesapları, testler, kaynak dokümanlar ve gerçek .env dosyası arşive alınmadığı kontrol edildi. Arşiv yeni bir klasöre açıldı; public_html dışında uygulama kaynaklarını bulma, ilk yönetici kurulumu, kurulum anahtarı reddi, kurulum kilidi ve MySQL üzerinde kalıcı analiz doğrulandı. Özel dosya erişimleri test router'ıyla kontrol edildi; gerçek Apache/LiteSpeed .htaccess işleyişi hostingde ayrıca doğrulanacak.

Canlı moddaki HTTP ve değiştirilmiş Host başlıkları, yalnızca yapılandırılmış HTTPS adresine 308 yönlendirdi; dış siteye gidilmedi.

## Kalan yayın adımları

Gerçek domain ve MySQL bilgileri girilmesi, cPanel PHP/SSL kontrolü, hosting üzerinde uçtan uca test, gerçek işletmeci aydınlatma/iletişim bilgileri. Lighthouse ve NLP başarı ölçümü yapılmadı; bu testler hukuki doğruluk veya güvenlik sertifikası anlamına gelmez.
