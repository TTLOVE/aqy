<?php

namespace Model;
use Illuminate\Database\Eloquent\Model;
use Service\DATABASE;

/**

 * User Model

 */

class User extends Model
{
    public $primaryKey = 'uid';
    protected $table = 'user';
    protected $_model;
    protected $db;

    public function __construct()
    {
        $this->db = new DATABASE();
        $this->db->setDataBase('pic');
    }

    /**
     * 添加用户信息
     *
     * @param $openid 用户openid
     * @param $nickname 用户昵称
     * @param $logo 用户头像
     * @param $sex 用户性别(值为1时是男性，值为2时是女性，值为0时是未知)
     *
     * @return int
     */
    public function addUser($openid, $nickname, $logo, $sex)
    {
        $nowTime = time();
        $insertData = [
            [
                $openid,
                $nickname,
                $logo,
                $sex,
                $nowTime
            ],
        ];

        $insertParam = [
            'openid',
            'nickname',
            'logo',
            'sex',
            'add_time'
        ];
        $inserRowsCount = $this->db->batchInsert($this->table, $insertParam, $insertData);
        return $inserRowsCount>0 ? $this->db->lastInsertId() : 0;
    }

    /**
     * 根据用户id获取用户信息
     *
     * @param $uid 用户id
     *
     * @return array
     */
    public function getUserInfoByUid($uid)
    {
        $sql = "select * from {$this->table} where uid=?";
        $data = [$uid];

        $userInfo = $this->db->row($sql, $data);

        if (empty($userInfo)) {
            return [];
        } else {
            return $userInfo;
        }
    }

    /**
     * 根据用户openid获取用户信息
     *
     * @param $openid 用户openid
     *
     * @return array
     */
    public function getUserInfoByOpenid($openid)
    {
        $sql = "select * from {$this->table} where openid=?";
        $data = [$openid];

        $userInfo = $this->db->row($sql, $data);

        if (empty($userInfo)) {
            return [];
        } else {
            return $userInfo;
        }
    }
}
