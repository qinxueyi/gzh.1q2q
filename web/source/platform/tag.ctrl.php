<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
load()->model('material');
load()->model('mc');
load()->model('account');
load()->model('attachment');
load()->func('file');

$dos = array('display', 'add', 'delete','choose_tag','delete_tag');
$do = in_array($do, $dos) ? $do : 'display';

$_W['page']['title'] = '永久素材-微信素材';

if ($do == 'display') {
	$all_tag = pdo_getall('account_tag');

	$uniacid = $_GPC['__uniacid'];
	$a_tag = pdo_get('account_tag_link',array('uniacid'=>$uniacid));
	$tag = explode(',',$a_tag['tag_id']);
	//去掉当前已选的标签
	foreach ($all_tag as $k => $v) {
		foreach ($tag as $k1 => $v1) {
			if ($v1 == $v['id']) {
				unset($all_tag[$k]);
			}
		}
	}
	$accounts_tag = array();
	foreach ($tag as $k => $v) {
		$tags = pdo_get('account_tag',array('id'=>$v));
		if ($tags) {
			$accounts_tag[] = $tags;
		}
	}
}
if ($do == 'add') {
	$data['tag_name'] = $_GPC['tag_name'];
	$res = pdo_insert('account_tag',$data);
	if ($res) {
		itoast('添加成功', referer(), 'success');
	}

}

if ($do == 'delete_tag') {
	$account_tag = pdo_get('account_tag_link',array('uniacid'=>$_GPC['__uniacid']));
	$tag_id = explode(',',$account_tag['tag_id']);
	foreach ($tag_id as $k => $v) {
		if ($_GPC['id'] == $v) {
			unset($tag_id[$k]);
		}
	}
	$data['tag_id'] = implode(',',$tag_id);
	$res = pdo_update('account_tag_link',$data,array('uniacid'=>$_GPC['__uniacid']));
	if ($res) {
		$tag = pdo_get('account_tag_link',array('uniacid'=>$_GPC['__uniacid']));
		if (empty($tag['tag_id'])) {//删除标签为空的uniacid
			pdo_delete('account_tag_link',array('uniacid'=>$_GPC['__uniacid']));
		}
		itoast('删除成功', referer(), 'success');
	}
}

if($do == 'delete') {
	$id = intval($_GPC['id']);
	$res = pdo_delete('account_tag',array('id'=>$id));
	
	if ($res) {
		itoast('删除成功', referer(), 'success');
	}
}

if ($do == 'choose_tag') {
	//查找当前公众号是否已有标签
	$tag = pdo_get('account_tag_link',array('uniacid'=>$_GPC['__uniacid']));
	if ($tag) {//已有标签 执行编辑
		$tag_id = explode(',',$tag['tag_id']);
		foreach ($_GPC['ids'] as $k => $v) {
			$tag_id[] = $v;
		}
		$data['tag_id'] = implode(',',$tag_id);
		$res = pdo_update('account_tag_link',$data,array('uniacid'=>$_GPC['__uniacid']));
		if ($res) {
			itoast(1);
		}else{
			itoast(2);
		}

	}else{//没有标签 执行添加
		$data['uniacid'] = $_GPC['__uniacid'];
		$data['tag_id'] = implode(',',$_GPC['ids']);
		$res = pdo_insert('account_tag_link',$data);
		if ($res) {
			itoast(1);
		}else{
			itoast(2);
		}
	}

}


template('platform/tag');



