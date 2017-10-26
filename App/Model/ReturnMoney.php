<?php

namespace Model;
use Illuminate\Database\Eloquent\Model;
use Service\DATABASE;
/**

 * ReturnMoney Model

 */

class ReturnMoney extends Model
{
    public $timestamps = false;
    public $primaryKey = 'log_id';
    protected $table = 'store_return_money_log';
    protected $db;

    const RETURN_STATUS_UNPAY = 1; //未还款
    const RETURN_STATUS_PAY = 2; //正常还款成功
    const RETURN_STATUS_OVERDUE_UNPAY = 3; //逾期未还款
    const RETURN_STATUS_REFUND_PAY = 4; //退货

    public function __construct()
    {
        $this->db = new DATABASE();
        $this->db->setDataBase('finance');
    }

    /**
        * 根据日志id获取详细信息
        *
        * @param $logIdArray 日志id数组
        *
        * @return array
     */
    public function getRepaymentInfoByIdArray($logIdArray)
    {
        $inSql = $this->dbCreateIn($logIdArray, 'borrow_money_log_id');
        $sql = "select * from {$this->table} where {$inSql} group by borrow_money_log_id order by log_id asc";
        $data = [];

        $repaymentList = $this->db->query($sql, $data);

        if (empty($repaymentList)) {
            return [];
        } else {
            return $repaymentList;
        }
    }

    /**
        * 添加商家还款log
        *
        * @param $insertData 还款数据二维数组
        *
        * @return int
     */
    public function addStoreReturnMoneyLog($insertData)
    {
        if ( empty($insertData) ) {
            return false;
        }
        $insertParam = [
            'borrow_money_log_id',
            'store_id',
            'repayment_status',
            'period',
            'repayment_time',
            'repayment_capital',
            'repeyment_interest',
            'repayment_penalty',
            'repayment_ahead_fee',
            'repayment_mng_fee',
            'realpay_capital',
            'realpay_interest',
            'realpay_penalty',
            'realpay_ahead_fee',
            'realpay_mng_fee',
            'real_pay_time',
        ];
        $insertRowCount = $this->db->batchInsert($this->table, $insertParam, $insertData);
        return $insertRowCount;
    }

    /**
        * 获取还款列表
        *
        * @param $storeId 商家id
        * @param $beginTime 开始时间
        * @param $endTime 结束时间
        * @param $statusArr 状态数组
        *
        * @return array
     */
    public function getStoreRepaymentList($storeId, $beginTime, $endTime, $statusArr)
    {
        $statusInSql = $this->db->dbCreateIn($statusArr, 'repayment.repayment_status');
        $sql = "select repayment.*,product.* from {$this->table} as repayment " .
            " left join store_borrow_money_log as borrow " .
            " on repayment.borrow_money_log_id=borrow.log_id " .
            " left join product as product " .
            " on borrow.product_no=product.product_no " .
            " where repayment.store_id=? and repayment.repayment_time>=? and repayment.repayment_time<=? and {$statusInSql} order by repayment.repayment_time asc";
        $data = [$storeId, $beginTime, $endTime];

        $repaymentList = $this->db->query($sql, $data);

        if (empty($repaymentList)) {
            return [];
        } else {
            return $repaymentList;
        }
    }

    /**
        * 根据商家id获取待还款金额
        *
        * @param $storeId 商家id
        *
        * @return array
     */
    public function getStoreRepaymentMoney($storeId)
    {
        $sql = "select sum(repayment_capital) as repayment_capital,sum(repeyment_interest) as repeyment_interest, " .
            " sum(repayment_penalty) as repayment_penalty,sum(repayment_ahead_fee) as repayment_ahead_fee, " .
            " sum(repayment_mng_fee) as repayment_mng_fee " .
            " from {$this->table} " .
            " where store_id=? and repayment_status in (?,?) ";
        $data = [$storeId, self::RETURN_STATUS_UNPAY, self::RETURN_STATUS_OVERDUE_UNPAY];

        $repaymentInfo = $this->db->row($sql, $data);

        return $this->dealWithRepaymentInfo($repaymentInfo);
    }

    /**
        * 处理待还金额数据
        *
        * @param $repaymentInfo 待还金额数据
        *
        * @return float
     */
    private function dealWithRepaymentInfo($repaymentInfo)
    {
        $repaymentMoney = 0.00;
        if ( !empty($repaymentInfo) ) {
            $repaymentMoney = $repaymentInfo['repayment_capital'] + $repaymentInfo['repeyment_interest'] + 
                $repaymentInfo['repayment_penalty'] + $repaymentInfo['repayment_ahead_fee'] + $repaymentInfo['repayment_mng_fee'];
        }
        return $repaymentMoney/100;
    }

    /**
        * 根据日志id获取详细信息
        *
        * @param $logId 日志id
        *
        * @return array
     */
    public function getRepaymentInfo($logId)
    {
        $sql = "select * from {$this->table} where log_id=?";
        $data = [$logId];

        $returnDetail = $this->db->row($sql, $data);

        if (empty($returnDetail)) {
            return [];
        } else {
            return $returnDetail;
        }
    }

    /**
        * 根据主键id更新还款时间和状态
        *
        * @param $logId 主键日志id
        * @param $setStatus 设置状态码
        *
        * @return boolean
     */
    public function repaymentSuccess($logId, $setStatus)
    {
        $sql = "update {$this->table} set return_time=?,status=? where log_id=?";
        $data = [time(), $setStatus, $logId];

        $updateRow = $this->db->query($sql, $data);
        return $updateRow>0 ? true : false;
    }

    /**
        * 根据借款id获取还款列表
        *
        * @param $borrowLogId 借款id
        *
        * @return array
     */
    public function getRepaymentList($borrowLogId)
    {
        $sql = "select repayment.*,product.* from {$this->table} as repayment " .
            " left join store_borrow_money_log as borrow " .
            " on repayment.borrow_money_log_id=borrow.log_id " .
            " left join product as product " .
            " on borrow.product_no=product.product_no " .
            " where repayment.borrow_money_log_id=? order by repayment.log_id asc";
        $data = [$borrowLogId];
        $repaymentList = $this->db->query($sql, $data);

        if (empty($repaymentList)) {
            return [];
        } else {
            return $repaymentList;
        }
    }

    /**
     * 根据借款id获取还款列表(单纯还款数据)
     *
     * @param $borrowLogId 借款id
     *
     * @return array
     */
    public function getOnlyRepaymentList($borrowLogId)
    {
        $sql = "select * from {$this->table} where borrow_money_log_id=? order by log_id asc";
        $data = [$borrowLogId];
        $repaymentList = $this->db->query($sql, $data);

        if (empty($repaymentList)) {
            return [];
        } else {
            return $repaymentList;
        }
    }

    /**
     * 根据借款id和期数获取还款详情信息
     *
     * @param $borrowLogId 借款id
     * @param $period 期数
     *
     * @return array
     */
    public function getRepaymentInfoByBorrowIdAndPeriod($borrowLogId, $period)
    {
        $sql = "select repayment.*,product.* from {$this->table} as repayment " .
            " left join store_borrow_money_log as borrow " .
            " on repayment.borrow_money_log_id=borrow.log_id " .
            " left join product as product " .
            " on borrow.product_no=product.product_no " .
            " where repayment.borrow_money_log_id=? and repayment.period=?";
        $data = [$borrowLogId, $period];
        $repaymentInfo = $this->db->row($sql, $data);

        if (empty($repaymentInfo)) {
            return [];
        } else {
            return $repaymentInfo;
        }
    }

    /**
     * 根据主键id数组删除数据返回删除数量
     *
     * @param $idArr 主键id数组
     *
     * @return  int
     */
    public function deleteRepayByIdArr($idArr)
    {
        if ( empty($idArr) ) {
            return 0;
        }
        $deleteInSql = $this->db->dbCreateIn($idArr, 'log_id');
        $sql = "delete from {$this->table} where {$deleteInSql} order by log_id asc";
        $data = [];
        $deleteCount = $this->db->query($sql, $data);

        if (empty($deleteCount)) {
            return 0;
        } else {
            return $deleteCount;
        }
    }
    
    /**
     * 根据借款id获取账单状态
     * 
     * @author zengxiong
     * @since  2017年8月9日
     */
    public function getBillStatusByMoneyLogIds(array $ids)
    {
        if (empty($ids)){
            return false;
        }
        $sql  = 'SELECT borrow_money_log_id,status FROM store_return_money_log WHERE borrow_money_log_id '.$this->dbCreateIn($ids);
        $list = $this->db->query($sql);
        $list = empty($list) ? [] : $list;
        $tmpData = [];
        foreach ($list as $row){
            $tmpData[$row['borrow_money_log_id']]['status'][] = $row['status'];
        }
        foreach ($tmpData as $logId => $data){
            if (in_array(2, $data['status'])){
                $tmpData[$logId]['desc_status'] = '2';
                $tmpData[$logId]['desc'] = '已逾期';
            }elseif (in_array(1, $data['status'])){
                $tmpData[$logId]['desc_status'] = '1';
                $tmpData[$logId]['desc'] = '还款中';
            }else{
                $tmpData[$logId]['desc_status'] = '3';
                $tmpData[$logId]['desc'] = '已还清';
            }
            unset($tmpData[$logId]['status']);
        }
        return $tmpData;
    }

    /**
     * 根据过期时间戳返回过期天数
     *
     * @param $deadLineTime 过期时间戳
     *
     * @return int
     */
    public function dealWithOverdueDays($deadLineTime)
    {
        $delayTime = strtotime(date("Y-m-d 00:00:00"));
        $diffTimes = abs($deadLineTime-$delayTime);
        return intval($diffTimes/86400);
    }

    /**
     * 根据店铺id判断是否存在借款逾期
     *
     * @param $storeId 店铺id
     *
     * @return boolean
     */
    public function isOverdue($storeId)
    {
        if (empty($storeId)) {
            return false;
        }

        $status = ReturnMoney::RETURN_STATUS_OVERDUE_UNPAY;

        $sql = "select count(rm.log_id) as count from {$this->table} rm 
                where rm.store_id = {$storeId} and rm.repayment_status = {$status}";
        $data = $this->db->row($sql);

        if ($data['count']>0) {
            return true;

        } else {
            return false;
        }
    }

    /**
        * 根据借款log的主键获取还未还款数量
        *
        * @param $borrowLogId 借款log主键id
        *
        * @return int
     */
    public function getNotRepaymentCountByBorrowLogId($borrowLogId)
    {
        $sql = "select count(*) as count from {$this->table} " . 
            " where borrow_money_log_id=? and status not in (?,?)";
        $searchData = [$borrowLogId, RETURN_STATUS_PAID, RETURN_STATUS_OVERDUE_PAID];
        $notRepaymentCount = $this->db->row($sql, $searchData);
        return $notRepaymentCount==false ? 0 : $notRepaymentCount;
    }

    /**
        * 根据年月获取当月开始和结束时间戳
        *
        * @param $theYear 年份
        * @param $theMonth 月份
        *
        * @return array
     */
    public function getTheMonthTime($theYear, $theMonth)
    {
        $theMonth = intval($theMonth);
        if ( $theMonth>=12 ) {
            $nextYear = $theYear + 1;
            $nextMonth = ($theMonth+1)%12;
        } else {
            $nextYear = $theYear;
            $nextMonth = ($theMonth+1);
        }
        $theMonthBeginTime = strtotime(date($theYear . "-" . $theMonth . "-01 00:00:00"));
        $theMonthEndTime = strtotime(date($nextYear . "-" . $nextMonth . "-01 00:00:00")) - 1;

        return [
            'begin_time' => $theMonthBeginTime,
            'end_time' => $theMonthEndTime
        ];
    }
}
