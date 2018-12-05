<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
$dos = array( 'article-data','post');
$do = in_array($do, $dos) ? $do : 'article-data';
load()->model('article');
if($do == 'article-data'){
    
}
if($_W['isajax'] == true){
    global $_W,$_GPC;
    $pindex = max(1, intval($_GPC['page']));
    if($_GPC['limit']){
        $psize = $_GPC['limit'];
    }else {
        $psize = 10;
    }
    $sql = 'SELECT * FROM '.tablename('article_list').' WHERE id>0 ORDER BY id DESC ';
    $sql .= " limit " . ($pindex - 1) * $psize . ',' . $psize;
    $list = pdo_fetchall($sql);
    $total = pdo_fetchcolumn('SELECT COUNT(id) FROM '.tablename('article_list').' WHERE id>0');
    $data = getdata($list,$total);
    echo json_encode($data);
    exit;
}
function getdata($list,$total){
    return [
        'code' => 200,
        'message'=>'success',
        'total'  => $total,
        'rows'  => $list,
    ];
}
template('article/article-data');