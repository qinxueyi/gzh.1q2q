<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

load()->model('welcome');
load()->model('cloud');
load()->func('communication');
load()->func('db');
load()->model('extension');
load()->model('module');
load()->model('system');
load()->model('user');
load()->model('wxapp');
load()->model('account');
load()->model('message');
load()->model('visit');

$dos = array('platform', 'system', 'ext', 'get_fans_kpi', 'get_last_modules', 'get_system_upgrade', 'get_upgrade_modules', 'get_module_statistics', 'get_ads', 'get_not_installed_modules', 'system_home', 'set_top', 'add_welcome');
$do = in_array($do, $dos) ? $do : 'platform';

    /* $account2 = pdo_get('account',array('isdeleted'=>0));
    $uniacid2 = $account2['uniacid'];
    $_W['uniacid'] = $uniacid2;
    $_W['account'] = uni_fetch($uniacid2);
    $role = permission_account_user_role($_W['uid'], $uniacid2);
    uni_account_save_switch($uniacid2);
    uni_account_switch($uniacid2); */
    
if ($do == 'get_not_installed_modules') {
    $data = array();
    $not_installed_modules = module_get_all_unistalled('uninstalled', false);
    $not_installed_modules = $not_installed_modules['modules']['uninstalled'];
    $data['app_count'] = count($not_installed_modules['app']);
    $data['wxapp_count'] = count($not_installed_modules['wxapp_count']);
    $not_installed_modules['app'] = is_array($not_installed_modules['app']) ? array_slice($not_installed_modules['app'], 0, 4) : array();
    $not_installed_modules['wxapp'] = is_array($not_installed_modules['wxapp']) ? array_slice($not_installed_modules['wxapp'], 0, 4) : array();
    $data['module'] = array_merge($not_installed_modules['app'], $not_installed_modules['wxapp']);
    if (is_array($data['module']) && !empty($data['module'])) {
        foreach ($data['module'] as &$module) {
            if ($module['app_support'] == 2) {
                $module['link'] = url('module/manage-system/not_installed', array('account_type' => ACCOUNT_TYPE_OFFCIAL_NORMAL));
            } else {
                $module['link'] = url('module/manage-system/not_installed', array('account_type' => ACCOUNT_TYPE_APP_NORMAL));
            }
        }
    }
    iajax(0, $data);
}


if ($do == 'ext' && $_GPC['m'] != 'store' && !$_GPC['system_welcome']) {
    if (!empty($_GPC['version_id'])) {
        $version_info = wxapp_version($_GPC['version_id']);
    }
    $account_api = WeAccount::create();
    if (is_error($account_api)) {
        message($account_api['message'], url('account/display'));
    }
    $check_manange = $account_api->checkIntoManage();
    if (is_error($check_manange)) {
        $account_display_url = $account_api->accountDisplayUrl();
        itoast('', $account_display_url);
    }
}


if ($do == 'platform') {
    $last_uniacid = uni_account_last_switch();
    if (empty($last_uniacid)) {
        itoast('', url('account/display'), 'info');
    }
    if (!empty($last_uniacid) && $last_uniacid != $_W['uniacid']) {
        uni_account_switch($last_uniacid, url('home/welcome'));
    }
    define('FRAME', 'account');
    if (empty($_W['account']['endtime']) && !empty($_W['account']['endtime']) && $_W['account']['endtime'] < time()) {
        itoast('公众号已到服务期限，请联系管理员并续费', url('account/manage'), 'info');
    }
    $notices = welcome_notices_get();
    template('home/welcome');
} elseif ($do == 'system') {
    define('FRAME', 'system');
    $_W['page']['title'] = '欢迎页 - 系统管理';
    // if(!$_W['isfounder'] || user_is_vice_founder()){
    // 	header('Location: ' . url('account/manage', array('account_type' => 1)), true);
    // 	exit;
    // }
    // $reductions = system_database_backup();
    // if (!empty($reductions)) {
    // 	$last_backup = array_shift($reductions);
    // 	$last_backup_time = $last_backup['time'];
    // 	$backup_days = welcome_database_backup_days($last_backup_time);
    // } else {
    // 	$backup_days = 0;
    // }

    //获取总粉丝数
    //1.获取所有公众号
    $account = pdo_getall('account', array('isdeleted' => 0));
    $today = array();
    foreach ($account as $k => $v) {
        $account_info = pdo_get('account_wechats', array('uniacid' => $v['uniacid']));
        $account_user = pdo_get('uni_account_users', array('uniacid' => $v['uniacid']));

        uni_update_week_stat();
        $yesterday = date('Ymd', strtotime('-1 days'));
        $yesterday_stat = pdo_get('stat_fans', array('date' => $yesterday, 'uniacid' => $v['uniacid']));
        $yesterday_stat['new'] = intval($yesterday_stat['new']);
        $yesterday_stat['cancel'] = intval($yesterday_stat['cancel']);
        $yesterday_stat['jing_num'] = intval($yesterday_stat['new']) - intval($yesterday_stat['cancel']);
        $yesterday_stat['cumulate'] = intval($yesterday_stat['cumulate']);
        $today_stat = pdo_get('stat_fans', array('date' => date('Ymd'), 'uniacid' => $v['uniacid']));
        $today_stat['account_uniacid'] = $v['uniacid'];//公众号标识
        $today_stat['new'] = intval($today_stat['new']);
        $today_stat['cancel'] = intval($today_stat['cancel']);
        $today_stat['jing_num'] = $today_stat['new'] - $today_stat['cancel'];
        $today_stat['cumulate'] = intval($today_stat['jing_num']) + $yesterday_stat['cumulate'];
        $today_stat['account_name'] = $account_info['name'];//公众号名称

        if ($yesterday_stat['cumulate'] !== 0) {
            $today_stat['jing_rate'] = round($today_stat['jing_num'] / $yesterday_stat['cumulate'] * 100, 2);//净增长率
        } else {
            $today_stat['jing_rate'] = 0;
        }
        $today_stat['user'] = pdo_get('users', array('uid' => $account_user['uid']), 'username');
        $all_fans['cumulate'] += $today_stat['cumulate'];
        $all_fans_yesterday['cumulate'] += $yesterday_stat['cumulate'];
        $all_fans['new'] += $today_stat['new'];
        $all_fans['jing_num'] += $today_stat['jing_num'];
        $all_fans['cancel'] += $today_stat['cancel'];
        if ($all_fans_yesterday['cumulate'] !== 0) {
            $all_fans['new_rate'] = round($all_fans['jing_num'] / $all_fans_yesterday['cumulate'] * 100, 2);
        } else {
            $all_fans_yesterday['cumulate'] = 0;
        }
        $today[] = $today_stat;
    }
    //获取粉丝前十
    array_multisort(array_column($today, 'cumulate'), SORT_DESC, $today);
    $all_fans_sort = array_slice($today, 0, 10);
    //获取涨粉前十
    array_multisort(array_column($today, 'new'), SORT_DESC, $today);
    $new_fans_sort = array_slice($today, 0, 10);
    //获取掉粉前十
    array_multisort(array_column($today, 'cancel'), SORT_DESC, $today);
    $cancel_fans_sort = array_slice($today, 0, 10);

    template('home/welcome-system');
} elseif ($do == 'get_module_statistics') {
    $uninstall_modules = module_get_all_unistalled('uninstalled');
    $account_uninstall_modules_nums = $uninstall_modules['app_count'];
    $wxapp_uninstall_modules_nums = $uninstall_modules['wxapp_count'];

    $account_modules = user_module_by_account_type('account');
    $wxapp_modules = user_module_by_account_type('wxapp');

    $account_modules_total = count($account_modules) + $account_uninstall_modules_nums;
    $wxapp_modules_total = count($wxapp_modules) + $wxapp_uninstall_modules_nums;

    $module_statistics = array(
        'account_uninstall_modules_nums' => $account_uninstall_modules_nums,
        'wxapp_uninstall_modules_nums' => $wxapp_uninstall_modules_nums,
        'account_modules_total' => $account_modules_total,
        'wxapp_modules_total' => $wxapp_modules_total
    );
    iajax(0, $module_statistics, '');
} elseif ($do == 'ext') {
    $modulename = $_GPC['m'];
    if (!empty($modulename)) {
        $_W['current_module'] = module_fetch($modulename);
    }
    define('FRAME', 'account');
    define('IN_MODULE', $modulename);
    if ($_GPC['system_welcome'] && $_W['isfounder']) {
        $frames = buildframes('system_welcome');
    } else {
        $site = WeUtility::createModule($modulename);
        if (!is_error($site)) {
            $method = 'welcomeDisplay';
            if (method_exists($site, $method)) {
                define('FRAME', 'module_welcome');
                $entries = module_entries($modulename, array('menu', 'home', 'profile', 'shortcut', 'cover', 'mine'));
                $site->$method($entries);
                exit;
            }
        }
        $frames = buildframes('account');
    }
    $uni_account_module = table('module')->uniAccountModuleInfo($modulename);
    foreach ($frames['section'] as $secion) {
        foreach ($secion['menu'] as $menu) {
            if (!empty($menu['url'])) {
                if (!empty($uni_account_module['settings']['default_entry']) && !strpos($menu['url'], '&eid=' . $uni_account_module['settings']['default_entry'])) {
                    continue;
                }
                header('Location: ' . $_W['siteroot'] . 'web/' . $menu['url']);
                exit;
            }
        }
    }
    template('home/welcome-ext');
} elseif ($do == 'get_fans_kpi') {
    uni_update_week_stat();
    $yesterday = date('Ymd', strtotime('-1 days'));
    $yesterday_stat = pdo_get('stat_fans', array('date' => $yesterday, 'uniacid' => $_W['uniacid']));
    $yesterday_stat['new'] = intval($yesterday_stat['new']);
    $yesterday_stat['cancel'] = intval($yesterday_stat['cancel']);
    $yesterday_stat['jing_num'] = intval($yesterday_stat['new']) - intval($yesterday_stat['cancel']);
    $yesterday_stat['cumulate'] = intval($yesterday_stat['cumulate']);
    $today_stat = pdo_get('stat_fans', array('date' => date('Ymd'), 'uniacid' => $_W['uniacid']));
    $today_stat['new'] = intval($today_stat['new']);//新增关注
    $today_stat['cancel'] = intval($today_stat['cancel']);//取消关注人数
    $today_stat['jing_num'] = $today_stat['new'] - $today_stat['cancel'];//净增关注
    $today_stat['cumulate'] = intval($today_stat['jing_num']) + $yesterday_stat['cumulate'];//总关注人数
    if ($today_stat['cumulate'] < 0) {
        $today_stat['cumulate'] = 0;
    }
    iajax(0, array('yesterday' => $yesterday_stat, 'today' => $today_stat), '');
} elseif ($do == 'get_last_modules') {
    $last_modules = welcome_get_last_modules();
    if (is_error($last_modules)) {
        iajax(1, $last_modules['message'], '');
    } else {
        iajax(0, $last_modules, '');
    }
} elseif ($do == 'get_system_upgrade') {
    $upgrade = welcome_get_cloud_upgrade();
    iajax(0, $upgrade, '');
} elseif ($do == 'get_upgrade_modules') {
    $account_upgrade_modules = module_upgrade_new('account');
    $account_upgrade_module_nums = count($account_upgrade_modules);
    $wxapp_upgrade_modules = module_upgrade_new('wxapp');
    $wxapp_upgrade_module_nums = count($wxapp_upgrade_modules);

    $account_upgrade_module_list = array_slice($account_upgrade_modules, 0, 4);
    $wxapp_upgrade_module_list = array_slice($wxapp_upgrade_modules, 0, 4);
    $upgrade_module_list = array_merge($account_upgrade_module_list, $wxapp_upgrade_module_list);

    $upgrade_module = array(
        'upgrade_module_list' => $upgrade_module_list,
        'upgrade_module_nums' => array(
            'account_upgrade_module_nums' => $account_upgrade_module_nums,
            'wxapp_upgrade_module_nums' => $wxapp_upgrade_module_nums
        )
    );
    iajax(0, $upgrade_module, '');
} elseif ($do == 'get_ads') {
    $ads = welcome_get_ads();
    if (is_error($ads)) {
        iajax(1, $ads['message']);
    } else {
        iajax(0, $ads);
    }
}

if ($do == 'system_home') {
    $user_info = user_single($_W['uid']);
    $account_num = permission_user_account_num();

    $last_accounts_modules = pdo_getall('system_stat_visit', array('uid' => $_W['uid']), array(), '', array('displayorder desc', 'updatetime desc'), 20);

    if (!empty($last_accounts_modules)) {
        foreach ($last_accounts_modules as &$info) {
            if (!empty($info['uniacid'])) {
                $info['account'] = uni_fetch($info['uniacid']);
            }
            if (!empty($info['modulename'])) {
                $info['account'] = module_fetch($info['modulename']);
                $info['account']['switchurl'] = url('module/display/switch', array('module_name' => $info['modulename']));
                unset($info['account']['type']);
            }
        }
    }

    $types = array(MESSAGE_ACCOUNT_EXPIRE_TYPE, MESSAGE_WECHAT_EXPIRE_TYPE, MESSAGE_WEBAPP_EXPIRE_TYPE, MESSAGE_USER_EXPIRE_TYPE, MESSAGE_WXAPP_MODULE_UPGRADE);
    $messages = pdo_getall('message_notice_log', array('uid' => $_W['uid'], 'type' => $types, 'is_read' => MESSAGE_NOREAD), array(), '', array('id desc'), 10);
    $messages = message_list_detail($messages);
    template('home/welcome-system-home');
}


if ($do == 'set_top') {
    $id = intval($_GPC['id']);
    $system_visit_info = pdo_get('system_stat_visit', array('id' => $id));
    visit_system_update($system_visit_info, true);
    iajax(0, '设置成功', referer());
}

if ($do == 'add_welcome') {
    visit_system_update(array('uid' => $_W['uid'], 'uniacid' => intval($_GPC['uniacid']), 'modulename' => safe_gpc_string($_GPC['module'])), true);
    itoast(0, referer());
}



//二维数组排序 并获取前num条数据
//$array 需要排序的数组
//$sort 排序顺序 SORT_DESC、SORT_ASC
//$num 获取前几个数据
//只试用与php5.6以上
// function doubleArrSort($array,$sort = 'SORT_DESC',$field,$num)
// {
// 	$flag = array();
// 	foreach ($array as $k => $v) {
// 		$flag[] = $v[$field];
// 	}
// 	array_multisort($flag,$sort,$array);
// 	$list = array_slice($array,0,$num);
// 	return $list;
// }