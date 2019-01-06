<?php
/**
 * 提供 api 接口，操作 db\YTRecord
 * 
 * @version 0.1.0
 * @author ren1244 n1244506804@gmail.com
 */ 
session_start();
$_SESSION['uid']=1; //還沒實做使用者

/** 
 * [CREATE]新增資料
 *
 * @param string url 影片超連結
 * @return bool 成功或失敗
 */

/** 
 * [READ]讀取資料
 *
 * @return array 最近5筆資料
 */

/** 
 * [DELETE]新增資料
 *
 * @param int rid 紀錄ID
 * @return bool 成功或失敗
 */

 
//辨識 http 方法
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
try {
    $pdo=new PDO('mysql:host=localhost;dbname=test6','test6','test6');
    //$pdo=new PDO('mysql:host=localhost;dbname=ren1244_demo','ren1244_demo','d0l7NzpKbk8UxlAI');
} catch(PDOException $e) {
    exit('error:'.$e);
}
require_once(__dir__ .'/../db/main.php');
$db=new YTRecord($pdo);

//資料庫存取
$dbAccessFunc['GET']=function($db){
    $r=$db->read((int)$_SESSION['uid'],0,5);
    if($r===false) {
        http_response_code(500);
        exit('<h1>Internal Server Error</h1>');
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($r);
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