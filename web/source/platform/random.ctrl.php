<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/18
 * Time: 15:01
 */

defined('IN_IA') or exit('Access Denied');
load()->web('export');
load()->web('util');
load()->func('file');
load()->model('material');
load()->model('account');

if ($do = 'random') {
    echo 111;die();
    $info = pdo_get('wechat_news',array('id'=>847),array('attach_id_array','imgUrl'));
    $imgUrl = explode(',',$info['imgUrl'])[array_rand(explode(',',$info['imgUrl']))];//首页图片地址
    if ($_GPC['jump'] == 'different'){
        $wechat_new = pdo_get('wechat_news',array('id'=>$_GPC['attach_id']));
        echo $wechat_new['url'];
        die();
    }
    template('platform/random');
}
