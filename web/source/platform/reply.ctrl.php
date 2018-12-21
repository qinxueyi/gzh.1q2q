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
 if(empty($role)) {
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
 }else{
 $account = pdo_get('account',array('isdeleted'=>0));
 $uniacid = $account['uniacid'];
 $_W['uniacid'] = $uniacid;
 $_W['account'] = uni_fetch($uniacid);
 $role = permission_account_user_role($_W['uid'], $uniacid);
 if(empty($role)) {
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

load()->model('reply');
load()->model('module');
load()->model('material');

$dos = array('display', 'post', 'delete', 'change_status', 'change_keyword_status', 'getAccessToken', 'delete_event', 'status');
$do = in_array($do, $dos) ? $do : 'display';

$m = empty($_GPC['m']) ? 'keyword' : trim($_GPC['m']);
if (in_array($m, array('keyword', 'special', 'welcome', 'default', 'apply', 'service', 'userapi', 'delay'))) {
    permission_check_account_user('platform_reply');
} else {
    permission_check_account_user('', true, 'reply');
    $modules = uni_modules();
    $_W['current_module'] = $modules[$m];
    define('IN_MODULE', $m);
}
$_W['page']['title'] = '自动回复';
if (empty($m)) {
    itoast('错误访问.', '', '');
}
if ($m == 'special') {
    $mtypes = array(
        'image' => '图片消息',
        'voice' => '语音消息',
        'video' => '视频消息',
        'shortvideo' => '小视频消息',
        'location' => '位置消息',
        'trace' => '上报地理位置',
        'link' => '链接消息',
        'merchant_order' => '微小店消息',
        'ShakearoundUserShake' => '摇一摇:开始摇一摇消息',
        'ShakearoundLotteryBind' => '摇一摇:摇到了红包消息',
        'WifiConnected' => 'Wifi连接成功消息',
        'qr' => '二维码',
    );
}

$sysmods = system_modules();
if (in_array($m, array('custom'))) {
    $site = WeUtility::createModuleSite('reply');
    $site_urls = $site->getTabUrls();
}
if ($do == 'display') {
    //判断参数中是否含有uniacid公众号id
    if ($m == 'keyword' || !in_array($m, $sysmods)) {
        $pindex = max(1, intval($_GPC['page']));
        $psize = 8;
        $cids = $parentcates = $list = array();
        $condition = "uniacid = :uniacid AND module != 'cover' AND module != 'userapi'";
        $params = array();
        $params[':uniacid'] = $_W['uniacid'];
        if (isset($_GPC['type']) && !empty($_GPC['type'])) {
            $type = trim($_GPC['type']);
            if ($type == 'apply') {
                $condition .= " AND module NOT IN ('basic', 'news', 'images', 'voice', 'video', 'music', 'wxcard', 'reply')";
            } else {
                if (!in_array($type, array('basic', 'news', 'images', 'voice', 'video', 'music', 'wxcard'))) {
                    itoast('非法语句！', referer(), 'error');
                }
                $condition .= " AND (FIND_IN_SET('" . $type . "', `containtype`) OR module = :type)";
                $params[':type'] = $type;
            }
        }
        if (!in_array($m, $sysmods)) {
            $condition .= " AND `module` = :type";
            $params[':type'] = $m;
        }
        if (!empty($_GPC['keyword'])) {
            if ($_GPC['search_type'] == 'keyword') {
                $rule_keyword_rid_list = pdo_getall('rule_keyword', array('content LIKE' => "%{$_GPC['keyword']}%"), array('rid'), 'rid', array('id DESC'));
                if (!empty($rule_keyword_rid_list)) {
                    $condition .= " AND id IN (" . implode(",", array_keys($rule_keyword_rid_list)) . ")";
                }
            } else {
                $condition .= " AND `name` LIKE :keyword";
                $params[':keyword'] = "%{$_GPC['keyword']}%";
            }
        }
        if (!empty($_GPC['keyword']) && $_GPC['search_type'] == 'keyword' && empty($rule_keyword_rid_list)) {
            $replies = array();
            $pager = '';
        } else {
            $replies = reply_search($condition, $params, $pindex, $psize, $total);
            $pager = pagination($total, $pindex, $psize);
            if (!empty($replies)) {
                foreach ($replies as &$item) {
                    $condition = '`rid`=:rid';
                    $params = array();
                    $params[':rid'] = $item['id'];
                    $item['keywords'] = reply_keywords_search($condition, $params);
                    $item['allreply'] = reply_contnet_search($item['id']);
                    $entries = module_entries($item['module'], array('rule'), $item['id']);
                    if (!empty($entries)) {
                        $item['options'] = $entries['rule'];
                    }
                    if (!in_array($item['module'], array("basic", "news", "images", "voice", "video", "music", "wxcard", "reply"))) {
                        $item['module_info'] = module_fetch($item['module']);
                    }
                }
                unset($item);
            }
        }
        $entries = module_entries($m, array('rule'));
    }
    if ($m == 'special') {
        $setting = uni_setting_load('default_message', $_W['uniacid']);
        $setting = $setting['default_message'] ? $setting['default_message'] : array();
        $module = uni_modules();
    }
    if ($m == 'default' || $m == 'welcome') {
        $setting = uni_setting($_W['uniacid'], array($m));
        if (!empty($setting[$m])) {
            $rule_keyword_id = pdo_getcolumn('rule_keyword', array('uniacid' => $_W['uniacid'], 'content' => $setting[$m]), 'rid');
            $setting_keyword = $setting[$m];
        }
    }
    if ($m == 'service') {
        $service_list = reply_getall_common_service();
    }
    if ($m == 'userapi') {
        $pindex = max(1, intval($_GPC['page']));//^\[U\++[A-Za-z0-9]+\]$
        $psize = 8;
        $condition = "uniacid = :uniacid AND `module`=:module";
        $params = array();
        $params[':uniacid'] = $_W['uniacid'];
        $params[':module'] = 'userapi';
        if (!empty($_GPC['keyword'])) {
            if ($_GPC['search_type'] == 'keyword') {
                $rule_keyword_rid_list = pdo_getall('rule_keyword', array('content LIKE' => "%{$_GPC['keyword']}%"), array('rid'), 'rid', array('id DESC'));
                if (!empty($rule_keyword_rid_list)) {
                    $condition .= " AND id IN (" . implode(",", array_keys($rule_keyword_rid_list)) . ")";
                }
            } else {
                $condition .= " AND `name` LIKE :keyword";
                $params[':keyword'] = "%{$_GPC['keyword']}%";
            }
        }
        if (!empty($_GPC['keyword']) && $_GPC['search_type'] == 'keyword' && empty($rule_keyword_rid_list)) {
            $replies = array();
            $pager = '';
        } else {
            $replies = reply_search($condition, $params, $pindex, $psize, $total);
            $pager = pagination($total, $pindex, $psize);
            if (!empty($replies)) {
                foreach ($replies as &$item) {
                    $condition = '`rid`=:rid';
                    $params = array();
                    $params[':rid'] = $item['id'];
                    $item['keywords'] = reply_keywords_search($condition, $params);
                }
                unset($item);
            }
        }
    }

    if ($m == 'delay') {//延迟推送
        $data = pdo_getall('event_list', array('uniacid' => $_W['uniacid']),array(),'','sort DESC');
        $result = pdo_get('interaction_type', array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid']));
//        echo "<pre>";
//        var_dump($data);die();
        foreach ($data as $k => $v) {
            $data[$k]['time'] = changeTime($v['time'] / 1000);//转换时间
            if ($v['msgtype'] == 'text') {//文本类型
                $content = json_decode($v['content'], true);
                $data[$k]['content'] = $content['content'];
            } elseif ($v['msgtype'] == 'image') {//图片类型
                $content = json_decode($v['content'], true);
                $material = material_get($content['media_id']);
                $data[$k]['content'] = tomedia($material['attachment']);
            } elseif ($v['msgtype'] == 'news') {//图片类型
                $content = json_decode($v['content'], true);
                $data[$k]['content'] = $content['articles'];
            } elseif ($v['msgtype'] == 'mpnews') {//图文消息
                $content = json_decode($v['content'], true);
                $material = material_get($content['media_id']);
                $data[$k]['content'] = $material['news'][0]['thumb_url'];
                $data[$k]['newsid'] = $material['news'][0]['attach_id'];
            }
        }
    }

    if ($m == 'sort'){ //获取默认排序
        $id = $_GPC['id'];//当前推送id
        $direction = $_GPC['direction'];
        $uniacid = $_GPC['uniacid'];
        $sort = pdo_getcolumn('event_list',array('id' => $id),'sort');//当前排序
        $all_data = pdo_getall('event_list',array('uniacid'=>$uniacid));
        $sort_array = [];
        foreach($all_data as $val){
            array_push($sort_array,$val['sort']);
        }
        rsort($sort_array);
        if ($direction == 1){
            //判断是否在第一位
            if ($all_data){
                $max = 0;
                foreach ($all_data as $key => $val) {
                    $max = max($max, $val['sort']);
                    $max = (int)$max;
                }
                if ($max == $sort){
                    echo json_encode(array('success'=> 2));
                    die();
                }
            }
            $sort_ind = array_search($sort,$sort_array);//根据值获取下标
            $up_sort= $sort_array[$sort_ind-1];//上一个推送排序
            $up_id = pdo_getcolumn('event_list',array('sort' => $up_sort),'id');//上一个推送id
            $res = pdo_update('event_list',array('sort'=>$up_sort),array('id'=>$id));
            $res_n = pdo_update('event_list',array('sort'=>$sort),array('id'=>$up_id));
        }else{
            //判断是否在第一位
            if ($all_data){
                $min = 0;
                foreach ($all_data as $key => $val) {
                    $min = min($min , $val['boxnum']);
                    $min = (int)$min;
                }
                if ($min == $sort){
                    echo json_encode(array('success'=> 3));
                    die();
                }
            }
            $sort_ind = array_search($sort,$sort_array);//根据值获取下标
            $down_sort= $sort_array[$sort_ind+1];//上一个推送排序
            $down_id = pdo_getcolumn('event_list',array('sort' => $down_sort),'id');//上一个推送id
            $res = pdo_update('event_list',array('sort'=>$down_sort),array('id'=>$id));
            $res_n = pdo_update('event_list',array('sort'=>$sort),array('id'=>$down_id));
        }
        if ($res && $res_n){
            echo json_encode(array('success'=> 1));
        }
        exit;
    }

    template('platform/reply');

}

if ($do == 'post') {
    if ($m == 'keyword' || $m == 'userapi' || !in_array($m, $sysmods)) {
        $module['title'] = '关键字自动回复';
        if ($_W['isajax'] && $_W['ispost']) {
            $keyword = safe_gpc_string($_GPC['keyword']);
            $sensitive_word = detect_sensitive_word($keyword);
            if (!empty($sensitive_word)) {
                iajax(-2, '含有敏感词:' . $sensitive_word);
            }
            $keyword = preg_replace('/，/', ',', $keyword);
            $keyword_arr = explode(',', $keyword);
            $result = pdo_getall('rule_keyword', array('uniacid' => $_W['uniacid'], 'content IN' => $keyword_arr), array('rid'));
            if (!empty($result)) {
                $keywords = array();
                foreach ($result as $reply) {
                    $keywords[] = $reply['rid'];
                }
                $rids = implode($keywords, ',');
                $sql = "SELECT `id`, `name` FROM " . tablename('rule') . " WHERE `id` IN ($rids)";
                $rules = pdo_fetchall($sql);
                iajax(-1, $rules, '');
            }
            iajax(0, '');
        }
        $rid = intval($_GPC['rid']);
        if (!empty($rid)) {
            $reply = reply_single($rid);
            if (empty($reply) || $reply['uniacid'] != $_W['uniacid']) {
                itoast('抱歉，您操作的规则不在存或是已经被删除！', url('platform/reply', array('m' => $m)), 'error');
            }
            if (!empty($reply['keywords'])) {
                foreach ($reply['keywords'] as &$keyword) {
                    $keyword = array_elements(array('type', 'content'), $keyword);
                }
                unset($keyword);
            }
        }
        if (checksubmit('submit')) {
            $keywords = @json_decode(htmlspecialchars_decode($_GPC['keywords']), true);

            if (empty($keywords)) {
                itoast('必须填写有效的触发关键字.');
            }
            $rulename = trim($_GPC['rulename']);
            $containtype = '';
            $_GPC['reply'] = (array)$_GPC['reply'];
            foreach ($_GPC['reply'] as $replykey => $replyval) {
                if (!empty($replyval)) {
                    $type = substr($replykey, 6);
                    $containtype .= $type == 'image' ? 'images' : $type . ',';
                }
            }
            if (empty($containtype) && in_array($m, $sysmods) && $m != 'userapi') {
                itoast('必须填写有效的回复内容！');
            }
            $rule = array(
                'uniacid' => $_W['uniacid'],
                'name' => $rulename,
                'module' => $m == 'keyword' ? 'reply' : $m,
                'containtype' => $containtype,
                'status' => $_GPC['status'] == 'true' ? 1 : 0,
                'displayorder' => intval($_GPC['displayorder_rule']),
            );
            if ($_GPC['istop'] == 1) {
                $rule['displayorder'] = 255;
            } else {
                $rule['displayorder'] = range_limit($rule['displayorder'], 0, 254);
            }
            if ($m == 'userapi') {
                $module = WeUtility::createModule('userapi');
            } else {
                $module = WeUtility::createModule('core');
            }
            $msg = $module->fieldsFormValidate();
            $module_info = module_fetch($m);
            if (!empty($module_info) && empty($module_info['issystem'])) {
                $user_module = WeUtility::createModule($m);
                if (empty($user_module)) {
                    itoast('抱歉，模块不存在请重新选择其它模块！', '', '');
                }
                $user_module_error_msg = $user_module->fieldsFormValidate();
            }
            if ((is_string($msg) && trim($msg) != '') || (is_string($user_module_error_msg) && trim($user_module_error_msg) != '')) {
                itoast($msg . $user_module_error_msg, '', '');
            }
            if (!empty($rid)) {
                $result = pdo_update('rule', $rule, array('id' => $rid));
            } else {
                $result = pdo_insert('rule', $rule);
                $rid = pdo_insertid();
            }

            if (!empty($rid)) {
                pdo_delete('rule_keyword', array('rid' => $rid, 'uniacid' => $_W['uniacid']));

                $rowtpl = array(
                    'rid' => $rid,
                    'uniacid' => $_W['uniacid'],
                    'module' => $m == 'keyword' ? 'reply' : $m,
                    'status' => $rule['status'],
                    'displayorder' => $rule['displayorder'],
                );
                foreach ($keywords as $kw) {
                    $krow = $rowtpl;
                    $krow['type'] = range_limit($kw['type'], 1, 4);
                    $krow['content'] = htmlspecialchars($kw['content']);
                    pdo_insert('rule_keyword', $krow);
                }
                $kid = pdo_insertid();
                $module->fieldsFormSubmit($rid);
                if (!empty($module_info) && empty($module_info['issystem'])) {
                    $user_module->fieldsFormSubmit($rid);
                }
                itoast('回复规则保存成功！', url('platform/reply', array('m' => $m)), 'success');
            } else {
                itoast('回复规则保存失败, 请联系网站管理员！', url('platform/reply', array('m' => $m)), 'error');
            }
        }
        template('platform/reply-post');
    }
    if ($m == 'special') {
        $type = trim($_GPC['type']);
        $setting = uni_setting_load('default_message', $_W['uniacid']);
        $setting = $setting['default_message'] ? $setting['default_message'] : array();
        if (checksubmit('submit')) {
            $rule_id = intval(trim(htmlspecialchars_decode($_GPC['reply']['reply_keyword']), "\""));
            $module = trim(htmlspecialchars_decode($_GPC['reply']['reply_module']), "\"");
            if ((empty($rule_id) && empty($module)) || $_GPC['status'] === '0') {
                $setting[$type] = array('type' => '', 'module' => $module, 'keyword' => $rule_id);
                uni_setting_save('default_message', $setting);
                itoast('关闭成功', url('platform/reply', array('m' => 'special')), 'success');
            }
            $reply_type = empty($rule_id) ? 'module' : 'keyword';
            $reply_module = WeUtility::createModule('core');
            $result = $reply_module->fieldsFormValidate();
            if (is_error($result)) {
                itoast($result['message'], '', 'info');
            }

            if ($reply_type == 'module') {
                $setting[$type] = array('type' => 'module', 'module' => $module);
            } else {
                $rule = pdo_get('rule_keyword', array('id' => $rule_id, 'uniacid' => $_W['uniacid']));
                $setting[$type] = array('type' => 'keyword', 'keyword' => $rule['content']);
            }
            uni_setting_save('default_message', $setting);
            itoast('发布成功', url('platform/reply', array('m' => 'special')), 'success');
        }
        if ($setting[$type]['type'] == 'module') {
            $rule_id = $setting[$type]['module'];
        } else {
            $rule_id = pdo_getcolumn('rule_keyword', array('uniacid' => $_W['uniacid'], 'content' => $setting[$type]['keyword']), 'rid');
            $setting_keyword = $setting[$type]['keyword'];
        }
        template('platform/specialreply-post');
    }
    if ($m == 'default' || $m == 'welcome') {
        if (checksubmit('submit')) {
            $rule_keyword_id = intval(trim(htmlspecialchars_decode($_GPC['reply']['reply_keyword']), "\""));
            if (!empty($rule_keyword_id)) {
                $rule = pdo_get('rule_keyword', array('id' => $rule_keyword_id, 'uniacid' => $_W['uniacid']));
                $settings = array(
                    $m => $rule['content']
                );
            } else {
                $settings = array($m => '');
            }
            $item = pdo_fetch("SELECT uniacid FROM " . tablename('uni_settings') . " WHERE uniacid=:uniacid", array(':uniacid' => $_W['uniacid']));
            if (!empty($item)) {
                pdo_update('uni_settings', $settings, array('uniacid' => $_W['uniacid']));
            } else {
                $settings['uniacid'] = $_W['uniacid'];
                pdo_insert('uni_settings', $settings);
            }
            cache_delete("unisetting:{$_W['uniacid']}");
            cache_delete('we7:' . $_W['uniacid'] . ':keyword:' . md5($rule['content']));
            itoast('系统回复更新成功！', url('platform/reply', array('m' => $m)), 'success');
        }
    }
    if ($m == 'apply') {
        $module['title'] = '应用关键字';
        $installedmodulelist = uni_modules();
        foreach ($installedmodulelist as $key => &$value) {
            if ($value['type'] == 'system' || in_array($value['name'], $sysmods)) {
                unset($installedmodulelist[$key]);
                continue;
            }
            $value['official'] = empty($value['issystem']) && (strexists($value['author'], 'WeEngine Team') || strexists($value['author'], ''));
        }
        unset($value);
        foreach ($installedmodulelist as $name => $module) {
            if (empty($module['isrulefields']) && $name != "core") {
                continue;
            }
            $module['title_first_pinyin'] = get_first_pinyin($module['title']);
            if ($module['issystem']) {
                $path = '../framework/builtin/' . $module['name'];
            } else {
                $path = '../addons/' . $module['name'];
            }
            $cion = $path . '/icon-custom.jpg';
            if (!file_exists($cion)) {
                $cion = $path . '/icon.jpg';
                if (!file_exists($cion)) {
                    $cion = './resource/images/nopic-small.jpg';
                }
            }
            $module['icon'] = $cion;

            if ($module['enabled'] == 1) {
                $enable_modules[$name] = $module;
            } else {
                $unenable_modules[$name] = $module;
            }
        }
        $current_user_permissions = pdo_getall('users_permission', array('uid' => $_W['user']['uid'], 'uniacid' => $_W['uniacid']), array(), 'type');
        if (!empty($current_user_permissions)) {
            $current_user_permission_types = array_keys($current_user_permissions);
        }
        $moudles = true;
        template('platform/reply-post');
    }

    if ($m == 'delay') {//延迟推送
        $data['time'] = (intval($_GPC['delay-hour']) * 3600 + intval($_GPC['delay-minute']) * 60 + intval($_GPC['delay-second'])) * 1000;
        $data['uniacid'] = $_GPC['__uniacid'];
        $res['type'] = $_GPC['type'];
        $res['uniacid'] = $data['uniacid'];
        $res['acid'] = $_W['acid'];

        $result = pdo_get('interaction_type', array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid']));
        if ($result) {
            $result = pdo_update('interaction_type', array('type' => $res['type']), array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid']));
        } else {
            $res = pdo_insert('interaction_type', $res);
        }
        //判断数据类型
        if ($_GPC['reply']['reply_basic']) {//text类型
            $data['msgtype'] = 'text';
            $contents = htmlspecialchars_decode($_GPC['reply']['reply_basic']);
            $contents = explode(',', $contents);
            $get_content = array_rand($contents, 1);
            $msg = trim($contents[$get_content], '\"');

            //$msg = htmlspecialchars_decode($msg);

            $preg = preg_match_all("/\[U\+[A-Za-z0-9]+\]/", $content, $arr);
            foreach ($arr[0] as $k => $v) {
                $str = emoji($v);
                $res = preg_replace("/\[U\+[A-Za-z0-9]+\]/", $str, $content, 1);
                $content = $res;
            }
            if (!strpos($_GPC['reply']['reply_basic'], '[U+'))
            {
                $data['content'] = urldecode(json_encode(array('content' =>urlencode($msg))));
            }else{
                $data['content'] = urldecode(json_encode(array('content' => urlencode($res))));
            }
        } elseif ($_GPC['reply']['reply_news']) {
            $contents = htmlspecialchars_decode($_GPC['reply']['reply_news']);
            $contents = json_decode('[' . $contents . ']', true);
            $get_content = array_rand($contents, 1);
            $content = $contents[$get_content];
            if ($content['mediaid']) {
                $data['msgtype'] = 'mpnews';//mpnews类型
                $data['content'] = json_encode(array('media_id' => $content['mediaid']));
            } else {
                $data['msgtype'] = 'news';//news类型
                $wechat_news = pdo_get('wechat_news', array('attach_id' => $content['attach_id'], 'displayorder' => $content['displayorder']));
                $news['title'] = $wechat_news['title'];
                $news['description'] = $wechat_news['description'];
                $news['url'] = $wechat_news['content_source_url'];
                $news['picurl'] = $wechat_news['thumb_url'];
                $new['articles'][] = $news;
                $data['content'] = json_encode($new);
            }
        } elseif ($_GPC['reply']['reply_image']) {//image类型
            $data['msgtype'] = 'image';
            $contents = explode(',', htmlspecialchars_decode($_GPC['reply']['reply_image']));
            $get_content = array_rand($contents, 1);
            $content = trim($contents[$get_content], '\"');
            $data['content'] = json_encode(array('media_id' => $content));

        } elseif ($_GPC['reply']['reply_voice']) {//voice类型
            $data['msgtype'] = 'voice';
            $contents = htmlspecialchars_decode($_GPC['reply']['reply_voice']);
            $contents = explode(',', $contents);
            $get_content = array_rand($contents, 1);
            $content = trim($contents[$get_content], '\"');
            $data['content'] = json_encode(array('media_id' => $content));

        } elseif ($_GPC['reply']['reply_video']) {//vedio类型
            $data['msgtype'] = 'video';
        }
        $all_data = pdo_getall('event_list',array('uniacid'=>$data['uniacid']));
        //获取sort字段当前当前公众号最大值
        if ($all_data){
            $max = 0;
            foreach ($all_data as $key => $val) {
                $max = max($max, $val['sort']);
                $max = (int)$max;
            }
        }else{
            $data['sort'] = 1;
        }
        $data['sort'] = $max+1;
        $res = pdo_insert('event_list', $data);
        if ($res) {
            itoast('发布成功!', referer(), 'success');
        }
    }

}

if ($do == 'delete') {
    $rids = $_GPC['rid'];
    if (!is_array($rids)) {
        $rids = array($rids);
    }
    if (empty($rids)) {
        itoast('非法访问.', '', '');
    }
    foreach ($rids as $rid) {
        $rid = intval($rid);
        $reply = reply_single($rid);
        if (empty($reply) || $reply['uniacid'] != $_W['uniacid']) {
            itoast('抱歉，您操作的规则不在存或是已经被删除！', url('platform/reply', array('m' => $m)), 'error');
        }
        if (pdo_delete('rule', array('id' => $rid))) {
            pdo_delete('rule_keyword', array('rid' => $rid));
            if (!in_array($m, $sysmods)) {
                $reply_module = $m;
            } else {
                if ($m == 'userapi') {
                    $reply_module = 'userapi';
                } else {
                    $reply_module = 'reply';
                }
            }
            $module = WeUtility::createModule($reply_module);
            if (method_exists($module, 'ruleDeleted')) {
                $module->ruleDeleted($rid);
            }
        }
    }
    itoast('规则操作成功！', referer(), 'success');
}

if ($do == 'change_status') {
    $m = $_GPC['m'];
    if ($m == 'service') {
        $rid = intval($_GPC['rid']);
        $file = trim($_GPC['file']);
        if ($rid == 0) {
            $rid = reply_insert_without_service($file);
            if (empty($rid)) {
                iajax(1, '参数错误');
            }
        }
        $userapi_config = pdo_getcolumn('uni_account_modules', array('uniacid' => $_W['uniacid'], 'module' => 'userapi'), 'settings');
        $config = iunserializer($userapi_config);
        $config[$rid] = isset($config[$rid]) && $config[$rid] ? false : true;
        $module_api = WeUtility::createModule('userapi');
        $module_api->saveSettings($config);
        iajax(0, '');
    } else {
        $type = trim($_GPC['type']);
        $setting = uni_setting_load('default_message', $_W['uniacid']);
        $setting = $setting['default_message'] ? $setting['default_message'] : array();
        if (empty($setting[$type]['type'])) {
            if (!empty($setting[$type]['keyword'])) {
                $setting[$type]['type'] = 'keyword';
            }
            if (!empty($setting[$type]['module'])) {
                $setting[$type]['type'] = 'module';
            }
            if (empty($setting[$type]['type'])) {
                iajax(1, '请先设置回复内容', '');
            }
        } else {
            $setting[$type]['type'] = '';
        }
        $result = uni_setting_save('default_message', $setting);
        if ($result) {
            iajax(0, '更新成功！');
        }
    }
}

if ($do == 'change_keyword_status') {

    $id = intval($_GPC['id']);
    $result = pdo_get('rule', array('id' => $id), array('status'));
    if (!empty($result)) {
        $rule = $rule_keyword = false;
        if ($result['status'] == 1) {
            $rule = pdo_update('rule', array('status' => 0), array('id' => $id));
            $rule_keyword = pdo_update('rule_keyword', array('status' => 0), array('uniacid' => $_W['uniacid'], 'rid' => $id));
        } else {
            $rule = pdo_update('rule', array('status' => 1), array('id' => $id));
            $rule_keyword = pdo_update('rule_keyword', array('status' => 1), array('uniacid' => $_W['uniacid'], 'rid' => $id));
        }
        if ($rule && $rule_keyword) {
            iajax(0, '更新成功！', '');
        } else {
            iajax(-1, '更新失败！', '');
        }
    }
    iajax(-1, '更新失败！', '');
}


if ($do == 'getAccessToken') {
    $account_api = WeAccount::create();
    $token = $account_api->getAccessToken();
    return $token;
}

if ($do == 'delete_event') {
    $id = $_GPC['id'];
    $res = pdo_delete('event_list', array('id' => $id));
    if ($res) {
        itoast('删除成功!', referer(), 'success');
    }
}


if ($do == 'status') {
    $id = $_GPC['id'];
    $event = pdo_get('event_list', array('id' => $id));
    if ($event['status'] == 1) {//当前是启用状态 需要禁用
        $data['status'] = 0;
        $res = pdo_update('event_list', $data, array('id' => $id));
    } else {//当前是禁用状态 需要启用
        $data['status'] = 1;
        $res = pdo_update('event_list', $data, array('id' => $id));
    }
    if ($res) {
        itoast('修改状态成功!', referer(), 'success');
    }
}


//转换时间函数
function changeTime($time)
{
    $h = floor($time / 3600);
    $m = floor((($time % (3600 * 24)) % 3600) / 60);
    $s = floor(((($time % (3600 * 24)) % 3600) % 60));
    if ($h > '0') {
        $str = $h . '小时' . $m . '分' . $s . '秒';
    } else {
        if ($m != '0') {
            $str = $m . '分钟' . $s . '秒';
        } else {
            $str = $s . '秒';
        }
    }

    return $str;
}


//字节转Emoji表情
function bytes_to_emoji($cp)
{
    if ($cp > 0x10000) {       # 4 bytes
        return chr(0xF0 | (($cp & 0x1C0000) >> 18)) . chr(0x80 | (($cp & 0x3F000) >> 12)) . chr(0x80 | (($cp & 0xFC0) >> 6)) . chr(0x80 | ($cp & 0x3F));
    } else if ($cp > 0x800) {   # 3 bytes
        return chr(0xE0 | (($cp & 0xF000) >> 12)) . chr(0x80 | (($cp & 0xFC0) >> 6)) . chr(0x80 | ($cp & 0x3F));
    } else if ($cp > 0x80) {    # 2 bytes
        return chr(0xC0 | (($cp & 0x7C0) >> 6)) . chr(0x80 | ($cp & 0x3F));
    } else {                    # 1 byte
        return chr($cp);
    }
}


//字符串转字节
function getBytes($string)
{
    $bytes = array();
    for ($i = 0; $i < strlen($string); $i++) {
        $bytes[] = ord($string[$i]);
    }
    return $bytes;
}

function UnicodeEncode($str)
{
    //split word
    preg_match_all('/./u', $str, $matches);

    $unicodeStr = "";
    foreach ($matches[0] as $m) {
        //拼接
        $unicodeStr .= "&#" . base_convert(bin2hex(iconv('UTF-8', "UCS-4", $m)), 16, 10);
    }
    return $unicodeStr;
}

function emoji($data)
{
    $res = ltrim($data, '[');
    $res = rtrim($res, ']');
    $a = str_replace('U+', '0x', $res);//$a的值为0x1F628
    $str = bytes_to_emoji(intval($a, 16));
    return $str;
}