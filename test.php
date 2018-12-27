<?php
define('IN_API', true);
require_once './framework/bootstrap.inc.php';
load()->func('pdo');

$conditionModel = array("uniacid =" => 4, "status =" => 1);

$conditionModel['time <'] = 86400000;
$eventList = pdo_getall("event_list", $conditionModel);
print_r($eventList);

//print_r(hexdec($string));
//$redis = new Redis();
//$redis->connect("121.40.84.207", 6379);
//$redis->auth('weiying123');
////取出set需要发送的数据
//$pushMessageList = $redis->sMembers('pushMessage');
//if (!empty($pushMessageList)) {
//    $sendMessage = array();
//    foreach ($pushMessageList as $value) {
//        //转为数组
//        $setSendMessage = json_decode($value, true);
//        //消耗的时间
//        $consumeTime = (time() - $setSendMessage['begin_time']) * 1000;
//        //如果小于24小时可以放入异步定时器中
//        if ($setSendMessage['time'] - $consumeTime < 86400000) {
//            $setSendMessage['time'] = $setSendMessage['time'] - $consumeTime;
//            $sendMessage[] = $setSendMessage;
//            //移除set存放的数据
//            $redis->srem('pushMessage', $value);
//        }
//    }
//    $pushMessageList = $redis->sMembers('pushMessage');
//    print_r($pushMessageList);
//    print_r($sendMessage);


