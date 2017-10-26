<?php

namespace Model;
use \Curl\Curl;
use Leaf\Loger\LogDriver;

/**
 * klapi接口模块
 */
class KlApi
{
    private $client_id = '';
    private $client_secret = '';
    private $base_url = '';

    public function __construct($config = array())
    {
        $this->client_id = !isset($config['client_id']) ? KL_API['client_id'] : $config['client_id'];
        $this->client_secret = !isset($config['client_secret']) ? KL_API['client_secret'] : $config['client_secret'];
        $this->base_url = !isset($config['base_url']) ? KL_API['url'] : $config['base_url'];
    }

    /**
        * 请求klapi接口
        *
        * @param $paramArray 一维数组
        *
        * @return array
     */
    public function curlToKlApi($paramArray)
    {
        // 生成签名
        $paramArray['sign'] = $this->createSign($paramArray);
        // 生成请求链接
        $getUrl = $this->base_url. "APIEntrance.php?" .http_build_query($paramArray);

        // 发起请求
        $curl = new Curl();
        $curl->get($getUrl);
        $logDriver = new LogDriver();
        if ($curl->error) {
            $logDriver->error('klApi', "klapi接口中心返回失败,url:" . $getUrl . ",Error:" . $curl->errorCode . ': ' . $curl->errorMessage);
            return array(
                'my_status' => -3,
                'msg'    => '调起接口中心失败'
            );
        } else {
            $result = @json_decode($curl->response, true);
            if ( !empty($result) && isset($result['data']['status']) && $result['data']['status']==1 ) {
                $result['my_status'] = 1;
                return $result;
            } else {
                $logDriver->error('klApi', "klapi接口中心返回失败,url:" . $getUrl . ",returnData:" . json_encode($result));
                $result['my_status'] = -10000;
                return $result;
            }
        }
    }

    /**
        * 根据参数返回签名数据
        *
        * @param $paramArray 一维数组
        *
        * @return string
     */
    private function createSign($paramArray)
    {
        ksort($paramArray);
        $str = implode('', $paramArray); 
        $sign = md5($str . $this->client_secret);
        return $sign;
    }
}
