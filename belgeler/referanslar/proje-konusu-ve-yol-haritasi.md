# İTP Dönem Projesi & TÜBİTAK 2209-A Proje Seçimi ve Yol Haritası

Bu belge, **İnternet Tabanlı Programlama (İTP)** dersi dönem projesi ile **TÜBİTAK 2209-A Üniversite Öğrencileri Araştırma Projeleri Destekleme Programı** için yapılan konu belirleme, değerlendirme ve planlama görüşmesinin tam özetidir.

---

## 1. Temel Kısıtlar ve Ortak Strateji

İki sürecin resmi belgeleri (`kaynak/` klasöründeki yönerge ve bilgi notları) incelendiğinde ortaya çıkan temel kurallar şunlardır:

### İTP Dersi Dönem Projesi Yönergesi Kriterleri
* **Kapsam:** Çekirdek akış en fazla **5 ekranda** tamamlanabilmelidir (en az 3 ekran zorunlu).
* **Zorunlu Teknik Gereksinimler:**
  * Semantik HTML5 ve duyarlı CSS.
  * Sunucu taraflı katman (`Node.js/Express` veya `Next.js`) ve en az 1 REST API ucu.
  * İlişkisel veya belge tabanlı veritabanı (`PostgreSQL`, `MySQL`, `MongoDB` vb.) — en az 2 varlık ve aralarında anlamlı ilişki.
  * Kayıt/giriş akışı, hash'lenmiş parola, **en az 2 farklı kullanıcı rolü** ve en az 1 korumalı sayfa.
  * Güvenlik önlemleri (XSS, SQL Injection, CSRF, girdi doğrulama).
  * GitHub deposu (`itp2026-grupNN-projeadi`), canlı URL (ücretsiz bulut) ve Lighthouse erişilebilirlik puanı ≥ 85.
* **Uygun Görülmeyen Konular (Madde 7):** *"Yalnızca bir yapay zekâ servisine istek gönderip yanıtı ekrana yazan, kendi veri katmanı bulunmayan uygulamalar"* kabul edilmemektedir.

### TÜBİTAK 2209-A Kriterleri
* Yalnızca "bir web sitesi/uygulama geliştirmek" yeterli değildir; **araştırma sorusu / hipotezi**, **ölçülebilir bağımlı/bağımsız değişkenleri**, **literatür boşluğu** ve **Öncelikli Alanlar (2025-2)** ile uyumu bulunmalıdır.
* **Ön Çalışma Avantajı:** Başvuru rehberinde *"Araştırma önerisinde herhangi bir ön çalışma veya fizibilite yapıldıysa bunların sunulması beklenir"* ifadesi yer almaktadır.

### Belirlenen Strateji
İTP yönergesindeki basit örnek konular yerine doğrudan **TÜBİTAK 2209-A düzeyinde bilimsel ve yenilikçi bir proje** kurgulanmasına; bu projenin **çekirdek web arayüzü ve veri katmanının** ise İTP dersi kapsamında canlıya alınan bir MVP olarak uyarlanmasına karar verilmiştir.

---

## 2. Değerlendirilen İddialı TÜBİTAK 2209-A Konu Alternatifleri

1. **KVKK ve Bilişim Hukuku Uyumluluğu İçin Türkçe NLP Tabanlı Gizlilik Politikası Denetim Sistemi** *(Öncelikli Alan: Bilişim hukuku + Yapay zekâ)* — **SEÇİLEN KONU**
2. **Küçük Ölçekli Web Uygulamaları İçin Açıklanabilir (XAI) API Anomali ve Saldırı Tespit Platformu** *(Öncelikli Alan: Siber güvenlik + Yapay zekâ)*
3. **Afet Sonrası Çok Kriterli Karar Destek (MCDM) ve Dinamik Rota/Kaynak Optimizasyon Platformu** *(Öncelikli Alan: Afet yönetimi teknolojileri + Modelleme ve simülasyon teknolojileri)*
4. **Disleksi ve Öğrenme Güçlüğü Olan Öğrenciler İçin Göz/Etkileşim İzleme Tabanlı Uyarlanabilir Okuma Sistemi** *(Öncelikli Alan: Eğitimde dijitalleşme ve yenilikçilik + Yapay zekâ)*
5. **Tarımsal Üretimde Görüntü İşleme ve Yerel İklim Verisiyle Erken Hastalık Tahmin & Çiftçi Doğrulama Ağı** *(Öncelikli Alan: Yapay zekâ + Tarım biyoteknolojisi)*

---

## 3. Seçilen Konu: KVKK ve Bilişim Hukuku Uyumluluğu İçin Türkçe NLP Tabanlı Gizlilik Politikası Denetim Sistemi

* **TÜBİTAK 2209-A Öncelikli Alanı:** *Bilişim hukuku* ve *Yapay zekâ*
* **Problem Tanımı:** Web sitelerinin ve uygulamaların aydınlatma metinleri teknik/hukuki karmaşıklık nedeniyle kullanıcılar tarafından anlaşılamamakta ve çoğu zaman 6698 sayılı KVKK'nın zorunlu maddelerini (özellikle Madde 10 ve Madde 11) eksik bırakmaktadır.
* **Bilimsel Yöntem ve Deney:**
  * 6698 sayılı KVKK Madde 10 ve ilgili Tebliğ esas alınarak 6-8 kriterlik bir **etiketleme rubriği** oluşturulur.
  * Farklı sektörlerden (e-ticaret, finans, eğitim vb.) halka açık Türkçe aydınlatma metinleri toplanıp etiketlenerek **Türkçe KVKK Aydınlatma Metni Veri Seti** oluşturulur.
  * *Kural Tabanlı (Regex/Anahtar Kelime)* baseline yöntemi ile *Türkçe BERT (`bert-base-turkish-cased` vb.) / Hibrit Model* karşılaştırılır.
  * Başarım metrikleri olarak **F1-Skoru, Precision, Recall** ve uzman hukukçu değerlendirmesiyle tutarlılık (**Cohen's Kappa**) ölçülür.
* **İTP Dersine Uyarlama (Web MVP):**
  * **Roller:** Standart Kullanıcı (metin/URL tarayan), Hukuk/Denetim Uzmanı (model çıktısını doğrulayan/etiketleyen), Yönetici.
  * **Veri Katmanı (Varlıklar):** `Kullanici`, `Politika_Belgesi`, `Analiz_Raporu`, `Kriter_Risk_Skoru`, `Uzman_Dogrulama`.
  * **Çekirdek Ekranlar (Max 5):**
    1. Giriş / Kayıt
    2. Politika Metni / URL Tarama Ekranı
    3. Madde Bazlı Uyumluluk & Risk Raporu
    4. Geçmiş Analizler & Karşılaştırma Paneli
    5. Uzman Doğrulama / Etiketleme Kuyruğu

---

## 4. Genel Yol Haritası (İTP + TÜBİTAK 2209-A Paralel Süreci)

### Adım 1: Kapsam ve Bilimsel Çerçevenin Netleştirilmesi (Şimdi – 3. Hafta)
* İTP dersi hocası ve 2209-A danışmanı ile fikrin paylaşılması (İTP Madde 10 dışındaki Python/NLP servisi gibi teknolojiler için 9. haftadan önce onay alınması gerekir).
* 6698 sayılı KVKK Madde 10 ve ilgili Tebliğ'e dayalı değerlendirme rubriğinin çıkarılması.
* İTP 3. hafta teslimi için ana konu önerisi (ve yönerge gereği 1 yedek konu önerisi) hazırlanıp 4. haftada sunulması.

### Adım 2: Veri Seti ve Literatür Altyapısı (4. – 6. Hafta | İTP Aşama 1)
* Türkçe ve İngilizce *Automated Privacy Policy Analysis / GDPR-KVKK NLP* literatürünün taranması (DOI numaralarının kaydedilmesi — 2209-A'da zorunlu).
* Halka açık Türkçe aydınlatma metinlerinin toplanıp etiketlenmesi.
* **5. Hafta:** `itp2026-grupNN-projeadi` GitHub deposunun açılması, hocanın eklenmesi, branch/PR düzeninin kurulması.
* **6. Hafta:** 6-10 sayfalık **İhtiyaç Analizi Raporu**'nun (PDF) teslim edilmesi.

### Adım 3: Sistem Mimarisi, ER Modeli ve Yöntem Tasarımı (7. – 9. Hafta | İTP Aşama 2)
* Veritabanı ER diyagramı, API uç noktası tablosu ve 5 ekranlık wireframe'lerin hazırlanması.
* 2209-A için karşılaştırmalı deney tasarımının (Baseline vs. BERT/Hibrit) detaylandırılması.
* **9. Hafta:** Tasarım Dosyası + ER Diyagramı + API Tablosu teslimi.

### Adım 4: Web MVP Geliştirme ve Canlı Yayın (10. – 13. Hafta | İTP Aşama 3 & 4)
* Arayüz, Auth (hash'li parola + rol yetkilendirmesi), REST API, veritabanı ve analiz motoru entegrasyonu.
* Güvenlik (XSS, SQLi, CSRF) ve Lighthouse erişilebilirlik (≥ 85) kontrolleri.
* **12. Hafta:** Ara kontrol (çalışan çekirdek akış demosu).
* **13. Hafta:** Ücretsiz bulut ortamında (Vercel/Render/Railway) canlı yayın, test tablosu, yapay zekâ kullanım beyanı ve final raporu teslimi.

### Adım 5: 2209-A Başvurusu ve Bilimsel Deneylerin Yürütülmesi
* Literatür, pilot veri seti/demo ön çalışması, yöntem, en fazla 12 aylık Çalışma Takvimi (başarı ölçütleri toplamı = 100), Risk Yönetimi (B Planı) ve Yaygın Etki bölümleriyle 2209-A başvurusunun tamamlanması.
* Canlı platform üzerinden veri setinin büyütülmesi, model kıyaslamalarının ve kullanılabilirlik (SUS) testlerinin yürütülüp raporlanması.

---

## 5. Hemen Uygulanacak İlk Eylem Planı (Hocaya Sunum Paketi)

Hocanın karşısına yalnızca fikirle değil, **somut bir ön çalışma (PoC)** ve **resmi raporla** çıkmak için kararlaştırılan 4 adımlı plan:

1. **Küçük Demo Uygulaması (PoC):**
   * Örnek veya yapıştırılan bir KVKK Aydınlatma Metnini 6698 sayılı KVKK Madde 10 kriterlerine göre (Veri Sorumlusu Kimliği, İşleme Amacı, Aktarım Bilgisi, Toplama Yöntemi & Hukuki Sebep, Madde 11 Hakları, Başvuru Yolu) analiz eden; ekranda uyumluluk skoru (%), eksik/riskli maddeler ve metin içi vurguları gösteren çalışan bir demo.
2. **GitHub Deposu ve `README.md`:**
   * İTP Ek-1 şablonuna uygun, projenin amacı, konusu, özellikleri, teknoloji yığını, kurulum adımları ve 2209-A vizyonunu içeren düzenli bir `README.md`.
3. **Canlı URL Yayını:**
   * Demo uygulamasının Vercel veya Render üzerinden yayına alınarak tek tıkla erişilebilir hâle getirilmesi.
4. **TÜBİTAK 2209-A Uyumlu Kapsamlı Proje Raporu:**
   * `2209-A_Basvuru_Icerigi_Bilgi_Notu.pdf` başlıklarına uygun (Özet, Bilimsel Nitelik/Konunun Önemi, Amaç-Hedefler, Yöntem + Demo Ön Çalışması, Çalışma Takvimi, Risk Yönetimi, Yaygın Etki ve İTP Entegrasyonu) kapsamlı raporun hazırlanıp GitHub depo linki ve canlı demo URL'si ile birlikte hocaya iletilmesi.
