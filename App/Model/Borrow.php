<?php

namespace Model;
use Illuminate\Database\Eloquent\Model;
use Service\DATABASE;
/**

 * Borrow Model

 */

class Borrow extends Model
{
    public $primaryKey = 'log_id';
    protected $table = 'store_borrow_money_log';
    protected $_model;
    protected $db;
    /**
     * 申请成功状态
     */
    const APPLY_SUCCESS = 1;

    public function __construct()
    {
        $this->db = new DATABASE();
        $this->db->setDataBase('finance');
    }

    /**
        * 添加商家借贷申请记录表（返回自增id）
        *
        * @param $storeId 商家id
        * @param $storeName 商家名称
        * @param $storeRealName 店主名称
        * @param $eicsSn 发起本交易的报文流水号
        * @param $financeUserId U融汇账号
        * @param $productNo 产品标识号
        * @param $loanId 融资的唯一标识
        * @param $applyAmount 借款金额，单位：分
        * @param $loanRate 借款利率
        * @param $mngRate 资金管理费率
        * @param $loanType 借款类型 03：等额本息，按月分期 05：等额本息，按日分期
        * @param $cloType 到账类型 01:U融汇账号；(默认) 02:银行卡
        * @param $loanPeriod 借款期限
        * @param $gracePeriod 宽限期 单位：天
        * @param $startDate 借款开始计算利息时间
        * @param $applyTime 申请时间
        * @param $auditingTime 审核通过时间 
        *
        * @return int
     */
    public function addStoreBorrowMoneyLog($storeId, $storeName, $storeRealName, $eicsSn, $financeUserId, $productNo, $loanId, 
        $applyAmount, $loanRate, $mngRate, $loanType, $cloType, $loanPeriod, $gracePeriod, $startDate, $applyTime, $auditingTime=0)
    {
        $nowTime = time();
        $insertData = [
            [
                $storeId,
                $storeName,
                $storeRealName,
                $eicsSn,
                $financeUserId,
                $productNo,
                $loanId,
                self::APPLY_SUCCESS,
                $applyAmount,
                $loanRate,
                $mngRate,
                $loanType,
                $cloType,
                $loanPeriod,
                $gracePeriod,
                $startDate,
                $applyTime,
                $auditingTime
            ],
        ];

        $insertParam = [
            'store_id',
            'store_name',
            'store_real_name',
            'eics_sn',
            'finance_user_id',
            'product_no',
            'loan_id',
            'status',
            'apply_amt',
            'loan_rate',
            'mng_rate',
            'loan_type',
            'col_type',
            'loan_period',
            'grace_period',
            'start_date',
            'apply_time',
            'auditing_time',
        ];
        $inserRowsCount = $this->db->batchInsert($this->table, $insertParam, $insertData);
        return $inserRowsCount>0 ? $this->db->lastInsertId() : 0;
    }

    /**
        * 添加商家借贷申请记录表
        *
        * @param $logId 日志主键id
        * @param $storeId 商家id
        * @param $storeName 商家名称
        * @param $storeRealName 店主名称
        * @param $eicsSn 发起本交易的报文流水号
        * @param $financeUserId U融汇账号
        * @param $productNo 产品标识号
        * @param $loanId 融资的唯一标识
        * @param $applyAmount 借款金额，单位：分
        * @param $loanRate 借款利率
        * @param $mngRate 资金管理费率
        * @param $loanType 借款类型 03：等额本息，按月分期 05：等额本息，按日分期
        * @param $cloType 到账类型 01:U融汇账号；(默认) 02:银行卡
        * @param $loanPeriod 借款期限
        * @param $gracePeriod 宽限期 单位：天
        * @param $startDate 借款开始计算利息时间
        * @param $applyTime 申请时间
        * @param $setStatus 设置状态码
        * @param $auditingTime 审核通过时间 
        *
        * @return int
     */
    public function updateStoreBorrowMoneyLog($logId, $storeId, $storeName, $storeRealName, $eicsSn, $financeUserId, $productNo, $loanId, 
        $applyAmount, $loanRate, $mngRate, $loanType, $cloType, $loanPeriod, $gracePeriod, $startDate, $applyTime, $setStatus, $auditingTime=0)
    {
        $updateSql = "update " . $this->table .
            " set store_id=?,store_name=?,store_real_name=?,eics_sn=?,finance_user_id=?,product_no=?,loan_id=?, " .
            " status=?,apply_amt=?,loan_rate=?,mng_rate=?,loan_type=?,col_type=?,loan_period=?, " .
            " grace_period=?,start_date=?,apply_time=?,auditing_time=? " . 
            " where log_id=?";
        $updateData = [
            $storeId,
            $storeName,
            $storeRealName,
            $eicsSn,
            $financeUserId,
            $productNo,
            $loanId,
            empty($setStatus) ? self::APPLY_SUCCESS : $setStatus,
            $applyAmount,
            $loanRate,
            $mngRate,
            $loanType,
            $cloType,
            $loanPeriod,
            $gracePeriod,
            $startDate,
            $applyTime,
            $auditingTime,
            $logId
        ];

        $updateStatus = $this->db->query($updateSql, $updateData);
        return $updateStatus;
    }

    /**
        * 根据商家id获取贷款列表(不分页)
        *
        * @param $storeId 商家id
        *
        * @return array
     */
    public function getBorrowList($storeId)
    {
        $sql = "select borrow.*,product.* from {$this->table} as borrow " . 
            " left join product as product on borrow.product_no=product.product_no " .
            " where borrow.store_id=? order by borrow.log_id desc";
        $data = [$storeId];

        $listData = $this->db->query($sql, $data);

        if (empty($listData)) {
            return [];
        } else {
            return $listData;
        }
    }

    /**
        * 根据商家id查询是否过期
        *
        * @param $storeId 商家id
        *
        * @return int
     */
    public function checkIsOverdue($storeId)
    {
        $sql = "select count(*) from {$this->table} where store_id=? and status=6 ";
        $data = [$storeId];

        $isOverdueNum = $this->db->single($sql, $data);
        if (empty($isOverdueNum)) {
            return 0;
        } else {
            return $isOverdueNum;
        }
    }

    /**
        * 根据日志id获取详细信息
        *
        * @param $logId 日志id
        *
        * @return array
     */
    public function getReturnDetail($logId)
    {
        $sql = "select borrow.*,product.* from {$this->table} as borrow " .
            " left join `product` as product " . 
            " on borrow.product_no=product.product_no where log_id=?";
        $data = [$logId];

        $returnDetail = $this->db->row($sql, $data);

        if (empty($returnDetail)) {
            return [];
        } else {
            return $returnDetail;
        }
    }

    /**
        * 根据主键id更新还款成功状态
        *
        * @param $logId 主键日志id
        *
        * @return boolean
     */
    public function repaymentAllMoneySuccess($logId)
    {
        $sql = "update {$this->table} set status=? where log_id=?";
        $data = [self::REPAYMENT_SUCCESS, $logId];

        $updateRow = $this->db->query($sql, $data);
        return $updateRow>0 ? true : false;
    }

    /**
        * 根据日志id获取详细信息
        *
        * @param $logId 日志id
        *
        * @return array
     */
    public function getBorrowDetail($logId)
    {
        $sql = "select * from {$this->table} where log_id=?";
        $data = [$logId];

        $borrowDetail = $this->db->row($sql, $data);

        if (empty($borrowDetail)) {
            return [];
        } else {
            return $borrowDetail;
        }
    }

    /**
        * 根据融资协议号获取详细信息
        *
        * @param $loanId 融资协议号
        *
        * @return array
     */
    public function getBorrowDetailByLoanId($loanId)
    {
        $sql = "select * from {$this->table} where loan_id=?";
        $data = [$loanId];

        $borrowDetail = $this->db->row($sql, $data);
        if (empty($borrowDetail)) {
            return [];
        } else {
            return $borrowDetail;
        }
    }
}
