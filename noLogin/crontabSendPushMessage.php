<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/11/7
 * Time: 15:16
 */

/**
 *发送超过24H客户消息 10H检查一次
 */
$redis = new Redis();
$redis->connect("121.40.84.207", 6379);
$redis->auth('weiying123');
//取出set需要发送的数据
$pushMessageList = $redis->sMembers('pushMessage');
if (!empty($pushMessageList)) {
    $sendMessage = array();
    foreach ($pushMessageList as $value) {
        //转为数组
        $setSendMessage = json_decode($value, true);
        //消耗的时间
        $consumeTime = (time() - $setSendMessage['begin_time']) * 1000;
        //如果小于24小时可以放入异步定时器中
        if ($setSendMessage['time'] - $consumeTime < 86400000) {
            $setSendMessage['time'] = $setSendMessage['time'] - $consumeTime;
            $sendMessage[] = $setSendMessage;
            //移除set存放的数据
            $redis->srem('pushMessage', $value);
        }

    }
    //发送数据
    if (!empty($sendMessage)) {
        $param['data'] = serialize($sendMessage);
        sendUserMessage("http://gzh.1q2q.com:9501", $param);
    }

}
return true;
function sendUserMessage($url, $post_data = array(), $cookie = array())
{
    $url_arr = parse_url($url);

    $port = isset($url_arr['port']) ? $url_arr['port'] : 80;

    if ($url_arr['scheme'] == 'https') {

        $url_arr['host'] = 'ssl://' . $url_arr['host'];

    }

    $fp = fsockopen($url_arr['host'], $port, $errno, $errstr, 30);

    if (!$fp) return false;

    $getPath = isset($url_arr['path']) ? $url_arr['path'] : '/index.php';

    $getPath .= isset($url_arr['query']) ? '?' . $url_arr['query'] : '';

    $method = 'GET';  //默认get方式

    if (!empty($post_data)) $method = 'POST';

    $header = "$method  $getPath  HTTP/1.1\r\n";

    $header .= "Host: " . $url_arr['host'] . "\r\n";

    if (!empty($cookie)) {  //传递cookie信息

        $_cookie = strval(NULL);

        foreach ($cookie AS $k => $v) {

            $_cookie .= $k . "=" . $v . ";";

        }
        $cookie_str = "Cookie:" . base64_encode($_cookie) . "\r\n";

        $header .= $cookie_str;

    }

    if (!empty($post_data)) {  //传递post数据

        $_post = array();

        foreach ($post_data AS $_k => $_v) {

            $_post[] = $_k . "=" . urlencode($_v);

        }


        $_post = implode('&', $_post);

        $post_str = "Content-Type:application/x-www-form-urlencoded; charset=UTF-8\r\n";

        $post_str .= "Content-Length: " . strlen($_post) . "\r\n";  //数据长度

        $post_str .= "Connection:Close\r\n\r\n";

        $post_str .= $_post;  //传递post数据

        $header .= $post_str;

    } else {

        $header .= "Connection:Close\r\n\r\n";
    }

    fwrite($fp, $header);
    //echo fread($fp,1024);
    usleep(1000); // 这一句也是关键，如果没有这延时，可能在nginx服务器上就无法执行成功
    fclose($fp);
    return true;

}