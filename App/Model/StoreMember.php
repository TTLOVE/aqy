<?php

namespace Model;
use Illuminate\Database\Eloquent\Model;
use Service\DATABASE;

/**
 * StoreMember Model 商家店员信息
 */
class StoreMember extends Model
{

    public $timestamps = false;
    protected $table = 'ecm_store_member';
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
        *
        * @return array
     */
    public function getMemberInfoByMemberId($memberId)
    {
        $storeMemberInfo = $this->db->row('select * from ' . $this->table . ' where member_id = ?', [$memberId]);
        return ($storeMemberInfo===false) ? [] : $storeMemberInfo; 
    }
}
