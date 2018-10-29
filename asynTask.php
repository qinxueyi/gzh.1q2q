<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/10/23
 * Time: 11:27
 */
//发送客服消息
echo "正在执行异步任务";
$msg = "你好";
swoole_timer_after(8000, function () use ($msg) {
    echo $msg;
    $accessToken = getAccessToken("wxb87c867dc23a726a", "e3be07134dc3d51c8ba73661196a76da");
    $sendArr['touser'] = "oNfBl00SZZWPBvPrHS9qkU5_rARg";
    $sendArr['msgtype'] = "text";
    $message['content'] = "你好";
    $sendArr['text'] = $message;
    $json = json_encode($sendArr, JSON_UNESCAPED_UNICODE);
    $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=" . $accessToken;
    $resp = http_post_json($url, $json);
    print_r($resp);
});


//function sendServiceMessage($model = false)
//{
////    $accessToken = getAccessToken("wxb87c867dc23a726a", "e3be07134dc3d51c8ba73661196a76da");
////    $sendArr['touser'] = "oNfBl00SZZWPBvPrHS9qkU5_rARg";
////    $sendArr['msgtype'] = "text";
////    $message['content'] = "你好";
////    $sendArr['text'] = $message;
////    $json = json_encode($sendArr, JSON_UNESCAPED_UNICODE);
////    $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=" . $accessToken;
////    $resp = http_post_json($url, $json);
////    print_r($resp);
//
//}

function getAccessToken($appId, $appSecRet)
{
    $token_access_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appId . "&secret=" . $appSecRet;
    $res = file_get_contents($token_access_url); //获取文件内容或获取网络请求的内容
    $result = json_decode($res, true); //接受一个 JSON 格式的字符串并且把它转换为 PHP 变量
    $access_token = $result['access_token'];
    return $access_token;
}

/**
 * PHP发送Json对象数据
 *
 * @param $url 请求url
 * @param $jsonStr 发送的json字符串
 * @return array
 */
function http_post_json($url, $jsonStr)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($jsonStr)
        )
    );
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return json_decode($response, true);
}