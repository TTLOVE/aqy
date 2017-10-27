<?php

namespace Model;
use Illuminate\Database\Eloquent\Model;
use Service\DATABASE;

/**

 * UserPoster Model

 */

class UserPoster extends Model
{
    public $primaryKey = 'uid';
    protected $table = 'user_poster';
    protected $_model;
    protected $db;

    public function __construct()
    {
        $this->db = new DATABASE();
        $this->db->setDataBase('pic');
    }

    /**
     * 添加用户海报记录信息
     *
     * @param $uid 用户id
     *
     * @return int
     */
    public function addUserPoster($uid)
    {
        $nowTime = time();
        $insertData = [
            [
                $uid,
                $nowTime
            ],
        ];

        $insertParam = [
            'uid',
            'add_time'
        ];
        $inserRowsCount = $this->db->batchInsert($this->table, $insertParam, $insertData);
        return $inserRowsCount>0 ? $this->db->lastInsertId() : 0;
    }

    /**
     * 根据用户id获取用户信息
     *
     * @param $id 主键id
     *
     * @return array
     */
    public function getUserPosterInfoById($id)
    {
        $sql = "select * from {$this->table} where id=?";
        $data = [$id];

        $userPosterInfo = $this->db->row($sql, $data);

        if (empty($userPosterInfo)) {
            return [];
        } else {
            return $userPosterInfo;
        }
    }
}
