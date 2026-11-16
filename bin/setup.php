<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';
if(PHP_SAPI !== 'cli') exit;
Database::migrate();
echo "Veritabanı şeması hazır.\n";
if(!in_array('--demo',$argv,true)) exit;
if(getenv('APP_ENV') === 'production') { fwrite(STDERR,"Üretimde demo hesabı oluşturulamaz.\n");exit(1); }
$password = getenv('DEMO_PASSWORD');
if(!$password || strlen($password)<12) { fwrite(STDERR,"DEMO_PASSWORD en az 12 karakter olmalıdır.\n");exit(1); }
foreach([['Demo Kullanıcı','kullanici@kvkk.test','user'],['Demo Uzman','uzman@kvkk.test','expert'],['Demo Yönetici','yonetici@kvkk.test','admin']] as [$name,$email,$role]) {
 if(!query('SELECT id FROM users WHERE email=?',[$email])->fetchColumn()) query('INSERT INTO users(name,email,password,role,created_at) VALUES(?,?,?,?,?)',[$name,$email,password_hash($password,PASSWORD_DEFAULT),$role,date('c')]);
}
$owner=query('SELECT * FROM users WHERE email=?',['kullanici@kvkk.test'])->fetch();
if(!query('SELECT id FROM documents WHERE user_id=?',[$owner['id']])->fetchColumn()) {
 foreach(require __DIR__.'/../app/samples.php' as $sample) createAnalysis($owner,$sample);
}
echo "Üç demo hesabı ve örnek analizler hazır. Mevcut hesapların parolaları değiştirilmedi.\n";
