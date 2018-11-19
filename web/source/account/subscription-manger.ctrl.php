<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/11/14
 * Time: 11:23
 * 一次性订阅消息
 */

$do = $_GET['do'];

/**
 * 添加场景
 */
function addScene()
{
    $once_subscription_list_data = array();
    $once_subscription_list_data['scene_name'] = $_POST['scene_name'];
    $once_subscription_list_data['authorization_url'] = $_POST['authorization_url'];
    $once_subscription_list_data['template_id'] = $_POST['template_id'];
    $once_subscription_list_data['reply_title'] = $_POST['reply_title'];
    $once_subscription_list_data['reply_content'] = $_POST['reply_content'];
    $once_subscription_list_data['skip_type'] = $_POST['skip_type'];
    $once_subscription_list_data['color'] = $_POST['color'];
    $once_subscription_list_data['click_url'] = $_POST['click_url'];
    pdo_insert("once_subscription_list", $once_subscription_list_data);

}

/**
 * 修改场景
 */
function updateScenc()
{
    $once_subscription_list_data = array();
    $once_subscription_list_data['scene_name'] = $_POST['scene_name'];
    $once_subscription_list_data['authorization_url'] = $_POST['authorization_url'];
    $once_subscription_list_data['template_id'] = $_POST['template_id'];
    $once_subscription_list_data['reply_title'] = $_POST['reply_title'];
    $once_subscription_list_data['reply_content'] = $_POST['reply_content'];
    $once_subscription_list_data['skip_type'] = $_POST['skip_type'];
    $once_subscription_list_data['color'] = $_POST['color'];
    $once_subscription_list_data['click_url'] = $_POST['click_url'];
    pdo_update("once_subscription_list", $once_subscription_list_data, array("id" => $_POST['id']));
}
