<?php
/**
 * Created by PhpStorm.
 * User: qxy
 * Date: 2018/10/14
 * Time: 13:17
 * 数据统计 --文章
 */
define('IN_API', true);
require_once '../framework/bootstrap.inc.php';
load()->func('pdo');
load()->web('export');
load()->web('util');

/**
 * @param $appId
 * @param $appSecRet
 * 获取accessToken
 */
$do = $_GET['do'];
function getAccessToken($appId, $appSecRet)
{
    $token_access_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appId . "&secret=" . $appSecRet;
    $res = file_get_contents($token_access_url); //获取文件内容或获取网络请求的内容
    $result = json_decode($res, true); //接受一个 JSON 格式的字符串并且把它转换为 PHP 变量
    $access_token = $result['access_token'];
    return $access_token;
}

/**
 * 获取图文群发每日数据
 */

function getArticleList($model)
{
    //获取accessToken
    $accessToken = file_get_contents("http://gzh.1q2q.com/getAccessToken.php?uniacid=" . $model['uniacid']);
    $date = date("Y-m-d", strtotime("-1 day"));
    $json = json_encode(array("access_token" => $accessToken, "begin_date" => $date, "end_date" => $date));
    $getArticleListUrl = "https://api.weixin.qq.com/datacube/getarticlesummary?access_token=" . $accessToken;
    $data = http_post_json($getArticleListUrl, $json);
    print_r($data);
    if (!empty($data)) {
        foreach ($data['list'] as $value) {
            $articleModel = array();
            $articleModel['statistics_date'] = $date;
            $articleModel['uniacid'] = $model["uniacid"];//公众号id
            $articleModel['title'] = $value["title"];
            $articleModel['position'] = substr($value['msgid'], -1); //位置
            $articleModel['reader_num'] = $value['int_page_read_count']; //阅读数
            $round = round($value['int_page_read_count'] / $model['cumulate_user'], 2) * 100;
            $articleModel['reader_rate'] = $round . "%"; //阅读率
            $articleModel['original_reader_num'] = $value['ori_page_read_user'];//原文阅读数
            $originalRound = round($value['ori_page_read_user'] / $model['cumulate_user'], 2) * 100;
            $articleModel['original_reader_rate'] = $originalRound . "%"; //原文阅读率
            $articleModel['fan_num'] = $model['cumulate_user'];
            //保存数据
            pdo_insert('article_list', $articleModel);
        }
    }
    echo "success";  

}

/**
 * 保存数据
 */
if ($do == "saveArticleList") {
    //查询所有公众号
    $accountWeChatList = pdo_getall('account_wechats', array("styleid" => "0"));
    foreach ($accountWeChatList as $accountWeChat) {
        getArticleList($accountWeChat);
    }
}

/**
 * @param $url
 * @param $jsonStr
 * @return mixed
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

/**
 * @return array
 * 查询所有公众号列表
 */
function weChatList()
{
    $weChatList = pdo_getall('account_wechats');
    return $weChatList;
}
