<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<style type="text/css">

    .select {
        width: 200px;
        height: 35px;
        border: 1px solid #dddddd;
        border-radius: 4px;
        -webkit-appearance: none;
        margin-left: 20px;
    }

    .select:hover {
        border: 1px solid #277de2;
    }

    .articleTwo :hover {
        border: 1px solid #277de2;
    }

    .articleTwo {
        border: 1px solid #dddddd;
        border-radius: 4px;
        width: 180px;
        height: 33px;
    }

    #button {
        width: 50px;
        height: 33px;
    }

    #export {
        margin-left: 100px;
        width: 50px;
        height: 33px;

    }
    .right-content{
        width:80%;
    }
</style>
<div id="parent">
    选择公众号:
    <select name="uniacid" id="selectWeChat" class="select">
        <option select="select"></option>
        <?php 

           $weChatList=weChatList();
           foreach ($weChatList as $weChat){
           echo '<option value='.$weChat['uniacid'].'>'.$weChat['name'].'</option>';
        }
        ?>
    </select>
    分组:
    <select name="selectAge" id="selectNum2">
        <option selected="selected"></option>
        <option value="1">18-21</option>
        <option value="2">22-25</option>
    </select>
    <input type="text" id="article_time" class="articleTwo" readonly="readonly"/>
    <script>
        layui.use('laydate', function () {
            var laydate = layui.laydate;
            //执行一个laydate实例
            laydate.render({
                elem: '#article_time' //指定元素
                , type: 'date'
                , range: true
                , max: -1
            });
        });
    </script>

    <input type="button" id="button" value="搜索" onclick="search()">
    <input type="button" id="export" value="导出" onclick="exports()">

</div>
<table id="demo" lay-filter="test"></table>

<script>
    layui.use('table', function () {
        search();
    });
</script>
<script>
    function search($isExport) {

        var table = layui.table;
        $uniacid = $("#selectWeChat").val(); //公众号Id
        $time = $("#article_time").val(); //time
        $url = "/web/index.php?c=account&a=fan-list&do=selectList"; //查询粉丝列表
        if ($uniacid != "") {
            $url += "&uniacid=" + $uniacid;
        }
        if ($time != "") {
            $url += "&time=" + $time.substring(13);
        }
        table.render({
            elem: '#demo'
            , url: $url //数据接口
            , page: true //开启分页
            , cols: [[ //表头
                {field: 'name', title: '公众号名称', fixed: 'left'}
                , {field: 'statistics_date', title: '统计日期'}
                , {field: 'add_fan', title: '新增粉丝'}
                , {field: 'cancel_fan', title: '取关粉丝'}
                , {field: 'auto_fan', title: '净增粉丝'}
                , {field: 'cancel_fan_rate', title: '取关率'}
                , {field: 'sum_fan', title: '粉丝数'}
                , {field: 'active_fan', title: '活跃粉丝'}
                , {field: 'active_rate', title: '粉丝活跃度'}
            ]]
        });
    }
    //导出
    function exports() {
        $page = $(".layui-input").val();
        $limit = $('.layui-laypage-limits select').val();
        $uniacid = $("#selectWeChat").val(); //公众号Id
        $time = $("#article_time").val(); //time
        $url = "http://wq.com/index.php?c=account&a=fan-list&do=selectList&export=yes&page=" + $page + "&limit=" + $limit;
        if ($uniacid != "") {
            $url += "&uniacid=" + $uniacid;
        }
        if ($time != "") {
            $url += "&time=" + $time.substring(13);
        }
        window.location.href = $url;

    }

</script>
