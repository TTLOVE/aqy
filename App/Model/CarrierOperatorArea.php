<?php

namespace Model;
use Illuminate\Database\Eloquent\Model;
use Service\DATABASE;

/**
 * CarrierOperatorArea Model 运营商放款区域
 */
class CarrierOperatorArea extends Model
{

    public $timestamps = false;
    protected $table = 'carrieroperator_area';
    protected $db;

    public function __construct()
    {
        $this->db = new DATABASE();
        $this->db->setDataBase('finance');
    }

    /**
        * 根据类型获取商家所属运营商ID
        * @param int $type 类型
        * @param int $Id 对应类型的id
     *
        * @return array
     */
    public function getOperatorIdByType($Id, $type)
    {
        if ($type==0) {
            $data = $this->db->row("select operator_id from " . $this->table . ' where quarter_id = ?', [$Id]);

        }
        if ($type==1) {
            $data = $this->db->row("select operator_id from " . $this->table . ' where city_id = ?', [$Id]);

        }
        if ($type==2) {
            $data = $this->db->row("select operator_id from " . $this->table . ' where province_id = ?', [$Id]);

        }
        return ($data===false) ? [] : $data;
    }
    
    /**
     * 根据店铺id获取所属运营商
     * 
     * @param int $storeId 店铺id
     * @return Ambigous <number, string>
     * @author zengxiong
     * @since  2017年8月7日
     */
    public function getOperatorIdByStoreId($storeId)
    {
        if (empty($storeId)){
            return false;
        }
        $storeModel = new StoreInfo();
        $storeInfo  = $storeModel->getStoreInfoById($storeId,'quarter_id,city_id,province_id');
        
        $operatorId = 0;
        foreach ($storeInfo as $key => $value) {
            $sql = 'SELECT operator_id FROM carrieroperator_area WHERE '.$key.' = '.$value;
            $operatorId = $this->db->single($sql);
            if (!empty($operatorId)){
                break;
            }
        }
        return $operatorId;
    }
    
    /**
     * 获取商家可用额度
     *
     * @author zengxiong
     * @since  2017年8月7日
     */
    public function getStoreUsableLimit($storeId)
    {
        if (empty($storeId)){
            return false;
        }
        $operatorModel = new operator();
        
        //获取运营商id
        $operatorId = $this->getOperatorIdByStoreId($storeId);
        //获取运营商信息
        $operatorInfo = $operatorModel->getOperatorInfoByid($operatorId);
        //最大额度
        $moneyLimit = is_array($operatorInfo) && isset($operatorInfo['money_limit']) ? $operatorInfo['money_limit'] : 0;
        //获取借了没还清的总金额
        $sql = 'SELECT SUM(borrow_money) borrow_money FROM store_borrow_money_log WHERE  store_id = ? AND status = 1';
        $returnWait = $this->db->single($sql,[$storeId]);
        
        return array(
            'operatorId' => $operatorId,//运营商
            'moneyLimit' => $moneyLimit,//最大额度
            'usableLimit'=> $moneyLimit - $returnWait,//可用额度
        );
        
    }
}
