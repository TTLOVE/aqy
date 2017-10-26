<?php

namespace Model;
use Illuminate\Database\Eloquent\Model;
use Service\DATABASE;

/**

 * Admin Model

 */

class Admin extends Model
{

    public $timestamps = false;
    protected $table = 'admin';
    protected $_model;
    protected $db;

    public function __construct()
    {
        $this->db = new DATABASE();
        $this->db->setDataBase('finance');
    }

    /**
     * 根据用户名密码获取管理员信息
     *
     * @param $username
     * @param $password
     *
     * @return array|false
     */
    public function checkLogin($username, $password)
    {

        $fields = 'id,username,password,salt,last_login_time';
        $arr_fields = explode(',', $fields);

        $where  = [
            'username' => $username
        ];

        $adminInfo = Admin::where($where)->select($arr_fields)->first();

        if(empty($adminInfo)) {
            return false;
        } else {
            $adminInfo = $adminInfo->toArray();
        }
        if($adminInfo['password'] === $this->encryptPassword($password, $adminInfo['salt'])) {
            unset($adminInfo['password']);
            unset($adminInfo['salt']);

            return $adminInfo;
        } else {

            return false;
        }
    }

    /**
     * 生成盐值串
     *
     * @param $length
     *
     * @return string
     */
    private function generateSalt($length = 4)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_[]{}<>~`+=,.;:/?|';
        $randMax = strlen($chars) - 1;
        $salt = '';
        for ($i = 0; $i < $length; $i++) {
            $salt .= $chars[ mt_rand(0, $randMax) ];
        }

        return $salt;
    }

    /**
     * 密码加盐加密
     *
     * @param $pass
     * @param $salt
     *
     * @return string
     */
    private function encryptPassword($pass, $salt)
    {
        return md5(md5($pass . $salt) . md5($salt));
    }

}
