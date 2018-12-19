<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="welcome-container" id="js-home-welcome" ng-controller="WelcomeCtrl" ng-cloak>
	<div class="welcome-container">
		<div class="panel we7-panel account-stat">
			<div class="panel-heading">
				今日关键指标/昨日关键指标
				<button style="float:right;" class="layui-btn layui-btn-normal layui-btn-xs"><a href="<?php  echo url('home/welcome/system')?>" style="color:rgb(255,255,255)">统计数据</a></button> 
			</div>
			<div class="panel-body we7-padding-vertical">
				<div class="col-sm-3 text-center">
					<div class="title">新关注</div>
					<div>
						<span class="today" ng-init="0" ng-bind="fans_kpi.today.new"></span>
						<span class="pipe">/</span>
						<span class="yesterday" ng-init="0" ng-bind="fans_kpi.yesterday.new"></span>
					</div>
				</div>
				<div class="col-sm-3 text-center">
					<div class="title">取消关注</div>
					<div>
						<span class="today" ng-init="0" ng-bind="fans_kpi.today.cancel"></span>
						<span class="pipe">/</span>
						<span class="yesterday" ng-init="0" ng-bind="fans_kpi.yesterday.cancel"></span>
					</div>
				</div>
				<div class="col-sm-3 text-center">
					<div class="title">净增关注</div>
					<div>
						<span class="today" ng-init="0" ng-bind="fans_kpi.today.jing_num"></span>
						<span class="pipe">/</span>
						<span class="yesterday" ng-init="0" ng-bind="fans_kpi.yesterday.jing_num"></span>
					</div>
				</div>
				<div class="col-sm-3 text-center">
					<div class="title">累计关注</div>
					<div>
						<span class="today" ng-init="0" ng-bind="fans_kpi.today.cumulate"></span>
					</div>
				</div>
			</div>
			<div class="panel-body we7-padding-vertical">
				<div class="col-sm-3 text-center">
					<div class="title">总阅读人数</div>
					<div>
						<span class="today" ng-init="0" ng-bind="fans_kpi.todayArticle.reader_num"></span>
						<span class="pipe">/</span>
						<span class="yesterday" ng-init="0" ng-bind="fans_kpi.yesterdayArticle.reader_num"></span>
					</div>
				</div>
				<div class="col-sm-3 text-center">
					<div class="title">总分享人数</div>
					<div>
						<span class="today" ng-init="0" ng-bind="fans_kpi.todayArticle.share_user"></span>
						<span class="pipe">/</span>
						<span class="yesterday" ng-init="0" ng-bind="fans_kpi.yesterdayArticle.share_user"></span>
					</div>
				</div>	
				<div class="col-sm-3 text-center">
					<div class="title">头条打开率</div>
					<div>
						<span class="today" ng-init="0" ng-bind="fans_kpi.todayArticle.original_reader_rate"></span>
						<span class="pipe">/</span>
						<span class="yesterday" ng-init="0" ng-bind="fans_kpi.yesterdayArticle.original_reader_rate"></span>
					</div>
				</div>
			</div>
		</div>
		<style type="text/css">
			.tables{padding:10px 0px;}
			.tab span{font-size:13.5px;padding:10px 10px;}
			.tab{padding:5px 10px;width:200px;float: left;}
			.layui-input{float: left;}
			.tab_r{float:right;}
			.clear{clear:both;}
		</style>
		<div class="panel we7-panel account-stat">
			<div class="panel-heading">
				时间段数据分析
			</div>
			<p class="tables">
				<div class="tab"><label>时间：</label><span id="week_seven">近7天</span><span id="mouth_three">近30天</span></div>
				<input type="text" class="layui-input" id="test6" placeholder=" - " style="width:200px;height:30px" value="">
				<!-- <button class="layui-btn layui-btn-normal layui-btn-sm" style="margin-left:10px;">提交</button> -->
<!-- 				<div class="tab_r">共有:<span ng-init="0" ng-bind="fans_kpi.today.new"></span>人新增关注，<span ng-init="0" ng-bind="fans_kpi.today.cancel"></span>人取消关注，净增量-1701人，头条平均打开率1.54%</div> -->
			</p>
			<p class="clear"></p>
        	<div id="speedChart" style="float:left;">
                                    <!-- 为 ECharts 准备一个具备大小（宽高）的 DOM -->
            	<div id="speedChartMain" style="width: 750px; height: 300px;"></div>
        	</div>
        	<div style="float:right">
        		<span>新增数据来源</span><br>
        		<div class="chartbox" id="pie" style="width:500px;height:300px;"></div>
        	</div>
        	
		</div>
		<script type="text/javascript">
			var chart={discount(data){
				var myChart = echarts.init(document.getElementById('speedChartMain'));
				    option = {
				    tooltip: {
				        trigger: 'axis'
				    },
				    legend: {
				        data:['新增关注','累加阅读人数']
				    },
				    grid: {
				        left: '3%',
				        right: '4%',
				        bottom: '3%',
				        containLabel: true
				    },
				    toolbox: {
				        feature: {
				            saveAsImage: {}
				        }
				    },
				    xAxis: {
				        type: 'category',
				        boundaryGap: false,
				        data: data.key
				    },
				    yAxis: {

				        type: 'value'
				    },
				    series: [
				        {
				            name:'新增关注',
				            type:'line',
				            stack: '总量',
				            itemStyle:{  
				                normal:{
				                	color:'rgb(255,184,13)',
				                    lineStyle:{    
				                         color:'rgb(255,184,13)'    
				                     }    
				                 }  
				            },  
				            data:data.stat
				        },
				        {
				            name:'累加阅读人数',
				            type:'line',
				            stack: '总量',
				            itemStyle:{  
				                normal:{  
				                	color:'rgb(227,109,123)',  
				                    lineStyle:{    
				                         color:'rgb(227,109,123)'    
				                    }    
				                }  
		            		},  
				            data:data.article
				        }
				    ]
				};
				myChart.setOption(option);
			},
			annular(data){
				var myChartPie = echarts.init(document.getElementById('pie'));
				var i=0;
				var  colors=['rgb(255,184,13)',
							'rgb(227,109,123)',
							'rgb(75,152,231)',
							'rgb(71,198,140)',
							'rgb(204,146,238)',
							'rgb(181,237,126)',
							'rgb(58,223,247)',
							'rgb(57,224,24)',
							];
				var option1 = {
				    tooltip : {
				        trigger: 'item',
				        formatter: "{a} <br/>{b} : {c} ({d}%)"
				    },
				    legend: {
				        orient : 'vertical',
				        x : 'left',
				        data:[
				        	{name:'公众号搜索',icon:'circle'},
				        	{name:'扫码二维码',icon:'circle'},
				        	{name:'图文右上角菜单',icon:'circle'},
				        	{name:'图文页公众号名称',icon:'circle'},
				        	{name:'名片分享',icon:'circle'},
				        	{name:'支付后关注',icon:'circle'},
				        	{name:'其他合计',icon:'circle'},
				        	{name:'公众号迁移',icon:'circle'},
				        	{name:'暂无数据',icon:'circle'}
				        	]
				    },
				    toolbox: {
				        show : true,
				        feature : {
				            mark : {show: true},
				            dataView : {show: true, readOnly: false},
				            magicType : {
				                show: true, 
				                type: ['pie', 'funnel'],
				                option: {
				                    funnel: {
				                        x: '25%',
				                        width: '50%',
				                        funnelAlign: 'center',
				                        max: 1548
				                    }
				                }
				            },
				            restore : {show: true},
				            saveAsImage : {show: true}
				        }
				    },
				    calculable : true,
				    series : [
				        {
				            name:'用户来源',
				            type:'pie',
				            radius : ['50%', '70%'],
				            itemStyle : {
				                normal : {
				                    label : {
				                        show : false
				                    },
				                    labelLine : {
				                        show : false
				                    },
				                    color:function(){
		                                return colors[i++];                                
		                            }

				                },
				                emphasis : {
				                    label : {
				                        show : true,
				                        position : 'center',
				                        textStyle : {
				                            fontSize : '17',
				                            fontWeight : 'bold'
				                        }
				                    }
				                }
				            },
				            data:data
				            // [
				                // {value:335, name:'公众号搜索'},
				                // {value:310, name:'扫码二维码'},
				                // {value:234, name:'图文右上角菜单'},
				                // {value:135, name:'图文页公众号名称'},
				                // {value:154, name:'名片分享'},
				                // {value:180, name:'支付后关注'},
				                // {value:197, name:'其他合计'}
				            // ]
				        }
				    ]
				};
		        // 使用刚指定的配置项和数据显示图表。

		        myChartPie.setOption(option1);
				},
				GetDateStr(AddDayCount) { 
				   	var dd = new Date();
				   	dd.setDate(dd.getDate()+AddDayCount);//获取AddDayCount天后的日期
				   	var y = dd.getFullYear(); 
				   	var m = (dd.getMonth()+1)<10?"0"+(dd.getMonth()+1):(dd.getMonth()+1);//获取当前月份的日期，不足10补0
				   	var d = dd.getDate()<10?"0"+dd.getDate():dd.getDate();//获取当前几号，不足10补0
				   	return y+"-"+m+"-"+d; 
				}
			}
			layui.use('laydate', function(){
  			var laydate = layui.laydate;
  					  //日期范围
			  	laydate.render({
			    	elem: '#test6',
			    	range: true,
			    	done: function(value, date, endDate){
			    		$.post("<?php  echo url('home/welcome')?>", {'do' : 'get_user_statistics','section':value}, function(data) {
							data = $.parseJSON(data);
							if(data.message.errno==0){
								chart.discount(data.message.message);
							}
						});
			    	}
			  	});
			});

			$('#week_seven').click(function(){
				var week = chart.GetDateStr(-6);
				$.post("<?php  echo url('home/welcome')?>", {'do' : 'get_user_statistics','week':week}, function(data) {
					data = $.parseJSON(data);
					if(data.message.errno==0){
						chart.discount(data.message.message);
					}else{
						util.message(data.message.message, '', 'error');
					}
				});
			});
			$('#mouth_three').click(function(){
				var mouth = chart.GetDateStr(-29);
				$.post("<?php  echo url('home/welcome')?>", {'do' : 'get_user_statistics','mouth':mouth}, function(data) {
					data = $.parseJSON(data);
					if(data.message.errno==0){
						chart.discount(data.message.message);
					}
				});
			});
			$.post("<?php  echo url('home/welcome')?>", {'do' : 'getall_user_statistics',}, function(data) {
					data = $.parseJSON(data);
					if(data.message.errno==0){
						if(data.message.message != false){
							chart.annular(data.message.message);
						}else{
							chart.annular([{value:1, name:'暂无数据'}]);
						}
						
					}
				});	
			$('#week_seven').click();	
		</script>
		<p class="clear"></p>
		<div class="panel we7-panel notice notice-tab">
			<div class="panel-heading">
				<?php  if(permission_check_account_user('see_notice_post')) { ?><a href="./index.php?c=article&a=notice&do=post" class="color-default pull-right we7-margin-left">+新建</a><?php  } ?>
				<a href="./index.php?c=article&a=notice-show" class="color-default pull-right">更多</a>
				<div class="menu">
					<span class="topic active notice">公告</span>
					
				</div>
			</div>
			<ul class="list-group active">
				<li class="list-group-item" ng-repeat="notice in notices" ng-if="notices">
					<a ng-href="{{notice.url}}" class="text-over" target="_blank" ng-style="{'color': notice.style.color, 'font-weight': notice.style.bold ? 'bold' : 'normal'}" ng-bind="notice.title"></a>
					<span class="pull-right color-gray" ng-bind="notice.createtime"></span>
				</li>
				<li class="list-group-item text-center" ng-if="!notices">
					<span>暂无数据</span>
				</li>
			</ul>
			
		</div>	
		
	</div>
</div>
<script>
	angular.module('homeApp').value('config', {
		notices: <?php echo !empty($notices) ? json_encode($notices) : 'null'?>,
	});
	angular.bootstrap($('#js-home-welcome'), ['homeApp']);
	$(function(){
		$('[data-toggle="tooltip"]').tooltip();
		var $topic = $('.welcome-container .notice .menu .topic');
		var $ul = $('.welcome-container .notice ul');

		$topic.mouseover(function(){
			var $this = $(this);
			var $index = $this.index();
			if ($this.is('.we7notice')) {
				$this.parent().prev().hide();
			} else {
				$this.parent().prev().show();
			}
			$topic.removeClass('active');
			$this.addClass('active');
			$ul.removeClass('active');
			$ul.eq($index).addClass('active');
		})
	})
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>