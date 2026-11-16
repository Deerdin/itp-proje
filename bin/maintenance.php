<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';
if(PHP_SAPI!=='cli') exit;
query('DELETE FROM rate_limits WHERE expires_at<?',[time()]);
query('DELETE FROM login_attempts WHERE last_attempt<?',[time()-86400]);
query('DELETE FROM guest_trials WHERE used_at<?',[date('c',time()-366*86400)]);
$removed=0;
foreach(glob(ROOT.'/storage/sessions/sess_*')?:[] as $file) {
    if(is_file($file) && !is_link($file) && filemtime($file)<time()-7200 && unlink($file)) $removed++;
}
echo 'Süresi dolmuş güvenlik kayıtları temizlendi. Oturum: '.$removed.PHP_EOL;
