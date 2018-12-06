<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/10/12
 * Time: 12:57
 */
/**
 * @param $appId
 * @param $appSecRet
 * 获取accessToken
 */
define('IN_API', true);
require_once '../framework/bootstrap.inc.php';
load()->func('pdo');
load()->web('export');
load()->web('util');
$do = $_GET["do"];

function weChatList()
{
    $weChatList = pdo_getall('account_wechats');
    return $weChatList;
}

function getAccessToken($appId, $appSecRet)
{
    $token_access_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appId . "&secret=" . $appSecRet;
    $res = file_get_contents($token_access_url); //获取文件内容或获取网络请求的内容
    $result = json_decode($res, true); //接受一个 JSON 格式的字符串并且把它转换为 PHP 变量
    $access_token = $result['access_token'];
    return $access_token;
}

/**
 * 粉丝数据接口
 */
function getUserInfo($model)
{   //获取accessToken
    $date = date("Y-m-d", strtotime("-1 day"));
    $accessToken = file_get_contents("http://1q2q.chaotuozhe.com/getAccessToken.php?uniacid=" . $model['uniacid']);
    if($accessToken){
        $json = json_encode(array("access_token" => $accessToken, "begin_date" => $date, "end_date" => $date));
        $getAddReduceFanUrl = "https://api.weixin.qq.com/datacube/getusersummary?access_token=" . $accessToken;
        //获取用户增减数据
        $getAddReduceFan = http_post_json($getAddReduceFanUrl, $json);
        //获取累计用户数据
        $getAddUpFanUrl = "https://api.weixin.qq.com/datacube/getusercumulate?access_token=" . $accessToken;
        $getAddUpFan = http_post_json($getAddUpFanUrl, $json);
        $fan = array();
        $fan['statistics_date'] = $date;
        $fan['uniacid'] = $model["uniacid"];//公众号id
        $fan['type'] = getaccounttype($model["uniacid"]);//公众号类型
        $fan['sum_fan'] = $getAddUpFan['list'][0]['cumulate_user'];//总粉丝
        $fan['add_fan'] = 0;//新增粉丝
        $fan['cancel_fan'] = 0;//取关粉丝
        $fan['auto_fan'] = 0;//净增粉丝
        $fan['cancel_fan_rate'];//取关率
        $fan['active_fan'] = 0; //活跃粉丝
        $fan['active_rate']; //活跃度
        $sql = 'SELECT count(fanid) FROM '.tablename('mc_mapping_fans').' WHERE uniacid = '.$model['uniacid'].' and sex = 1';
        $sql2 = 'SELECT count(fanid) FROM '.tablename('mc_mapping_fans').' WHERE uniacid = '.$model['uniacid'].' and sex = 2';
        $fan['sum_sex_nan'] = pdo_fetchcolumn($sql); //男粉丝
        $fan['sum_sex_nv'] = pdo_fetchcolumn($sql2); //男粉丝
        $activeEvent = pdo_get('active_event', array('uniacid =' => $model["uniacid"], 'statistics_date =' => $date));
        if (!empty($activeEvent)) {
            $fan['active_fan'] = $activeEvent['active_fan_sum'];
            $activeRateRound = round($fan['active_fan'] / $fan['sum_fan'], 4) * 100;
            $fan['active_rate'] = $activeRateRound . "%";
        }
        foreach ($getAddReduceFan["list"] as $item) {
            $fan['add_fan'] = $fan['add_fan'] + $item['new_user'];
            $fan['cancel_fan'] = $fan['cancel_fan'] + $item['cancel_user'];
        }
        $round = round($fan['cancel_fan'] / $fan['sum_fan'], 4) * 100;
        $fan['cancel_fan_rate'] = $round . "%";
        $fan['auto_fan'] = $fan['add_fan'] - $fan['cancel_fan'];  //计算净增粉丝
        if(pdo_get('vipcn_fan',array('uniacid'=>$model["uniacid"],'statistics_date'=>$date))){
            pdo_update('vipcn_fan',$fan,array('uniacid'=>$model["uniacid"],'statistics_date'=>$date));
        }else{
            $str = pdo_insert('vipcn_fan', $fan);
        }
        return $str;
    }
}

/**
 * 保存数据
 */
if ($do == "saveVipCnFan") {
    //查询所有公众号
    $vipCnFanList = pdo_getall('account', array("isdeleted" => "0"));
    foreach ($vipCnFanList as $vipCnFan) {
        getUserInfo($vipCnFan);
    }
}


if($do == "subscribe_scene"){
    $vipCnFanList = pdo_getall('account', array("isdeleted" => "0"));
    foreach ($vipCnFanList as $vipCnFan) {
        getuserscene($vipCnFan);
    }
}

function getuserscene($uniacid){
    $accessToken = file_get_contents("http://1q2q.chaotuozhe.com/getAccessToken.php?uniacid={$uniacid['uniacid']}");
    if($accessToken){
        $all = pdo_getall('mc_mapping_fans',array('uniacid'=>$uniacid['uniacid'],'subscribe_scene'=>0),array('openid','fanid'));
        foreach ($all as $val){
            $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$accessToken}&openid={$val['openid']}&lang=zh_CN";
            $data = ihttp_get($url);
            $data = (array)json_decode($data['content']);
            $arr = array(
                'sex' => $data['sex'],
                'subscribe_scene' => $data['subscribe_scene'],
            );
            $c = pdo_update('mc_mapping_fans',$arr,array('uniacid'=>$uniacid['uniacid'],'openid'=>$val['openid'],'fanid'=>$val['fanid']));
        }
    }
}

function getaccounttype($var){
    $level = pdo_get('account_wechats',array('uniacid'=>$var),'level');
    return $level['level'] == '4'?'公众号':'订阅号';
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


