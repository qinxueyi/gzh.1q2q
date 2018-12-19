<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/10/25
 * Time: 17:21
 */
defined('IN_IA') or exit('Access Denied');
load()->func('communication');
load()->classs('weixin.account');
load()->model('account');

load()->classs('weixin.platform');
$account_api = WeAccount::create();
$token = $account_api->getAccessToken();
return $token;

