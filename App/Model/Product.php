<?php

namespace Model;
use Illuminate\Database\Eloquent\Model;
use Service\DATABASE;
/**

 * Product Model

 */

class Product extends Model
{
    public $timestamps = false;
    public $primaryKey = 'product_no';
    protected $table = 'product';
    protected $_model;
    protected $db;

    public function __construct()
    {
        $this->db = new DATABASE();
        $this->db->setDataBase('finance');
    }

    /**
     * 获取产品列表
     *
     * @param $idArr 对应产品id数组
     *
     * @return array
     */
    public function getProductListByIdArr($idArr)
    {
        $inIdSql = $this->db->dbCreateIn($idArr, 'product_no');

        $sql = "select * from {$this->table} where {$inIdSql} ";
        $list = $this->db->query($sql);
        if (empty($list)) {
            return [];
        } else {
            return $list;
        }

    }

    /**
     * 添加产品
     *
     * @param $product
     *
     * @return int|false
     */
    public function addProduct($insertData)
    {
        if ( empty($insertData) ) {
            return false;
        }
        $insertParam = [
            'product_no',
            'product_name',
            'prod_min_amt',
            'prod_max_amt',
            'conf_re_method',
            'conf_min_period',
            'conf_max_period',
            'conf_rate',
            'conf_late_rate',
            'conf_mng_rate',
            'conf_pre_rate',
        ];
        $inserStatus = $this->db->batchInsert($this->table, $insertParam, $insertData);
        return $inserStatus;
    }

    /**
     * 根据id获取产品信息
     *
     * @param $productNo 产品编号
     *
     * @return array
     */
    public function getDetailOfProduct($productNo)
    {
        $where  = [
            'product_no' => $productNo
        ];

        $productModel = Product::where($where)->first();

        if(empty($productModel)) {
            return [];

        } else {
            return $productModel->toArray();
        }
    }

    /**
        * 根据产品id数组获取产品信息
        *
        * @param $idArr 产品数组id
        * @param $fields 读取产品的内容
        *
        * @return array
     */
    public function getDetailOfProductByIdArr($idArr, $fields='*')
    {
        $arrFields = explode(',', $fields);

        $productList = Product::whereIn('id', $idArr)->select($arrFields)->get();

        if(empty($productList)) {
            return [];

        } else {
            return $productList->toArray();
        }
    }

    /**
     * 根据id和操作类型更新产品状态
     *
     * @param $id
     * @param $type
     *
     * @return int|false
     */
    public function updateProductStatus($id, $type)
    {
        if ( empty($id) || empty($type) ) {
            return false;
        }
        //启用操作
        if ( $type==1 ) {
            $status = Product::PRODUCT_STATUS_ON;
        }
        //禁用操作
        if ( $type==2 ) {
            $status = Product::PRODUCT_STATUS_OFF;
        }
        //删除操作
        if ( $type==3 ) {
            $status = Product::PRODUCT_STATUS_DELETE;
        }

        $affectedRows = Product::where('id', '=', $id)->update(['status' => $status]);
        return $affectedRows;
    }
    
    /**
     * 获取运营商的高利贷产品数
     * 
     * @param array $operatorIds 运营商id
     * @param number $status 状态
     * @author zengxiong
     * @since  2017年8月4日
     */
    public function getOperatorProductCount(Array $operatorIds,$status = 2)
    {
        if (empty($operatorIds)){
            return false;
        }
        $sql = 'SELECT operator_id,COUNT(operator_id) as `count` FROM carrieroperator_usury WHERE operator_id '.$this->dbCreateIn($operatorIds).' AND status = ? GROUP BY operator_id';
        return $this->db->query($sql,array($status),'operator_id');
    }

}
