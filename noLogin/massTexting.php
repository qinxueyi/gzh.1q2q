<?php
/**
 * Created by PhpStorm.
 * User: qxy
 * Date: 2018/12/5
 * Time: 11:56
 * 群发消息
 */
define('IN_API', true);
require_once "../framework/bootstrap.inc.php";
require_once "../web/common/common.func.php";
load()->model('material');
load()->model('mc');
load()->model('account');
load()->model('attachment');
load()->func('global');

$group = intval($_GPC['group']);
$type = trim($_GPC['type']);
$id = intval($_GPC['id']);
$uniacid = intval($_GPC['uniacid']);
$acid = intval($_GPC['acid']);
$media_id = intval($_GPC['media']);
$result = fansSendAll($group, $type, $media_id, $uniacid);

if (is_error($result)) {
    // iajax(1, $result['message'], '');
    return $result;
}
$groups = pdo_get('mc_fans_groups', array('uniacid' => $uniacid, 'acid' => $acid));
if (!empty($groups)) {
    $groups = iunserializer($groups['groups']);
}
if ($group == -1) {
    $groups = array(
        $group => array(
            'name' => '全部粉丝',
            'count' => 0
        )
    );
}
$record = array(
    'uniacid' => $uniacid,
    'acid' => $acid,
    'groupname' => $groups[$group]['name'],
    'fansnum' => $groups[$group]['count'],
    'msgtype' => $type,
    'group' => $group,
    'attach_id' => $id,
    'media_id' => $media['media_id'],
    'status' => 0,
    'type' => 0,
    'sendtime' => TIMESTAMP,
    'createtime' => TIMESTAMP,
);
pdo_insert('mc_mass_record', $record);
//iajax(0, '发送成功！', '');


/**
 * @param $group
 * @param $msgtype
 * @param $media_id
 * @param $uniacid
 * @return array|bool|mixed|string
 * 群发
 */
function fansSendAll($group, $msgtype, $media_id, $uniacid)
{
    $types = array('text' => 'text', 'image' => 'image', 'news' => 'mpnews', 'voice' => 'voice', 'video' => 'mpvideo', 'wxcard' => 'wxcard');
    if (empty($types[$msgtype])) {
        return error(-1, '消息类型不合法');
    }
    $is_to_all = false;
    if ($group == -1) {
        $is_to_all = true;
    }
    $data = array(
        'filter' => array(
            'is_to_all' => $is_to_all,
            'group_id' => $group
        ),
        'msgtype' => $types[$msgtype],
        $types[$msgtype] => array(
            'media_id' => $media_id
        )
    );
    if ($msgtype == 'wxcard') {
        unset($data['wxcard']['media_id']);
        $data['wxcard']['card_id'] = $media_id;
    }
    $token = file_get_contents("http://1q2q.chaotuozhe.com/getAccessToken.php?uniacid=" . $uniacid);
    if (is_error($token)) {
        return $token;
    }
    $url = "https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token={$token}";
    $data = urldecode(json_encode($data));
    $response = ihttp_request($url, $data);
    if (is_error($response)) {
        return error(-1, "访问公众平台接口失败, 错误: {$response['message']}");
    }
    $result = @json_decode($response['content'], true);
    if (empty($result)) {
        return error(-1, "接口调用失败, 元数据: {$response['meta']}");
    } elseif (!empty($result['errcode'])) {
        return error(-1, "访问微信接口错误, 错误代码: {$result['errcode']}, 错误信息: {$result['errmsg']},错误详情 未知错误");
    }
    return $result;
}


