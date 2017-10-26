<?php

namespace Model;
use Illuminate\Database\Eloquent\Model;
use Service\DATABASE;

/**
 * ClientSession Model 商家身份认证信息模块
 */
class ClientSession extends Model
{

    public $timestamps = false;
    protected $table = 'life_client_session';
    protected $db;

    public function __construct()
    {
        $this->db = new DATABASE();
        $this->db->setDataBase('shenghuoquan');
    }

    /**
     * 根据mAuth获取用户id
     *
     * @param string $mAuth 用户验证信息
     *
     * @return int
     */
    public function getUidByMauth($mAuth)
    {
        $uid = $this->db->single('select uid from ' . $this->table . ' where m_auth = ?', [$mAuth]);
        return empty(intval($uid)) ? 0 : intval($uid);
    }
}
