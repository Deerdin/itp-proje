<?php
declare(strict_types=1);
function db(): PDO { return Database::connection(); }
function query(string $sql, array $params=[]): PDOStatement { $s=db()->prepare($sql);$s->execute($params);return $s; }
function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function url(string $page, array $params=[]): string { return 'index.php?' . http_build_query(['page'=>$page]+$params); }
function go(string $page, array $params=[]): void { header('Location: '.url($page,$params),true,303);exit; }
function csrf(): string { return '<input type="hidden" name="csrf" value="'.e($_SESSION['csrf']).'">'; }
function verifyCsrf(?string $token): void {
    if (!$token || !hash_equals($_SESSION['csrf'] ?? '',$token)) throw new DomainException('Güvenlik doğrulaması başarısız. Sayfayı yenileyip tekrar deneyin.');
}
function user(): ?array {
    if (empty($_SESSION['user_id'])) return null;
    $u=query('SELECT id,name,email,role FROM users WHERE id=?',[$_SESSION['user_id']])->fetch();
    return $u ?: null;
}
function expert(?array $u): bool { return $u && in_array($u['role'],['expert','admin'],true); }
function roleName(string $role): string { return ['user'=>'Standart kullanıcı','expert'=>'Denetim uzmanı','admin'=>'Yönetici'][$role] ?? 'Kullanıcı'; }
function statusName(string $status): string { return ['found'=>'Bulgu var','partial'=>'Kısmi bulgu','missing'=>'Bulgu yok'][$status] ?? $status; }
function badge(string $status): string { return '<span class="badge '.e($status).'">'.e(statusName($status)).'</span>'; }
function flash(string $text): void { $_SESSION['flash']=$text; }
function findAnalysis(int $id, array $u): ?array {
    $row=query('SELECT a.*,d.title,d.content,d.sector,d.user_id,u.name AS owner FROM analyses a JOIN documents d ON d.id=a.document_id JOIN users u ON u.id=d.user_id WHERE a.id=?',[$id])->fetch();
    if (!$row || ((int)$row['user_id'] !== (int)$u['id'] && !expert($u))) return null;
    $row['result']=json_decode($row['result_json'],true,512,JSON_THROW_ON_ERROR);return $row;
}
function createAnalysis(array $u, array $input): int {
    ['title'=>$title,'content'=>$text,'sector'=>$sector]=validateAnalysisInput($input);
    $result=Analyzer::run($text);$now=date('c');db()->beginTransaction();
    try {
        query('INSERT INTO documents(user_id,title,sector,content,created_at) VALUES(?,?,?,?,?)',[$u['id'],$title,$sector,$text,$now]);
        $doc=(int)db()->lastInsertId();
        query('INSERT INTO analyses(document_id,score,model_version,result_json,created_at) VALUES(?,?,?,?,?)',[$doc,$result['score'],Analyzer::VERSION,json_encode($result,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR),$now]);
        $id=(int)db()->lastInsertId();db()->commit();return $id;
    } catch(Throwable $e) { db()->rollBack();throw $e; }
}
function sectors(): array { return ['E-ticaret','Finans','Eğitim','Teknoloji','Sağlık','Diğer']; }
function saveReview(array $u,int $id,array $input): void {
    if(!expert($u)) throw new DomainException('Bu işlem için uzman yetkisi gerekir.');
    if(!findAnalysis($id,$u)) throw new DomainException('Analiz bulunamadı.');
    $key=(string)($input['criterion']??'');$verdict=(string)($input['verdict']??'');$note=trim((string)($input['note']??''));
    if(!array_key_exists($key,Analyzer::criteria()) || !in_array($verdict,['sufficient','insufficient','uncertain'],true)) throw new DomainException('Geçerli bir kriter ve değerlendirme seçin.');
    if(mb_strlen($note)<10 || mb_strlen($note)>2000) throw new DomainException('Gerekçe 10–2.000 karakter olmalıdır.');
    // One annotation per expert and criterion; another expert's label remains independent.
    db()->beginTransaction();
    try {
        $existing=query('SELECT id FROM reviews WHERE analysis_id=? AND expert_id=? AND criterion=?',[$id,$u['id'],$key])->fetchColumn();
        if($existing) query('UPDATE reviews SET verdict=?,note=?,updated_at=? WHERE id=?',[$verdict,$note,date('c'),$existing]);
        else query('INSERT INTO reviews(analysis_id,expert_id,criterion,verdict,note,updated_at) VALUES(?,?,?,?,?,?)',[$id,$u['id'],$key,$verdict,$note,date('c')]);
        db()->commit();
    } catch(Throwable $e) { db()->rollBack();throw $e; }
}
function icon(string $name): string {
    $paths=[
        'shield'=>'<path d="M12 3 4 6v6c0 5 8 9 8 9s8-4 8-9V6l-8-3Z"/><path d="m8 12 3 3 5-6"/>',
        'grid'=>'<rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/>',
        'file'=>'<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6M8 13h8M8 17h5"/>',
        'history'=>'<path d="M3 11a9 9 0 1 1 3 8M3 4v7h7m2-4v6l4 2"/>',
        'check'=>'<path d="m5 12 4 4L19 6"/>',
        'arrow'=>'<path d="M5 12h14m-5-5 5 5-5 5"/>',
        'plus'=>'<path d="M12 5v14M5 12h14"/>',
        'logout'=>'<path d="M9 21H4V3h5m5 5 4 4-4 4m-6-4h12"/>',
        'download'=>'<path d="M12 3v12m-5-5 5 5 5-5M4 16v5h16v-5"/>',
        'search'=>'<circle cx="10" cy="10" r="7"/><path d="m16 16 5 5"/>',
        'book'=>'<path d="M12 5c-3-3-7-3-10-2v16c4-1 7-1 10 2 3-3 6-3 10-2V3c-3-1-7-1-10 2Zm0 0v16"/>',
        'chart'=>'<path d="M4 3v18h17M9 16v-5m5 5V6m5 10v-7"/>',
        'lock'=>'<rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3m-4 5v2"/>',
        'menu'=>'<path d="M4 6h16M4 12h16M4 18h16"/>',
    ];
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.($paths[$name]??$paths['file']).'</svg>';
}

function validateAnalysisInput(array $input): array {
    $title=trim(inputString($input,'title'));$text=trim(inputString($input,'content'));$sector=inputString($input,'sector','Diğer');
    if(mb_strlen($title)<3 || mb_strlen($title)>150) throw new DomainException('Belge adı 3–150 karakter olmalıdır.');
    if(mb_strlen($text)<100 || mb_strlen($text)>50000) throw new DomainException('Analiz metni 100–50.000 karakter olmalıdır.');
    if(!in_array($sector,sectors(),true)) throw new DomainException('Geçerli bir sektör seçin.');
    return ['title'=>$title,'content'=>$text,'sector'=>$sector];
}
function guestToken(): string {
    $token=$_COOKIE['kvkk_trial']??'';
    if(!is_string($token) || !preg_match('/^[a-f0-9]{64}$/D',$token)) {
        $token=bin2hex(random_bytes(32));
        setcookie('kvkk_trial',$token,['expires'=>time()+31536000,'path'=>'/','httponly'=>true,
            'secure'=>secureRequest(),'samesite'=>'Lax']);
        $_COOKIE['kvkk_trial']=$token;
    }
    return $token;
}
function guestUsed(): bool {
    return (bool)query('SELECT token_hash FROM guest_trials WHERE token_hash=?',[hash('sha256',guestToken())])->fetchColumn();
}
function createGuestAnalysis(array $input): void {
    $data=validateAnalysisInput($input);
    $result=Analyzer::run($data['content']);
    try {
        query('INSERT INTO guest_trials(token_hash,used_at) VALUES(?,?)',[hash('sha256',guestToken()),date('c')]);
    } catch(PDOException $e) {
        if(substr((string)$e->getCode(),0,2)==='23') throw new DomainException('Misafir analiz hakkınızı kullandınız. Devam etmek için giriş yapın.');
        throw $e;
    }
    $_SESSION['guest_report']=$data+['id'=>0,'user_id'=>null,'created_at'=>date('c'),'model_version'=>Analyzer::VERSION,
        'score'=>$result['score'],'result'=>$result];
}
function guestReport(): ?array {
    return $_SESSION['guest_report']??null;
}

function rateLimit(string $scope, int $limit, int $seconds): void {
    // Independent of the session cookie, so repeated new sessions do not bypass request limits.
    // Production uses a keyed hash of REMOTE_ADDR; forwarding headers are never trusted.
    if (PHP_SAPI==='cli') return;
    $address=$_SERVER['REMOTE_ADDR']??'unknown';
    $bucket=(int)floor(time()/$seconds);
    $key=hash_hmac('sha256',$scope.'|'.$address.'|'.$bucket,getenv('APP_KEY')?:'local-development');
    $driver=db()->getAttribute(PDO::ATTR_DRIVER_NAME);
    if ($driver==='mysql') query('INSERT INTO rate_limits(rate_key,hits,expires_at) VALUES(?,1,?) ON DUPLICATE KEY UPDATE hits=hits+1',[$key,($bucket+1)*$seconds]);
    else query('INSERT INTO rate_limits(rate_key,hits,expires_at) VALUES(?,1,?) ON CONFLICT(rate_key) DO UPDATE SET hits=hits+1',[$key,($bucket+1)*$seconds]);
    if ((int)query('SELECT hits FROM rate_limits WHERE rate_key=?',[$key])->fetchColumn()>$limit) {
        http_response_code(429);header('Retry-After: '.max(1,($bucket+1)*$seconds-time()));
        throw new DomainException('Çok fazla istek gönderdiniz. Lütfen biraz bekleyip tekrar deneyin.');
    }
    if (random_int(1,100)===1) query('DELETE FROM rate_limits WHERE expires_at<?',[time()]);
}

/** Display the supplied brand asset, preserving its pixels and transparency. */
function brandLogo(bool $onDark=false): string {
    $image='<image href="assets/verilex-logo.png" width="2172" height="724"/>';
    if($onDark) {
        $content='<defs><clipPath id="verilex-symbol"><rect x="140" y="90" width="450" height="610"/></clipPath><clipPath id="verilex-wordmark"><rect x="590" y="90" width="1582" height="610"/></clipPath><filter id="verilex-white" color-interpolation-filters="sRGB"><feColorMatrix type="matrix" values="0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 1 0"/></filter></defs><g clip-path="url(#verilex-symbol)">'.$image.'</g><g clip-path="url(#verilex-wordmark)" filter="url(#verilex-white)">'.$image.'</g>';
    } else $content=$image;
    return '<svg class="brand-logo '.($onDark?'brand-logo--light':'brand-logo--default').'" viewBox="144 99 2012 593" role="img" aria-label="VeriLex" focusable="false">'.$content.'</svg>';
}
function brandMonogram(): string {
    return '<svg class="brand-monogram" viewBox="140 120 435 500" aria-hidden="true" focusable="false"><image href="assets/verilex-logo.png" width="2172" height="724"/></svg>';
}
