<?php
// The development server does not read .htaccess. Keep its public surface explicit.
if (PHP_SAPI !== 'cli-server') { http_response_code(404);exit; }
$path=rawurldecode(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH) ?: '/');
if($path==='/robots.txt') return false;
if($path==='/' || $path==='/index.php') { require __DIR__.'/index.php';return true; }
if($path==='/install.php') { require __DIR__.'/install.php';return true; }
if($path==='/api.php') { require __DIR__.'/api.php';return true; }
if(preg_match('~^/assets/fonts/[a-zA-Z0-9_-]+\.(?:woff2|ttf)$~D',$path) && is_file(__DIR__.$path)) return false;
if(preg_match('~^/assets/[a-zA-Z0-9_-]+\.(css|js|svg|png|webp)$~D',$path) && is_file(__DIR__.$path)) return false;
http_response_code(404);echo 'Sayfa bulunamadı.';return true;
