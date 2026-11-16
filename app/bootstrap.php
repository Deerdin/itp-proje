<?php
declare(strict_types=1);
const ROOT = __DIR__ . '/..';
if (!defined('PUBLIC_ROOT')) define('PUBLIC_ROOT',ROOT);
if (is_file(ROOT . '/.env')) {
    foreach (file(ROOT . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line=trim($line);
        if ($line==='' || $line[0]==='#' || !str_contains($line,'=')) continue;
        [$key,$value]=explode('=',$line,2);$key=trim($key);$value=trim($value);
        if (!preg_match('/^[A-Z][A-Z0-9_]*$/D',$key)) continue;
        if (strlen($value)>=2 && (($value[0]==='"' && substr($value,-1)==='"') || ($value[0]==="'" && substr($value,-1)==="'"))) $value=substr($value,1,-1);
        if (getenv($key)===false) putenv($key.'='.$value);
    }
}
require_once __DIR__.'/Config.php';
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', ROOT.'/storage/app.log');
error_reporting(E_ALL);
date_default_timezone_set('Europe/Istanbul');
set_exception_handler(function (Throwable $error): void {
    $reference=bin2hex(random_bytes(4));
    // Never send a stack trace, SQL statement, password or filesystem location to the client.
    error_log('[VeriLex '.$reference.'] '.get_class($error).' code='.$error->getCode().' at '.basename($error->getFile()).':'.$error->getLine());
    if (PHP_SAPI==='cli') { fwrite(STDERR,$error->getMessage().PHP_EOL);exit(1); }
    http_response_code(500);
    if (basename($_SERVER['SCRIPT_NAME'] ?? '')==='api.php') {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error'=>'İşlem tamamlanamadı. Lütfen daha sonra tekrar deneyin.','reference'=>$reference],JSON_UNESCAPED_UNICODE);
    } else {
        echo '<!doctype html><html lang="tr"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>İşlem tamamlanamadı</title><h1>Şu anda işleminizi tamamlayamıyoruz.</h1><p>Lütfen daha sonra tekrar deneyin. Hata kodu: '.$reference.'</p><a href="index.php">Ana sayfaya dön</a></html>';
    }
});
enforceProductionConfig();
if (PHP_SAPI!=='cli') {
    if (production()) {
        $canonical=parse_url(getenv('APP_URL'));
        $requestHost=strtolower((string)($_SERVER['HTTP_HOST']??''));
        $expectedHost=strtolower($canonical['host'].(isset($canonical['port'])?':'.$canonical['port']:''));
        if (!secureRequest() || $requestHost!==$expectedHost) {
            // Use only the configured origin; never build redirects from the Host header.
            $path=parse_url($_SERVER['REQUEST_URI']??'/',PHP_URL_PATH) ?: '/';
            if (str_starts_with($path,'//') || preg_match('/[\r\n]/',$path)) $path='/';
            $query=parse_url($_SERVER['REQUEST_URI']??'',PHP_URL_QUERY);
            $origin='https://'.$expectedHost;
            header('Location: '.$origin.$path.($query?'?'.$query:''),true,308);exit;
        }
        header('Strict-Transport-Security: max-age=31536000');
    }
    header('Cache-Control: no-store');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: same-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; font-src 'self'; img-src 'self' data:; object-src 'none'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'");
    if ((int)($_SERVER['CONTENT_LENGTH']??0)>400000) {
        http_response_code(413);header('Content-Type: text/plain; charset=utf-8');echo 'Gönderilen içerik çok büyük.';exit;
    }
    ini_set('session.use_strict_mode','1');
    ini_set('session.use_only_cookies','1');
    ini_set('session.gc_maxlifetime','7200');
    if (production()) {
        $sessionPath=ROOT.'/storage/sessions';
        if (!is_dir($sessionPath) && !mkdir($sessionPath,0700,true)) throw new RuntimeException('Özel oturum dizini oluşturulamadı.');
        session_save_path($sessionPath);
    }
    session_name('kvkk_session');
    session_set_cookie_params(['httponly'=>true,'secure'=>secureRequest(),'samesite'=>'Lax','path'=>'/']);
    session_start();
    if (isset($_SESSION['last_seen']) && time()-$_SESSION['last_seen']>7200) { $_SESSION=[];session_regenerate_id(true); }
    $_SESSION['last_seen']=time();
    $_SESSION['csrf']=$_SESSION['csrf']??bin2hex(random_bytes(32));
}
require_once __DIR__.'/Database.php';
require_once __DIR__.'/Analyzer.php';
require_once __DIR__.'/functions.php';
