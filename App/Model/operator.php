<?php
namespace Model;

use Illuminate\Database\Eloquent\Model;
use Service\DATABASE;

/**
 * 运营商
 * 
 * @author    zengxiong
 * @since     2017年8月1日
 * @version   1.0
 */
class operator extends Model
{

    public $timestamps = false;
    protected $table = 'carrieroperator';
    protected $_model;
    protected $db;

    public function __construct()
    {
        $this->db = new DATABASE();
        $this->db->setDataBase('finance');
    }
    
    /**
     * 添加运营商
     * 
     * @param unknown $data
     * @author zengxiong
     * @since  2017年8月1日
     */
    public function addOperator(array $data)
    {
        $field = $param = '';
        $value = array();
        foreach ($data as $k => $v){
            if (empty($v)){
                return false;
            }
            $field .=  $k.',';
            $param .= '?,';
            $value[] = $v;
        }
        $field = trim($field,',');
        $param = trim($param,',');
        if (empty($field) || empty($param) || empty($value)){
            return false;
        }
        return $this->db->query("INSERT INTO carrieroperator(".$field.") VALUES(".$param.")", $value);
    }
    
    /**
     * 添加运营商的地区信息
     * 
     * @param array $data
     * @author zengxiong
     * @since  2017年8月2日
     */
    public function addCarrieroperatorArea(array $data)
    {
        $operatorId = isset($data['operator_id']) ? intval($data['operator_id']) : 0;
        $provinceId = isset($data['province_id']) ? intval($data['province_id']) : 0;
        $cityId     = isset($data['city_id']) ? intval($data['city_id']) : 0;
        $quarterId  = isset($data['quarter_id']) ? intval($data['quarter_id']) : 0;
        if (empty($operatorId) || empty($provinceId) || empty($cityId) || empty($quarterId)){
            return false;
        }
        return $this->db->query("INSERT INTO carrieroperator_area(operator_id,province_id,city_id,quarter_id) VALUES(?,?,?,?)", array($operatorId,$provinceId,$cityId,$quarterId));
    }
    
    /**
     * 根据状态获取运营商的列表
     * 
     * @param number $status
     * @return Ambigous <NULL, array>
     * @author zengxiong
     * @since  2017年8月3日
     */
    public function getOperatorListBystatus($status = 1,$page = 1,$pageSize = 10)
    {
        $limit = 'LIMIT '.($page - 1) * $pageSize .','.$pageSize;
        $dataSql = 'SELECT operator_id,company_name,money_limit,tmp_money_limit,operator_name FROM carrieroperator WHERE status = ? '. $limit;
        $data =  $this->db->query($dataSql,array($status));
        
        $totalSql = 'SELECT count(operator_id) `count` FROM carrieroperator WHERE status = ?'; 
        $total = $this->db->single($totalSql,array($status));
        return array($data,$total);
    }
    
    /**
     * 读取运营商信息
     * 
     * @param unknown $operatorId
     * @param number $status
     * @return boolean|mixed
     * @author zengxiong
     * @since  2017年8月3日
     */
    public function getOperatorInfoByid($operatorId,$status = 1)
    {
        $operatorId = intval($operatorId);
        if (empty($operatorId)){
            return false;
        }
        $where = !empty($status) ? 'AND status = ?' : '';
        $param = !empty($status) ? array($operatorId,$status) : array($operatorId);
        $sql = 'SELECT * FROM carrieroperator WHERE operator_id = ? '.$where;
        
        return $this->db->row($sql,$param);
    }
    
    /**
     * 运营商审核
     * 
     * @param unknown $id
     * @param unknown $status
     * @author zengxiong
     * @since  2017年8月3日
     */
    public function checkPassOperator($id,$status)
    {
        if (empty($id) || empty($status)){
            return false;
        }
        $sql = 'UPDATE carrieroperator SET status = ? WHERE operator_id = ?';
        return $this->db->query($sql,array($status,$id));
    }
    
    /**
     * 更改临时额度
     * 
     * @author zengxiong
     * @since  2017年8月3日
     */
    public function updateTmpMoneyLimit($operatorId,$moneyLimit)
    {
        $operatorId = intval($operatorId);
        $moneyLimit = intval($moneyLimit);
        if (empty($operatorId) || empty($moneyLimit)){
            return false;
        }
        return $this->db->query('UPDATE carrieroperator SET tmp_money_limit = ? WHERE  operator_id = ?',array($moneyLimit,$operatorId));
    }

    /**
     * 审核变更额度
     * 
     * @param unknown $operatorId 运营商id
     * @param unknown $status 1通过,0不通过
     * @author zengxiong
     * @since  2017年8月3日
     */
    public function updateMoneyLimit($operatorId,$status = 0)
    {
        if (empty($operatorId)){
            return false;
        }
        
        $info = $this->getOperatorInfoByid($operatorId,2);
        if (empty($info) || empty($info['tmp_money_limit'])){
            return false;
        }
        
        if ($status == 1){
            $sql = 'UPDATE  carrieroperator SET money_limit = tmp_money_limit,tmp_money_limit = 0 WHERE operator_id = ?';
        }else{
            $sql = 'UPDATE  carrieroperator SET tmp_money_limit = 0 WHERE operator_id = ?';
        }
        return $this->db->query($sql,array($operatorId));
    }
    
    /**
     * 添加更改临时额度日志
     * 
     * @param int $operatorId
     * @param int $moneyLimit
     * @author zengxiong
     * @since  2017年8月10日
     */
    public function addCarrieroperatorMoneyLimitLog($operatorId,$moneyLimit)
    {
        if (empty($operatorId) || empty($moneyLimit)){
            return false;
        }
        return $this->db->batchInsert('carrieroperator_money_limit_log',array('operator_id','status','apply_time','money_limit'),array(array($operatorId,1,time(),$moneyLimit)));
    }
    
    /**
     * 添加更改临时额度日志
     * 
     * @param unknown $operatorId 运营商id
     * @param unknown $status 1：待审核，2：审核通过，3：审核不通过
     * @return boolean|Ambigous <NULL, unknown, int>
     * @author zengxiong
     * @since  2017年8月10日
     */
    public function updateCarrieroperatorMoneyLimitLog($operatorId,$status)
    {
        if (empty($operatorId) || empty($status)){
            return false;
        }

        $sql = 'UPDATE carrieroperator_money_limit_log SET status = ?,edit_time= ?  WHERE operator_id = ? AND status = 1';
        return $this->db->query($sql,array($status,time(),$operatorId));
    }
    
}
