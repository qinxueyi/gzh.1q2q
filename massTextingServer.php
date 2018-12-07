<?php
/**
 * Created by PhpStorm.
 * User: qxy
 * Date: 2018/12/5
 * Time: 13:39
 * 监听9502端口并使用swoole 异步定时器 延迟群发
 */

$http = new swoole_http_server("0.0.0.0", 9502);
$http->on('request', function ($request, $response) {
    $sear = $request->post;
    $arr = $sear['data'];
    $data = unserialize($arr);
   // print_r($data)
    if (!empty($data)) {
        foreach ($data as $value) {
            swoole_timer_after($value['time'], function () use ($value) {
                $group = $value['group'];
                $type = $value['type'];
                $id = $value['id'];
                $uniacid =$value['media']['uniacid'];
                $acid = $value['media']['acid'];
                $media_id = $value['media']['media_id'];
                $url = "group=" . $group . "&type=" . $type . "&id=" . $id . "&uniacid=" . $uniacid . "&acid=" . $acid . "&media_id=" . $media_id;
                $response = file_get_contents("http://1q2q.chaotuozhe.com/noLogin/massTexting.php?" . $url);
                print_r("http://1q2q.chaotuozhe.com/noLogin/massTexting.php?" . $url);
                print_r($response);
            });
        }
    }
});

$http->start();


