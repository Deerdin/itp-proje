/**
 * KVKK-Denetçi PoC (Kural Tabanlı Baseline Analiz Motoru)
 * 6698 sayılı KVKK Madde 10 ve Aydınlatma Yükümlülüğü Tebliği esaslı 8 kriterli denetim.
 */

const KRITERLER = [
  {
    id: "K1",
    ad: "Veri Sorumlusu ve Temsilcisinin Kimliği",
    dayanak: "6698 s. KVKK m.10/1-a",
    agirlik: 15,
    oneri: "Veri sorumlusunun tam ticari unvanını, MERSİS/vergi bilgisini ve varsa temsilcisini açıkça belirtin.",
    oruntuler: [
      /veri sorumlusu/i,
      /a\.ş\.|ltd\.?\s*şti\.?|anonim şirketi|limited şirketi/i,
      /mersis|unvan|temsilci/i,
      /sıfatıyla/i
    ]
  },
  {
    id: "K2",
    ad: "Kişisel Verilerin İşlenme Amacı",
    dayanak: "6698 s. KVKK m.10/1-b",
    agirlik: 15,
    oneri: "Toplanan kişisel verilerin hangi somut ve meşru amaçlarla işlendiğini (ör. üyelik, faturalandırma, destek) maddeleyerek yazın.",
    oruntuler: [
      /işlenme amac|işleme amac|amaçlarıyla işlen/i,
      /hizmetlerin sunulması|sözleşmenin ifası|müşteri memnuniyeti/i,
      /faturalandırma|sipariş|üyelik işlemleri|destek talepleri/i,
      /amacıyla işlenmektedir/i
    ]
  },
  {
    id: "K3",
    ad: "Verilerin Kimlere ve Hangi Amaçla Aktarılabileceği",
    dayanak: "6698 s. KVKK m.10/1-c",
    agirlik: 15,
    oneri: "Kişisel verilerin yurt içi/yurt dışı üçüncü kişilere (yetkili kamu kurumları, iş ortakları, tedarikçiler) hangi amaçla aktarıldığını açıklayın.",
    oruntuler: [
      /aktarıl|aktarım/i,
      /üçüncü kişi|iş ortak|tedarikçi|yetkili kamu kurum/i,
      /yurt içi|yurt dışı|yurtiçi|yurtdışı/i,
      /maddeleri? çerçevesinde paylaşı/i
    ]
  },
  {
    id: "K4",
    ad: "Kişisel Veri Toplamanın Yöntemi",
    dayanak: "6698 s. KVKK m.10/1-ç",
    agirlik: 10,
    oneri: "Verilerin hangi kanallarla (web formu, çerezler, mobil uygulama, çağrı merkezi, e-posta) ve otomatik/otomatik olmayan yollarla toplandığını belirtin.",
    oruntuler: [
      /toplama yöntem|toplanma yöntem|yöntemleriyle toplan/i,
      /otomatik|otomatik olmayan/i,
      /elektronik ortam|internet sitesi|web sitesi|mobil uygulama|çerez|formlar aracılığıyla/i,
      /fiziki ortam|çağrı merkezi/i
    ]
  },
  {
    id: "K5",
    ad: "Kişisel Veri Toplamanın Hukuki Sebebi",
    dayanak: "6698 s. KVKK m.10/1-ç & m.5-6",
    agirlik: 15,
    oneri: "Veri işlemenin dayandığı hukuki sebepleri (ör. açık rıza, sözleşmenin kurulması/ifası, hukuki yükümlülük, meşru menfaat, Madde 5/6) açıkça yazın.",
    oruntuler: [
      /hukuki sebep|hukuki sebebi|hukuki sebepler/i,
      /5\.\s*madde|6\.\s*madde|madde 5|madde 6/i,
      /sözleşmenin kurulması|hukuki yükümlülük|meşru menfaat|açık rıza/i,
      /kanunlarda açıkça öngörülmesi/i
    ]
  },
  {
    id: "K6",
    ad: "İlgili Kişinin Madde 11 Kapsamındaki Hakları",
    dayanak: "6698 s. KVKK m.10/1-d & m.11",
    agirlik: 15,
    oneri: "İlgili kişinin KVKK Madde 11 kapsamındaki bilgi talep etme, düzeltme, silme/yok etme ve itiraz haklarını eksiksiz listeleyin.",
    oruntuler: [
      /11\.\s*madde|madde 11|11’inci madde/i,
      /ilgili kişinin hakları|haklarınız/i,
      /silinmesini veya yok edilmesini|düzeltilmesini isteme|işlenip işlenmediğini öğrenme/i,
      /zararın giderilmesini talep/i
    ]
  },
  {
    id: "K7",
    ad: "Başvuru Usulü ve İletişim Kanalı",
    dayanak: "Aydınlatma Tebliği m.5 & Başvuru Tebliği",
    agirlik: 10,
    oneri: "İlgili kişilerin haklarını kullanmak için başvurabileceği somut kanalı (KEP adresi, e-posta, ıslak imzalı dilekçe adresi) ekleyin.",
    oruntuler: [
      /kep|kayıtlı elektronik posta/i,
      /@[a-z0-9.-]+\.[a-z]{2,}/i,
      /başvuru|başvuruda bulunabilir|iletebilirsiniz/i,
      /veri sorumlularına başvuru usul ve esasları/i
    ]
  },
  {
    id: "K8",
    ad: "Saklama Süresi / İmha ve Güncelleme Bilgisi",
    dayanak: "KVKK m.7 & Şeffaflık İlkesi",
    agirlik: 5,
    oneri: "Verilerin ne kadar süre saklanacağını, süre dolduğunda nasıl silineceğini/anonimleştirileceğini ve güncelleme tarihini belirtin.",
    oruntuler: [
      /saklama süresi|saklanacaktır|muhafaza edil/i,
      /imha|anonim|periyodik imha/i,
      /güncelleme tarihi|yürürlük tarihi/i
    ]
  }
];

const ORNEK_METINLER = {
  uyumlu: `ANKA E-TİCARET TEKNOLOJİLERİ A.Ş. KİŞİSEL VERİLERİN İŞLENMESİNE İLİŞKİN AYDINLATMA METNİ

1. Veri Sorumlusunun Kimliği
6698 sayılı Kişisel Verilerin Korunması Kanunu ("KVKK") uyarınca, kişisel verileriniz veri sorumlusu sıfatıyla Anka E-Ticaret Teknolojileri A.Ş. (MERSİS: 012345678900001) tarafından aşağıda açıklanan kapsamda işlenebilecektir.

2. Kişisel Verilerin İşlenme Amacı
Toplanan kişisel verileriniz; üyelik işlemlerinin yürütülmesi, sipariş ve teslimat süreçlerinin yönetilmesi, faturalandırma faaliyetlerinin gerçekleştirilmesi, müşteri destek taleplerinin yanıtlanması ve bilgi güvenliği süreçlerinin yürütülmesi amaçlarıyla işlenmektedir.

3. Kişisel Verilerin Kimlere ve Hangi Amaçla Aktarılabileceği
Kişisel verileriniz; yasal yükümlülüklerin yerine getirilmesi amacıyla yetkili kamu kurum ve kuruluşlarına, ödeme ve lojistik hizmetlerinin sunulması amacıyla yurt içi iş ortaklarımıza ve tedarikçilerimize KVKK'nın 8. ve 9. maddeleri çerçevesinde aktarılabilecektir.

4. Kişisel Veri Toplamanın Yöntemi ve Hukuki Sebebi
Kişisel verileriniz; internet sitesi üzerindeki kayıt formları, mobil uygulama ve çerezler aracılığıyla elektronik ortamda otomatik veya otomatik olmayan yöntemlerle toplanmaktadır. Bu veriler KVKK'nın 5. maddesinde yer alan sözleşmenin kurulması ve ifası, veri sorumlusunun hukuki yükümlülüğünü yerine getirmesi ve meşru menfaat hukuki sebeplerine dayanılarak işlenmektedir.

5. İlgili Kişinin Madde 11 Kapsamındaki Hakları ve Başvuru Usulü
KVKK'nın 11. maddesi uyarınca kişisel verilerinizin işlenip işlenmediğini öğrenme, düzeltilmesini isteme, silinmesini veya yok edilmesini talep etme ve zararın giderilmesini talep etme haklarına sahipsiniz. Haklarınıza ilişkin taleplerinizi kvkk@ankaeticaret.example.com e-posta adresine veya anka@hs01.kep.tr KEP adresine yazılı olarak iletebilirsiniz.

6. Saklama Süresi ve İmha
Kişisel verileriniz ilgili mevzuatta öngörülen saklama süresi boyunca muhafaza edilecek, sürenin sona ermesiyle periyodik imha politikamız uyarınca silinecek veya anonim hale getirilecektir. Son güncelleme tarihi: 01.10.2026.`,

  riskli: `BULUTİK SAAS PLATFORMU GİZLİLİK VE AYDINLATMA POLİTİKASI

Bulutik Yazılım Hizmetleri olarak kullanıcılarımızın gizliliğine önem veriyoruz. Platformumuzu kullandığınızda adınız, e-posta adresiniz ve kullanım kayıtlarınız sistemlerimiz tarafından işlenmektedir.

Kişisel verileriniz; hesap oluşturma, abonelik yönetimi, ürün geliştirme ve müşteri destek taleplerinin karşılanması amacıyla işlenmektedir. Verileriniz web sitemizdeki formlar ve çerezler aracılığıyla elektronik ortamda toplanmaktadır.

Platformumuzun kesintisiz çalışması için verileriniz bulut altyapı sağlayıcılarımız ve iş ortaklarımızla paylaşılabilmektedir.

Kullanıcılarımız diledikleri zaman bizimle iletişime geçerek verileri hakkında bilgi alabilirler.`,

  yetersiz: `Gizlilik Politikası

Web sitemizi ziyaret ettiğinizde bazı bilgileriniz kaydedilebilir. Bu bilgiler hizmet kalitemizi artırmak için kullanılır ve gizli tutulur. Sitemizi kullanarak bu şartları kabul etmiş sayılırsınız.`
};

const textarea = document.getElementById("politika-metni");
const girisIstatistik = document.getElementById("giris-istatistik");
const analizBtn = document.getElementById("analiz-et-btn");
const temizleBtn = document.getElementById("temizle-btn");
const jsonKopyalaBtn = document.getElementById("json-kopyala-btn");
const jsonIndirBtn = document.getElementById("json-indir-btn");

const ozetBos = document.getElementById("ozet-bos");
const ozetDolu = document.getElementById("ozet-dolu");
const kriterBolumu = document.getElementById("kriter-sonuclari-bolumu");
const vurgulamaBolumu = document.getElementById("vurgulama-bolumu");

let sonRapor = null;

function guncelleGirisIstatistik() {
  const metin = textarea.value.trim();
  const kelime = metin ? metin.split(/\s+/).length : 0;
  girisIstatistik.textContent = `${kelime} kelime • ${textarea.value.length} karakter`;
}

function cumlelereBol(metin) {
  return metin
    .split(/(?<=[.!?])\s+|\n+/)
    .map((c) => c.trim())
    .filter((c) => c.length > 3);
}

function analizEt(metin) {
  const cumleler = cumlelereBol(metin);
  const kelimeler = metin.trim().split(/\s+/).filter(Boolean);
  const kelimeSayisi = kelimeler.length;
  const cumleSayisi = Math.max(cumleler.length, 1);
  const ortCumleUzunlugu = Math.round((kelimeSayisi / cumleSayisi) * 10) / 10;
  const okumaDakika = Math.max(1, Math.ceil(kelimeSayisi / 180));

  const cumleEtiketleri = cumleler.map((cumle) => ({
    cumle,
    kriterler: []
  }));

  let toplamPuan = 0;
  let tamSayisi = 0;
  let kismiSayisi = 0;
  let eksikSayisi = 0;

  const kriterSonuclari = KRITERLER.map((kriter) => {
    let eslesenOruntuSayisi = 0;
    const eslesenCumleIndeksleri = new Set();

    kriter.oruntuler.forEach((regex) => {
      if (regex.test(metin)) {
        eslesenOruntuSayisi += 1;
      }
      cumleler.forEach((cumle, idx) => {
        if (regex.test(cumle)) {
          eslesenCumleIndeksleri.add(idx);
        }
      });
    });

    eslesenCumleIndeksleri.forEach((idx) => {
      cumleEtiketleri[idx].kriterler.push(`${kriter.id}: ${kriter.ad}`);
    });

    let durum = "eksik";
    let kazanilanPuan = 0;

    if (eslesenOruntuSayisi >= 2) {
      durum = "tam";
      kazanilanPuan = kriter.agirlik;
      tamSayisi += 1;
    } else if (eslesenOruntuSayisi === 1) {
      durum = "kismi";
      kazanilanPuan = Math.round(kriter.agirlik * 0.5);
      kismiSayisi += 1;
    } else {
      eksikSayisi += 1;
    }

    toplamPuan += kazanilanPuan;

    return {
      id: kriter.id,
      ad: kriter.ad,
      dayanak: kriter.dayanak,
      agirlik: kriter.agirlik,
      kazanilanPuan,
      durum,
      eslesenCumleSayisi: eslesenCumleIndeksleri.size,
      oneri: kriter.oneri
    };
  });

  let riskSeviyesi = "Yüksek Risk";
  let riskSinifi = "err";
  let daireSinifi = "high-risk";
  let riskAciklama = "Metin 6698 sayılı KVKK Madde 10 zorunlu unsurlarının önemli bir bölümünü karşılamıyor.";

  if (toplamPuan >= 80) {
    riskSeviyesi = "Düşük Risk (Yüksek Uyum)";
    riskSinifi = "ok";
    daireSinifi = "low-risk";
    riskAciklama = "Metin KVKK Madde 10 çekirdek kriterlerinin büyük çoğunluğunu açık biçimde içermektedir.";
  } else if (toplamPuan >= 50) {
    riskSeviyesi = "Orta Risk (Kısmi Uyum)";
    riskSinifi = "warn";
    daireSinifi = "mid-risk";
    riskAciklama = "Temel unsurların bir kısmı mevcut ancak zorunlu maddelerde eksikler veya belirsizlikler var.";
  }

  return {
    tarih: new Date().toISOString(),
    motor: "kural-tabanli-baseline-v0.1",
    uyumlulukSkoru: toplamPuan,
    riskSeviyesi,
    riskSinifi,
    daireSinifi,
    riskAciklama,
    ozetSayilar: { tamSayisi, kismiSayisi, eksikSayisi },
    okunabilirlik: { kelimeSayisi, cumleSayisi: cumleler.length, ortCumleUzunlugu, okumaDakika },
    kriterSonuclari,
    cumleEtiketleri
  };
}

function sonucuGoster(rapor) {
  sonRapor = rapor;
  ozetBos.classList.add("hidden");
  ozetDolu.classList.remove("hidden");
  kriterBolumu.classList.remove("hidden");
  vurgulamaBolumu.classList.remove("hidden");
  jsonKopyalaBtn.disabled = false;
  jsonIndirBtn.disabled = false;

  const skorDairesi = document.getElementById("skor-dairesi");
  skorDairesi.className = `score-circle ${rapor.daireSinifi}`;
  document.getElementById("skor-degeri").textContent = `${rapor.uyumlulukSkoru}%`;

  const riskRozeti = document.getElementById("risk-rozeti");
  riskRozeti.className = `risk-badge ${rapor.riskSinifi}`;
  riskRozeti.textContent = rapor.riskSeviyesi;
  document.getElementById("risk-aciklama").textContent = rapor.riskAciklama;

  document.getElementById("tam-sayisi").textContent = rapor.ozetSayilar.tamSayisi;
  document.getElementById("kismi-sayisi").textContent = rapor.ozetSayilar.kismiSayisi;
  document.getElementById("eksik-sayisi").textContent = rapor.ozetSayilar.eksikSayisi;

  document.getElementById("metrik-kelime").textContent = rapor.okunabilirlik.kelimeSayisi;
  document.getElementById("metrik-cumle").textContent = rapor.okunabilirlik.cumleSayisi;
  document.getElementById("metrik-ort-cumle").textContent = rapor.okunabilirlik.ortCumleUzunlugu;
  document.getElementById("metrik-okuma").textContent = `${rapor.okunabilirlik.okumaDakika} dk`;

  // Tabloyu XSS-guvenli DOM API ile doldur
  const tbody = document.getElementById("kriter-tablo-govde");
  tbody.innerHTML = "";

  rapor.kriterSonuclari.forEach((k) => {
    const tr = document.createElement("tr");

    const tdAd = document.createElement("td");
    const strongAd = document.createElement("strong");
    strongAd.textContent = `${k.id} — ${k.ad}`;
    const spanDayanak = document.createElement("span");
    spanDayanak.className = "criterion-sub";
    spanDayanak.textContent = k.dayanak;
    tdAd.append(strongAd, spanDayanak);

    const tdAgirlik = document.createElement("td");
    tdAgirlik.textContent = `${k.kazanilanPuan} / ${k.agirlik}`;

    const tdDurum = document.createElement("td");
    const spanDurum = document.createElement("span");
    if (k.durum === "tam") {
      spanDurum.className = "status-tag ok";
      spanDurum.textContent = "Mevcut";
    } else if (k.durum === "kismi") {
      spanDurum.className = "status-tag warn";
      spanDurum.textContent = "Kısmi";
    } else {
      spanDurum.className = "status-tag err";
      spanDurum.textContent = "Eksik";
    }
    tdDurum.appendChild(spanDurum);

    const tdOneri = document.createElement("td");
    if (k.durum === "tam") {
      tdOneri.textContent = `${k.eslesenCumleSayisi} cümlede ilgili yasal bileşen tespit edildi.`;
    } else if (k.durum === "kismi") {
      tdOneri.textContent = `Kısmi eşleşme (${k.eslesenCumleSayisi} cümle). Öneri: ${k.oneri}`;
    } else {
      tdOneri.textContent = `Tespit edilemedi. Öneri: ${k.oneri}`;
    }

    tr.append(tdAd, tdAgirlik, tdDurum, tdOneri);
    tbody.appendChild(tr);
  });

  // Cumle bazli vurgulama
  const vurguAlani = document.getElementById("vurgulu-metin-alani");
  vurguAlani.innerHTML = "";

  rapor.cumleEtiketleri.forEach((item) => {
    const div = document.createElement("div");
    div.className = item.kriterler.length > 0 ? "sentence-item matched" : "sentence-item";

    const p = document.createElement("div");
    p.textContent = item.cumle;
    div.appendChild(p);

    if (item.kriterler.length > 0) {
      const tags = document.createElement("div");
      tags.className = "sentence-tags";
      item.kriterler.forEach((etiket) => {
        const chip = document.createElement("span");
        chip.className = "tag-chip";
        chip.textContent = etiket;
        tags.appendChild(chip);
      });
      div.appendChild(tags);
    }

    vurguAlani.appendChild(div);
  });
}

// Olay Dinleyicileri
textarea.addEventListener("input", guncelleGirisIstatistik);

document.querySelectorAll("[data-preset]").forEach((btn) => {
  btn.addEventListener("click", () => {
    const key = btn.getAttribute("data-preset");
    textarea.value = ORNEK_METINLER[key] || "";
    guncelleGirisIstatistik();
    const rapor = analizEt(textarea.value);
    sonucuGoster(rapor);
  });
});

temizleBtn.addEventListener("click", () => {
  textarea.value = "";
  guncelleGirisIstatistik();
  sonRapor = null;
  ozetBos.classList.remove("hidden");
  ozetDolu.classList.add("hidden");
  kriterBolumu.classList.add("hidden");
  vurgulamaBolumu.classList.add("hidden");
  jsonKopyalaBtn.disabled = true;
  jsonIndirBtn.disabled = true;
});

analizBtn.addEventListener("click", () => {
  const metin = textarea.value.trim();
  if (metin.length < 20) {
    alert("Lütfen analiz için en az 20 karakterlik bir aydınlatma metni girin veya hazır örneklerden birini seçin.");
    textarea.focus();
    return;
  }
  const rapor = analizEt(metin);
  sonucuGoster(rapor);
});

jsonKopyalaBtn.addEventListener("click", async () => {
  if (!sonRapor) return;
  try {
    await navigator.clipboard.writeText(JSON.stringify(sonRapor, null, 2));
    jsonKopyalaBtn.textContent = "Kopyalandı!";
    setTimeout(() => {
      jsonKopyalaBtn.textContent = "JSON Kopyala";
    }, 1800);
  } catch {
    alert("Panoya kopyalama başarısız oldu. JSON İndir düğmesini kullanabilirsiniz.");
  }
});

jsonIndirBtn.addEventListener("click", () => {
  if (!sonRapor) return;
  const blob = new Blob([JSON.stringify(sonRapor, null, 2)], { type: "application/json;charset=utf-8" });
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = "kvkk-analiz-raporu.json";
  a.click();
  URL.revokeObjectURL(url);
});

guncelleGirisIstatistik();
