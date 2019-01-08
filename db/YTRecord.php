<?php
/**
 * 資料表 YTRecord 存取
 * 
 * @version 0.1.1
 * @author ren1244 n1244506804@gmail.com
 * @since 0.1.1 2019/01/08 ren1244: 使用DBAccess的方法，減少直接 sql_execute
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
          CREATE TABLE YTRecord (
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
        $this->table('YTRecord')
             ->where('uid',$uid)
             ->where('vid',$vid)
             ->delete();
        
        $r=$this->table('YTRecord')
                ->insert(['uid'=>$uid, 'title'=>$title, 'vid'=>$vid]);
        if($r === false || $r === 0) {
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
        $this->table('YTRecord');
        if(!is_null($uid)) {
             $this->where('uid',$uid);
        }
        if(!is_null($nCount) && !is_null($nStart)) {
            $this->limit($nCount,$nStart);
        }
        $r=$this->order('-id')->select();
        if($r===false){
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
        $r=$this->table('YTRecord')
                ->where('uid',$uid)
                ->where('id',$rid)
                ->delete();
        if($r===false || $r===0){
            return false;
        }
        return true;
    }
}
