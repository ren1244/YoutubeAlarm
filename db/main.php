<?php
class DBAccess
{
    public $pdo;
    public $err;
    
    public function __construct($pdo)
    {
        $this->pdo=$pdo;
        $this->err="";
    }
    
    //執行 sql, 並回傳 $stat 或是 false
    protected function sql_execute($sql, $param=false, $dbg=false)
    {
        if(!$param)
            $param=[];
        $arr=explode(':?',$sql);
        $n=count($param);
        if($n+1 != count($arr)){
            $this->err="sql_execute:參數數量不合";
            return false;
        }
        foreach($arr as $k=>$v){
            if($k==0){
                $sql=$v;
            } else {
                $sql.=':param'.$k.$v;
            }
        }
        if($dbg)
            echo $sql;
        $stat=$this->pdo->prepare($sql);
        if($stat===false)
            return false;
        for($i=0;$i<$n;++$i){
            $type=gettype($param[$i]);
            if($type=='integer') {
                $type=PDO::PARAM_INT;
            } elseif($type=='boolean') {
                $type=PDO::PARAM_BOOL;
            } else {
                $type=PDO::PARAM_STR;
            }
            if($dbg)
                echo " bindValue(".$param[$i].")";
            $stat->bindValue(":param".($i+1), $param[$i], $type);
        }
        if(!$stat->execute()){
            $this->err="sql_execute:執行錯誤:".implode("###",$stat->errorInfo());
            return false;
        }
        return $stat;
    }
}

require(__dir__ .'/YTRecord.php');