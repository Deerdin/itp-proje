<?php
declare(strict_types=1);
final class Analyzer
{
    public const VERSION = 'baseline-tr-2.0';
    public static function criteria(): array
    {
        return [
            'identity' => ['title'=>'Veri sorumlusunun kimliği','basis'=>'KVKK m.10/1-a','kind'=>'Çekirdek','weight'=>18,
                'groups'=>['veri\s+sorumlu|sorumlu\s+sıfatıyla','(?:anonim\s+şirket|limited\s+şirket|\ba\.?ş\.?(?=\s|$|[,;)])|\bltd\.?(?=\s|$|[,;)])|üniversitesi|belediyesi|derneği)'],
                'advice'=>'Veri sorumlusunun açık unvanını ve varsa temsilcisinin kimliğini belirtin.'],
            'purpose' => ['title'=>'Kişisel veri işleme amaçları','basis'=>'KVKK m.10/1-b','kind'=>'Çekirdek','weight'=>18,
                'groups'=>['ama[çc]|maksat|işlenmekte|işlenir|işleme','sipariş|teslimat|fatura|istihdam|sözleşme|eğitim|müşteri\s+(?:ilişki|hizmet)|üyelik|bordro|ödeme|muhasebe|randevu|sağlık\s+hizmet|burs|işe\s+alım|satış|pazarlama|talep\s+ve\s+şik[aâ]yet'],
                'advice'=>'İşlenen veri kategorilerini somut ve belirli işleme amaçlarıyla ilişkilendirin.'],
            'transfer' => ['title'=>'Aktarım ve alıcı grupları','basis'=>'KVKK m.10/1-c','kind'=>'Çekirdek','weight'=>18,
                'groups'=>['aktar|paylaş','kargo|tedarikçi|iş\s+orta|yetkili\s+(?:kamu|kurum)|hizmet\s+sağlayıcı|aktarılma[zm]|paylaşılma[zm]','amac|ama[çc]|için|üzere'],
                'advice'=>'Aktarım yapılan alıcı gruplarını ve aktarım amaçlarını açıklayın; aktarım yoksa açıkça belirtin.'],
            'collection' => ['title'=>'Kişisel veri toplama yöntemi','basis'=>'KVKK m.10/1-ç','kind'=>'Çekirdek','weight'=>13,
                'groups'=>['toplan|topla[nm]|elde\s+edil|temin\s+edil','\bform|internet\s+site|web\s+site|elektronik|fizik[iî]|otomatik|çağrı\s+merkezi|mobil\s+uygulama|e-posta|çerez|kamera|anket|mülakat'],
                'advice'=>'Verilerin hangi kanallardan ve hangi yöntemlerle toplandığını belirtin.'],
            'legal' => ['title'=>'İşlemenin hukuki sebebi','basis'=>'KVKK m.10/1-ç','kind'=>'Çekirdek','weight'=>15,
                'groups'=>['hukuki\s+(?:sebe[pb]|dayanak)|kanun.{0,30}(?:5|6)\b|(?:5|6)\.?\s*(?:inci\s+|ıncı\s+)?madde','sözleşme|hukuki\s+yükümlülük|meşru\s+menfaat|açık\s+rıza|kanunlarda\s+(?:açıkça\s+)?öngörül'],
                'advice'=>'Her işleme amacı için uygun hukuki sebebi somutlaştırın; yalnızca kanuna genel atıf yapmakla yetinmeyin.'],
            'rights' => ['title'=>'İlgili kişinin hakları','basis'=>'KVKK m.10/1-d ve m.11','kind'=>'Çekirdek','weight'=>18,
                'groups'=>['11\.?\s*(?:inci\s+)?madde|madde\s*11|haklar|hak(?:kınız|kına)','işlenip\s+işlenmedi|bilgi\s+talep','düzeltil|silin|itiraz|zarar'],
                'advice'=>'Kanunun 11. maddesindeki hakları erişilebilir ve anlaşılır biçimde açıklayın.'],
            'contact' => ['title'=>'Başvuru kanalı','basis'=>'Tamamlayıcı kontrol','kind'=>'Destekleyici','weight'=>0,
                'groups'=>['başvur|talep.{0,20}ilet','[\w.+-]+@[\w.-]+\.[a-z]{2,}|kep\s+adres|posta\s+adres'],
                'advice'=>'İlgili kişi başvuruları için somut ve erişilebilir bir iletişim kanalı sunun.'],
            'retention' => ['title'=>'Saklama ve imha bilgisi','basis'=>'Tamamlayıcı kontrol','kind'=>'Destekleyici','weight'=>0,
                'groups'=>['sakla|muhafaza|saklan','süre|yıl|sona\s+er|imha|sili[nm]|anonim'],
                'advice'=>'Saklama süresini veya belirleme ölçütlerini ve süre sonunda uygulanacak işlemi açıklayın.'],
        ];
    }
    public static function run(string $text): array
    {
        $sentences = self::sentences($text);
        $results = []; $score = 0.0;
        foreach (self::criteria() as $key => $criterion) {
            $bestHits = 0; $evidence = []; $issues = []; $negatedEvidence = [];
            $groupCount = count($criterion['groups']);
            foreach ($sentences as $index => $sentence) {
                $normalized = self::normalize($sentence);
                if (!self::matches($criterion['groups'][0], $normalized)) continue;
                $context = $sentence;
                // Continue a stated subject only through an explicit reference, not through arbitrary document-wide words.
                $next = $sentences[$index + 1] ?? '';
                if (self::matches('^(?:bu (?:veri|bilgi|amaç|işlem|kapsam)|söz konusu|bunlar)', self::normalize($next))) {
                    $context .= ' ' . $next;
                }
                $normalized = self::normalize($context);
                if (self::matches('belirtilme(?:miş|mekte)|açıklanma(?:mış|makta)|yer verilme(?:miş|mekte)|tanınma(?:mış|makta)|hak.{0,20}(?:yoktur|bulunmamaktadır)', $normalized)) {
                    $negatedEvidence[] = $sentence;
                    $issues[] = 'Metin bu bilginin açıklanmadığını veya bir hakkın tanınmadığını belirtiyor; olumlu kanıt sayılmadı.';
                    continue;
                }
                $hits = 0;
                foreach ($criterion['groups'] as $pattern) if (self::matches($pattern, $normalized)) $hits++;
                if ($key === 'transfer' && self::matches('(?:aktarılma[zm]|paylaşılma[zm]|aktarım.{0,12}yapılma[zm])', $normalized)) {
                    $hits = $groupCount;
                    $issues[] = 'Aktarım yapılmadığına ilişkin açık beyan bulundu; fiilî durum uzman tarafından doğrulanmalıdır.';
                }
                if ($key === 'purpose' && self::matches('hizmet.{0,20}geliştir|deneyim.{0,20}iyileştir|her türlü|çeşitli amaç', $normalized) && $hits < $groupCount) {
                    $issues[] = 'Genel amaç ifadeleri somut işleme amacının yerine sayılmadı.';
                }
                if ($key === 'retention' && !self::matches('\d+\s*(?:yıl|ay|gün)|yasal|mevzuat|kanun|amac.{0,30}(?:gerek|süre)|amaç.{0,30}(?:gerek|süre)', $normalized)) {
                    $hits = min($hits, 1);
                    $issues[] = 'Somut süre veya saklama süresini belirleyen bir ölçüt açıkça bulunamadı.';
                }
                if ($key === 'legal' && $hits === $groupCount && !self::matches('sözleşme.{0,30}(?:kurul|ifa)|hukuki yükümlülük|meşru menfaat|kanunlarda.{0,20}öngörül|açık rıza', $normalized)) {
                    $hits = min($hits, $groupCount - 1);
                    $issues[] = 'Hukuki sebep adı var; dayanağın somut koşulu bu bağlamda açık değil.';
                }
                $bestHits = max($bestHits, $hits);
                $evidence[] = ['text'=>$context,'hits'=>$hits];
            }
            usort($evidence, fn(array $a, array $b): int => $b['hits'] <=> $a['hits']);
            $ratio = $bestHits / $groupCount;
            $score += $criterion['weight'] * $ratio;
            if (!$evidence && $negatedEvidence) $evidence = array_map(fn($sentence)=>['text'=>$sentence,'hits'=>0],$negatedEvidence);
            $status = $ratio >= 1 ? 'found' : ($bestHits ? 'partial' : 'missing');
            $results[$key] = [
                'title'=>$criterion['title'],'basis'=>$criterion['basis'],'kind'=>$criterion['kind'],
                'weight'=>$criterion['weight'],'status'=>$status,'matched_groups'=>$bestHits,'total_groups'=>$groupCount,
                'evidence'=>array_values(array_unique(array_column(array_slice($evidence,0,4),'text'))),
                'issues'=>array_values(array_unique($issues)),'advice'=>$criterion['advice'],
            ];
        }
        preg_match_all('/[\p{L}\p{N}]+(?:[’\x{0027}][\p{L}]+)?/u', $text, $words);
        $count = count($words[0]);
        $average = round($count/max(1,count($sentences)),1);
        $warnings = [];
        if ($average > 25) $warnings[] = 'Ortalama cümle uzunluğu 25 kelimeyi aşıyor. Uzun cümleleri bölerek anlaşılabilirliği artırın.';
        if (self::matches('gerektiğinde|gerekli görüldüğünde|her türlü|ilgili üçüncü kişi|çeşitli amaç',self::normalize($text))) {
            $warnings[] = 'Belirsiz kapsam bildiren ifadeler bulundu. Amaçları, koşulları ve alıcı gruplarını somutlaştırın.';
        }
        if (self::matches('aydınlatma.{0,80}(?:kabul etmiş|rıza vermiş)|(?:okuyarak|okumanızla).{0,60}rıza',self::normalize($text))) {
            $warnings[] = 'Aydınlatmanın okunmasıyla rıza verildiğini ima eden bir ifade bulundu. Aydınlatma ve açık rıza süreçlerini uzmanla ayrı değerlendirin.';
        }
        if ($results['rights']['status']==='partial') $warnings[] = 'Haklara ilişkin genel atıf yeterli ayrıntı içermeyebilir. İlgili kişi haklarını metinde açıklayın.';
        return ['version'=>self::VERSION,'score'=>(int)round($score),'criteria'=>$results,'warnings'=>$warnings,
            'metrics'=>['words'=>$count,'sentences'=>count($sentences),'average_sentence'=>$average,
                'long_sentences'=>count(array_filter($sentences,fn($s)=>preg_match_all('/[\p{L}\p{N}]+/u',$s)>25)),
                'reading_minutes'=>max(1,(int)ceil($count/200))],
            'method'=>'Cümle bağlamında konu işareti, somut açıklama, olumsuz beyan ve belirsizlik kontrolleri. Belge genelindeki ilgisiz kelimeler birleştirilmez.',
            'notice'=>'Kural tabanlı kapsam taramasıdır. Örüntü eşleşmesi hukuki yeterlilik veya KVKK uyumluluğu kanıtı değildir. Uzman incelemesi gerekir.'];
    }
    private static function matches(string $pattern, string $text): bool
    {
        return (bool)preg_match('~'.$pattern.'~u',$text);
    }
    private static function sentences(string $text): array
    {
        $protected = preg_replace_callback('/\b(?:A\.Ş\.|Ltd\.|Şti\.|Dr\.|Prof\.|Doç\.|m\.)/iu', fn($match)=>str_replace('.',"\u{E000}",$match[0]),trim($text));
        $parts = preg_split('/(?<=[.!?])\s+(?=[\p{Lu}“"(])|(?<=;)\s+|\R+/u',$protected,-1,PREG_SPLIT_NO_EMPTY) ?: [];
        return array_map(fn($part)=>str_replace("\u{E000}",'.',$part),$parts);
    }
    private static function normalize(string $text): string
    {
        return mb_strtolower(str_replace(['İ','I'],['i','ı'],$text),'UTF-8');
    }
}
