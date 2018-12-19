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
    $accessToken = file_get_contents("http://gzh.1q2q.com/getAccessToken.php?uniacid=" . $model['uniacid']);
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
    $fan['sum_fan'] = $getAddUpFan['list'][0]['cumulate_user'];//总粉丝
    $fan['add_fan'] = 0;//新增粉丝
    $fan['cancel_fan'] = 0;//取关粉丝
    $fan['auto_fan'] = 0;//净增粉丝
    $fan['cancel_fan_rate'];//取关率
    $fan['active_fan'] = 0; //活跃粉丝
    $fan['active_rate']; //活跃度
    $activeEvent = pdo_get('active_event', array('uniacid =' => $model["uniacid"], 'statistics_date =' => $date));
    $fan['active_fan'] = $activeEvent['active_fan_sum'];
    $activeRateRound = round($fan['active_fan'] / $fan['sum_fan'], 4) * 100;
    $fan['active_rate'] = $activeRateRound . "%";
    foreach ($getAddReduceFan["list"] as $item) {
        $fan['add_fan'] = $fan['add_fan'] + $item['new_user'];
        $fan['cancel_fan'] = $fan['cancel_fan'] + $item['cancel_user'];
    }
    $round = round($fan['cancel_fan'] / $fan['add_fan'], 4) * 100;
    $fan['cancel_fan_rate'] = $round . "%";
    $fan['auto_fan'] = $fan['add_fan'] - $fan['cancel_fan'];  //计算净增粉丝
    print_r($fan);

//
//    $arr['statistics_date'] = '2018-10-15';
//    $arr['uniacid'] = 3;
//    $arr['sum_fan'] = '20384';
//    $arr['add_fan'] = '1';
//    $arr['cancel_fan'] = '44';
//    $arr['cancel_fan_cate'] = '100%';
//    $arr['auto_fan'] = '-43';
    $str = pdo_insert('vipcn_fan', $fan);
    return $str;
}

/**
 * 保存数据
 */
if ($do == "saveVipCnFan") {
    $vipCnFanList = pdo_getall('account_wechats', array("acid" => "4"));
    foreach ($vipCnFanList as $vipCnFan) {
        getUserInfo($vipCnFan);
    }
}

/**
 * 查询粉丝列表与导出
 */
if ($do == "selectList") {
    $sql = "SELECT * FROM we8.ims_vipcn_fan fan inner join ims_account_wechats wechats
            on fan.uniacid = wechats.uniacid where 1=1";
    //按时间
    if (!empty($_GET['time'])) {
        $time = "'{$_GET['time']}'";
        $sql .= " and  fan.statistics_date = " . $time;
    }
    //按公众号
    if (!empty($_GET['uniacid'])) {
        $sql .= " and  fan.uniacid = " . $_GET['uniacid'];
    }
    $sql .= " order by fan.id desc";
    //获取数据
    $result = pdo_fetchall($sql);
    
    foreach ($result as &$val){
        $val['statistics_date'] = date('Y-m-d',strtotime($val['statistics_date']));
        if($val['sum_fan']){
            $val['sex_nan_bili'] = round($val['sum_sex_nan']/$val['sum_fan'],4) * 100;//男粉比例
            $val['sex_nv_bili'] = round($val['sum_sex_nv']/$val['sum_fan'],4) * 100;//男粉比例
        }
    }
    //分页
    if (empty($_GET['limit']) || !is_numeric($_GET['limit'])) {
        $_GET['limit'] = 10;
    }
    if (empty($_GET['page']) || !is_numeric($_GET['page'])) {
        $_GET['page'] = 1;
    }
//    if (empty($result)) {
//        echo responseMsg(0, "success", [], count($result));
//    }
    $data = paging($_GET['limit'], $_GET['page'], $result);
    //是否导出
    if ($_GET['export'] == "yes") {
        $indexKey = array('name', 'statistics_date', 'add_fan', 'cancel_fan', 'auto_fan', 'cancel_fan_rate', 'sum_fan', 'active_fan', 'active_rate');
        exportExcel($data['result'], "fanExcel", $indexKey, 2, false, "fan");
        exit;
    }
    echo responseMsg(0, "success", $data['result'], count($result));
    exit;
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

template('account/fan-list');

