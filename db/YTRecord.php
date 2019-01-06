<?php
/**
 * 資料表 YTRecord 存取
 * 
 * @version 0.1.0
 * @author ren1244 n1244506804@gmail.com
 */

class YTRecord extends DBAccess
{
    /** 
     * 建立資料表 YTRecord
     *
     * @return bool 成功或失敗
     */
    public function init()
    {
        $sql=<<<SQLSTAT
          CREATE OR REPLACE TABLE YTRecord (
             `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
             `uid` INT UNSIGNED NOT NULL,
             `title` varchar(512) NOT NULL,
             `vid` varchar(32) NOT NULL
          )
SQLSTAT;
       if($this->sql_execute($sql)===false)
          return false;
       $sql='CREATE INDEX YTRecord_index_uid ON YTRecord(`uid`)';
       if($this->sql_execute($sql)===false)
          return false;
       return true;
    }
    
    /** 
     * 新增資料
     *
     * @param int    uid 使用者 id
     * @param string url youtube 的超連結
     * @return bool 成功或失敗
     */
    public function create($uid, $title, $vid)
    {
        $stat=$this->sql_execute(
            'DELETE FROM YTRecord WHERE `uid`=:? AND `vid`=:?',
            [$uid, $vid]
        );
        
        $stat=$this->sql_execute(
            'INSERT INTO YTRecord(`uid`,`title`,`vid`) VALUE(:?, :?, :?)',
            [$uid, $title, $vid]
        );
        if($stat === false || $stat->rowCount() === 0) {
            return false;
        }
        return true;
    }
    
    /** 
     * 讀取資料
     *
     * @param int|NULL uid 使用者 id, 若為NULL為任意使用者
     * @param int nStart 起始位置
     * @param int nCount 數量
     * @return array|false 關聯陣列[{'uid','url'},...]或失敗
     */
    public function read($uid=NULL, $nStart=NULL, $nCount=NULL)
    {
        $arr=[];
        $sql='SELECT * FROM YTRecord';
        if(!is_null($uid)) {
           $sql.=' WHERE `uid`=:?';
           $arr[]=$uid;
        }
        $sql.=' ORDER BY `id` DESC';
        if(!is_null($nCount) && !is_null($nStart)) {
           $sql.=' LIMIT :?,:?';
           $arr[]=$nStart;
           $arr[]=$nCount;
        }
        $stat=$this->sql_execute($sql,$arr);
        if($stat === false) {
            echo '[A:'.$this->err.']';
            var_dump($sql);
            var_dump($arr);
           return false;
        }
        $r=$stat->fetchALL(PDO::FETCH_ASSOC);
        if($r===false) {
           return false;
        }
        return $r;
    }
    
    /** 
     * 刪除資料
     *
     * @param int uid 使用者id
     * @param int rid 資料id
     * @return bool 成功或失敗
     */
    public function remove($uid, $rid)
    {
        $stat=$this->sql_execute(
            'DELETE FROM YTRecord WHERE `uid`=:? AND `id`=:?',
            [$uid,$rid]
        );
        if($stat===false || $stat->rowCount()===0){
            return false;
        }
        return true;
    }
}
