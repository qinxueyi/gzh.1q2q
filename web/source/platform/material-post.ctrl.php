<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
load()->web('export');
load()->web('util');
load()->func('file');
load()->model('material');
load()->model('account');
$dos = array('news', 'tomedia', 'addnews', 'upload_material', 'upload_news','getContent_material');
$do = in_array($do, $dos) ? $do : 'news';
permission_check_account_user('platform_material');

$_W['page']['title'] = '新增素材-微信素材';

if ($do == 'tomedia') {
	iajax('0', tomedia($_GPC['url']), '');
}

if ($do == 'news') {
	$type = trim($_GPC['type']);
	$newsid = intval($_GPC['newsid']);
	$upload_limit = material_upload_limit();
	if (empty($newsid)) {
		if ($type == 'reply') {
			$reply_news_id = intval($_GPC['reply_news_id']);
			$news = pdo_get('news_reply', array(
				'id' => $reply_news_id 
			));
			$news_list = pdo_getall('news_reply', array(
				'parent_id' => $news['id'] 
			), array(), '', ' displayorder ASC');
			$news_list = array_merge(array(
				$news 
			), $news_list);
			if (!empty($news_list)) {
				foreach ($news_list as $key => &$row_news) {
					$row_news = array(
						'uniacid' => $_W['uniacid'],
						'thumb_url' => $row_news['thumb'],
						'title' => urldecode($row_news['title']),
						'author' => $row_news['author'],
						'digest' => $row_news['description'],
						'content' => $row_news['content'],
						'url' => $row_news['url'],
						'displayorder' => $key,
						'show_cover_pic' => intval($row_news['incontent']),
						'content_source_url' => $row_news['content_source_url'],
						'imgUrl' => $row_news['imgurl'],
						'attach_id_array' => $row_news['attach_id_array'],
					);
				}
				unset($row_news);
			}
		}
	} else {
		$attachment = material_get($newsid);
		if (is_error($attachment)){
			itoast('图文素材不存在，或已删除', url('platform/material'), 'warning');
		}
		$news_list = $attachment['news'];
	}
	if (!empty($_GPC['new_type'])) {
		$new_type = trim($_GPC['new_type']);
		if (!in_array($new_type, array('reply', 'link'))) {
			$new_type = 'reply';
		}
	}
	if (!empty($news_list)) {
		foreach ($news_list as $key => $row_news) {
			if (empty($row_news['author']) && empty($row_news['content'])) {
				$new_type = 'link';
			} else {
				$new_type = 'reply';
			}
		}
	}
	template('platform/material-post');
}

if ($do == 'addnews') {
	// var_dump($_GPC['news']);
	// die;
	$is_sendto_wechat = trim($_GPC['target']) == 'wechat' ? true : false;
	$attach_id = intval($_GPC['attach_id']);
	if (empty($_GPC['news'])) {
		iajax(- 1, '提交内容参数有误');
	}
	$attach_id = material_news_set($_GPC['news'], $attach_id);
	if (is_error($attach_id)) {
		iajax(-1, $attach_id['message']);
	}
	if (!empty($_GPC['news_rid'])) {
		pdo_update('news_reply', array('media_id' => $attach_id), array('id' => intval($_GPC['news_rid'])));
	}
	if ($is_sendto_wechat) {
		$result = material_local_news_upload($attach_id);
	}
	if (is_error($result)){
		iajax(-1, '提交微信素材失败');
	}else{
		iajax(0, '编辑图文素材成功');
	}
}

if ($do == 'upload_material') {
	$material_id = intval($_GPC['material_id']);
	$result = material_local_upload($material_id);
	if (is_error($result)) {
		iajax(1, $result['message']);
	}
	iajax(0, json_encode($result));
}

if ($do == 'upload_news') {
	$material_id = intval($_GPC['material_id']);
	$result = material_local_news_upload($material_id);
	if (is_error($result)){
		iajax(-1, $result['message']);
	} else {
		iajax(0, '转换成功');
	}
}

if($do == 'getContent_material'){
	$uniacid[':uniacid'] = $_GPC['uniacid'];
	if (empty($_GPC['limit']) || !is_numeric($_GPC['limit'])) {
        $_GPC['limit'] = 10;
    }
    if (empty($_GPC['page']) || !is_numeric($_GPC['page'])) {
        $_GPC['page'] = 1;
    }
	$select_sql = "SELECT  %s FROM " . tablename('wechat_attachment') . " AS a RIGHT JOIN " . tablename('wechat_news') . " AS b ON a.id = b.attach_id WHERE  a.uniacid = :uniacid AND a.type = 'news' AND a.id <> '' AND b.content <> '' %s";
	$list_sql = sprintf($select_sql, "a.id as id, a.filename, a.attachment, a.media_id, a.type, a.model, a.tag, a.createtime, b.displayorder, b.title, b.digest, b.thumb_url, b.thumb_media_id, b.attach_id, b.url,b.id as newid", " ORDER BY a.createtime DESC, b.displayorder ASC LIMIT " . ($_GPC['page']-1) * $_GPC['limit'] . ", " . $_GPC['limit']);
	$total_sql = sprintf($select_sql, "count(*)", '');
	$total = pdo_fetchcolumn($total_sql, $uniacid);
	$news_list = array();
	$totalPage = ceil($total/$_GPC['limit']);
	$news_list['data'] = pdo_fetchall($list_sql, $uniacid);
	$news_list['page'] = $_GPC['page'];
	$news_list['total'] = $total;
	$news_list['totalPage'] = $totalPage;
	if($news_list['data']){
		echo responseMsg(0, "success", $news_list);
	}else{
		echo responseMsg(1, "error", '');
	}

	// pdo_getall('wechat_news','')	
}		
