<?php

namespace Model;
use Illuminate\Database\Eloquent\Model;
use Service\DATABASE;

/**

 * PosterLog Model

 */

class PosterLog extends Model
{
    public $primaryKey = 'id';
    protected $table = 'poster_log';
    protected $_model;
    protected $db;

    public function __construct()
    {
        $this->db = new DATABASE();
        $this->db->setDataBase('pic');
    }

    /**
     * 添加海报详细信息
     *
     * @param $insertData 二维数组插入信息
     *
     * @return int
     */
    public function addPosterLog($insertData)
    {
        $nowTime = time();

        $insertParam = [
            'poster_id',
            'template_id',
            'words',
            'pic_url'
        ];
        $inserRowsCount = $this->db->batchInsert($this->table, $insertParam, $insertData);
        return $inserRowsCount>0 ? $inserRowsCount : 0;
    }

    /**
     * 根据user_poster主键id获取对应海报列表信息
     *
     * @param $posrtId user_poster主键id
     *
     * @return array
     */
    public function getPosterLogListByPosterId($posterId)
    {
        $sql = "select * from {$this->table} where poster_id=? order by id asc";
        $data = [$posterId];

        $listData = $this->db->query($sql, $data);

        if (empty($listData)) {
            return [];
        } else {
            return $listData;
        }
    }
}
