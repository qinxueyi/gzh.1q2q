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
    $sql = 'SELECT * FROM '.tablename('article_list').' WHERE id>0';
    $sql2 = 'SELECT count(id) FROM '.tablename('article_list').' WHERE id>0';
    if($_GPC['date']){
        $sql .= " and statistics_date = '{$_GPC['date']}'";
        $sql2 .= " and statistics_date = '{$_GPC['date']}'";
    }
    if($_GPC['position']){
        $sql .= ' and position ='.$_GPC['position'];
        $sql2 .= ' and position ='.$_GPC['position'];
    }
    $sql .= " ORDER BY id DESC ";
    $total = pdo_fetchcolumn($sql2);
    $sql .= " limit " . ($pindex - 1) * $psize . ',' . $psize;
    $list = pdo_fetchall($sql);
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