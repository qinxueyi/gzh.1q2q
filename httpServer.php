<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/10/23
 * Time: 12:54
 * 监听9501端口并使用swoole 异步定时器 延迟推送 客服消息
 * todo
 *
 */
$http = new swoole_http_server("0.0.0.0", 9501);
$http->on('request', function ($request, $response) {
    $sear = $request->post;
    $arr = $sear['data'];
    $data = unserialize($arr);
    print_r($data);
    if (!empty($data)) {
        foreach ($data as $value) {
            swoole_timer_after($value['time'], function () use ($value) {
                $accessToken = file_get_contents("http://1q2q.chaotuozhe.com/getAccessToken.php?uniacid=" . $value['uniacid']);
                $sendArr['touser'] = $value['openId'];
                $sendArr['msgtype'] = $value['msgtype'];
                $sendArr[$value['msgtype']] = json_decode($value["content"], true);
                $json = json_encode($sendArr, JSON_UNESCAPED_UNICODE);
                print_r($json);
                $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=" . $accessToken;
                $resp = http_post_json($url, $json);
                print_r($resp);
            });
        }
    }

});


$http->start();

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