<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

load()->model('user');

$dos = array('rank', 'display', 'switch');
$do = in_array($_GPC['do'], $dos)? $do : 'display' ;
$_W['page']['title'] = '公众号列表 - 公众号';

$state = permission_account_user_role($_W['uid'], $_W['uniacid']);
$account_info = permission_user_account_num();
if($do == 'switch') {
	$uniacid = intval($_GPC['uniacid']);
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
	$module_name = trim($_GPC['module_name']);
	$version_id = intval($_GPC['version_id']);
	if (empty($module_name)) {
		$url = url('home/welcome');
	} else {
		$url = url('home/welcome/ext', array('m' => $module_name, 'version_id' => $version_id));
	}
	if($_GPC['ls'] == 'hui'){
	    header('location:'.getenv("HTTP_REFERER"));
	    exit;
	}
	uni_account_switch($uniacid, $url);
}

if ($do == 'rank' && $_W['isajax'] && $_W['ispost']) {
	$uniacid = intval($_GPC['id']);

	$exist = pdo_get('uni_account', array('uniacid' => $uniacid));
	if (empty($exist)) {
		iajax(1, '公众号不存在', '');
	}
	uni_account_rank_top($uniacid);
	iajax(0, '更新成功！', '');
}

if ($do == 'display') {
	$pindex = max(1, intval($_GPC['page']));
	$psize = 8;
	$account_table = table('account');
	$account_table->searchWithType(array(ACCOUNT_TYPE_OFFCIAL_NORMAL, ACCOUNT_TYPE_OFFCIAL_AUTH));
	$account_count = $account_table->searchAccountList();
	$total = count($account_count);
	$account_table->searchWithType(array(ACCOUNT_TYPE_OFFCIAL_NORMAL, ACCOUNT_TYPE_OFFCIAL_AUTH));
	$keyword = trim($_GPC['keyword']);
	if (!empty($keyword)) {
		$account_table->searchWithKeyword($keyword);
	}
	
	$letter = $_GPC['letter'];
	if(isset($letter) && strlen($letter) == 1) {
		$account_table->searchWithLetter($letter);
	}
	$pager = pagination($total, $pindex, $psize);
	$account_table->accountRankOrder();
	$account_table->searchWithPage($pindex, $psize);
	$account_list = $account_table->searchAccountList();
	if (isset($_GPC['tag_id'])) {
		$tag_id = $_GPC['tag_id'];
		$account_list = $account_table->searchWithTag($tag_id);
	}
	$account_list = array_values($account_list);
	$tag1 = pdo_getall('account_tag');
	foreach($account_list as &$account) {
		$account = uni_fetch($account['uniacid']);
		$account_tag_id = pdo_get('account_tag_link',array('uniacid'=>$account['uniacid']),'tag_id');
		if ($account_tag_id) {
			$tag_array = explode(',',$account_tag_id['tag_id']);
			foreach ($tag_array as $k => $v) {
				$tag_name[] = pdo_get('account_tag',array('id'=>$v));
			}
			$account['tag'] = $account_tag_id['tag_id'];
		}else{
			$account['tag'] = '暂无标签';
		}
		$account['role'] = permission_account_user_role($_W['uid'], $account['uniacid']);
		$account['fans_total'] = pdo_getcolumn("mc_mapping_fans", array('uniacid' => $account['uniacid'], 'acid' => $account['acid'], 'follow' => 1), 'count(*)');
		uni_update_week_stat();
		$yesterday = date('Ymd', strtotime('-1 days'));
		$yesterday_stat = pdo_get('stat_fans', array('date' => $yesterday, 'uniacid' => $account['uniacid']));
		$yesterday_stat['new'] = intval($yesterday_stat['new']);
		$yesterday_stat['cancel'] = intval($yesterday_stat['cancel']);
		$yesterday_stat['jing_num'] = intval($yesterday_stat['new']) - intval($yesterday_stat['cancel']);
		$yesterday_stat['cumulate'] = intval($yesterday_stat['cumulate']);
			$today_stat = pdo_get('stat_fans', array('date' => date('Ymd'), 'uniacid' => $account['uniacid']));
		$today_stat['new'] = intval($today_stat['new']);
		$today_stat['cancel'] = intval($today_stat['cancel']);
		$today_stat['jing_num'] = $today_stat['new'] - $today_stat['cancel'];
		$account['cumulate'] = intval($today_stat['jing_num']) + $yesterday_stat['cumulate'];
	}
	
	if ($_W['ispost']) {
		iajax(0, $account_list);
	}
	
}
template('account/display');








