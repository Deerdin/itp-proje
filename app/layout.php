<!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title) ?> · VeriLex</title>
<meta name="description" content="<?= e($descriptions[$page]??'VeriLex çalışma alanı') ?>">
<link rel="preload" href="assets/fonts/manrope-variable.ttf" as="font" type="font/ttf" crossorigin>
<link rel="icon" type="image/svg+xml" href="assets/favicon.svg?v=<?= filemtime(PUBLIC_ROOT . '/assets/favicon.svg') ?>"><link rel="stylesheet" href="assets/app.css?v=<?= filemtime(PUBLIC_ROOT . '/assets/app.css') ?>">
<script src="assets/app.js?v=<?= filemtime(PUBLIC_ROOT . '/assets/app.js') ?>" defer></script>
</head>
<body class="<?= $page==='auth'?'auth-body '.(($_GET['mode']??'')==='register'?'auth-register':''):'' ?>">
<a class="skip" href="#main">İçeriğe geç</a>
<?php if($page==='auth'): require __DIR__.'/views/auth.php'; else: ?>
<button class="menu-backdrop" aria-label="Menüyü kapat" type="button"></button>
<aside class="sidebar" id="sidebar">
<button class="sidebar-close icon-button" aria-label="Yan menüyü kapat" type="button">×</button>
<a class="brand" aria-label="VeriLex ana sayfa" href="<?= url('analyze') ?>"><?= brandLogo() ?></a>
<p class="nav-label">ÇALIŞMA ALANI</p>
<nav aria-label="Ana menü">
<a class="nav-item <?= $page==='analyze'?'active':'' ?>" href="<?= url('analyze') ?>"><?= icon('grid') ?><?= $u?'Genel bakış':'Metin analizi' ?></a>
<a class="nav-item <?= $page==='history'||$page==='report'?'active':'' ?>" href="<?= url('history') ?>"><?= icon('history') ?>Analiz geçmişi</a>
<?php if(expert($u)): ?><a class="nav-item <?= $page==='review'?'active':'' ?>" href="<?= url('review') ?>"><?= icon('check') ?>Uzman incelemesi</a><?php endif ?>
</nav>
<div class="sidebar-bottom">
<?php if($u): ?><div class="profile"><span class="avatar"><?= e(mb_strtoupper(mb_substr($u['name'],0,1))) ?></span><div><strong><?= e($u['name']) ?></strong><small><?= e(roleName($u['role'])) ?></small></div><form method="post"><?= csrf() ?><input type="hidden" name="action" value="logout"><button class="icon-button" title="Çıkış yap" aria-label="Çıkış yap"><?= icon('logout') ?></button></form></div><?php else: ?>
<div class="guest-account"><a class="button full" href="<?= url('auth') ?>">Giriş yap <?= icon('arrow') ?></a><a class="guest-register" href="<?= url('auth',['mode'=>'register']) ?>">Hesap oluştur</a></div>
<?php endif ?>
</div>
</aside>
<div class="app-shell">
<header class="topbar<?= $page==='analyze'?' home-topbar':'' ?>"><div class="breadcrumb"><button class="icon-button mobile-toggle" aria-label="Menüyü aç" aria-expanded="false" aria-controls="sidebar"><?= icon('menu') ?></button><a class="mobile-brand" href="<?= url('analyze') ?>" aria-label="VeriLex ana sayfa"><?= brandMonogram() ?></a><?php if($page!=='analyze'): ?><strong><?= e($title) ?></strong><?php endif ?></div></header>
<main id="main" class="main">
<?php if($error): ?><div role="alert" class="alert error"><?= e($error) ?></div><?php endif ?>
<?php if(isset($_SESSION['flash'])): ?><div role="status" class="alert success"><?= e($_SESSION['flash']) ?></div><?php unset($_SESSION['flash']);endif ?>
<?php
if(in_array($page,['analyze','history','report','review','privacy'],true)) require __DIR__.'/views/'.$page.'.php';
else echo '<section class="empty"><h1>'.e($title).'</h1><p>Çalışma alanından devam edebilirsiniz.</p><a class="button" href="'.url('analyze').'">Genel bakışa dön</a></section>';
?>
<footer class="footer"><span>© <?= date('Y') ?> VeriLex <span class="footer-dot">·</span> Aydınlatma metni analizi</span><span><a href="<?= url('privacy') ?>">Veri kullanımı</a></span></footer>
</main></div>
<?php endif ?>
</body></html>
