<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<style>
select.we7-select, select {
   height: 18px!important;
    background: none!important;
    -webkit-appearance: menulist;
}
.layui-form-item {
    position: absolute;
    z-index: 998;
    margin-top: 18px;
}
#sousuo{
	margin-top:-4px;
}
.layui-form-item .layui-inline {
    margin-right: 0;
}
</style>
<div class="demoTable layui-form">
  <div class="layui-form-item">
    <div class="layui-inline">
      <label class="layui-form-label">时间</label>
      <div class="layui-input-inline">
        <input name="statistics_date" class="layui-input" id="test31" type="text" placeholder="yyyy-MM-dd">
      </div>
    </div>
    <div class="layui-inline">
      <label class="layui-form-label">条目</label>
      <div class="layui-input-inline">
        <select name="position" lay-verify="required"  lay-filter="test">
	        <option value=""></option>
	        <option value="1">头条</option>
	        <option value="2">第二条</option>
	        <option value="3">第三条</option>
	        <option value="4">第四条</option>
	        <option value="5">第五条</option>
	        <option value="6">第六条</option>
	        <option value="7">第七条</option>
	        <option value="8">第八条</option>
	      </select>
      </div>
    </div>
    <div class="layui-inline">
      <div class="layui-input-inline">
        <button class="layui-btn" id="sousuo">搜索</button>
        <input type="hidden" id="position">
      </div>
    </div>
  </div>
</div>
<table class="layui-hide" id="test" lay-filter="test"></table>
<script>
layui.use(['table','laydate','form'], function(){
	var laydate = layui.laydate;
	var form = layui.form;
	var table = layui.table;
	laydate.render({elem: '#test31',theme: 'grid'});
	  table.render({
	    elem: '#test'
	    ,url:'./index.php?c=article&a=article-data'
	    ,toolbar: true
	    ,title: '用户数据表'
	    ,totalRow: true
	    ,cols: [[
	      {field:'id', title:'ID',width:80,  totalRowText: '合计行'}
	      ,{field:'title', title:'标题'}
	      ,{field:'fan_num', title:'送达人数', sort: true,totalRow: true}
	      ,{field:'reader_num', title:'阅读人数', sort: true, totalRow: true}
	      ,{field:'reader_rate', title:'打开率', sort: true, totalRow: true}
	      ,{field:'share_user', title:'分享数',sort: true,totalRow: true}
	      ,{field:'original_reader_rate', title:'原文阅读率',sort: true,totalRow: true}
	    ]]
	    ,page: true
	    ,response: {
	      statusCode: 200 //重新规定成功的状态码为 200，table 组件默认为 0
	    }
	  ,type:'post'
	  ,id: 'testReload'
	    ,parseData: function(res){ //将原始数据解析成 table 组件所规定的数据
	      return {
	        "code": res.code, //解析接口状态
	        "msg": res.message, //解析提示文本
	        "count": res.total, //解析数据长度
	        "data": res.rows //解析数据列表
	      };
	    }
	  });
	  form.on('select(test)', function(data){
	  		$("#position").val(data.value)  
	  })
	  
	  $('#sousuo').on('click', function(){
		  var date = $('input[name=statistics_date]');
	      var position = $('#position').val();
	      //执行重载
	      table.reload('testReload', {
	    	  where: {
	        	date: date,
	        	position: position,
	        }
	      });
	  });
	});
</script>