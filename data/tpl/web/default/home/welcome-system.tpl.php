<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<!--系统管理首页-->
<!-- <div class="welcome-container js-system-welcome" ng-controller="WelcomeCtrl" ng-cloak>
	<div class="ad-img we7-margin-bottom">
		<a ng-href="{{ad.url}}" target="_blank" ng-repeat="ad in ads"><img ng-src="{{ad.src}}" alt="" class="img-responsive" style="margin: 0 auto;"></a>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="panel we7-panel account-stat">
				<div class="panel-heading">微信应用模块</div>
				<div class="panel-body we7-padding-vertical">
					<div class="col-sm-4 text-center">
						<div class="title">未安装应用</div>
						<div class="num">
							<a href="<?php  echo url('module/manage-system/not_installed', array('account_type' => 1))?>" class="color-default">{{account_uninstall_modules_nums}}</a>
						</div>
					</div>
					<div class="col-sm-4 text-center">
						<div class="title">可升级应用</div>
						<div class="num">
						{{upgrade_module_nums.account_upgrade_module_nums}}
						</div>
					</div>
					<div class="col-sm-4 text-center">
						<div class="title">应用总数</div>
						<div class="num">
							<a href="<?php  echo url('module/manage-system/installed', array('account_type' => 1))?>" class="color-default">{{account_modules_total}}</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="panel we7-panel account-stat">
				<div class="panel-heading">小程序应用模块</div>
				<div class="panel-body we7-padding-vertical">
					<div class="col-sm-4 text-center">
						<div class="title">未安装应用</div>
						<div class="num">
							<a href="<?php  echo url('module/manage-system/not_installed', array('account_type' => 4))?>" class="color-default">{{wxapp_uninstall_modules_nums}}</a>
						</div>
					</div>
					<div class="col-sm-4 text-center">
						<div class="title">可升级应用</div>
						<div class="num">
						{{upgrade_module_nums.wxapp_upgrade_module_nums}}
						</div>
					</div>
					<div class="col-sm-4 text-center">
						<div class="title">应用总数</div>
						<div class="num">
							<a href="<?php  echo url('module/manage-system/installed', array('account_type' => 4))?>" class="color-default">{{wxapp_modules_total}}</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="modal-loading" style="width:100%">
			<div style="text-align:center;background-color: transparent;">
				<img style="width:48px; height:48px; margin-top:10px;margin-bottom:10px;" src="resource/images/loading.gif" title="正在努力加载...">
			</div>
		</div>
	</div>
	<div class="panel we7-panel system-update" ng-if="upgrade_show == 1">
		<div class="panel-heading">
			<span class="color-gray pull-right">当前版本：<?php echo IMS_FAMILY;?><?php echo IMS_VERSION;?>（<?php echo IMS_RELEASE_DATE;?>）</span>
			系统更新
		</div>
		<div class="panel-body we7-padding-vertical clearfix">
			<div class="col-sm-3 text-center">
				<div class="title">更新文件</div>
				<div class="num">{{upgrade.file_nums}} 个</div>
			</div>
			<div class="col-sm-3 text-center">
				<div class="title">更新数据库</div>
				<div class="num">{{upgrade.database_nums}} 项</div>
			</div>
			<div class="col-sm-3 text-center">
				<div class="title">更新脚本</div>
				<div class="num">{{upgrade.script_nums}} 项</div>
			</div>
			<div class="col-sm-3 text-center">
				<a href="<?php  echo url('cloud/upgrade');?>" class="btn btn-danger">去更新</a>
			</div>
		</div>
	</div>
	<div class="panel we7-panel database">
		<div class="panel-heading">
			数据库备份提醒
		</div>
		<div class="panel-body clearfix">
			<div class="col-sm-9">
				<span class="day"><?php  echo $backup_days;?></span>
				<span class="color-default">天</span>
				没有备份数据库了,请及时备份!
			</div>
			<div class="col-sm-3 text-center">
				<a class="btn btn-default" href="<?php  echo url('system/database');?>">开始备份</a>
			</div>
		</div>
	</div>
	<div class="panel we7-panel apply-list">
		<div class="panel-heading">
			<span class="pull-right">
				<a href="<?php  echo url('module/manage-system', array('account_type' => 1))?>" class="color-default">查看更多公众号应用</a>
				<span class="we7-padding-horizontal inline-block color-gray">|</span>
				<a href="<?php  echo url('module/manage-system', array('account_type' => 4))?>" class="color-default">查看更多小程序应用</a>
			</span>
			可升级应用
		</div>
		<div class="panel-body">
			<a href="{{module.link}}" target="_blank" class="apply-item" ng-repeat="module in upgrade_module_list">
				<img src="{{module.logo}}" class="apply-img"/>
				<span class="text-over">{{module.title|limitTo:4}}</span>
				<span class="color-red">升级</span>
			</a>
			<div class="text-center" ng-if="upgrade_modules_show == 0">
				没有可升级的应用
			</div>
		</div>
	</div>
	<div class="panel we7-panel apply-list" ng-show="not_installed_show == 1">
		<div class="panel-heading">
			<span class="pull-right">
				<a href="<?php  echo url('module/manage-system/not_installed', array('account_type' => 1))?>" class="color-default" ng-if="not_installed_module.app_count > 4">查看更多公众号应用</a>
				<span class="we7-padding-horizontal inline-block color-gray" ng-if="module.app_count > 4 && module.wxapp_count > 4">|</span>
				<a href="<?php  echo url('module/manage-system/not_installed', array('account_type' => 4))?>" class="color-default" ng-if="not_installed_module.wxapp_count > 4">查看更多小程序应用</a>
			</span>
			未安装应用
		</div>
		<div class="panel-body">
			<a href="{{module.link}}" target="_blank" class="apply-item" ng-repeat="module in not_installed_module.module">
				<img src="{{module.thumb}}" class="apply-img"/>
				<span class="text-over">{{module.title|limitTo:4}}</span>
				<span class="color-red">未安装</span>
			</a>
		</div>
	</div>
</div> -->
<!--end 系统管理首页-->
<!-- <script type="text/javascript">
	$(function(){
		angular.module('systemApp').value('config', {
			notices: <?php echo !empty($notices) ? json_encode($notices) : 'null'?>,
			systemUpgradeUrl : "<?php  echo url('home/welcome/get_system_upgrade')?>",
			upgradeModulesUrl: "<?php  echo url('home/welcome/get_upgrade_modules')?>",
			moduleStatisticsUrl: "<?php  echo url('home/welcome/get_module_statistics')?>"
		});
		angular.bootstrap($('.js-system-welcome'), ['systemApp']);
	});
</script> -->

<div class="welcome-container js-system-welcome" ng-controller="WelcomeCtrl" ng-cloak>
	<div class="ad-img we7-margin-bottom">
		<a ng-href="{{ad.url}}" target="_blank" ng-repeat="ad in ads"><img ng-src="{{ad.src}}" alt="" class="img-responsive" style="margin: 0 auto;"></a>
	</div>
	<div class="row">
		<div class="col-sm-7">
			<div class="panel we7-panel account-stat">
				<div class="panel-heading"><b>统计数据</b></div>
				<div class="panel-body we7-padding-vertical">
					<div class="col-sm-4 text-center">
						<div class="title">总粉丝数</div>
						<div class="num">
							<span><?php  echo $all_fans['cumulate'] ?></span>
						</div>
					</div>
					<div class="col-sm-4 text-center">
						<div class="title">关注数</div>
						<div class="num">
						<?php  echo $all_fans['new'] ?>
						</div>
					</div>
					<div class="col-sm-4 text-center">
						<div class="title">取关数</div>
						<div class="num">
							<span>
							<?php  echo $all_fans['cancel'] ?>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-5">
			<div class="panel we7-panel account-stat">
				<div class="panel-heading"><b>统计增长</b></div>
				<div class="panel-body we7-padding-vertical">
					<div class="col-sm-6 text-center">
						<div class="title">净增数</div>
						<div class="num">
							<span>
								<?php  echo $all_fans['jing_num'] ?>
							</span>
						</div>
					</div>
					<div class="col-sm-6 text-center">
						<div class="title">增长率</div>
						<div class="num">
						<?php  echo $all_fans['new_rate'] ?>%
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- <div id="modal-loading" style="width:100%">
			<div style="text-align:center;background-color: transparent;">
				<img style="width:48px; height:48px; margin-top:10px;margin-bottom:10px;" src="resource/images/loading.gif" title="正在努力加载...">
			</div>
		</div> -->
	</div>
	

	<!-- <div class="panel we7-panel system-update" ng-if="upgrade_show == 1">
		<div class="panel-heading">
			<span class="color-gray pull-right">当前版本：<?php echo IMS_FAMILY;?><?php echo IMS_VERSION;?>（<?php echo IMS_RELEASE_DATE;?>）</span>
			系统更新
		</div>
		<div class="panel-body we7-padding-vertical clearfix">
			<div class="col-sm-3 text-center">
				<div class="title">更新文件</div>
				<div class="num">{{upgrade.file_nums}} 个</div>
			</div>
			<div class="col-sm-3 text-center">
				<div class="title">更新数据库</div>
				<div class="num">{{upgrade.database_nums}} 项</div>
			</div>
			<div class="col-sm-3 text-center">
				<div class="title">更新脚本</div>
				<div class="num">{{upgrade.script_nums}} 项</div>
			</div>
			<div class="col-sm-3 text-center">
				<a href="<?php  echo url('cloud/upgrade');?>" class="btn btn-danger">去更新</a>
			</div>
		</div>
	</div> -->
	<!-- <div class="panel we7-panel database">
		<div class="panel-heading">
			数据库备份提醒
		</div>
		<div class="panel-body clearfix">
			<div class="col-sm-9">
				<span class="day"><?php  echo $backup_days;?></span>
				<span class="color-default">天</span>
				没有备份数据库了,请及时备份!
			</div>
			<div class="col-sm-3 text-center">
				<a class="btn btn-default" href="<?php  echo url('system/database');?>">开始备份</a>
			</div>
		</div>
	</div> -->
	<!-- <div class="panel we7-panel apply-list">
		<div class="api">
			<div class="panel we7-panel" id="js-system-account-analysis" ng-controller="systemAccountAnalysisCtrl" ng-cloak>
				<div class="panel-heading tab">
					<a href="javascript:;"><b>所有公众号流量访问值</b></a>
				</div>
				<div class="panel-body data-view">
					<div class="tab-bar-time clearfrix">
						<div class="btn-group" role="group">
							<button type="button" class="btn btn-default" ng-class="{'active': timeType == 'week'}" ng-click="getAccountApi('week')">周统计</button>
							<button type="button" class="btn btn-default" ng-class="{'active': timeType == 'month'}" ng-click="getAccountApi('month')">月统计</button>
							<div class="btn-group" role="group" ng-class="{'active': timeType == 'daterange'}">
								<button class="btn btn-default daterange daterange-date" we7-date-range-picker ng-model="dateRange"><span>{{dateRange.startDate}} </span>至<span> {{dateRange.endDate}}</span> <i class="fa fa-calendar"></i></button>
							</div>
						</div>
					</div>
					<div class="col-sm-12" id="chart-line" style="height:500px;width:850px;"></div>
				</div>
			</div>
		</div>
	</div> -->
	<div class="panel we7-panel apply-list" ng-show="not_installed_show == 1">
		<div class="panel-heading">
			<b>粉丝前十</b>
		</div>
		<table class="table we7-table table-hover">
			<col width="80px"/>
			<col width="90px"/>
			<col width="150px"/>
			<tr>
				<th>公众号</th>
				<th>运营者</th>
				<th>粉丝数</th>
			</tr>
			<?php  if(is_array($all_fans_sort)) { foreach($all_fans_sort as $index => $item) { ?>
			<tr>
				<td><?php  echo $item['account_name'];?></td>
				<td><?php  echo $item['user']['username'];?></td>
				<td><?php  echo $item['cumulate'];?></td>
			</tr>
			<?php  } } ?>
		</table>
	</div>
	<div class="panel we7-panel apply-list" ng-show="not_installed_show == 1">
		<div class="panel-heading">
			<b>涨粉前十</b>
		</div>
		<table class="table we7-table table-hover">
			<col width="80px"/>
			<col width="90px"/>
			<col width="150px"/>
			<col width="150px"/>
			<tr>
				<th>公众号</th>
				<th>运营者</th>
				<th>净增数</th>
				<th>增长率</th>
			</tr>
			<?php  if(is_array($new_fans_sort)) { foreach($new_fans_sort as $index => $item) { ?>
			<tr>
				<td><?php  echo $item['account_name'];?></td>
				<td><?php  echo $item['user']['username'];?></td>
				<td><?php  echo $item['jing_num'];?></td>
				<td><?php  echo $item['jing_rate'];?>%</td>
			</tr>
			<?php  } } ?>
		</table>
	</div>
	<div class="panel we7-panel apply-list" ng-show="not_installed_show == 1">
		<div class="panel-heading">
			<b>掉粉前十</b>
		</div>
		<table class="table we7-table table-hover">
			<col width="80px"/>
			<col width="90px"/>
			<col width="150px"/>
			<col width="150px"/>
			<tr>
				<th>公众号</th>
				<th>运营者</th>
				<th>净增数</th>
				<th>增长率</th>
			</tr>
			<?php  if(is_array($cancel_fans_sort)) { foreach($cancel_fans_sort as $index => $item) { ?>
			<tr>
				<td><?php  echo $item['account_name'];?></td>
				<td><?php  echo $item['user']['username'];?></td>
				<td><?php  echo $item['jing_num'];?></td>
				<td><?php  echo $item['jing_rate'];?>%</td>
			</tr>
			<?php  } } ?>
		</table>
	</div>
</div>

<script type="text/javascript">
	$(function(){
		angular.module('systemApp').value('config', {
			notices: <?php echo !empty($notices) ? json_encode($notices) : 'null'?>,
			// systemUpgradeUrl : "<?php  echo url('home/welcome/system')?>",
			// upgradeModulesUrl: "<?php  echo url('home/welcome/get_upgrade_modules')?>",
			// moduleStatisticsUrl: "<?php  echo url('home/welcome/get_module_statistics')?>"
			all_fans:"<?php  echo url('home/welcome/system')?>"
		});
		angular.bootstrap($('.js-system-welcome'), ['systemApp']);
	});
	require(['daterangepicker'], function() {
		angular.module('statisticsApp').value('config', {
			'links': {
				'accountApi': "<?php  echo url('statistics/account/get_account_api')?>",
			},
		});
		angular.bootstrap($('#js-system-account-analysis'), ['statisticsApp']);
	});

</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>