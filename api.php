<?php
declare(strict_types=1);
define('PUBLIC_ROOT', __DIR__);
$bootstrap = is_file(__DIR__.'/app/bootstrap.php') ? __DIR__.'/app/bootstrap.php' : __DIR__.'/../kvkk-private/app/bootstrap.php';
require $bootstrap;
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Robots-Tag: noindex, nofollow');
function respond($data,int $code=200): void { http_response_code($code);echo json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR);exit; }
$u=user();
if(isset($_GET['guest']) && $_SERVER['REQUEST_METHOD']==='GET') {
    $a=guestReport();if(!$a) respond(['error'=>'Misafir raporu bulunamadı.'],404);
    if(isset($_GET['download'])) header('Content-Disposition: attachment; filename="verilex-misafir-analizi.json"');
    respond(['title'=>$a['title'],'sector'=>$a['sector'],'created_at'=>$a['created_at'],'analysis'=>$a['result'],'reviews'=>[],'guest'=>true]);
}
if(!$u) respond(['error'=>'Giriş yapmanız gerekir.'],401);
$method=$_SERVER['REQUEST_METHOD'];$id=(int)($_GET['id']??0);
try {
 if($method==='POST') {
    verifyCsrf($_SERVER['HTTP_X_CSRF_TOKEN']??null);
    $body=file_get_contents('php://input');
    if(strlen($body)>400000) respond(['error'=>'İstek çok büyük.'],413);
    try { $input=json_decode($body,true,32,JSON_THROW_ON_ERROR); } catch(JsonException $e) { respond(['error'=>'Geçersiz JSON.'],400); }
    if(!is_array($input)) respond(['error'=>'JSON nesnesi gerekir.'],400);
    rateLimit('analysis',30,600);
    $newId=createAnalysis($u,$input);respond(['id'=>$newId,'report_url'=>url('report',['id'=>$newId])],201);
 }
 if($method==='GET' && $id) {
    $a=findAnalysis($id,$u);if(!$a) respond(['error'=>'Analiz bulunamadı.'],404);
    $reviews=query('SELECT r.criterion,r.verdict,r.note,r.updated_at,u.name AS expert FROM reviews r JOIN users u ON u.id=r.expert_id WHERE analysis_id=?',[$id])->fetchAll();
    if(isset($_GET['download'])) header('Content-Disposition: attachment; filename="verilex-analiz-'.$id.'.json"');
    respond(['id'=>(int)$a['id'],'title'=>$a['title'],'sector'=>$a['sector'],'created_at'=>$a['created_at'],'analysis'=>$a['result'],'reviews'=>$reviews]);
 }
 if($method==='GET') {
    respond(['data'=>query('SELECT a.id,d.title,d.sector,a.score,a.created_at FROM analyses a JOIN documents d ON d.id=a.document_id WHERE d.user_id=? ORDER BY a.id DESC LIMIT 100',[$u['id']])->fetchAll()]);
 }
 header('Allow: GET, POST');respond(['error'=>'Desteklenmeyen metot.'],405);
} catch(DomainException $e) { respond(['error'=>$e->getMessage()],http_response_code()===429?429:(str_contains($e->getMessage(),'Güvenlik')?403:422)); }
