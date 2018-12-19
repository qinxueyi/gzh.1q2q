<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/18
 * Time: 15:01
 */
require_once './framework/bootstrap.inc.php';
load()->func('pdo');
load()->func('tpl');
load()->web('export');
load()->web('util');
load()->func('file');
load()->model('material');
load()->model('account');
if ($id = $_GET['id']){
    $news_list = pdo_getall('wechat_news',array('id'=>$id));
    $imgUrl = explode(',',$news_list[0]['imgUrl'])[array_rand(explode(',',$news_list[0]['imgUrl']))];//首页图片地址
}
if ($_POST['jump'] == 'different'){
    $attach_id = $_POST['attach_id'];
    $wechat_new = pdo_get('wechat_news',array('id'=>$attach_id));
    echo $wechat_new['url'];
    die();
}
include_once './web/themes/default/platform/random.html';