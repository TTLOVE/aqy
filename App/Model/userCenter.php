<?php

namespace Model;
use Illuminate\Database\Eloquent\Model;
use Service\DATABASE;

/**
 * 链接userCenter读取数据
 * 
 * @author    zengxiong
 * @since     2017年8月8日
 * @version   1.0
 */
class userCenter extends Model
{

    public $timestamps = false;
    protected $table = 'lqc_users';
    protected $db;

    public function __construct()
    {
        $this->db = new DATABASE();
        $this->db->setDataBase('userCenter');
    }

    /**
     * 根据id获取商家信息
     * 
     * @param int $userId 用户id
     * @param string $fields 读取字段
     * @author zengxiong
     * @since  2017年8月8日
     */
    public function getUserInfoById($userId, $fields='userid,mobile')
    {
        if (empty($userId) || empty($fields)){
            return false;
        }
        $sql = 'SELECT '.$fields.' FROM lqc_users WHERE userid = ?';
        return $this->db->row($sql,array($userId));
    }
}
