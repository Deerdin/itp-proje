<?php
declare(strict_types=1);
define('PUBLIC_ROOT',__DIR__);
$bootstrap=is_file(__DIR__.'/app/bootstrap.php')?__DIR__.'/app/bootstrap.php':__DIR__.'/../kvkk-private/app/bootstrap.php';
require $bootstrap;
header('X-Robots-Tag: noindex, nofollow');
$token=getenv('INSTALL_TOKEN')?:'';
$lockPath=ROOT.'/storage/installed.lock';
if ((!production() && getenv('ALLOW_WEB_INSTALL')!=='1') || strlen($token)<64 || is_file($lockPath)) {
    http_response_code(404);exit('Sayfa bulunamadı.');
}
$error=null;$success=false;
if($_SERVER['REQUEST_METHOD']==='POST') {
    try {
        verifyCsrf(inputString($_POST,'csrf'));
        $_SESSION['install_attempts']=(int)($_SESSION['install_attempts']??0)+1;
        if($_SESSION['install_attempts']>10) throw new DomainException('Çok fazla kurulum denemesi. Daha sonra tekrar deneyin.');
        if(!hash_equals($token,inputString($_POST,'install_token'))) throw new DomainException('Kurulum anahtarı geçersiz.');
        $name=trim(inputString($_POST,'name'));$email=mb_strtolower(trim(inputString($_POST,'email')));$password=inputString($_POST,'password');
        if(mb_strlen($name)<2 || mb_strlen($name)>100) throw new DomainException('Ad soyad 2–100 karakter olmalıdır.');
        if(!filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($email)>190) throw new DomainException('Geçerli bir e-posta adresi girin.');
        if(mb_strlen($password)<12 || strlen($password)>72) throw new DomainException('Parola en az 12 karakter olmalı ve 72 baytı aşmamalıdır.');
        $mutex=fopen(ROOT.'/storage/install.mutex','c');
        if(!$mutex || !flock($mutex,LOCK_EX)) throw new RuntimeException('Kurulum kilidi alınamadı.');
        try {
            if(is_file($lockPath)) throw new DomainException('Kurulum zaten tamamlanmış.');
            Database::migrate();
            if((int)query('SELECT COUNT(*) FROM users')->fetchColumn()>0) throw new DomainException('Veritabanı boş değil. Yeni bir veritabanı seçin veya mevcut hesabınızla giriş yapın.');
            // Prepare the lock marker first, so a write failure cannot leave a partially installed account.
            $marker=fopen($lockPath,'x');
            if(!$marker) throw new RuntimeException('Kurulum kilidi oluşturulamadı.');
            try {
                query('INSERT INTO users(name,email,password,role,created_at) VALUES(?,?,?,?,?)',[$name,$email,password_hash($password,PASSWORD_DEFAULT),'admin',date('c')]);
                fwrite($marker,date('c'));fclose($marker);$success=true;
                unset($_SESSION['install_attempts']);
            } catch(Throwable $exception) { fclose($marker);unlink($lockPath);throw $exception; }
        } finally {flock($mutex,LOCK_UN);fclose($mutex);}
    } catch(DomainException $exception) {$error=$exception->getMessage();}
}
?>
<!doctype html><html lang="tr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Site kurulumu · VeriLex</title><link rel="icon" type="image/svg+xml" href="assets/favicon.svg"><link rel="stylesheet" href="assets/app.css?v=<?= filemtime(PUBLIC_ROOT.'/assets/app.css') ?>"></head><body>
<main class="install-main"><section class="panel install-panel"><a class="brand install-brand" href="index.php" aria-label="VeriLex ana sayfa"><?= brandLogo() ?></a><h1><?= $success?'Kurulum tamamlandı':'Site kurulumu' ?></h1>
<?php if($success): ?><p>Yönetici hesabınız oluşturuldu. Kurulum ekranı kapatıldı.</p><p>Dosya yöneticisinden <strong>install.php</strong> dosyasını kaldırın ve özel yapılandırmadaki INSTALL_TOKEN değerini silin.</p><a class="button" href="<?= url('auth') ?>">Giriş yap</a>
<?php else: ?><p>MySQL bağlantısını doğrulayın ve ilk yönetici hesabını oluşturun.</p><?php if($error): ?><div class="alert error" role="alert"><?= e($error) ?></div><?php endif ?>
<form method="post" class="stack"><?= csrf() ?><label>Kurulum anahtarı<input type="password" name="install_token" required autocomplete="off" minlength="64"></label><label>Ad soyad<input name="name" required minlength="2" maxlength="100" autocomplete="name" placeholder="Adınız ve soyadınız"></label><label>E-posta adresi<input type="email" name="email" required maxlength="190" autocomplete="email" inputmode="email" autocapitalize="none" spellcheck="false" placeholder="ornek@eposta.com"></label><label>Parola<input type="password" name="password" required minlength="12" maxlength="72" autocomplete="new-password" placeholder="En az 12 karakter"></label><button class="button">Kurulumu tamamla</button></form>
<?php endif ?></section></main></body></html>
