<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/10/19
 * Time: 15:19
 * 接受微信事件推送
 */

define("RECEIVE", 'success');
require_once './web/common/WechatXml/wxBizMsgCrypt.php';
//sendServiceMessage();
//exit;
//将微信请求的xml解析明文模式
load()->func('pdo');
$requestXml = decryptMessage();
//将xml转为array
$jsonData = json_encode(simplexml_load_string($requestXml, 'SimpleXMLElement', LIBXML_NOCDATA));
$receiveData = json_decode($jsonData, true);

$strAppId = $_GET["appid"];
$appId = substr($strAppId, 1);


//非法公众号
$accountWeChats = selectAccountWeChats($appId);
if (empty($accountWeChats)) {
    echo RECEIVE;
    exit;
}
$receiveData["uniacid"] = $accountWeChats["uniacid"];
$receiveData['account_wechats'] = $accountWeChats;
//event MsgType 出错
$msgType = receiveType($receiveData);
if (!$msgType) {
    echo RECEIVE;
    exit;
} else {
    //记录活跃粉丝
    insertOrUpdateActiveFanList($accountWeChats);
}


/**
 * @param $accountWeChats
 * 活跃粉丝表统计
 */
function insertOrUpdateActiveFanList($accountWeChats)
{

//是否之前已经推送
    $activeEvent = selectActiveEvent($accountWeChats['uniacid']);
    $receiveData['uniacid'] = $accountWeChats['uniacid'];
//print_r($receiveData);
    if (empty($activeEvent)) {
        //新增
        $activeEvent['uniacid'] = $accountWeChats['uniacid'];
        $activeEvent['statistics_date'] = date("Y-m-d");
        $activeEvent['active_fan_sum'] = 1;
        pdo_insert('active_event', $activeEvent);
    } else {
        //修改活跃人数
        $activeEvent['active_fan_sum'] = $activeEvent['active_fan_sum'] + 1;
        pdo_update('active_event', $activeEvent, array('uniacid' => $activeEvent['uniacid']));
    }
}

//☆ 如果是关注 则insert ConcernList
//☆ 延迟推送
if ($receiveData['Event'] == "subscribe") {
    //插入记录表
    insertConcern($receiveData);
    //将大于24H客服消息放入Redis
    $pushMessageListByExceedDay = getPushMessage($receiveData, false);
    if (!empty($pushMessageListByExceedDay)) {
        savePushMessageByExceedDay($pushMessageListByExceedDay);
    }
    //异步延迟推送<24H
    $pushMessageList = getPushMessage($receiveData, true);
    $param['data'] = serialize($pushMessageList);
    sendUserMessage("http://gzh.1q2q.com:9501", $param);
}


/**
 * 将超过24H客服消息保存在Redis
 */
function savePushMessageByExceedDay($pushMessage)
{
    $redis = new Redis();
    $redis->connect("121.40.84.207", 6379);
    $redis->auth('weiying123');
    foreach ($pushMessage as $value) {
        $redis->sAdd('pushMessage', json_encode($value));
    }
    return;
}

/**
 * @param $receiveData
 * 获取推送数据
 */
function getPushMessage($receiveData, $limit = true)
{
    //获取openid
    $openId = $receiveData['FromUserName'];
    $conditionModel = array("uniacid =" => $receiveData['uniacid'],"status =" => 1);
    //小于24H
    if ($limit) {
        $conditionModel['time <'] = 86400000;
        $eventList = pdo_getall("event_list", $conditionModel);
    }//大于24H
    else {
        $conditionModel['time >='] = 86400000;
        $eventList = pdo_getall("event_list", $conditionModel);
    }
    foreach ($eventList as $k => $value) {
        $value['openId'] = $openId;
        $value['begin_time'] = time();
        $eventList[$k] = $value;
    }
    return $eventList;
//    $myfile = fopen("a.txt", "w") or die("Unable to open file!");
////    fwrite($myfile, "openid:" . $openId);
////    fwrite($myfile, "appid:" . $appId);
////    fwrite($myfile, "secret:" . $secret);
//    fwrite($myfile, "List:" . json_encode($eventList);
//    fclose($myfile);

}

/**
 * @param $appId
 * 根据appId 查询 weWhatsModel
 */
function selectAccountWeChats($appId)
{
    $accountWeChats = pdo_get('account_wechats', array('key' => $appId));
    return $accountWeChats;

}

/**
 * @param $appId
 * @return bool
 * 查询今天有没有公众号推送事件与发送内容
 */
function selectActiveEvent($uniacid)
{
    $activeEvent = pdo_get('active_event', array('uniacid =' => $uniacid, 'statistics_date =' => date("Y-m-d")));
    return $activeEvent;
}

/**
 * @param $receiveModel
 * @return bool
 * 判断推送类型
 */
function receiveType($receiveModel)
{
    $msgType = array("text", "image", "voice", "video", "shortvideo", "location", "link", "event");
    if (in_array($receiveModel["MsgType"], $msgType)) {
        if ($receiveModel["MsgType"] != "event") {
            echo "success";
        }
        return true;
    } else {
        return false;
    }

}

/**
 * @param $activeEvent
 * 用户关注公众号 记录concernList
 */
function insertConcern($activeEvent)
{
    $concernList = array();
    $concernList['openid'] = $activeEvent['FromUserName'];
    $concernList['uniacid'] = $activeEvent['uniacid'];
    $concernList['concern_date'] = date('Y-m-d h:i:s', time());
    $concernList['remark'] = json_encode($activeEvent);
    //print_r($activeEvent);
    pdo_insert("concern_list", $concernList);
}

//将xml数据解密
function decryptMessage()
{
    $timestamp = $_GET['timestamp'];
    $nonce = $_GET["nonce"];
    $msg_signature = $_GET['msg_signature'];
    $encrypt_type = $_GET['encrypt_type'];
    $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
    $pc = new WXBizMsgCrypt("beifu1q2q", "FJhDWWB0m3XRQr6BqK5LxwfixQMHMS070jl2y8SjcVv", "wxbd393edabf09e2d7");
    $decryptMsg = "";  //解密后的明文
    $pc->DecryptMsg($msg_signature, $timestamp, $nonce, $postStr, $decryptMsg);
    return $decryptMsg;
}

/**
 * 后台守护进程在监听9501端口 (具体文件httpServer.php)
 * @param $url 发送url 触发延迟推送
 * @param array $post_data
 * @param array $cookie
 * @return bool
 * 异步http request
 */
function sendUserMessage($url, $post_data = array(), $cookie = array())
{

    $url_arr = parse_url($url);

    $port = isset($url_arr['port']) ? $url_arr['port'] : 80;

    if ($url_arr['scheme'] == 'https') {

        $url_arr['host'] = 'ssl://' . $url_arr['host'];

    }

    $fp = fsockopen($url_arr['host'], $port, $errno, $errstr, 30);

    if (!$fp) return false;

    $getPath = isset($url_arr['path']) ? $url_arr['path'] : '/index.php';

    $getPath .= isset($url_arr['query']) ? '?' . $url_arr['query'] : '';

    $method = 'GET';  //默认get方式

    if (!empty($post_data)) $method = 'POST';

    $header = "$method  $getPath  HTTP/1.1\r\n";

    $header .= "Host: " . $url_arr['host'] . "\r\n";

    if (!empty($cookie)) {  //传递cookie信息

        $_cookie = strval(NULL);

        foreach ($cookie AS $k => $v) {

            $_cookie .= $k . "=" . $v . ";";

        }
        $cookie_str = "Cookie:" . base64_encode($_cookie) . "\r\n";

        $header .= $cookie_str;

    }

    if (!empty($post_data)) {  //传递post数据

        $_post = array();

        foreach ($post_data AS $_k => $_v) {

            $_post[] = $_k . "=" . urlencode($_v);

        }


        $_post = implode('&', $_post);

        $post_str = "Content-Type:application/x-www-form-urlencoded; charset=UTF-8\r\n";

        $post_str .= "Content-Length: " . strlen($_post) . "\r\n";  //数据长度

        $post_str .= "Connection:Close\r\n\r\n";

        $post_str .= $_post;  //传递post数据

        $header .= $post_str;

    } else {

        $header .= "Connection:Close\r\n\r\n";
    }

    fwrite($fp, $header);
    //echo fread($fp,1024);
    usleep(1000); // 这一句也是关键，如果没有这延时，可能在nginx服务器上就无法执行成功
    fclose($fp);
    return true;

}


////发送客服消息
//function sendServiceMessage()
//{
//    $accessToken = getAccessToken("wxb87c867dc23a726a", "e3be07134dc3d51c8ba73661196a76da");
//    $sendArr['touser'] = "oNfBl00SZZWPBvPrHS9qkU5_rARg";
//    $sendArr['msgtype'] = "text";
//    $message['content'] = "你好";
//    $sendArr['text'] = $message;
//    $json = json_encode($sendArr, JSON_UNESCAPED_UNICODE);
//    $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=" . $accessToken;
//    $resp = http_post_json($url, $json);
//    print_r($resp);
//
//}
//
//function getAccessToken($appId, $appSecRet)
//{
//    $token_access_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appId . "&secret=" . $appSecRet;
//    $res = file_get_contents($token_access_url); //获取文件内容或获取网络请求的内容
//    $result = json_decode($res, true); //接受一个 JSON 格式的字符串并且把它转换为 PHP 变量
//    $access_token = $result['access_token'];
//    return $access_token;
//}
//
///**
// * PHP发送Json对象数据
// *
// * @param $url 请求url
// * @param $jsonStr 发送的json字符串
// * @return array
// */
//function http_post_json($url, $jsonStr)
//{
//    $ch = curl_init();
//    curl_setopt($ch, CURLOPT_POST, 1);
//    curl_setopt($ch, CURLOPT_URL, $url);
//    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//            'Content-Type: application/json; charset=utf-8',
//            'Content-Length: ' . strlen($jsonStr)
//        )
//    );
//    $response = curl_exec($ch);
//    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//    curl_close($ch);
//    return json_decode($response, true);
//}