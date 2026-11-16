# TÜBİTAK 2209-A ARAŞTIRMA PROJESİ ÖNERİ RAPORU

**Proje Başlığı:** KVKK ve Bilişim Hukuku Uyumluluğu İçin Türkçe Doğal Dil İşleme (NLP) Tabanlı Açıklanabilir Gizlilik Politikası Denetim Sistemi  
**İlişkili TÜBİTAK 2209-A Öncelikli Alanları:** Bilişim Hukuku, Yapay Zekâ  
**Ders Entegrasyonu:** İnternet Tabanlı Programlama (İTP) Dönem Projesi — Atatürk Üniversitesi Uygulamalı Bilimler Fakültesi (Oltu), Bilişim Sistemleri ve Teknolojileri  
**Çalışan Ön Çalışma (PoC) Deposu:** `https://github.com/Deerdin/itp-proje`

---

## ÖZET

Dijital hizmetlerin yaygınlaşmasıyla birlikte web siteleri ve mobil uygulamalar tarafından sunulan kişisel veri aydınlatma metinlerinin sayısı hızla artmıştır. Ancak bu metinler, yoğun hukuki ve teknik terminoloji içermeleri nedeniyle son kullanıcılar tarafından yeterince anlaşılamamakta; veri sorumluları ise 6698 sayılı Kişisel Verilerin Korunması Kanunu'nun (KVKK) özellikle 10. maddesinde zorunlu kılınan unsurları (veri sorumlusu kimliği, işleme amacı, aktarım, toplama yöntemi ve hukuki sebep, 11. madde hakları) uygulamada eksik veya muğlak bırakabilmektedir. Uluslararası literatürde İngilizce gizlilik politikalarını (GDPR/CCPA) analiz eden çeşitli makine öğrenmesi çalışmaları bulunmasına karşın, sondan eklemeli morfolojik yapıya sahip Türkçe için KVKK Madde 10 kriterlerine dayalı, uzman doğrulamalı ve açıklanabilir (XAI) bir otomatik denetim veri seti ve karşılaştırmalı derin öğrenme modeli eksikliği sürmektedir.

Bu araştırmada; 6698 sayılı KVKK Madde 10 ve Aydınlatma Yükümlülüğü Tebliği esas alınarak **8 kriterli bir değerlendirme rubriği** geliştirilecek ve farklı sektörlerden (e-ticaret, finans, eğitim, sağlık, SaaS) toplanan halka açık Türkçe aydınlatma metinleri cümle düzeyinde etiketlenerek özgün bir **Türkçe KVKK Aydınlatma Metni Veri Seti** oluşturulacaktır. Yöntem kapsamında; (1) ön çalışma olarak geliştirilen **kural tabanlı (Regex ve ağırlıklı terim) baseline modeli** ile (2) Türkçe önceden eğitilmiş dil modeli (**BERTurk**) ve **hibrit (Kural + BERTurk) yaklaşım** aynı veri seti üzerinde karşılaştırılacaktır. Modellerin başarımı Precision, Recall, Macro-F1 ve istatistiksel anlamlılık (McNemar testi) ile değerlendirilecek; model-uzman tutarlılığı Cohen's Kappa katsayısıyla ölçülecektir. Ayrıca sistem, cümle bazlı kanıt vurgulama özelliğiyle web tabanlı bir karar destek arayüzüne (İTP dönem projesi MVP'si) dönüştürülerek Sistem Kullanılabilirlik Ölçeği (SUS) ile test edilecektir.

Proje, en fazla 10 aylık (≤12 ay) bir çalışma takvimiyle yürütülecektir. Elde edilecek açık kaynak veri seti, karşılaştırmalı model bulguları ve web tabanlı denetim aracı; KOBİ'lerin ve yazılım geliştiricilerin KVKK uyum maliyetlerini azaltmayı, tüketicilerin dijital veri okuryazarlığını artırmayı ve bilişim hukuku–yapay zekâ kesişiminde ulusal literatüre tekrarlanabilir bir bilimsel katkı sunmayı hedeflemektedir.

---

## 1. ARAŞTIRMA ÖNERİSİNİN BİLİMSEL NİTELİĞİ

### 1.1. Konunun Önemi ve Araştırma Önerisinin Bilimsel Niteliği

Kişisel verilerin korunması, dijital ekonominin temel güven unsurlarından biridir. Türkiye'de 6698 sayılı KVKK Madde 10 ve ilgili Tebliğ uyarınca veri sorumluları; verilerin hangi amaçla işleneceğini, kimlere aktarılabileceğini, toplama yöntemini, hukuki sebebini ve ilgili kişinin Madde 11 haklarını açık ve anlaşılır biçimde bildirmekle yükümlüdür. Buna karşın uygulamada yayımlanan aydınlatma metinleri ya uzun ve karmaşık hukuki ifadeler nedeniyle kullanıcılar tarafından okunmamakta ya da kopyala-yapıştır şablonlar yüzünden zorunlu yasal unsurları eksik bırakmaktadır.

Uluslararası literatürde gizlilik politikalarının otomatik analizi konusunda Wilson vd. (2016) tarafından oluşturulan OPP-115 derlemi (DOI: `10.18653/v1/P16-1126`) ve Harkous vd. (2018) tarafından geliştirilen Polisis derin öğrenme çerçevesi önemli dönüm noktalarıdır. Ancak bu çalışmalar İngilizce dil yapısına ve GDPR/ABD hukuk sistemine odaklanmaktadır. Türkiye özelinde Can (2019), KVKK için onam ontolojisi ve TF-IDF/terim çıkarımı tabanlı bir yaklaşım sunmuştur (DOI: `10.21205/deufmd.2019216220`); fakat Türkçe'nin sondan eklemeli zengin morfolojisini bağlamsal olarak modelleyen, 6698 sayılı KVKK Madde 10'un tüm alt bileşenlerini cümle düzeyinde sınıflandıran ve bunu açıklanabilir bir web arayüzüyle birleştiren açık erişimli bir Türkçe BERT/Hibrit karşılaştırma çalışması literatürde bulunmamaktadır.

**Araştırma Soruları ve Hipotezler:**
* **AS1:** Türkçe aydınlatma metinlerinde KVKK Madde 10 zorunlu unsurlarının tespitinde bağlamsal dil modeli (`BERTurk`) tabanlı yaklaşım, kural tabanlı (Regex/anahtar kelime) baseline yöntemine göre istatistiksel olarak anlamlı düzeyde daha yüksek Macro-F1 başarımı sağlar mı?
* **H1:** İnce ayarlı `BERTurk` ve kural tabanlı örüntülerin birleştirildiği hibrit model, tek başına kural tabanlı baseline yöntemine kıyasla Macro-F1 skorunda en az %12 mutlak artış sağlayacaktır.
* **H2:** Cümle bazlı kanıt vurgulama (açıklanabilirlik) sunan web arayüzü, standart kullanıcılarda ve geliştiricilerde kullanılabilirlik açısından SUS ≥ 75 puanına ulaşacaktır.

**Öncelikli Alanlar ve Ulusal Strateji İlişkisi:** Bu proje, TÜBİTAK 2209-A *Öncelikli Alanlar (2025-2)* listesinde yer alan **"Bilişim hukuku"** ve **"Yapay zekâ"** alanlarıyla doğrudan örtüşmektedir. Aynı zamanda 12. Kalkınma Planı ve 2030 Sanayi ve Teknoloji Stratejisi'nin dijital dönüşüm, yerli yapay zekâ uygulamaları ve veri güvenliği hedeflerine doğrudan katkı sunmaktadır.

### 1.2. Amaç ve Hedefler

Projenin temel amacı, 6698 sayılı KVKK Madde 10 uyumluluğunu cümle düzeyinde otomatik olarak denetleyen, eksik/riskli maddeleri raporlayan ve kararlarını metin içi kanıtlarla açıklayan Türkçe NLP tabanlı bir denetim sistemi geliştirmektir.

**Ölçülebilir Hedefler:**
1. **Rubrik ve Veri Seti Hedefi:** KVKK Madde 10 ve Aydınlatma Tebliği'ne dayalı 8 kriterli bir etiketleme rehberi oluşturmak; en az **150 farklı Türkçe aydınlatma metnini** (yaklaşık 2.500–3.500 cümle) iki bağımsız değerlendiriciyle etiketleyerek **Cohen's Kappa ≥ 0.80** tutarlılığına ulaşan açık bir veri seti üretmek.
2. **Modelleme ve Kıyaslama Hedefi:** Kural tabanlı baseline, ince ayarlı `BERTurk` (Schweter, 2020; DOI: `10.5281/zenodo.3770924`) ve Hibrit modelleri eğitip karşılaştırarak **Macro-F1 ≥ 0.82** başarımına ulaşmak.
3. **Sistem ve Kullanılabilirlik Hedefi:** Modelleri rol tabanlı bir web platformuna (İTP MVP) entegre etmek ve en az 20 katılımcı ile yapılacak **SUS (System Usability Scale)** testinde **≥ 75** puan elde etmek.

---

## 2. YÖNTEM

### 2.1. Araştırma Tasarımı ve Değişkenler

Araştırma; veri seti oluşturma, model geliştirme/karşılaştırma ve web tabanlı karar destek sistemi doğrulaması olmak üzere üç aşamalı deneysel bir tasarıma dayanmaktadır.

* **Bağımsız Değişkenler:** Sınıflandırma mimarisi (Kural Tabanlı Baseline, İnce Ayarlı `BERTurk`, Hibrit Model), metin sektörü (e-ticaret, finans, eğitim, SaaS, sağlık), cümle uzunluğu ve hukuki terim yoğunluğu.
* **Bağımlı Değişkenler:** Kriter bazlı ve genel Precision, Recall, Macro-F1 skorları, model çıkarım süresi (ms), uzman etiketleriyle uyuşma oranı (Cohen's Kappa) ve kullanıcı kullanılabilirlik puanı (SUS).

### 2.2. Veri Toplama ve 8 Kriterli Etiketleme Rubriği

Kamuya açık Türkçe web sitelerinden toplanacak aydınlatma metinleri anonimleştirilerek cümlelere bölünecektir. Her cümle çok etiketli (multi-label) olarak aşağıdaki 8 kritere göre etiketlenecektir:
1. **K1 (%15):** Veri sorumlusunun ve varsa temsilcisinin kimliği (KVKK m.10/1-a)
2. **K2 (%15):** Kişisel verilerin işlenme amacı (KVKK m.10/1-b)
3. **K3 (%15):** İşlenen verilerin kimlere ve hangi amaçla aktarılabileceği (KVKK m.10/1-c)
4. **K4 (%10):** Kişisel veri toplamanın yöntemi (KVKK m.10/1-ç)
5. **K5 (%15):** Kişisel veri toplamanın hukuki sebebi (KVKK m.10/1-ç, m.5–6)
6. **K6 (%15):** İlgili kişinin Madde 11 kapsamındaki hakları (KVKK m.10/1-d)
7. **K7 (%10):** Başvuru usulü ve iletişim kanalı (KEP, e-posta, adres)
8. **K8 (%5):** Saklama süresi, imha ve güncelleme bilgisi

### 2.3. Ön Çalışma / Fizibilite (Geliştirilen PoC Uygulaması)

Başvuru öncesinde yöntemin uygulanabilirliğini doğrulamak amacıyla **KVKK-Denetçi PoC v0.1** prototipi geliştirilmiştir (`index.html`, `styles.css`, `app.js`). Bu ön çalışmada:
* 8 kriterli KVKK Madde 10 rubriği için kural tabanlı baseline motoru (Türkçe düzenli ifadeler ve ağırlıklı terim eşleştirme) kodlanmıştır.
* Yapıştırılan veya hazır senaryolardan seçilen (tam uyumlu e-ticaret, eksik SaaS, yetersiz kısa metin) aydınlatma metinleri için ağırlıklı uyumluluk skoru (%), okunabilirlik metrikleri, kriter bazlı eksik/öneri tablosu ve cümle içi kanıt vurgulama (XAI) çıktıları gerçek zamanlı olarak üretilebilmiştir.
* İlk gözlemler, kural tabanlı yöntemin açık anahtar kelimeler içeren metinlerde hızlı sonuç verdiğini ancak dolaylı hukuki ifadelerde (ör. *"ilgili mevzuat gereğince yetkili mercilerle paylaşılır"* gibi açıkça "aktarım" kelimesi geçmeyen cümlelerde) yanlış negatif ürettiğini göstermiştir. Bu bulgu, bağlamsal `BERTurk` modeline duyulan ihtiyacı somut olarak kanıtlamaktadır.

### 2.4. İstatistiksel Yöntemler

Veri seti %70 eğitim, %15 doğrulama ve %15 test olarak katmanlı (stratified) biçimde ayrılacaktır. Etiketleyici tutarlılığı **Cohen's Kappa** ile, modeller arası performans farkının istatistiksel anlamlılığı **McNemar testi** (p < 0.05) ile analiz edilecektir.

---

## 3. PROJE YÖNETİMİ

### 3.1. Çalışma Takvimi (10 Aylık Plan)

> *Not: TÜBİTAK 2209-A kuralları gereği proje süresi ≤ 12 ay planlanmış; literatür taraması, sonuç raporu yazımı ve malzeme alımı ayrı iş adımı olarak gösterilmemiş; Başarı Ölçütü katkı değerlerinin toplamı tam olarak 100 olacak şekilde düzenlenmiştir.*

| İş Paketi (İP) No | İş Paketinin Adı ve Kapsamı | Süre (Ay) | Başarı Ölçütü (Katkı %) |
| :--- | :--- | :---: | :---: |
| **İP 1** | 8 Kriterli KVKK Madde 10 Etiketleme Rehberinin Oluşturulması ve Sektörel Aydınlatma Metinlerinin Toplanması | 1–2. Ay | 150+ metnin derlenmesi ve rubrik doğrulaması (**20**) |
| **İP 2** | Cümle Düzeyinde Çok Etiketli Veri Setinin Oluşturulması ve Etiketleyici Tutarlılık Analizi | 3–4. Ay | ≥ 2.500 cümle etiketleme, Cohen's Kappa ≥ 0.80 (**20**) |
| **İP 3** | Kural Tabanlı Baseline, İnce Ayarlı `BERTurk` ve Hibrit NLP Modellerinin Geliştirilmesi ve Kıyaslanması | 5–7. Ay | Test setinde Macro-F1 ≥ 0.82 ve McNemar anlamlılığı (**30**) |
| **İP 4** | Açıklanabilir Web Karar Destek Platformunun (MVP) Kurulumu ve NLP Servis Entegrasyonu | 8–9. Ay | 5 ekranlı tam yığın sistemin canlıya alınması (**15**) |
| **İP 5** | Uzman Doğrulama, Pilot Saha Testi ve SUS Kullanılabilirlik Değerlendirmesi | 10. Ay | ≥ 20 katılımcı ile SUS ≥ 75 ve uzman onayı (**15**) |
| | **TOPLAM** | **10 Ay** | **100** |

### 3.2. Risk Yönetimi

| Olası Risk | Etki | B Planı (Temel Hedeflerden Sapmadan Alınacak Tedbir) |
| :--- | :---: | :--- |
| Etiketleyiciler arası tutarlılığın düşük kalması (Kappa < 0.80) | Yüksek | Belirsiz maddeler için hukuk danışmanı hakemliğinde rubrik örnekleri genişletilecek ve 20 metinlik kalibrasyon turu tekrarlanacaktır. |
| Az temsil edilen kriterlerde (ör. K8 Saklama/İmha) sınıf dengesizliği | Orta | Odaklı örnekleme ile ek metin toplanacak; eğitim sırasında sınıf ağırlıklı kayıp fonksiyonu (weighted BCE loss) ve geri-çeviri/eşanlamlı veri artırma uygulanacaktır. |
| `BERTurk` modelinin ücretsiz bulut ortamında bellek/hız sınırına takılması | Orta | Model kuantizasyonu (INT8 / ONNX Runtime) veya DistilBERTurk mimarisine geçilerek düşük gecikmeli API çıkarımı korunacaktır. |

### 3.3. Araştırma Olanakları

Araştırma; Atatürk Üniversitesi Uygulamalı Bilimler Fakültesi (Oltu) Bilişim Sistemleri ve Teknolojileri laboratuvarındaki bilgisayar altyapısı, açık kaynaklı Python NLP ekosistemi (PyTorch, Hugging Face Transformers), Google Colab / Kaggle GPU kaynakları ve versiyon kontrolü için GitHub altyapısı kullanılarak ek donanım maliyeti gerektirmeden yürütülecektir.

---

## 4. ARAŞTIRMA ÖNERİSİNİN YAYGIN ETKİSİ

* **Akademik Çıktılar:** Ulusal bir hakemli dergide veya uluslararası bir konferansta (ör. IEEE UBMK / ASYU) en az 1 bildiri/makale sunulması; anonimleştirilmiş Türkçe KVKK Aydınlatma Metni Veri Setinin ve eğitilmiş model ağırlıklarının araştırmacılara açık erişimli (GitHub/Hugging Face) olarak paylaşılması.
* **Ekonomik ve Sektörel Çıktılar:** Hukuk/uyum bütçesi kısıtlı KOBİ'lerin ve yazılım girişimlerinin kendi aydınlatma metinlerini ön denetimden geçirebilmesi, veri ihlali ve idari para cezası risklerinin erken aşamada azaltılması.
* **Toplumsal Çıktılar:** Açıklanabilir skor ve sade özetler sayesinde son kullanıcıların kişisel verilerinin nasıl işlendiğini daha kolay kavraması ve dijital okuryazarlığın güçlenmesi.

---

## 5. İTP DÖNEM PROJESİ ENTEGRASYONU

Bu araştırma önerisinin web arayüzü ve veri yönetim katmanı, **İnternet Tabanlı Programlama** dersi dönem projesi kapsamında aşağıdaki mimariyle canlıya alınacaktır:
* **Çekirdek Ekranlar (5 Ekran):** (1) Giriş/Kayıt, (2) Politika Metni/URL Tarama, (3) Madde Bazlı Uyumluluk & Risk Raporu, (4) Geçmiş Analizler & Karşılaştırma Paneli, (5) Uzman Doğrulama/Etiketleme Kuyruğu.
* **Veri Modeli (5 Varlık):** `Kullanici`, `Politika_Belgesi`, `Analiz_Raporu`, `Kriter_Risk_Skoru`, `Uzman_Dogrulama`.
* **Güvenlik ve Kalite:** Hash'lenmiş parola, rol tabanlı yetkilendirme, XSS/SQLi/CSRF korumaları ve Lighthouse ≥ 85 erişilebilirlik hedefi.

---

## KAYNAKÇA

1. Can, Ö. (2019). Kişisel Verilerin Korunması Kanunu için Onam Ontolojisi Geliştirimi. *Dokuz Eylül Üniversitesi Mühendislik Fakültesi Fen ve Mühendislik Dergisi*, 21(62), 627–639. DOI: `10.21205/deufmd.2019216220`
2. Harkous, H., Fawaz, K., Lebret, R., Schaub, F., Shin, K. G., & Aberer, K. (2018). Polisis: Automated Analysis and Presentation of Privacy Policies Using Deep Learning. *27th USENIX Security Symposium (USENIX Security 18)*, 531–548. (arXiv:1802.02561)
3. Schweter, S. (2020). *BERTurk - BERT models for Turkish* (v1.0.0). Zenodo. DOI: `10.5281/zenodo.3770924`
4. Wilson, S., Schaub, F., Dara, A. A., Liu, F., Cherivirala, S., Leon, P. G., Andersen, M. S., Zimmeck, S., Sathyendra, K. M., Russell, N. C., Norton, T. B., Hovy, E., Reidenberg, J., & Sadeh, N. (2016). The Creation and Analysis of a Website Privacy Policy Corpus. *Proceedings of the 54th Annual Meeting of the Association for Computational Linguistics (ACL 2016)*, 1330–1340. DOI: `10.18653/v1/P16-1126`
5. T.C. Resmî Gazete. (2016). *6698 Sayılı Kişisel Verilerin Korunması Kanunu*.
6. T.C. Resmî Gazete. (2018). *Aydınlatma Yükümlülüğünün Yerine Getirilmesinde Uyulacak Usul ve Esaslar Hakkında Tebliğ*.
