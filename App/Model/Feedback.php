<?php

namespace Model;
use Illuminate\Database\Eloquent\Model;
use Service\DATABASE;
/**

 * Feedback Model

 */

class Feedback extends Model
{
    protected $table = 'feedback';

    public function __construct()
    {
        $this->db = new DATABASE();
        $this->db->setDataBase('finance');
    }

    public function add($comment,$storeID)
    {
        $sql = "insert into {$this->table}(comment_id,comment,add_time) value(?,?,?)";
        $para = [$storeID, $comment, time()];

        $data = $this->db->query($sql, $para);

        if (empty($data)) {
            return false;
        } else {
            return true;
        }
    }

}
