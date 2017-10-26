<?php

namespace Model;
use Illuminate\Database\Eloquent\Model;
use Service\DATABASE;

/**
 * Application Model 商家身份认证信息模块
 */
class Application extends Model
{

    public $timestamps = false;
    protected $table = 'kl_applications';
    protected $db;

    public function __construct()
    {
        $this->db = new DATABASE();
        $this->db->setDataBase('shenghuoquan');
    }

    /**
     * 根据appId获取服务商信息
     *
     * @param int $appId 服务商id
     *
     * @return array
     */
    public function getApplicationInfoBuId($appId)
    {
        $applicationInfo = $this->db->row('select * from ' . $this->table . ' where app_id = ?', [$appId]);
        return empty($applicationInfo) ? [] : $applicationInfo;
    }
}
