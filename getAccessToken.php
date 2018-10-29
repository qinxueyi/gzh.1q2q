<?php

define('IN_API', true);
require_once './framework/bootstrap.inc.php';
// load()->classs('account');
// load()->classs('weixin.account');


$account_api = WeAccount::create($_GPC['uniacid']);
$token = $account_api->getAccessToken();
echo $token;

