<?php
define('IN_API', true);
require_once './framework/bootstrap.inc.php';
load()->func('pdo');
//$do = "getToken";
//if ($do == "getToken") {
//    echo getComponentVertfyToken();
//}
//连接redis
$redis = new Redis();
$redis->connect("121.40.84.207", 6379);
$redis->auth('weiying123');

getAuthoizerAccessToken($redis);
//获取授权方接口调用凭据
function getAuthoizerAccessToken($redis)
{
    $ret = $redis->get('authoizer_access_token');
    if ($ret) {
        return $ret;
    } else {
        //重新获取authoizer_access_token
        //获取componentVerifyToken
        $componentVerifyToken = getComponentVerifyToken($redis);
        //获取pre_auth_code
        $preAuthCode = getPreAuthCode($componentVerifyToken);
        $weChatsInfo = getWeChatsInfo($componentVerifyToken, $preAuthCode);
        $redis->setex('authoizer_access_token', 7200, $weChatsInfo['authorizer_access_token']);
        //print_r($weChatsInfo);
        return $weChatsInfo['authoizer_access_token'];
    }
}

function getComponentVerifyToken($redis)
{
    $ret = $redis->get('component_verify_token');
    if ($ret) {
        return $ret;
    } else {
        //重新获取componentVerifyToken
        $componentVerifyToken = createComponentVerifyToken();
        $redis->setex('component_verify_token', 7200, $componentVerifyToken['component_access_token']);
        return $componentVerifyToken['component_access_token'];
    }
}

//获取componentVerifyToken
function createComponentVerifyToken()
{
    $ticketModel = pdo_get('core_cache', array("key" => "account:ticket"));
    $ticket = unserialize($ticketModel["value"]);
    $component_verify_token_req = array(
        'component_appid' => 'wxbd393edabf09e2d7',
        'component_appsecret' => 'ad189eb81eaba4317254b1ea7de73bba',
        'component_verify_ticket' => $ticket
    );
    //获取component_verify_token
    $component_verify_token_resp = http_post_json('https://api.weixin.qq.com/cgi-bin/component/api_component_token', json_encode($component_verify_token_req));
    return $component_verify_token_resp;


}

//获取pre_auth_code
function getPreAuthCode($componentVerifyToken)
{
    $pre_auth_code_req = array('component_appid' => 'wxbd393edabf09e2d7');
    $pre_auth_code_resp = http_post_json('https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token=' . $componentVerifyToken, json_encode($pre_auth_code_req));
    return $pre_auth_code_resp;
}

//授权码换取公众号或小程序的接口调用凭据和授权信息
function getWeChatsInfo($componentVerifyToken, $pre_auth_code_resp)
{
    $we_chatsInfo_req = array(
        'component_appid' => 'wxbd393edabf09e2d7',
        'authorization_code' => $pre_auth_code_resp['pre_auth_code']
    );
    $we_chatsInfo_resp = http_post_json("https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=" . $componentVerifyToken, json_encode($we_chatsInfo_req));
    print_r($we_chatsInfo_resp);
    return $we_chatsInfo_resp;
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

?>