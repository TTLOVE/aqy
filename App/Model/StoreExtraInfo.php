<?php

namespace Model;
use Illuminate\Database\Eloquent\Model;
use Service\DATABASE;

/**
 * StoreExtraInfo Model 商家身份认证信息模块
 */
class StoreExtraInfo extends Model
{

    public $timestamps = false;
    protected $table = 'store_extra_info';
    protected $db;

    public function __construct()
    {
        $this->db = new DATABASE();
        $this->db->setDataBase('finance');
    }

    /**
        * 根据商家id获取商家设置的额外信息
        *
        * @param $storeId
        *
        * @return 
     */
    public function getStoreExtraInfoByStoreId($storeId)
    {
        $storeExtraInfo = $this->db->row('select * from ' . $this->table . ' where store_id = ?', [$storeId]);
        return ($storeExtraInfo===false) ? [] : $storeExtraInfo; 
    }

    /**
        * 保存商家额外信息
        *
        * @param $storeId 商家id
        * @param $returnMoneyMax 可接受最高月还款额度
        * @param $educationalStatus 教育程度
        * @param $socialSecurity 现单位是否缴纳社保
        * @param $carInfomation 车辆情况
        * @param $operatingLife 经营年限
        * @param $operatingStream 经营流水
        *
        * @return boolean
     */
    public function saveStoreExtraInfo($storeId, $returnMoneyMax, $educationalStatus, $socialSecurity, $carInfomation, $operatingLife, $operatingStream)
    {
        $nowTime = time();
        $storeExtraInfo = $this->getStoreExtraInfoByStoreId($storeId);
        // 如果没有数据，则插入
        if ( empty($storeExtraInfo) ) {
            $insertData = [
                $storeId,
                $nowTime,
                $nowTime,
                $returnMoneyMax,
                $educationalStatus,
                $socialSecurity,
                $carInfomation,
                $operatingLife,
                $operatingStream
            ];
            $inserStatus = $this->db->query("insert into " . $this->table . 
                "(store_id,add_time,update_time,return_money_max,educational_status,social_security,car_infomation,operating_life,operating_stream) " .
                " values(?,?,?,?,?,?,?,?,?)", $insertData);
            return $inserStatus;
        } else {
            $updateData = [
                $nowTime,
                $returnMoneyMax,
                $educationalStatus,
                $socialSecurity,
                $carInfomation,
                $operatingLife,
                $operatingStream,
                $storeId
            ];
            // 如果有数据就更新
            $updateStatus = $this->db->query("update " . $this->table . 
                " set update_time=?,return_money_max=?,educational_status=?,social_security=?,car_infomation=?,operating_life=?,operating_stream=? where store_id=?", $updateData);
            return $updateStatus;
        }
    }
    
    /**
     * 是否开放交易流水
     * 
     * @param int $storeId
     * @param number $status  是否开放流水  0:不开放,1:开放
     * @return boolean|Ambigous <NULL, unknown, int>
     * @author zengxiong
     * @since  2017年8月7日
     */
    public function updateOpenStream($storeId,$status)
    {
        if (empty($storeId)){
            return false;
        }
        $sql = 'UPDATE store_extra_info SET open_stream = ? WHERE store_id = ?';
        
        return $this->db->query($sql,array(intval($status),$storeId));
    }
}
