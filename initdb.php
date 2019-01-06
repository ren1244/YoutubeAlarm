<?php
require_once(__dir__ .'/config.php');
require_once(__dir__ .'/db/dbClassLoader.php');

try {
    $pdo=new PDO(
        'mysql:host='.$cfg_db['host'].';dbname='.$cfg_db['dbname'],
        $cfg_db['user'],
        $cfg_db['password']
    );    
} catch(PDOException $e) {
    exit('error:'.$e);
}

$db=new YTRecord($pdo);
if($db->init()){
    echo '成功';
} else {
    echo '錯誤:'.$db->err;
}