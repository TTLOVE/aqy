<?php

namespace Model;
use Illuminate\Database\Eloquent\Model;
use Service\DATABASE;

/**
 * AppUser Model 商家用户模块
 */
class AppUser extends Model
{

    public $timestamps = false;
    protected $table = 'mall_app_users';
    protected $db;

    public function __construct()
    {
        $this->db = new DATABASE();
        $this->db->setDataBase('shenghuoquan');
    }

    /**
        * 根据用户id获取ａｐｐ用户信息
        *
        * @param int $uid 用户id
        *
        * @return array
     */
    public function getUserInfoById($uid)
    {
        $userInfo = $this->db->row('select * from ' . $this->table . ' where userid = ?', [$uid]);
        return ($userInfo==false) ? [] : $userInfo;
    }
}
