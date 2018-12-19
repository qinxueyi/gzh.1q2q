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
}
 */

load()->model('mc');
load()->model('menu');
$dos = array('display', 'delete', 'refresh', 'post', 'push', 'copy', 'current_menu','account','editAccount');
$do = in_array($do, $dos) ? $do : 'display';
$_W['page']['title'] = '公众号 - 自定义菜单';

if($_W['isajax']) {
	if(!empty($_GPC['method'])) {
		$do = $_GPC['method'];
	}
}

if($do == 'account'){
	if($_W['ispost']){
		$id = $_GPC['accountId'];
		  $sql = 'SELECT `uniacid` FROM '. tablename('uni_account_menus') . " WHERE  `id` = ".$id." AND `isdeleted`= 0 GROUP BY uniacid"; 
        $result = pdo_fetchall($sql);
        if($result){
        	$data = array();
			foreach ($result as $k => $v) {
				array_push($data,$v['uniacid']);
			}
			 iajax(0, $data);
        }else{
        	iajax(1, '未找到可用公众号');	
        }
	}
}

if($do == 'editAccount'){
	if($_W['ispost']){
		$menu_Id = $_GPC['menuId'];
		$accountId = $_GPC['account'];
		// 获取该模板信息
		$menu = menu_get($menu_Id);
		$data = $menu;
		if (empty($menu)) {
			itoast('菜单不存在或已经删除', url('platform/menu/display'), 'error');
		}
		// 若菜单存在 配置自定义菜单的参数
		$type = $menu['type'];
		// 新增数据是选中的菜单模板信息为准
		//提交菜单
		//判断公众号是不是数组传值
		if(is_array($accountId)){
			// 失败的公众号数组
			$error = array();
			foreach ($accountId as $key => $value) {
			    $res = pdo_get('uni_account_menus',array('title'=>$menu['title'],'uniacid'=>$value));
				
				$name = pdo_get('account_wechats',array('uniacid'=>$value),array('name'));
					// 若菜单已经存在该公众号时，只修改状态
				if($res){
				    $result = menu_push($res['id'],$value);
					if(!$result){
						array_push($error,$name['name']);
					}
				}else{
					// 否则添加新的数据
					unset($data['id']);
					$data['status']=STATUS_OFF;
					$data['uniacid']=$value;
					$id = pdo_insert('uni_account_menus', $data);
					if($id){
						$uid = pdo_insertid();
						$result = menu_push($uid,$value);

						if(!$result){
							array_push($error,$name['name']);
						}
					}else{
						array_push($error,$name['name']);
					}
				}
				
			}	
			if($error){
				$err = implode(",", $error);
				iajax(0, $err.'出现错误其他批量操作完成');	
			}else{
				iajax(0, '批量操作完成');	
			}
		}else{
			iajax(0, '缺少参数');
		}
	}
}

if($do == 'display') {
	set_time_limit(0);

	$type = !empty($_GPC['type']) ? intval($_GPC['type']) : MENU_CURRENTSELF;
	if ($type == MENU_CONDITIONAL) {
		$update_conditional_menu = menu_update_conditional();
		if(is_error($update_conditional_menu)) {
			itoast($update_conditional_menu['message'], '', 'error');
		}
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$condition = " WHERE uniacid = :uniacid";
	$params[':uniacid'] = $_W['uniacid'];
	if (isset($_GPC['keyword'])) {
		$condition .= " AND title LIKE :keyword";
		$params[':keyword'] = "%{$_GPC['keyword']}%";
	}
	if (!empty($type)) {
		$condition .= " AND type = :type";
		$params[':type'] = $type;
	}
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('uni_account_menus') . $condition, $params);
	$data = pdo_fetchall("SELECT * FROM " . tablename('uni_account_menus') . $condition . " ORDER BY type ASC, status DESC,id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
	$pager = pagination($total, $pindex, $psize);
	if ($type == MENU_CONDITIONAL) {
		$names = array(
			'sex' => array('不限', '男', '女'),
			'client_platform_type' => array('不限', '苹果', '安卓', '其他')
		);
		$groups = mc_fans_groups(true);
	}
	template('platform/menu');
}

if ($do == 'push') {
	$id = intval($_GPC['id']);
	$result = menu_push($id);
	if (is_error($result)) {
		iajax(-1, $result['message']);
	} else {
		iajax(0, '修改成功！', referer());
	}
}

if ($do == 'copy') {
	$id = intval($_GPC['id']);
	$menu = menu_get($id);
	if (empty($menu)) {
		itoast('菜单不存在或已经删除', url('platform/menu/display'), 'error');
	}
	if ($menu['type'] != MENU_CONDITIONAL) {
		itoast('该菜单不能复制', url('platform/menu/display'), 'error');
	}
	unset($menu['id'], $menu['menuid']);
	$menu['status'] = STATUS_OFF;
	$menu['title'] = $menu['title'] . '- 复本';
	pdo_insert('uni_account_menus', $menu);
	$id = pdo_insertid();
	itoast('', url('platform/menu/post', array('id' => $id, 'copy' => 1, 'type' => MENU_CONDITIONAL)));
}

if ($do == 'post') {
	$type = intval($_GPC['type']);
	$id = intval($_GPC['id']);
	$copy = intval($_GPC['copy']);
	if (empty($type)) {
		if (!$_W['isajax']) {
			$update_self_menu = menu_update_currentself();
			if (is_error($update_self_menu)) {
				itoast($update_self_menu['message'], '', 'info');
			}
		}
		$type = MENU_CURRENTSELF;
		$default_menu = menu_default();
		$id = intval($default_menu['id']);
	}
	$params = array();
	if ($id > 0) {
		$menu = menu_get($id);
		if (empty($menu)) {
			itoast('菜单不存在或已经删除', url('platform/menu/display'), 'error');
		}
		if (!empty($menu['data'])) {
			$menu['data'] = iunserializer(base64_decode($menu['data']));
			if (!empty($menu['data']['button'])) {
				foreach ($menu['data']['button'] as &$button) {
					if (!empty($button['url'])) {
						$button['url'] = preg_replace('/(.*)redirect_uri=(.*)&response_type(.*)wechat_redirect/', '$2', $button['url']);
						$button['url'] = urldecode($button['url']);
					}
					if (empty($button['sub_button'])) {
						if ($button['type'] == 'media_id') {
							$button['type'] = 'click';
						}
						$button['sub_button'] = array();
					} else {
						$button['sub_button'] = !empty($button['sub_button']['list']) ? $button['sub_button']['list'] : $button['sub_button'];
						foreach ($button['sub_button'] as &$subbutton) {
							if (!empty($subbutton['url'])) {
								$subbutton['url'] = preg_replace('/(.*)redirect_uri=(.*)&response_type(.*)wechat_redirect/', '$2', $subbutton['url']);
								$subbutton['url'] = urldecode($subbutton['url']);
							}
							if ($subbutton['type'] == 'media_id') {
								$subbutton['type'] = 'click';
							}
						}
						unset($subbutton);
					}
				}
				unset($button);
			}
			if (!empty($menu['data']['matchrule']['province'])) {
				$menu['data']['matchrule']['province'] .= '省';
			}
			if (!empty($menu['data']['matchrule']['city'])) {
				$menu['data']['matchrule']['city'] .= '市';
			}
			if (empty($menu['data']['matchrule']['sex'])) {
				$menu['data']['matchrule']['sex'] = 0;
			}
			if (empty($menu['data']['matchrule']['group_id'])) {
				$menu['data']['matchrule']['group_id'] = -1;
			}
			if (empty($menu['data']['matchrule']['client_platform_type'])) {
				$menu['data']['matchrule']['client_platform_type'] = 0;
			}
			if (empty($menu['data']['matchrule']['language'])) {
				$menu['data']['matchrule']['language'] = '';
			}
			$params = $menu['data'];
			$params['title'] = $menu['title'];
			$params['type'] = $menu['type'];
			$params['id'] = $menu['id'];
			$params['status'] = $menu['status'];
		}
		$type = $menu['type'];
	}
	$status = $params['status'];
	$groups = mc_fans_groups();
	$languages = menu_languages();
	if ($_W['isajax'] && $_W['ispost']) {
		set_time_limit(0);
		$_GPC['group']['title'] = trim($_GPC['group']['title']);
		$_GPC['group']['type'] = intval($_GPC['group']['type']) == 0 ? 1 : intval($_GPC['group']['type']);
		$post = $_GPC['group'];
		if (empty($post['title'])) {
			iajax(-1, '请填写菜单组名称！', '');
		}
		$check_title_exist_condition = array(
			'title' => $post['title'],
			'type' => $type,
		);
		if (!empty($id)) {
			$check_title_exist_condition['id <>'] = $id;
		}
		$check_title_exist = pdo_getcolumn('uni_account_menus', $check_title_exist_condition, 'id');
		if (!empty($check_title_exist)) {
			iajax(-1, '菜单组名称已存在，请重新命名！', '');
		}
				if ($post['type'] == MENU_CONDITIONAL && empty($post['matchrule'])) {
			iajax(-1, '请选择菜单显示对象', '');
		}
		if (!empty($post['button'])) {
			foreach ($post['button'] as $key => &$button) {
				$keyword_exist = strexists($button['key'], 'keyword:');
				if ($keyword_exist) {
					$button['key'] = substr($button['key'], 8);
				}
				if (!empty($button['sub_button'])) {
					foreach ($button['sub_button'] as &$subbutton) {
						$sub_keyword_exist = strexists($subbutton['key'], 'keyword:');
						if ($sub_keyword_exist) {
							$subbutton['key'] = substr($subbutton['key'], 8);
						}
					}
					unset($subbutton);
				}
			}
			unset($button);
		}

		$is_conditional = $post['type'] == MENU_CONDITIONAL ? true : false;
		$menu = menu_construct_createmenu_data($post, $is_conditional);
		if ($_GPC['submit_type'] == 'publish' || $is_conditional) {
			$account_api = WeAccount::create();
			$result = $account_api->menuCreate($menu);
		} else {
			$result = true;
		}
		if (is_error($result)) {
			iajax($result['errno'], $result['message']);
		} else {
			if ($post['matchrule']['group_id'] != -1) {
				$menu['matchrule']['groupid'] = $menu['matchrule']['tag_id'];
				unset($menu['matchrule']['tag_id']);
			}
			$menu = json_decode(urldecode(json_encode($menu)), true);

			$insert = array(
				'uniacid' => $_W['uniacid'],
				'menuid' => $result,
				'title' => $post['title'],
				'type' => $post['type'],
				'sex' => intval($menu['matchrule']['sex']),
				'group_id' => isset($menu['matchrule']['group_id']) ? $menu['matchrule']['group_id'] : -1,
				'client_platform_type' => intval($menu['matchrule']['client_platform_type']),
				'area' => trim($menus['matchrule']['country']) . trim($menu['matchrule']['province']) . trim($menu['matchrule']['city']),
				'data' => base64_encode(iserializer($menu)),
				'status' => STATUS_ON,
				'createtime' => TIMESTAMP,
			);

			if ($post['type'] == MENU_CURRENTSELF) {
				if (!empty($id)) {
					pdo_update('uni_account_menus', $insert, array('uniacid' => $_W['uniacid'], 'type' => MENU_CURRENTSELF, 'id' => $id));
				} else {
					pdo_insert('uni_account_menus', $insert);
				}
				iajax(0, '创建菜单成功', url('platform/menu/display'));
			} elseif ($post['type'] == MENU_CONDITIONAL) {
				if ($post['status'] == STATUS_OFF && $post['id'] > 0) {
					pdo_update('uni_account_menus', $insert, array('uniacid' => $_W['uniacid'], 'type' => MENU_CONDITIONAL, 'id' => $post['id']));
				} else {
					pdo_insert('uni_account_menus', $insert);
				}
				iajax(0, '创建菜单成功', url('platform/menu/display', array('type' => MENU_CONDITIONAL)));
			}
		}
	}
	template('platform/menu');
}

if ($do == 'delete') {
	$id = intval($_GPC['id']);
	$result = menu_delete($id);
	if (is_error($result)) {
		itoast($result['message'], referer(), 'error');
	}
	itoast('删除菜单成功', referer(), 'success');
}

if ($do == 'current_menu') {
	$current_menu = $_GPC['current_menu'];
	if ($current_menu['type'] == 'click') {
		if (!empty($current_menu['media_id']) && empty($current_menu['key'])) {
			$wechat_attachment = pdo_get('wechat_attachment', array('media_id' => $current_menu['media_id']));
			if ($wechat_attachment['type'] == 'news') {
				$material = pdo_get('wechat_news', array('uniacid' => $_W['uniacid'], 'attach_id' => $wechat_attachment['id']));
				$material['items'][0]['thumb_url'] =  tomedia($material['thumb_url']);
				$material['items'][0]['title'] = $material['title'];
				$material['items'][0]['digest'] = $material['digest'];
				$material['type'] = 'news';
			} elseif ($wechat_attachment['type'] == 'video') {
				$material['tag'] = iunserializer($wechat_attachment['tag']);
				$material['attach'] = tomedia($wechat_attachment['attachment'], true);
				$material['type'] = 'video';
			} elseif ($wechat_attachment['type'] == 'voice') {
				$material['attach'] = tomedia($wechat_attachment['attachment'], true);
				$material['type'] = 'voice';
				$material['filename'] = $wechat_attachment['filename'];
			} elseif ($wechat_attachment['type'] == 'image') {
				$material['attach'] = tomedia($wechat_attachment['attachment'], true);
				$material['url'] = "url({$material['attach']})";
				$material['type'] = 'image';
			}
		} else {
			$keyword_info = explode(':', $current_menu['key']);
			if ($keyword_info[0] == 'keyword') {
				$rule_info = pdo_get('rule', array('name' => $keyword_info[1]), array('id'));
				$material['child_items'][0] = pdo_get('rule_keyword', array('rid' => $rule_info['id']), array('content'));
				$material['name'] = $keyword_info[1];
				$material['type'] = 'keyword';
			}
		}
	}
	if ($current_menu['type'] != 'click' && $current_menu['type'] != 'view') {
		$material = array();
		if ($current_menu['etype'] == 'module') {
			$module_name = explode(':', $current_menu['key']);
			load()->model('module');
			$material = module_fetch($module_name[1]);
			if ($material['issystem']) {
				$path = '/framework/builtin/' . $material['name'];
			} else {
				$path = '../addons/' . $material['name'];
			}
			$cion = $path . '/icon-custom.jpg';
			if (!file_exists($cion)) {
				$cion = $path . '/icon.jpg';
				if (!file_exists($cion)) {
					$cion = './resource/images/nopic-small.jpg';
				}
			}
			$material['icon'] = $cion;
			$material['type'] = $current_menu['type'];
			$material['etype'] = 'module';
		} elseif ($current_menu['etype'] == 'click') {
			$keyword_info = explode(':', $current_menu['key']);
			if ($keyword_info[0] == 'keyword') {
				$rule_info = pdo_get('rule', array('name' => $keyword_info[1]), array('id'));
				$material['child_items'][0] = pdo_get('rule_keyword', array('rid' => $rule_info['id']), array('content'));
				$material['name'] = $keyword_info[1];
				$material['type'] = $current_menu['type'];
				$material['etype'] = 'click';
			}
		}
	}
	iajax(0, $material);
}
	