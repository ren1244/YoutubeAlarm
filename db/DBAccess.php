 <?php
 /**
  * 作為基本的 class，提供一些簡單的方法
  * 
  * @version 0.1.1
  * @author ren1244
  * @since 0.1.0 2019/01/08 ren1244: 修正多個欄位排序SQL語法錯誤
  */
 
 class DBAccess
{
    public $pdo;
    public $err;
    private $tbName;
    private $whereCond;
    private $condArgs;
    
    public function __construct($pdo)
    {
        $this->pdo=$pdo;
        $this->err="";
    }
    
    /** 
     * 執行 sql 語法
     *
     * @param string sql sql敘述句
     * @param array param 參數陣列
     * @param bool dbg 發生錯誤時是否直接列印錯誤
     * @return resource|false PDOStatement物件或錯誤
     */
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
    
    /** 
     * 選擇某個table，每個資料庫存取都需要用到以初始化內部的狀態
     *
     * @param string tbName 表的名稱
     * @return resource DBAccess物件
     */
    public function table($tbName)
    {
        $this->tbName=$tbName;
        $this->whereCond=[]; //紀錄 ->where 設定的條件
        $this->condArgs=[]; //紀錄 ->where 設定的條件中的值
        $this->orderArr=[];
        $this->nLimit=false;
        $this->nStart=0;
        return $this;
    }
    
    /** 
     * 設定條件
     *
     * @param mixed a 左運算式
     * @param mixed b 運算符
     * @param mixed c 右運算式
     * @return resource DBAccess物件
     */
    public function where($a, $b, $c=NULL)
    {
        if(is_null($c)){
            $c=$b;
            $b='=';
        }
        $this->whereCond[]="`$a` $b :?";
        $this->condArgs[]=$c;
        return $this;
    }
    
    /** 
     * 設定select出來的順序
     *
     * @param string(s) 不定長度 欄位名稱前面加上+(升冪)或-(降冪)，如果省略為+
     * @return resource DBAccess物件
     */
    public function order()
    {
        $n=func_num_args();
        if($n===0){
            return $this;
        }
        $arr=func_get_args();
        for($i=0;$i<$n;++$i){
            $v=$arr[$i];
            if($v[0]==='-'){ //遞減排序
                $this->orderArr[]='`'.substr($v,1).'` DESC';
            } elseif($v[0]==='+') { //遞增排序
                $this->orderArr[]='`'.substr($v,1).'` ASC';
            } else { //遞增排序(省略加減號)
                $this->orderArr[]='`'.$v.'` ASC';
            }
        }
        return $this;
    }
    
    /** 
     * 設定select出來的個數與起始位置
     *
     * @param int num 個數
     * @param string start 起始位置
     * @return resource DBAccess物件
     */
    public function limit($num, $start=0)
    {
        $this->nLimit=$num;
        $this->nStart=$start;
        return $this;
    }
    
    /** 
     * 取得表的資料(前面可以搭配->where方法)
     *
     * @param string(s)|void 不定數量 欄位名稱，若省略則回傳全部欄位
     * @return array 取得的資料
     */
    public function select()
    {
        $n=func_num_args();
        $arr=func_get_args();
        $sql='SELECT '.($n>0?'`'.implode('`, `',$arr).'`':'* ').'FROM '.$this->tbName;
        if(count($this->whereCond)){
            $sql.=' WHERE '.implode(' AND ',$this->whereCond);
        }
        if(count($this->orderArr)){
            $sql.=' ORDER BY '.implode(', ',$this->orderArr);
        }
        if($this->nLimit>0){
            $sql.=' LIMIT '.$this->nStart.','.$this->nLimit;
        }
        $stat=$this->sql_execute($sql, $this->condArgs);
        if($stat===false){
            return false;
        }
        return $stat->fetchALL(PDO::FETCH_ASSOC);
    }
    
    /** 
     * 更新表內的資料(前面可以搭配->where方法)
     *
     * @param array assocArr 關聯陣列，key為欄位名稱，value為要設定的值
     * @return int|false 更改的欄位數或SQL錯誤
     */
    public function update($assocArr)
    {
        $keys=[];
        $vals=[];
        foreach($assocArr as $key => $val){
            $keys[]="`$key`";
            $vals[]=$val;
        }
        $nParams=count($keys);
        if($nParams===0){
            return false;
        }
        $sql='UPDATE '.$this->tbName.' SET '.implode('=:?,',$keys).'=:?';
        if(count($this->whereCond)){
            $sql.=' WHERE '.implode(' AND ',$this->whereCond);
        }
        var_dump($sql, array_merge($vals,$this->condArgs));
        $stat=$this->sql_execute($sql, array_merge($vals,$this->condArgs));
        if($stat===false){
            return false;
        }
        return $stat->rowCount();
    }
    
    /** 
     * 刪除表內的資料(前面可以搭配->where方法)
     *
     * @return int|false 更改的欄位數或SQL錯誤
     */
    public function delete()
    {
        $sql='DELETE FROM '.$this->tbName;
        if(count($this->whereCond)){
            $sql.=' WHERE '.implode(' AND ',$this->whereCond);
        }
        $stat=$this->sql_execute($sql, $this->condArgs);
        if($stat===false){
            return false;
        }
        return $stat->rowCount();
    }
    
    /** 
     * 更新表內的資料(前面可以搭配->where方法)
     *
     * @param array assocArr 關聯陣列，key為欄位名稱，value為要設定的值
     * @return int|false 插入的欄位數或SQL錯誤
     */
    public function insert($assocArr)
    {
        $keys=[];
        $vals=[];
        foreach($assocArr as $key => $val){
            $keys[]="`$key`";
            $vals[]=$val;
        }
        $nParams=count($keys);
        if($nParams===0){
            return false;
        }
        $sql='INSERT INTO '.$this->tbName.'('.implode(',',$keys).') VALUES(:?'.str_repeat(',:?',$nParams-1).')';
        $stat=$this->sql_execute($sql, $vals);
        if($stat===false){
            return false;
        }
        return $stat->rowCount();
    }    
}
