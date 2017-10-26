<?php

namespace Model;
use Illuminate\Database\Eloquent\Model;
use Service\DATABASE;

/**
 * StoreInfo Model 商家店员信息
 */
class StoreInfo extends Model
{

    public $timestamps = false;
    protected $table = 'ecm_store';
    protected $db;

    public function __construct()
    {
        $this->db = new DATABASE();
        $this->db->setDataBase('shenghuoquan');
    }

    /**
        * 根据店员id获取商家信息
        *
        * @param int $memberId 店员id
        * @param string $fields 查询字段
     *
        * @return array
     */
    public function getStoreInfoById($storeId, $fields='')
    {
        if (empty($fields)) {
            $fields = '*';
        }
        $storeInfo = $this->db->row("select {$fields} from " . $this->table . ' where store_id = ?', [$storeId]);
        return ($storeInfo===false) ? [] : $storeInfo; 
    }

    /**
        * 更新商家提现为自动
        *
        * @param $storeId 商家id
        *
        * @return int
     */
    public function updateStoreManualWithdrawToAuto($storeId)
    {
        $updateRows = $this->db->query("update " . $this->table . ' set manual_withdraw=? where store_id = ?', [1, $storeId]);
        return intval($updateRows); 
    }
}
