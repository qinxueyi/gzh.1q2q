<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/11/14
 * Time: 10:23
 */

getUserAuthorization();
function getUserAuthorization()
{
    $template_id = "28aiUeK_qEnnPJLtzqMiXef5hSBqu1RnonreOozBkVc";
    $app_id = "wxc405f08368115ce0";
    $redirect_url = "http://wq.com/noLogin/onceSubscriptionMessage.php";
    $redirect_url = urlencode($redirect_url);
    $url = "https://mp.weixin.qq.com/mp/subscribemsg?action=get_confirm&appid=$app_id&scene=1000&template_id=$template_id&redirect_url=$redirect_url&reserved=test#wechat_redirect";
    echo file_get_contents($url);

}

function getUserInfo()
{

}