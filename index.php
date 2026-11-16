<?php
declare(strict_types=1);
define('PUBLIC_ROOT', __DIR__);
$bootstrap = is_file(__DIR__.'/app/bootstrap.php') ? __DIR__.'/app/bootstrap.php' : __DIR__.'/../kvkk-private/app/bootstrap.php';
require $bootstrap;
$page=(string)($_GET['page']??'analyze');
if(!in_array($page,['auth','analyze','report','history','review','privacy'],true)) { http_response_code(404);$page='missing'; }
$u=user();$error=null;$old=$_POST;
if($_SERVER['REQUEST_METHOD']==='POST') {
 try {
    verifyCsrf(inputString($_POST,'csrf'));
    $action=(string)($_POST['action']??'');
    if(in_array($action,['login','register'],true)) {
        $email=mb_strtolower(trim(inputString($_POST,'email')));
        $password=inputString($_POST,'password');
        rateLimit('auth',30,900);
        if(!filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($email)>190) throw new DomainException('Geçerli bir e-posta adresi girin.');
        if($action==='register') {
            $name=trim(inputString($_POST,'name'));
            rateLimit('register',5,3600);
            if(mb_strlen($name)<2 || mb_strlen($name)>100) throw new DomainException('Görünen ad 2–100 karakter olmalıdır.');
            if(mb_strlen($password)<12 || strlen($password)>72) throw new DomainException('Parola en az 12 karakter olmalı ve 72 baytı aşmamalıdır.');
            if(query('SELECT id FROM users WHERE email=?',[$email])->fetchColumn()) throw new DomainException('Bu e-posta ile kayıt oluşturulamıyor. Giriş yapmayı deneyin.');
            query('INSERT INTO users(name,email,password,role,created_at) VALUES(?,?,?,?,?)',[$name,$email,password_hash($password,PASSWORD_DEFAULT),'user',date('c')]);
            $uid=(int)db()->lastInsertId();
        } else {
            $attempt=query('SELECT * FROM login_attempts WHERE email=?',[$email])->fetch();
            if($attempt && (int)$attempt['attempts']>=5 && time()-(int)$attempt['last_attempt']<900) throw new DomainException('Çok fazla giriş denemesi. 15 dakika sonra tekrar deneyin.');
            $account=query('SELECT * FROM users WHERE email=?',[$email])->fetch();
            $dummy='$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi';
            $valid=password_verify($password,$account['password']??$dummy);
            if(!$account || !$valid) {
                if($attempt) query('UPDATE login_attempts SET attempts=?,last_attempt=? WHERE email=?',[(time()-(int)$attempt['last_attempt']>=900?1:(int)$attempt['attempts']+1),time(),$email]);
                else query('INSERT INTO login_attempts(email,attempts,last_attempt) VALUES(?,?,?)',[$email,1,time()]);
                throw new DomainException('E-posta veya parola hatalı.');
            }
            query('DELETE FROM login_attempts WHERE email=?',[$email]);$uid=(int)$account['id'];
        }
        session_regenerate_id(true);$_SESSION['user_id']=$uid;$_SESSION['csrf']=bin2hex(random_bytes(32));
        if(guestReport() && ($_GET['next']??'')==='guest') go('report',['guest'=>1]);
        go('analyze');
    }
    if($action==='analyze') rateLimit('analysis',30,600);
    if($action==='analyze' && !$u) {
        $input=validateAnalysisInput($_POST);
        if(guestUsed()) { $_SESSION['pending_analysis']=$input;go('auth',['reason'=>'quota']); }
        createGuestAnalysis($input);go('report',['guest'=>1]);
    }
    if(!$u) go('auth');
    if($action==='save_guest') {
        $report=guestReport();
        if(!$report) throw new DomainException('Kaydedilecek misafir analizi bulunamadı.');
        $id=createAnalysis($u,$report);unset($_SESSION['guest_report']);
        flash('Misafir analiziniz hesabınıza kaydedildi.');go('report',['id'=>$id]);
    }
    if($action==='logout') { $_SESSION=[];session_regenerate_id(true);go('auth'); }
    if($action==='analyze') { $id=createAnalysis($u,$_POST);flash('Analiz tamamlandı ve geçmişinize kaydedildi.');go('report',['id'=>$id]); }
    if($action==='review') { saveReview($u,(int)($_POST['id']??0),$_POST);flash('Uzman değerlendirmeniz kaydedildi.');go('review',['id'=>(int)$_POST['id']]); }
    if($action==='rename' || $action==='delete') {
        $a=findAnalysis((int)($_POST['id']??0),$u);
        if(!$a || (int)$a['user_id']!==(int)$u['id']) throw new DomainException('Bu belge üzerinde işlem yapma yetkiniz yok.');
        if($action==='delete') { query('DELETE FROM documents WHERE id=?',[$a['document_id']]);flash('Belge ve ilişkili analiz verileri silindi.');go('history'); }
        $title=trim((string)($_POST['title']??''));
        if(mb_strlen($title)<3 || mb_strlen($title)>150) throw new DomainException('Belge adı 3–150 karakter olmalıdır.');
        query('UPDATE documents SET title=? WHERE id=?',[$title,$a['document_id']]);flash('Belge adı güncellendi.');go('report',['id'=>$a['id']]);
    }
    if($action==='role') {
        if($u['role']!=='admin') throw new DomainException('Yönetici yetkisi gerekir.');
        $id=(int)($_POST['user_id']??0);$role=(string)($_POST['role']??'');
        if($id===(int)$u['id']) throw new DomainException('Kendi rolünüzü bu ekrandan değiştiremezsiniz.');
        if(!in_array($role,['user','expert','admin'],true)) throw new DomainException('Geçersiz rol.');
        query('UPDATE users SET role=? WHERE id=?',[$role,$id]);flash('Kullanıcı rolü güncellendi.');go('review');
    }
 } catch(DomainException $ex) { $error=$ex->getMessage(); }
}
$u=user();
if(!$u && !in_array($page,['auth','analyze','report','privacy','missing'],true)) go('auth');
$guestUsed=(!$u && $page!=='privacy') ? guestUsed() : false;
if($u && $page==='analyze' && !$_POST && isset($_SESSION['pending_analysis'])) { $old=$_SESSION['pending_analysis'];unset($_SESSION['pending_analysis']); }
if($u && $page==='auth') go('analyze');
if($page==='review' && !expert($u)) { http_response_code(403);$page='forbidden'; }
$titles=['privacy'=>'Veri kullanımı','auth'=>'Giriş yap','analyze'=>'Genel bakış','report'=>'Analiz raporu','history'=>'Analiz geçmişi','review'=>'Uzman çalışma alanı','missing'=>'Sayfa bulunamadı','forbidden'=>'Erişim yetkisi gerekli'];
$descriptions=['privacy'=>'Metin, hesap ve oturum verilerinin nasıl kullanıldığını öğrenin.','auth'=>'Türkçe aydınlatma metinlerini incelemek için güvenli çalışma alanınıza giriş yapın.','analyze'=>'Yeni aydınlatma metni analizi başlatın ve son çalışmalarınızı takip edin.','report'=>'Kriter bazlı bulguları, metin kanıtlarını ve uzman değerlendirmelerini inceleyin.','history'=>'Kaydedilmiş analizlerinizi arayın, filtreleyin ve karşılaştırın.','review'=>'Analiz bulgularını gerekçeli uzman etiketleriyle değerlendirin.'];
$titles['analyze']=$u?'Genel bakış':'Metin analizi';
if(in_array($page,['auth','report','history','review','forbidden','missing'],true)) header('X-Robots-Tag: noindex, nofollow');
$title=$titles[$page]??'VeriLex';
require ROOT.'/app/layout.php';
