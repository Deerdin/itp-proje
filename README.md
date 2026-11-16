# KVKK-Denetçi — Türkçe NLP Tabanlı Gizlilik Politikası ve Aydınlatma Metni Denetim Sistemi

**KVKK-Denetçi**, web siteleri ve mobil uygulamalardaki Türkçe aydınlatma metinlerini **6698 sayılı Kişisel Verilerin Korunması Kanunu (KVKK) Madde 10** ve **Aydınlatma Yükümlülüğünün Yerine Getirilmesinde Uyulacak Usul ve Esaslar Hakkında Tebliğ** esaslarına göre otomatik olarak denetleyen bir karar destek ve uyumluluk analizi platformudur. Proje, **Atatürk Üniversitesi Uygulamalı Bilimler Fakültesi (Oltu) — Bilişim Sistemleri ve Teknolojileri** programı kapsamında **İnternet Tabanlı Programlama (İTP)** dönem projesi ve **TÜBİTAK 2209-A** araştırma önerisi (Öncelikli Alanlar: *Bilişim Hukuku* ve *Yapay Zekâ*) bütünleşik vizyonuyla geliştirilmektedir.

---

## Canlı URL ve Deneme Hesapları

* **Canlı Demo URL (PoC v0.1):** *GitHub Pages / Vercel yayını eklenecek* (`https://deerdin.github.io/itp-proje/` veya Vercel URL)
* **Yerel Çalıştırma:** Mevcut PoC sürümü sıfır bağımlılıkla doğrudan `index.html` dosyasının tarayıcıda açılmasıyla çalışmaktadır.
* **Deneme Hesapları (İTP MVP Aşaması):**
  * *Standart Kullanıcı / Hukuk Uzmanı / Yönetici* rolleri ve kimlik doğrulama akışı Geliştirme Aşaması (10.–13. Hafta) kapsamında eklenecektir.

---

## Ekran Görüntüleri

> PoC v0.1 arayüzü tek ekranda metin girişi, uyumluluk skoru (%), okunabilirlik metrikleri, 8 kriterli denetim tablosu ve cümle bazlı açıklanabilirlik (XAI) vurgularını sunmaktadır. Canlı yayın sonrasında ekran görüntüleri `docs/screenshots/` klasörüne eklenecektir.

---

## Özellikler

### Mevcut Ön Çalışma (PoC v0.1)
* **8 Kriterli KVKK Madde 10 Denetimi:** Veri sorumlusu kimliği, işleme amacı, aktarım, toplama yöntemi, hukuki sebep, Madde 11 hakları, başvuru kanalı ve saklama/imha bilgisi kontrolü.
* **Kural Tabanlı Baseline NLP Motoru:** Türkçe ek/kök duyarlı düzenli ifadeler (Regex) ve ağırlıklı terim eşleştirme ile 100 üzerinden ağırlıklı uyumluluk skoru hesaplama.
* **Açıklanabilirlik (XAI) Vurgulama:** Her cümlenin hangi KVKK kriterini karşıladığını rozetlerle gösterme.
* **Okunabilirlik Analizi:** Kelime sayısı, cümle sayısı, ortalama cümle uzunluğu ve tahmini okuma süresi.
* **JSON Rapor Dışa Aktarma:** Analiz çıktısını yapılandırılmış JSON formatında panoya kopyalama ve indirme.
* **Hazır Örnek Senaryolar:** Tam uyumlu e-ticaret, eksik/riskli SaaS ve yetersiz kısa metin örnekleriyle tek tıkla test.

### Hedeflenen İTP Web MVP Özellikleri (10.–13. Hafta)
* 5 çekirdek ekranlı tam yığın web mimarisi (Giriş/Kayıt, Tarama Ekranı, Madde Bazlı Risk Raporu, Geçmiş Analizler & Karşılaştırma, Uzman Doğrulama/Etiketleme Kuyruğu).
* Hash'lenmiş parola ve rol tabanlı erişim kontrolü (Standart Kullanıcı, Hukuk/Denetim Uzmanı, Yönetici).
* İlişkisel veritabanı katmanı (`Kullanici`, `Politika_Belgesi`, `Analiz_Raporu`, `Kriter_Risk_Skoru`, `Uzman_Dogrulama`).
* İnce ayarlı Türkçe BERT (`BERTurk`) hibrit model entegrasyonu.

---

## Teknoloji Yığını

| Katman | PoC v0.1 (Mevcut) | Hedef İTP Web MVP & 2209-A Mimarisi |
| :--- | :--- | :--- |
| **Ön Yüz** | Semantik HTML5, Duyarlı CSS3, Vanilla JS | Semantik HTML5, CSS3 / Next.js veya Express şablonları |
| **Sunucu & API** | İstemci taraflı PoC motoru | Node.js / Express REST API (XSS, SQLi, CSRF korumalı) |
| **Veritabanı** | — | PostgreSQL / SQLite (İlişkisel şema) |
| **NLP / Yapay Zekâ** | Kural Tabanlı Baseline (Regex + Ağırlık) | Python FastAPI mikroservisi + `BERTurk` hibrit sınıflandırıcı |
| **Yayın** | GitHub Pages / Vercel (Statik) | Vercel / Render (Ücretsiz bulut ortamı) |

---

## Kurulum

### PoC v0.1 (Mevcut Sürüm)
1. Depoyu klonlayın:
   ```bash
   git clone https://github.com/Deerdin/itp-proje.git
   cd itp-proje
   ```
2. `index.html` dosyasını doğrudan modern bir tarayıcıda açın (veya VS Code Live Server ile başlatın).

---

## Ortam Değişkenleri

Mevcut statik PoC sürümü ortam değişkeni gerektirmez. İTP Geliştirme aşamasında sunucu ve veritabanı katmanı eklendiğinde `.env.example` dosyası üzerinden aşağıdaki değişkenler yapılandırılacaktır:
* `PORT`: Sunucu çalışma portu
* `DATABASE_URL`: Veritabanı bağlantı adresi
* `JWT_SECRET` / `SESSION_SECRET`: Oturum ve kimlik doğrulama gizli anahtarı
* `NLP_SERVICE_URL`: Python tabanlı BERTurk analiz servisi uç noktası

---

## Proje Yapısı

```text
itp-proje/
├── index.html                        # KVKK-Denetçi PoC ana sayfası (semantik HTML5)
├── styles.css                        # Duyarlı ve erişilebilir arayüz stilleri
├── app.js                            # 8 kriterli KVKK m.10 kural tabanlı baseline motoru
├── docs/
│   └── TUBITAK_2209A_Proje_Raporu.md # Hocaya sunulacak TÜBİTAK 2209-A uyumlu proje raporu
├── .gitignore                        # Git dışlama kuralları
└── README.md                         # Proje dokümantasyonu (İTP Ek-1 uyumlu)
```

---

## Ekip ve Katkılar

* **Proje Ekibi:** *Grup üyelerinin ad-soyad, GitHub kullanıcı adı ve görev dağılımları 5. hafta tesliminde güncellenecektir.*
* **Ders Sorumlusu:** Doç. Dr. Hamza POLAT (Atatürk Üniversitesi)

---

## Bilinen Eksikler (PoC Aşaması)

* Sunucu taraflı veritabanı, kullanıcı kaydı/girişi ve geçmiş analizlerin kalıcı olarak saklanması henüz eklenmemiştir (İTP 10.–13. hafta takviminde geliştirilecektir).
* Mevcut analiz motoru kural tabanlı baseline yaklaşımla çalışmaktadır; anlamsal belirsizliklerin çözümü için `BERTurk` tabanlı derin öğrenme modeli 2209-A süreciyle entegre edilecektir.

---

## Yapay Zekâ Kullanım Beyanı

Bu ön çalışma aşamasında; arayüz iskeleti, 8 kriterli rubrik örüntülerinin kodlanması ve dokümantasyon taslaklarının yapılandırılmasında üretken yapay zekâ asistanından destek alınmıştır. Tüm hukuki kriterler 6698 sayılı KVKK Madde 10 metniyle karşılaştırılarak ekip tarafından doğrulanmıştır.

---

## Lisans ve Kaynaklar

* Bu proje eğitim ve akademik araştırma amaçlı geliştirilmektedir (MIT Lisansı önerilir).
* **Temel Mevzuat:** 6698 Sayılı Kişisel Verilerin Korunması Kanunu; Aydınlatma Yükümlülüğünün Yerine Getirilmesinde Uyulacak Usul ve Esaslar Hakkında Tebliğ.
* **Akademik Kaynaklar:** Wilson vd. (2016) `10.18653/v1/P16-1126`, Harkous vd. (2018) Polisis, Schweter (2020) `10.5281/zenodo.3770924`, Can (2019) `10.21205/deufmd.2019216220`.
