<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/11/14
 * Time: 11:23
 */

$do = $_GET['do'];
//发起用户授权
if ($do == "getUserAuthorization") {
    $template_id = "hviz35sN0-zViDRLAqtvm1zeTE3zlq5ySGItBEXXBMo";
    $app_id = "wxb87c867dc23a726a";
    $redirect_url = "http://gzh.1q2q.com/web/index.php?c=account&a=once-subscription&do=getUserInfo";
    $redirect_url = urlencode($redirect_url);
    $url = "https://mp.weixin.qq.com/mp/subscribemsg?action=get_confirm&appid=$app_id&scene=1000&template_id=$template_id&redirect_url=$redirect_url&reserved=test#wechat_redirect";
    echo $url;
}

//通过API推送订阅模板消息给到授权微信用户
if ($do == "getUserInfo") {
    if ($_GET['action'] == "confirm") {
        $accessToken = file_get_contents("http://gzh.1q2q.com/getAccessToken.php?uniacid=4");
        $reply_url = "https://api.weixin.qq.com/cgi-bin/message/template/subscribe?access_token=$accessToken";
        //发送消息
        $reply_content = array();
        $reply_content['touser'] = $_GET['openid'];
        $reply_content['template_id'] = $_GET['template_id'];
        $reply_content['scene'] = $_GET['scene'];
        $reply_content['title'] = '欢迎';
        $data = array();
        $content = array();
        $content['value'] = "欢迎来到金曲来一首";
        $content['color'] = "#00FFFF";
        $reply_content['data']['content'] = $content;
        $response = http_post_json($reply_url, json_encode($reply_content));
        print_r($response);
    } else {
        echo "打扰了";
    }
}


function getTemplateId()
{


}

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