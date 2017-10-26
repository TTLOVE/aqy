<?php
 
namespace Model;

use Model\KlApi;
use Leaf\Loger\LogDriver;

/**
 * Class Message
 * @author xiaozhu
 */
class Message
{
    /**
        * 发送服务商模板消息
        *
        * @param $appId 服务商id
        * @param $title 标题
        * @param $toUserArray 传输的用户id（带上lq_，一维数组）
        * @param $goUrl 跳转链接
        * @param $tplContentArray 模板内容（二维数组）
        *
        * @return array
     */
    public function sendMsgToApplication($appId, $title, $toUserArray, $goUrl, $tplContentArray)
    {
        // 读取服务商信息
        $applicationInfo = (new Application())->getApplicationInfoBuId($appId);
        // 还款成功发送消息
        $ext = [
            'msg_type' => 'tpl_content',
            'target' => 'url',
            'url' => $goUrl,
            'app_id' => "" . $appId . "",
            'nickname' => isset($applicationInfo['nane']) ? $applicationInfo['nane'] : '我要借款',
            'avatar' => isset($applicationInfo['logo']) ? $applicationInfo['logo'] : '',
            'tpl_contents' => $tplContentArray,
        ];
        $sendMsgQuery = array(
            'client_id' => KL_API['client_id'],
            'c' => 'easemobmsg|index',
            'm' => 'index',
            'msg_type' => 21,
            'from_user' => 'app_' . $appId,
            'to_user' => json_encode($toUserArray),
            'msg' => $title,
            'target_type' => 'users',
            'ext' => json_encode($ext),
            't' => time(),
        );
        $sendMsgData = (new KlApi())->curlToKlApi($sendMsgQuery);
        if ( !isset($sendMsgData['my_status']) || $sendMsgData['my_status']!=1 ) {
            (new LogDriver())->error('message', '发送消息失败!data : ' . json_encode($sendMsgQuery) . ';result:'.json_encode($sendMsgData));
        }
        return $sendMsgData;
    }
}
