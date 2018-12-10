<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
/* if ($_GPC['uniacid']) {
    $uniacid = intval($_GPC['uniacid']);
    $_W['uniacid'] = $uniacid;
    $_W['account'] = uni_fetch($uniacid);
    $role = permission_account_user_role($_W['uid'], $uniacid);
    if (empty($role)) {
        itoast('操作失败, 非法访问.', '', 'error');
    }
    if (empty($_W['isfounder'])) {
        $account_endtime = uni_fetch($uniacid);
        $account_endtime = $account_endtime['endtime'];
        if ($account_endtime > 0 && TIMESTAMP > $account_endtime) {
            itoast('公众号已到期。', '', 'error');
        }
    }
    uni_account_save_switch($uniacid);
    uni_account_switch($uniacid);
} else {
    $account = pdo_get('account', array('isdeleted' => 0));
    $uniacid = $account['uniacid'];
    $_W['uniacid'] = $uniacid;
    $_W['account'] = uni_fetch($uniacid);
    $role = permission_account_user_role($_W['uid'], $uniacid);
    if (empty($role)) {
        itoast('操作失败, 非法访问.', '', 'error');
    }
    if (empty($_W['isfounder'])) {
        $account_endtime = uni_fetch($uniacid);
        $account_endtime = $account_endtime['endtime'];
        if ($account_endtime > 0 && TIMESTAMP > $account_endtime) {
            itoast('公众号已到期。', '', 'error');
        }
    }
    uni_account_save_switch($uniacid);
    uni_account_switch($uniacid);
} */


load()->model('material');
load()->model('mc');
load()->model('account');
load()->model('attachment');
load()->func('file');

$dos = array('display', 'sync', 'delete', 'send');
$do = in_array($do, $dos) ? $do : 'display';
$_W['page']['title'] = '永久素材-微信素材';
if ($do == 'send') {
    $group = intval($_GPC['group']);
    $type = trim($_GPC['type']);
    $id = intval($_GPC['id']);
    $media = pdo_get('wechat_attachment', array('uniacid' => $_W['uniacid'], 'id' => $id));
    if (empty($media)) {
        iajax(1, '素材不存在', '');
    }
    $group = $group > 0 ? $group : -1;
    $account_api = WeAccount::create();
    $result = $account_api->fansSendAll($group, $type, $media['media_id']);
    if (is_error($result)) {
        iajax(1, $result['message'], '');
    }
    $groups = pdo_get('mc_fans_groups', array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid']));
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
        'uniacid' => $_W['uniacid'],
        'acid' => $_W['acid'],
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
    iajax(0, '发送成功！', '');
}
//延迟群发，将数据保存在Redis
if ($do == "delay_send") {
    $redis = new Redis();
    $redis->connect("121.40.84.207", 6379);
    $redis->auth('weiying123');
    //接受参数
    $group = intval($_GPC['group']);
    $type = trim($_GPC['type']);
    $id = intval($_GPC['id']);
    $uniacid = intval($_GPC['uniacid']);
    $acid = intval($_W['acid']);
    $delay_time = intval($_GPC['delay_time'])*1000;//单位毫秒
    $media = pdo_get('wechat_attachment', array('uniacid' => $uniacid, 'id' => $id));
    if (empty($media)) {
        iajax(1, '素材不存在', '');
    }
    $group = $group > 0 ? $group : -1;
    $arr['group'] = $group;
    $arr['type'] = $type;
    $arr['id'] = $id;
    $arr['uniacid'] = $uniacid;
    $arr['media'] = $media;
    $arr['begin_time'] = time();
    $arr['delay_time'] = $delay_time;
    //存放类型Set
    $redis->sAdd('massTexting', json_encode($arr));
    iajax(0, '操作成功！', '');
}


if ($do == 'display') {
    $type = in_array(trim($_GPC['type']), array('news', 'image', 'voice', 'video')) ? trim($_GPC['type']) : 'news';
    $server = in_array(trim($_GPC['server']), array(MATERIAL_LOCAL, MATERIAL_WEXIN)) ? trim($_GPC['server']) : '';
    $group = mc_fans_groups(true);
    var_dump($group);exit;
    $page_index = max(1, intval($_GPC['page']));
    $page_size = 24;
    $search = addslashes($_GPC['title']);

    if ($type == 'news') {
        $material_news_list = material_news_list($server, $search, array('page_index' => $page_index, 'page_size' => $page_size));
    } else {
        if (empty($server)) {
            $server = MATERIAL_WEXIN;
        }
        $material_news_list = material_list($type, $server, array('page_index' => $page_index, 'page_size' => $page_size));
    }
    $material_list = $material_news_list['material_list'];
    $pager = $material_news_list['page'];
}

if ($do == 'delete') {
    if (isset($_GPC['uniacid'])) {
        $requniacid = intval($_GPC['uniacid']);
        attachment_reset_uniacid($requniacid);
    }

    $material_id = intval($_GPC['material_id']);
    $server = $_GPC['server'] == 'local' ? 'local' : 'wechat';
    $type = trim($_GPC['type']);
    $cron_record = pdo_get('mc_mass_record', array('uniacid' => $_W['uniacid'], 'attach_id' => $material_id), array('id'));
    if (!empty($cron_record)) {
        iajax('-1', '有群发消息未发送，不可删除');
    }
    if ($type == 'news') {
        $result = material_news_delete($material_id);
    } else {
        $result = material_delete($material_id, $server);
    }
    if (is_error($result)) {
        iajax('-1', $result['message']);
    }
    iajax('0', '删除素材成功');
}

if ($do == 'sync') {
    $account_api = WeAccount::create($_W['acid']);
    $pageindex = max(1, $_GPC['pageindex']);
    $type = empty($_GPC['type']) ? 'news' : $_GPC['type'];
    $news_list = $account_api->batchGetMaterial($type, ($pageindex - 1) * 20);
    $wechat_existid = empty($_GPC['wechat_existid']) ? array() : $_GPC['wechat_existid'];
    if ($pageindex == 1) {
        $original_newsid = pdo_getall('wechat_attachment', array('uniacid' => $_W['uniacid'], 'type' => $type, 'model' => 'perm'), array('id'), 'id');
        $original_newsid = array_keys($original_newsid);
        $wechat_existid = material_sync($news_list['item'], array(), $type);
        if ($news_list['total_count'] > 20) {
            $total = ceil($news_list['total_count'] / 20);
            iajax('1', array('type' => $type, 'total' => $total, 'pageindex' => $pageindex + 1, 'wechat_existid' => $wechat_existid, 'original_newsid' => $original_newsid), '');
        }
    } else {
        $wechat_existid = material_sync($news_list['item'], $wechat_existid, $type);
        $total = intval($_GPC['total']);
        $original_newsid = $_GPC['original_newsid'];
        if ($total != $pageindex) {
            iajax('1', array('type' => $type, 'total' => $total, 'pageindex' => $pageindex + 1, 'wechat_existid' => $wechat_existid, 'original_newsid' => $original_newsid), '');
        }
        if (empty($original_newsid)) {
            $original_newsid = array();
        }
        $original_newsid = array_filter($original_newsid, function ($item) {
            return is_int($item);
        });
    }
    $delete_id = array_diff($original_newsid, $wechat_existid);
    if (!empty($delete_id) && is_array($delete_id)) {
        foreach ($delete_id as $id) {
            pdo_delete('wechat_attachment', array('uniacid' => $_W['uniacid'], 'id' => $id));
            pdo_delete('wechat_news', array('uniacid' => $_W['uniacid'], 'attach_id' => $id));
        }
    }
    iajax(0, '更新成功！', '');
}
//var_dump($material_list);exit;
template('platform/material');