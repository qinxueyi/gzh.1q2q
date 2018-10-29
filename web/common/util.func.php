<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

function responseMsg($code, $msg, $data, $count = 0)
{
    $arr = array(
        "code" => $code,
        "msg" => $msg,
        "data" => $data,
        "count" => $count
    );
    return json_encode($arr);
}

//分页
function paging($pages_data_number, $pages_no, $arr, $is_paging = true)
{
    if (!$is_paging) {
        return array(
            "total_count" => 1,
            "pages_count" => 1,
            "result" => $arr
        );
    }

    // 如果$arr没有数据，则直接返回结果
    if (empty($arr)) {
        return array(
            "total_count" => 1,
            "pages_count" => 1,
            "result" => []
        );
    }
    $response = array();
    // 获取数据总数
    $total_count = count($arr);
    $response["total_count"] = $total_count;
    // 求出总共有多少页
    $pages_count = $total_count / $pages_data_number;
    $pages_count = is_int($pages_count) ? $pages_count : (int)($pages_count) + 1;
    $response["pages_count"] = $pages_count;
    //页数大于需要页数
    if ($pages_no > $pages_count) {
        return $response['result'] = [];
    }
    // 根据页码截取数据
    $begin_number = $pages_data_number * ($pages_no - 1) + 1;
    $end_number = $begin_number + $pages_data_number - 1;
    for ($i = 1; $i <= $total_count; $i++) {
        if ($i >= $begin_number && $i <= $end_number) {
            // 数组游标为 $i-1
            $response["result"][] = $arr[$i - 1];
        }
    }
    return $response;
}




