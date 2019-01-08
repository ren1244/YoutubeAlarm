<?php
/**
 * 資料表 YTRecord 存取
 * 
 * @version 0.2.0
 * @author ren1244 n1244506804@gmail.com
 * @since 0.1.1 2019/01/08 ren1244: 新增 hit ts 欄位
 * @since 0.1.0 2019/01/08 ren1244: 使用DBAccess的方法，減少直接 sql_execute
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
             `vid` varchar(32) NOT NULL,
             `hit` INT UNSIGNED NOT NULL,
             `ts` BIGINT UNSIGNED NOT NULL
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
     * @param string title 影片標題
     * @param string vid 影片id
     * @return bool 成功或失敗
     */
    public function create($uid, $title, $vid)
    {
        $curTime=time();
        //如果已經存在，增加點擊次數及更新時間
        $sql='UPDATE YTRecord SET `hit`=`hit`+1, `title`=:?, `ts`=:? WHERE `uid`=:? AND `vid`=:?';
        $stat=$this->sql_execute($sql,[$title,$curTime,$uid,$vid]);
        if($stat===false){
            return false;
        } elseif($stat->rowCount()>0){
            return true;
        }
        //否則就新增資料
        $r=$this->table('YTRecord')
                ->insert(['uid'=>$uid, 'title'=>$title, 'vid'=>$vid, 'hit'=>1, 'ts'=>$curTime]);
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
     * @param bool hothit 若為true回傳熱門點播,false為最近點播
     * @return array|false 關聯陣列[{'uid','url'},...]或失敗
     */
    public function read($uid=NULL, $nStart=NULL, $nCount=NULL, $hothit=false)
    {
        $this->table('YTRecord');
        if(!is_null($uid)) {
             $this->where('uid',$uid);
        }
        if(!is_null($nCount) && !is_null($nStart)) {
            $this->limit($nCount,$nStart);
        }
        if($hothit){
            $this->order('-hit');
        }
        $r=$this->order('-ts')->select();
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
