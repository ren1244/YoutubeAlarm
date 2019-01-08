<?php
/**
 * 提供 api 接口，操作 db\YTRecord，目前提供給前端的API見下列說明
 * 
 * @version 1.1.0
 * @author ren1244 n1244506804@gmail.com
 * @since 0.1.0 [GET]取得資料回傳格式改變，同時新增回傳「最熱門」
 */

/** 
 * [GET] 取得資料
 *
 * @return josn {'new':最近5筆資料,'hot':最熱門5筆資料}
 */

/** 
 * [POST] 增加新資料
 *
 * @param string vid youtube影片的id
 * @param string title youtube影片的標題
 * @return josn|httpCode [status:'ok'] 或 httpCode
 */

/** 
 * [POST] 刪除資料
 *
 * @param string _method 值為DELETE，以辨識這是DELTE方法
 * @param string rid record id
 * @return josn|httpCode [status:'ok'] 或 httpCode
 */

session_start();
$_SESSION['uid']=1; //還沒實做使用者
 
//辨識 http 方法：分為 GET, POST, DELETE
$method=$_SERVER['REQUEST_METHOD'];
if($method!=='GET' && $method!=='POST') {
    http_response_code(400);
    exit('<h1>Bad Request</h1>');
}

if($method==='POST' && isset($_POST['_method'])) {
    if($_POST['_method']==='DELETE') {
        $method='DELETE';
    } else {
        http_response_code(400);
        exit('<h1>Bad Request</h1>');
    }
}

//未登入過濾
if(!isset($_SESSION['uid'])) {
    http_response_code(403);
    exit('<h1>Forbidden</h1>');
}

//csrf_token 過濾(等實作使用者再來處理)

//參數過濾
function verifyId($x){
    return is_numeric($x) && is_int((int)$x);
}

$filterFunc['GET']=function(){
    return true;
};

$filterFunc['POST']=function(){
    if(!isset($_POST['title']) || !isset($_POST['vid'])) {
        return false;
    }
    $r=preg_match('/^[0-9a-zA-Z-_]{11}$/',$_POST['vid']);
    if($r===0){
        return false;
    } elseif($r===false) {
        http_response_code(500);
        exit('<h1>Internal Server Error</h1>fiter vid error');
    }
    return true;
};

$filterFunc['DELETE']=function(){
    if(!isset($_POST['rid']) || !verifyId($_POST['rid'])) {
        return false;
    }
    return true;
};

if(!$filterFunc[$method]()) {
    http_response_code(400);
    exit('<h1>Bad Request</h1>'.$method);
}

//連接資料庫
require_once(__dir__ .'/../config.php');
try {
    $pdo=new PDO(
        'mysql:host='.$cfg_db['host'].';dbname='.$cfg_db['dbname'],
        $cfg_db['user'],
        $cfg_db['password']
    );
} catch(PDOException $e) {
    exit('error:'.$e);
}
require_once(__dir__ .'/../db/dbClassLoader.php');
$db=new YTRecord($pdo);

//資料庫存取
$dbAccessFunc['GET']=function($db){
    $r=$db->read((int)$_SESSION['uid'],0,5); //最近
    if($r===false) {
        http_response_code(500);
        exit('<h1>Internal Server Error</h1>');
    }
    $r2=$db->read((int)$_SESSION['uid'],0,5,true); //熱門
    if($r2===false) {
        http_response_code(500);
        exit('<h1>Internal Server Error</h1>');
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['new'=>$r,'hot'=>$r2]);
};

$dbAccessFunc['POST']=function($db){
    $r=$db->create(
        (int)$_SESSION['uid'],
        $_POST['title'],
        $_POST['vid']
    );
    if($r===false) {
        http_response_code(500);
        exit('<h1>Internal Server Error</h1>');
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status'=>'ok']);
};

$dbAccessFunc['DELETE']=function($db){
    $r=$db->remove((int)$_SESSION['uid'],(int)$_POST['rid']);
    if($r===false) {
        http_response_code(500);
        exit('<h1>Internal Server Error</h1>');
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status'=>'ok']);
};

$dbAccessFunc[$method]($db);