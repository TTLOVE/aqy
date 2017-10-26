<?php

namespace Model;
use \Curl\Curl;
use Leaf\Loger\LogDriver;

/**
 * 钱包模块
 */
class KoalaPay
{
    private $client_id = '';
    private $app_id = '';
    private $app_key = '';
    private $base_url = '';

    public function __construct($config = array())
    {
        $this->client_id = !isset($config['client_id']) ? KL_PAY['mall_client_id'] : $config['client_id'];
        $this->app_id = !isset($config['app_id']) ? KL_PAY['app_id'] : $config['app_id'];
        $this->app_key = !isset($config['app_key']) ? KL_PAY['app_key'] : $config['app_key'];
        $this->base_url = !isset($config['base_url']) ? KL_PAY['url'] : $config['base_url'];
    }

    /**
        * 根据用户id，渠道id和成功返回页面获取请求富友添加用户页面信息
        *
        * @param $userId 用户id
        * @param $channelId 渠道id
        * @param $successUrl 申请成功返回页面
        *
        * @return array
     */
    public function goToFuyouAddAccountRequest($userId, $channelId, $successUrl)
    {
        // 参与签名对应的参数
        $signArray = array(
            'client_id' => $this->client_id,
            'app_id'    => $this->app_id,
            'round_str' => $this->createRoundStr(),
            'store_id'   => $userId,
            'source_client_id'   => $channelId,
            'page_notify_url'   => $successUrl,
        );

        // 构造其他参数
        $otherData = array(
            'token'   => $this->createToken($signArray['client_id'], $signArray['app_id'], $signArray['round_str'], $this->app_key),
            'sign'    => $this->createSign($signArray, $this->app_key)
        );

        // 生成要传输的参数
        $allData = array_merge($signArray, $otherData);
        $sendUrl = $this->base_url . 'klpay/api/v2/fuyou/baoli/newAccount/requestInfo.do';
        $postReturn = $this->postDataToKoalaPay($allData, $sendUrl, 3);

        if ( $postReturn['my_status'] == 1 ) {
            $returnSignArr = [
                'client_id'   => $postReturn['client_id'],
                'app_id'      => $postReturn['app_id'],
                'round_str'   => $postReturn['round_str'],
                'result_code' => $postReturn['result_code']
            ];

            $mySign = $this->createSign($returnSignArr, $this->app_key);

            if ( $postReturn['sign'] == $mySign ) {
                $returnData['status'] = 1;
                $returnData['msg'] = '获取富友信息成功';
                $returnData['info'] = $postReturn['result_obj'];
            } else {
                $returnData['status'] = -4;
                $returnData['msg'] = '签名验证失败';
            }
        } else {
            $returnData['status'] = $postReturn['my_status'];
            $returnData['msg'] = isset($postReturn['result_msg']) ? $postReturn['result_msg'] : '';

            if ( isset($postReturn['result_code']) ) {
                $returnData['result_code'] = $postReturn['result_code'];
            }
        }

        return $returnData;
    }

    /**
        * 根据用户id获取是否开启富友用户
        *
        * @param $userId 用户id
        * @param $channelId 渠道id
        *
        * @return array
     */
    public function getFuyouAccountInfo($userId, $channelId)
    {
        // 参与签名对应的参数
        $signArray = array(
            'client_id' => $this->client_id,
            'app_id'    => $this->app_id,
            'round_str' => $this->createRoundStr(),
            'store_id'   => $userId,
            'source_client_id'   => $channelId,
        );

        // 构造其他参数
        $otherData = array(
            'token'   => $this->createToken($signArray['client_id'], $signArray['app_id'], $signArray['round_str'], $this->app_key),
            'sign'    => $this->createSign($signArray, $this->app_key)
        );

        // 生成要传输的参数
        $allData = array_merge($signArray, $otherData);
        $sendUrl = $this->base_url . 'klpay/api/v2/fuyou/account.do';
        $postReturn = $this->postDataToKoalaPay($allData, $sendUrl, 3);

        if ( $postReturn['my_status'] == 1 ) {
            $returnSignArr = [
                'client_id'   => $postReturn['client_id'],
                'app_id'      => $postReturn['app_id'],
                'round_str'   => $postReturn['round_str'],
                'result_code' => $postReturn['result_code']
            ];

            $mySign = $this->createSign($returnSignArr, $this->app_key);

            if ( $postReturn['sign'] == $mySign ) {
                $returnData['status'] = 1;
                $returnData['msg'] = '获取富友信息成功';
                $returnData['info'] = $postReturn['result_obj'];
            } else {
                $returnData['status'] = -4;
                $returnData['msg'] = '签名验证失败';
            }
        } else {
            $returnData['status'] = $postReturn['my_status'];
            $returnData['msg'] = isset($postReturn['result_msg']) ? $postReturn['result_msg'] : '';

            if ( isset($postReturn['result_code']) ) {
                $returnData['result_code'] = $postReturn['result_code'];
            }
        }

        return $returnData;
    }

    /**
     * 根据用户id获取用户可借贷产品列表
     *
     * @param int    $userId  用户id
     *
     * @return array
     */
    public function getUserProductList($userId)
    {
        // 参与签名对应的参数
        $signArray = array(
            'client_id' => $this->client_id,
            'app_id'    => $this->app_id,
            'round_str' => $this->createRoundStr(),
            'pay_userid'   => $userId,
        );

        // 构造其他参数
        $otherData = array(
            'token'   => $this->createToken($signArray['client_id'], $signArray['app_id'], $signArray['round_str'], $this->app_key),
            'sign'    => $this->createSign($signArray, $this->app_key)
        );

        // 生成要传输的参数
        $allData = array_merge($signArray, $otherData);
        $sendUrl = $this->base_url . 'klpay/api/v2/baoli/cridit/product.do';
        $postReturn = $this->postDataToKoalaPay($allData, $sendUrl, 3);

        if ( $postReturn['my_status'] == 1 ) {
            $returnSignArr = [
                'client_id'   => $postReturn['client_id'],
                'app_id'      => $postReturn['app_id'],
                'round_str'   => $postReturn['round_str'],
                'result_code' => $postReturn['result_code']
            ];

            $mySign = $this->createSign($returnSignArr, $this->app_key);

            if ( $postReturn['sign'] == $mySign ) {
                $returnData['status'] = 1;
                $returnData['msg'] = '获取列表成功';
                $returnData['list'] = $this->dealWithProdoctList($postReturn['result_obj']);
            } else {
                $returnData['status'] = -4;
                $returnData['msg'] = '签名验证失败';
            }
        } else {
            $returnData['status'] = $postReturn['my_status'];
            $returnData['msg'] = isset($postReturn['result_msg']) ? $postReturn['result_msg'] : '';

            if ( isset($postReturn['result_code']) ) {
                $returnData['result_code'] = $postReturn['result_code'];
            }
        }

        return $returnData;
    }

    /**
     * 处理返回的数据
     *
     * @param array $productList 产品列表
     *
     * @return array
     */
    private function dealWithProdoctList($productList)
    {
        $lmtTotal = 0;
        $lmtAble = 0;
        if ( is_array($productList) && count($productList)>0 ) {
            if ( isset($productList['prod']) ) {
                $pList = $productList['prod'];
                unset($productList['prod']);
                $productList[] = $pList;
            }
            foreach ($productList as $key => $product) {
                $lmtTotal += $product['lmtTotal'];
                $lmtAble += $product['lmtAble'];
                if ( isset($product['loanTypes']['loan']) ) {
                    $loanTypes = $product['loanTypes']['loan'];
                } else {
                    $loanTypes = isset($product['loanTypes'][0]) ? $product['loanTypes'][0] : [];
                }
                unset($productList[ $key ]['loanTypes']);
                $productList[ $key ]['loanTypes'][] = $loanTypes;
            }
        } else {
            $productList = [];
        }
        $list = [
            'productList' => $productList,
            'lmtTotal' => $lmtTotal/100,
            'lmtAble' => $lmtAble/100
        ];
        return $list;
    }

    /**
     * 获取用户余额
     *
     * @return array
     */
    public function getUserMoney($userId)
    {
		// 参与签名对应的参数
		$signArray = array(
			'client_id' => $this->client_id,
            'app_id' => $this->app_id,
			'round_str' => $this->createRoundStr(),
			'user_id' => $userId,
		);

		// 构造其他参数
		$token = $this->createToken($signArray['client_id'], $signArray['app_id'], $signArray['round_str'], $this->app_key);

		$otherData = array(
			'token' => $token,
			'sign'  => $this->createSign($signArray, $this->app_key)
		);

		// 生成要传输的参数
		$allData = array_merge($signArray, $otherData);

		$sendUrl = $this->base_url . 'klpay/api/v2/fuyou/balance.do';
		$postReturn = $this->postDataToKoalaPay($allData, $sendUrl);

		$returnData = [
			'status' => 0,
			'msg'    => '获取失败'
		];

		if ( $postReturn['my_status'] == 1 ) {
			$returnSignArr = [
				'client_id'   => $postReturn['client_id'],
				'app_id'      => $postReturn['app_id'],
				'round_str'   => $postReturn['round_str'],
                'result_code'   => $postReturn['result_code']
			];
			$mySign = $this->createSign($returnSignArr, $this->app_key);
			if ( $postReturn['sign'] == $mySign ) {
				$returnData['status'] = 1;
                $returnData['data'] = [
                    'settle_balance'   => number_format(($postReturn['settle_balance']/100), 2, ".", ""),
                    'not_settle_balance'   => number_format(($postReturn['not_settle_balance']/100), 2, ".", ""),
                    'withdraw' => number_format(($postReturn['withdraw']/100), 2, ".", ""),
                    'withdraw_fee' => number_format(($postReturn['withdraw_fee']/100), 2, ".", ""),
                    'book_balance' => number_format(($postReturn['book_balance']/100), 2, ".", ""),
                ];
				$returnData['msg'] = '获取成功';
			} else {
				$returnData['status'] = -4;
			}
		} else {
			$returnData['status'] = $postReturn['my_status'];
            $returnData['msg'] = isset($postReturn['result_msg']) ? $postReturn['result_msg'] : '';

			if ( isset($postReturn['result_code']) ) {
				$returnData['result_code'] = $postReturn['result_code'];
			}
		}
		return $returnData;
    }

	/**
	 * 借款
	 * 
	 * @param array $param 参数
	 * @param number $type 0模拟,1真实
	 * @return boolean[]|string[]|unknown|mixed
	 * @author zengxiong
	 * @since  2017年9月26日
	 */
	public function doBorrowMoney(array $param, $type = 0)
	{
        if (empty($param['pay_userid']) || empty($param['product_no']) || empty($param['apply_amt']) || empty($param['loan_type']) ||
             empty($param['col_type']) || empty($param['mng_rate']) || empty($param['loan_period']) || empty($param['loan_rate'])) {
                 return ['status' => false,'msg' => '参数有误!'];
        }
        
	    // 参与签名对应的参数
	    $signArray = array(
	        'client_id'        => $this->client_id,
	        'app_id'           => $this->app_id,
	        'round_str'        => $this->createRoundStr(),
	        'pay_userid'       => $param['pay_userid'],//店铺ID
	        'page_notify_url'  => isset($param['page_notify_url']) ? $param['page_notify_url'] : '',//页面跳转地址,接收借款结果
	        'product_no'       => $param['product_no'],//产品id
	        'apply_amt'        => $param['apply_amt'],//借款金额
	        'loan_type'        => $param['loan_type'],//借款类型 03：等额本息，按月分期 05：等额本息，按日分期
	        'col_type'         => $param['col_type'],//到账类型 01:U融汇账户； 02:银行卡；
	        'loan_rate'        => $param['loan_rate'],//借款利率
	        'mng_rate'         => $param['mng_rate'],//资金管理费率
	        'loan_period'      => $param['loan_period'],//借款期限 : 期数
	        'remark'           => isset($param['remark']) ? $param['remark'] : '',//备注
	    );
	    // 构造其他参数
	    $otherData = array(
	        'token'   => $this->createToken($signArray['client_id'], $signArray['app_id'], $signArray['round_str'], $this->app_key),
	        'sign'    => $this->createSign($signArray, $this->app_key)
	    );
	    
	    // 生成要传输的参数
	    $allData = array_merge($signArray, $otherData);
	    if($type == 0){
	        //模拟贷款
	        $sendUrl = $this->base_url . 'klpay/api/v2/baoli/cridit/imitate/loan.do';
	    }elseif ($type == 1){
	        //真是贷款
	        $sendUrl = $this->base_url . 'klpay/api/v2/baoli/cridit/loan.do';
	    }

	    $postReturn = $this->postDataToKoalaPay($allData, $sendUrl, 3);
	    if ( $postReturn['my_status'] == 1 ) {
	        $returnSignArr = [
	            'client_id'   => $postReturn['client_id'],
	            'app_id'      => $postReturn['app_id'],
	            'round_str'   => $postReturn['round_str'],
	            'result_code' => $postReturn['result_code']
	        ];
	        
	        $mySign = $this->createSign($returnSignArr, $this->app_key);
	        if ( $postReturn['sign'] == $mySign ) {
	            $returnData['status'] = 1;
	            $returnData['msg'] = '请求成功';
	            $returnData['result_obj'] = $postReturn['result_obj'];
	        } else {
	            $returnData['status'] = -4;
	            $returnData['msg'] = '签名验证失败';
	        }
	    } else {
	        $returnData['status'] = $postReturn['my_status'];
            $returnData['msg'] = isset($postReturn['result_msg']) ? $postReturn['result_msg'] : '';
	        
	        if ( isset($postReturn['result_code']) ) {
	            $returnData['result_code'] = $postReturn['result_code'];
	        }
	    }
	    
	    return $returnData;
	}

    /**
        * 还款申请(模拟)
        *
        * @param $userId int 用户id
        * @param $notifyUrl string 返回页面
        * @param $repayAmt float 还款金额，单位分
        * @param $repayType string 还款类型(01：账户余额 02：银行卡代扣)
        * @param $loanId string 融资的唯一标识
        * @param $repayTag string 还款标志(01-还当期（逾期+当期）02-还清所有（逾期+当期+提前还款）)
        *
        * @return array
     */
    public function tryToReturnMoney($userId, $notifyUrl, $repayAmt, $repayType, $loanId, $repayTag)
    {
        // 参与签名对应的参数
        $signArray = array(
            'client_id' => $this->client_id,
            'app_id' => $this->app_id,
            'round_str' => $this->createRoundStr(),
            'pay_userid' => $userId,
            'page_notify_url' => $notifyUrl,
            'repay_amt' => $repayAmt,
            'repay_type' => $repayType,
            'loan_id' => $loanId,
            'repay_tag' => $repayTag,
        );

        // 构造其他参数
        $otherData = array(
            'token'   => $this->createToken($signArray['client_id'], $signArray['app_id'], $signArray['round_str'], $this->app_key),
            'sign'    => $this->createSign($signArray, $this->app_key)
        );

        // 生成要传输的参数
        $allData = array_merge($signArray, $otherData);
        $sendUrl = $this->base_url . 'klpay/api/v2/baoli/cridit/imitate/repay.do';
        $postReturn = $this->postDataToKoalaPay($allData, $sendUrl, 3);

        if ( $postReturn['my_status'] == 1 ) {
            $returnSignArr = [
                'client_id'   => $postReturn['client_id'],
                'app_id'      => $postReturn['app_id'],
                'round_str'   => $postReturn['round_str'],
                'result_code' => $postReturn['result_code']
            ];

            $mySign = $this->createSign($returnSignArr, $this->app_key);

            if ( $postReturn['sign'] == $mySign ) {
                $returnData['status'] = 1;
                $returnData['msg'] = '获取还款信息成功';
                $returnData['result_obj'] = $postReturn['result_obj'];
            } else {
                $returnData['status'] = -4;
                $returnData['msg'] = '签名验证失败';
            }
        } else {
            $returnData['status'] = $postReturn['my_status'];
            $returnData['msg'] = isset($postReturn['result_msg']) ? $postReturn['result_msg'] : '';

            if ( isset($postReturn['result_code']) ) {
                $returnData['result_code'] = $postReturn['result_code'];
            }
        }

        return $returnData;
    }

    /**
        * 还款申请(正式)
        *
        * @param $userId int 用户id
        * @param $notifyUrl string 返回页面
        * @param $repayAmt float 还款金额，单位分
        * @param $repayType string 还款类型(01：账户余额 02：银行卡代扣)
        * @param $loanId string 融资的唯一标识
        * @param $repayTag string 还款标志(01-还当期（逾期+当期）02-还清所有（逾期+当期+提前还款）)
        *
        * @return array
     */
    public function returnMoney($userId, $notifyUrl, $repayAmt, $repayType, $loanId, $repayTag)
    {
        // 参与签名对应的参数
        $signArray = array(
            'client_id' => $this->client_id,
            'app_id' => $this->app_id,
            'round_str' => $this->createRoundStr(),
            'pay_userid' => $userId,
            'page_notify_url' => $notifyUrl,
            'repay_amt' => $repayAmt,
            'repay_type' => $repayType,
            'loan_id' => $loanId,
            'repay_tag' => $repayTag,
        );

        // 构造其他参数
        $otherData = array(
            'token'   => $this->createToken($signArray['client_id'], $signArray['app_id'], $signArray['round_str'], $this->app_key),
            'sign'    => $this->createSign($signArray, $this->app_key)
        );

        // 生成要传输的参数
        $allData = array_merge($signArray, $otherData);
        $sendUrl = $this->base_url . 'klpay/api/v2/baoli/cridit/repay.do';
        $postReturn = $this->postDataToKoalaPay($allData, $sendUrl, 3);

        if ( $postReturn['my_status'] == 1 ) {
            $returnSignArr = [
                'client_id'   => $postReturn['client_id'],
                'app_id'      => $postReturn['app_id'],
                'round_str'   => $postReturn['round_str'],
                'result_code' => $postReturn['result_code']
            ];

            $mySign = $this->createSign($returnSignArr, $this->app_key);

            if ( $postReturn['sign'] == $mySign ) {
                $returnData['status'] = 1;
                $returnData['msg'] = '获取还款信息成功';
                $returnData['result_obj'] = $postReturn['result_obj'];
            } else {
                $returnData['status'] = -4;
                $returnData['msg'] = '签名验证失败';
            }
        } else {
            $returnData['status'] = $postReturn['my_status'];
            $returnData['msg'] = isset($postReturn['result_msg']) ? $postReturn['result_msg'] : '';

            if ( isset($postReturn['result_code']) ) {
                $returnData['result_code'] = $postReturn['result_code'];
            }
        }

        return $returnData;
    }

    /**
        * 根据用户id和融资协议号获取借款信息
        *
        * @param $userId int 用户id
        * @param $loanId string 融资协议号
        *
        * @return array
     */
    public function getBorrowInfoByLoanId($userId, $loanId)
    {
        // 参与签名对应的参数
        $signArray = array(
            'client_id' => $this->client_id,
            'app_id' => $this->app_id,
            'round_str' => $this->createRoundStr(),
            'pay_userid' => $userId,
            'loan_id' => $loanId,
        );

        // 构造其他参数
        $otherData = array(
            'token'   => $this->createToken($signArray['client_id'], $signArray['app_id'], $signArray['round_str'], $this->app_key),
            'sign'    => $this->createSign($signArray, $this->app_key)
        );

        // 生成要传输的参数
        $allData = array_merge($signArray, $otherData);
        $sendUrl = $this->base_url . 'klpay/api/v2/baoli/cridit/query.do';
        $postReturn = $this->postDataToKoalaPay($allData, $sendUrl, 3);

        if ( $postReturn['my_status'] == 1 ) {
            $returnSignArr = [
                'client_id'   => $postReturn['client_id'],
                'app_id'      => $postReturn['app_id'],
                'round_str'   => $postReturn['round_str'],
                'result_code' => $postReturn['result_code']
            ];

            $mySign = $this->createSign($returnSignArr, $this->app_key);

            if ( $postReturn['sign'] == $mySign ) {
                $returnData['status'] = 1;
                $returnData['msg'] = '获取借款信息成功';
                $returnData['result_obj'] = $postReturn['result_obj'];
            } else {
                $returnData['status'] = -4;
                $returnData['msg'] = '签名验证失败';
            }
        } else {
            $returnData['status'] = $postReturn['my_status'];
            $returnData['msg'] = isset($postReturn['result_msg']) ? $postReturn['result_msg'] : '';

            if ( isset($postReturn['result_code']) ) {
                $returnData['result_code'] = $postReturn['result_code'];
            }
        }

        return $returnData;
    }

    /**
     * 调起考拉钱包的接口
     *
     * @param array  $postData 传输的数据
     * @param String $sendUrl  对应的链接
     * @param int    $timeOut  curl链接时间
     *
     * @return array
     */
    private function postDataToKoalaPay($postData, $sendUrl, $timeOut = 3)
    {
        $curl = new Curl();
        $curl->setHeader('Content-Type', 'application/x-www-form-urlencoded');
        $curl->setTimeout($timeOut);
        $curl->setConnectTimeout($timeOut);

        $logDriver = new LogDriver();
        // 处理传输数据
        $postData = http_build_query($postData);
        $curl->post($sendUrl, $postData);
        if ($curl->error) {
            $logDriver->error('klPay', "钱包返回失败,url:" . $sendUrl . ",postData:" . $postData . 
                ",Error:" . $curl->errorCode . ': ' . $curl->errorMessage);
            return array(
                'my_status' => -3,
                'msg'    => '调起考拉钱包失败'
            );
        } else {
            $result = @json_decode(json_encode($curl->response), true);
            if ( !empty($result) && isset($result['result_code']) && $result['result_code']=='0001' ) {
                $result['my_status'] = 1;
                return $result;
            } else {
                $logDriver->error('klPay', "钱包返回失败,url:" . $sendUrl . ",postData:" . $postData . ",returnData:" . json_encode($result));
                $result['my_status'] = -10000;
                return $result;
            }
        }
    }

    /**
     * 生成签名
     *
     * @param array  $parameter 传出的参数
     * @param String $appKey    对应的appkey
     *
     * @return string
     */
    public function createSign($parameter, $appKey)
    {
        ksort($parameter);

        $sign = "";

        foreach ( $parameter as $key => $value ) {
            $sign .= empty($sign) ? $key . "=" . $value : "&" . $key . "=" . $value;
        }

        $sign .= "&app_key=" . $appKey;

        return md5($sign);
    }

    /**
     * 生成token
     *
     * @param int    $clientId
     * @param int    $appId
     * @param String $roundStr
     * @param String $appKey
     *
     * @return String
     */
    public function createToken($clientId, $appId, $roundStr, $appKey)
    {
        $token = md5("client_id=" . $clientId . "&app_id=" . $appId . "&round_str=" . $roundStr . "&app_key=" . $appKey);

        return $token;
    }

    /**
     * 生成随机字符串 默认长度为8
     *
     * @param int $length 长度
     *
     * @return String
     */
    public function createRoundStr($length = 8)
    {
        $strArray = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        $roundStr = '';

        for ( $i = 0; $i < $length; $i++ ) {
            $roundStr .= $strArray[mt_rand(0, 61)];
        }

        return $roundStr;
    }
}
