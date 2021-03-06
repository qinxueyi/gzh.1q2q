<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="panel panel-cut" id="js-account-display" ng-controller="AccountDisplay" ng-cloak>
	<div class="panel-heading">
		<span class="panel-heading-left"><i class="wi wi-wechat" style="font-size: 24px; margin-right: 10px; vertical-align:middle;"></i>公众号列表</span>
		<!-- <div class="font-default pull-right">
			
			<?php  if(!empty($account_info['uniacid_limit']) && (!empty($account_info['founder_uniacid_limit']) && $_W['user']['owner_uid'] || empty($_W['user']['owner_uid'])) || $_W['isfounder'] && !user_is_vice_founder()) { ?>
				<a href="./index.php?c=account&a=post-step" class="color-default"><i class="fa fa-plus"></i>新增公众号</a>
				<?php  } ?>
			
			
			<?php  if($state == ACCOUNT_MANAGE_NAME_FOUNDER || $state == ACCOUNT_MANAGE_NAME_MANAGER) { ?>
			<a href="<?php  echo url('account/manage', array('account_type' => ACCOUNT_TYPE_OFFCIAL_NORMAL))?>" class="color-default"><i class="wi wi-wechatstatistics"></i>公众号管理</a>
			<?php  } ?>
		</div> -->
	</div>
	<div class="panel-body" >
		<!-- <?php  if(!$_W['isfounder'] && !empty($account_info['uniacid_limit'])) { ?>
			<div class="alert alert-warning hidden">
				温馨提示：
				<i class="fa fa-info-circle"></i>
				Hi，<span class="text-strong"><?php  echo $_W['username'];?></span>，您所在的会员组： <span class="text-strong"><?php  echo $account_info['group_name'];?></span>
				账号有效期限：<span class="text-strong"><?php  echo date('Y-m-d', $_W['user']['starttime'])?> ~~ <?php  if(empty($_W['user']['endtime'])) { ?>无限制<?php  } else { ?><?php  echo date('Y-m-d', $_W['user']['endtime'])?><?php  } ?></span>，
				可创建 <span class="text-strong"><?php  echo $account_info['maxaccount'];?> </span>个公众号，已创建<span class="text-strong"> <?php  echo $account_info['uniacid_num'];?> </span>个，还可创建 <span class="text-strong"><?php  echo $account_info['uniacid_limit'];?> </span>个公众号。
			</div>
		<?php  } ?>
		<div class="cut-header" ng-if="searchShow">
			<form action="./index.php" method="get">
				<input type="hidden" name="c" value="account">
				<input type="hidden" name="a" value="display">
				
				<input type="text" name="letter" ng-model="activeLetter" ng-style="{'display': 'none'}">
				<div class="cut-search">
					<div class="input-group pull-left">
						<input class="form-control" name="keyword" value="<?php  echo $_GPC['keyword'];?>" type="text" placeholder="请输入微信公众号名称" >
						<span class="input-group-btn"><button class="btn btn-default button"><i class="fa fa-search"></i></button></span>
					</div>
				</div>
			</form>
		</div>
		<div class="clearfix"></div>
		<ul class="letters-list cut-wechat-letters" ng-if="searchShow">
			<li ng-repeat="letter in alphabet" ng-style="{'background-color': letter == activeLetter ? '#ddd' : 'none'}" ng-class="{'active': letter == activeLetter}" ng-click="searchModule(letter)">
				<a href="javascript:;" ng-bind="letter"></a>
			</li>
		</ul>
		<div class="cut-list clearfix" ng-if="accountList" infinite-scroll='loadMore()' infinite-scroll-disabled='busy' infinite-scroll-distance='0' infinite-scroll-use-document-bottom="true">
			<div class="item" ng-repeat="detail in accountList">
				<div class="content">
					<img ng-src="{{detail.logo}}" class="icon-account" onerror="this.src='./resource/images/nopic-107.png'"/>
					<div class="name" ng-bind="detail.name"></div>
					<div class="type">
						<span ng-if="detail.level == 1">类型：普通订阅号</span>
						<span ng-if="detail.level == 2">类型：普通服务号</span>
						<span ng-if="detail.level == 3">类型：认证订阅号</span>
						<span ng-if="detail.level == 4">类型：认证服务号</span>
					</div>
				</div>
				<div class="mask">
					<a ng-href="{{detail.switchurl}}" class="entry">
						<div>进入公众号 <i class="wi wi-angle-right"></i></div>
					</a>
					<?php  if(!permission_check_account_user('see_user_profile_account_num')) { ?>
					<a ng-href="{{links.welcome}}uniacid={{detail.uniacid}}" onclick="return ajaxopen(this.href);" class="home-show" title="添加到首页常用功能">
						<i class="wi wi-eye"></i>
					</a>
					<?php  } ?>
					<a href="javascript:;" class="stick" ng-click="stick(detail.uniacid)" title="置顶">
						<i class="wi wi-stick-sign"></i>
					</a>
				</div>
			</div>
		</div>
		<ul ng-if="!accountList" style="text-align:center;width:100%"><span ng-if="!accountList">暂无数据</span></ul> -->
		<?php  if(!empty($account_info['uniacid_limit']) && (!empty($account_info['founder_uniacid_limit']) && $_W['user']['owner_uid'] || empty($_W['user']['owner_uid'])) || $_W['isfounder'] && !user_is_vice_founder()) { ?>
		<div class="pull-right">
			<a href="<?php  echo url('account/post-step');?>" class="btn btn-primary we7-padding-horizontal">添加公众号</a>
		</div>
		<?php  } ?>
<table class="table we7-table table-hover vertical-middle table-manage" id="js-system-account-display" ng-controller="SystemAccountDisplay" ng-cloak>
	<col width="120px"/>
	<col width="220px"/>
	<col width="100px"/>
	<tr>
			<div class="dropdown dropdown-toggle we7-dropdown" style="width:400px;">
				<!-- <form action="" method="post">
					<span>搜索:</span>
					<input type="text" name="name" style="outline:none">
					<button type="submit" class="btn btn-default">查询</button>
				</form> -->
				<form action="" class="form-inline  pull-left" method="get">
					<input type="hidden" name="c" value="account">
					<input type="hidden" name="a" value="display">
					<div class="input-group form-group" style="width: 400px;">
						<input type="text" name="keyword" value="<?php  echo $_GPC['keyword'];?>" class="form-control" placeholder="搜索关键字"/>
						<span class="input-group-btn"><button class="btn btn-default"><i class="fa fa-search"></i></button></span>
					</div>
				</form>
			</div>
			<!-- <div class="dropdown dropdown-toggle we7-dropdown">
				<a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					公众号筛选
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu" aria-labelledby="dLabel">
					<li><a href="<?php  echo filter_url('type:all');?>" class="active">全部公众号</a></li>
					<li><a href="<?php  echo filter_url('type:expire');?>" class="active">公众号已到期</a></li>
					<li><a href="<?php  echo filter_url('type:noconnect');?>" class="active">未接入公众号</a></li>
				</ul>
			</div> -->
	</tr>
	<tr style="height:10px;">
		<span style="font-size:10px;">标签筛选:</span>
		<?php  if(is_array($tag1)) { foreach($tag1 as $index => $item) { ?>
			<a href="<?php  echo url('account/display',array('tag_id'=>$item['id']))?>" style="font-size:10px;margin-left:10px;"><?php  echo $item['tag_name'];?></a>
		<?php  } } ?>
	</tr>
	<tr>
		<th colspan="2" class="text-left">帐号</th>
		<th>微信号</th>
		<th>二维码</th>
		<th>粉丝数</th>
		<th>标签</th>
		<th class="text-right">操作</th>
	</tr>
	<!-- <tr class="color-gray" ng-repeat="list in account"> 原来的-->
	<tr class="color-gray" ng-repeat="list in accountList">
		<td class="text-left td-link">
			<?php  if($role_type) { ?>
			<a ng-href="{{links.post}}&acid={{list.acid}}&uniacid={{list.uniacid}}&account_type={{list.type}}"></a>
			<?php  } else { ?>
			<a href="javascript:;">
			<?php  } ?>
				<img ng-src="{{list.logo}}" class="img-responsive icon">
			</a>
		</td>
		<td class="text-left">
			<p class="color-dark" ng-bind="list.name"></p>
			<span class="color-gray" ng-if="list.level == 1">类型：普通订阅号</span>
			<span class="color-gray" ng-if="list.level == 2">类型：普通服务号</span>
			<span class="color-gray" ng-if="list.level == 3">类型：认证订阅号</span>
			<span class="color-gray" ng-if="list.level == 4" title="认证服务号/认证媒体/政府订阅号">类型：认证服务号</span>
			<span class="color-red" ng-if="list.isconnect == 0" data-toggle="tooltip" data-placement="right" title="公众号接入状态显示“未接入”解决方案：进入微信公众平台，依次选择: 开发者中心 -> 修改配置，然后将对应公众号在平台的url和token复制到微信公众平台对应的选项，公众平台会自动进行检测"><i class="wi wi-error-sign"></i>未接入</span>
			<span class="color-green" ng-if="list.isconnect == 1"><i class="wi wi-right-sign"></i>已接入</span>
		</td>
		<td><p class="color-dark" ng-bind="list.account"></td>
		<td><img ng-src="{{list.qrcode}}" class="img-responsive icon"></td>
		<td><p class="color-dark" ng-bind="list.fans_total"></td>
		<td><p class="color-dark" ng-bind="list.tag"></td>
		<td>
			<p ng-bind="list.end"></p>
		</td>
		<td class="vertical-middle table-manage-td">
			<div class="link-group">
				<a ng-href="{{links.switch}}uniacid={{list.uniacid}}">进入公众号</a>
				<?php  if($role_type) { ?>
				<a ng-href="{{links.post}}&acid={{list.acid}}&uniacid={{list.uniacid}}&account_type={{list.type}}" ng-show="list.role == 'manager' || list.role == 'owner' || list.role == 'founder'|| list.role == 'vice_founder'">管理设置</a>
				<?php  } ?>
			</div>
			<?php  if($role_type) { ?>
			<div class="manage-option text-right">
				<a href="{{links.post}}&acid={{list.acid}}&uniacid={{list.uniacid}}&account_type={{list.type}}" ng-show="list.role == 'owner' || list.role == 'founder' || list.role == 'vice_founder'">基础信息</a>
				<a href="{{links.post}}&do=sms&uniacid={{list.uniacid}}&acid={{list.acid}}&account_type={{list.type}}" ng-show="list.role == 'owner' || list.role == 'founder' || list.role == 'vice_founder'">短信信息</a>
				<a href="{{links.postUser}}&do=edit&uniacid={{list.uniacid}}&acid={{list.acid}}&account_type={{list.type}}">使用者管理</a>
				<a href="{{links.post}}&do=modules_tpl&uniacid={{list.uniacid}}&acid={{list.acid}}&account_type={{list.type}}">可用应用模板/模块</a>
				<a ng-href="{{links.del}}&acid={{list.acid}}&uniacid={{list.uniacid}}" ng-show="list.role == 'owner' || list.role == 'founder' || list.role == 'vice_founder'" onclick="if(!confirm('确认放入回收站吗？')) return false;" class="del">停用</a>
			</div>
			<?php  } ?>
		</td>
	</tr>
</table>

	</div>
	<div class="text-right">
	<?php  echo $pager;?>
</div>	
</div>
<script>
	switch_url = "<?php  echo url('account/display/switch')?>";
	console.log(switch_url);
	angular.module('accountApp').value('config', {
		accountList: <?php echo !empty($account_list) ? json_encode($account_list) : 'null'?>,
		links: {
			rank: "<?php  echo url('account/display/rank')?>",
			display: "<?php  echo url('account/display/display')?>",
			welcome: "<?php  echo url('home/welcome/add_welcome')?>",
			post: "<?php  echo url('account/post')?>",
			// postUser: "<?php  echo url('account/post-user')?>",
			del: "<?php  echo url('account/manage/delete')?>",
			post: "<?php  echo url('account/post')?>",
			switch: switch_url,
		},
		// links: {
		// 	del: "<?php  echo url('account/manage/delete')?>",
		// }

		scrollUrl : "<?php  echo url('account/display')?>",
		keyword : "<?php  echo $keyword;?>",
		letter : "<?php  echo $letter;?>"
	});
	angular.bootstrap($('#js-account-display'), ['accountApp']);
</script>

<!-- 后加的 -->


<!-- <div class="we7-page-title">公众号管理</div> -->
<!-- <ul class="we7-page-tab"> -->
	<!-- <li class="active" style="margin-left:30px"><a href="<?php  echo url('account/manage');?>">公众号列表</a></li> -->
		<!-- <?php  if($_W['role'] == ACCOUNT_MANAGE_NAME_OWNER || $_W['role'] == ACCOUNT_MANAGE_NAME_FOUNDER || $_W['role'] == ACCOUNT_MANAGE_NAME_VICE_FOUNDER) { ?>
		<li><a href="<?php  echo url('account/recycle');?>">公众号回收站</a></li>
		<?php  } ?> -->
<!-- </ul> -->
<!-- <div class="clearfix we7-margin-bottom">
	
		<?php  if(!$_W['isfounder'] && !empty($account_info['uniacid_limit']) || user_is_vice_founder()) { ?>
		<div class="alert alert-warning hidden">
			温馨提示：
			<i class="fa fa-info-circle"></i>
			Hi，<span class="text-strong"><?php  echo $_W['username'];?></span>，您所在的会员组： <span class="text-strong"><?php  echo $account_info['group_name'];?></span>，<?php  if(!user_is_vice_founder() && !empty($account_info['vice_group_name'])) { ?> <span class="text-strong"><?php  echo $account_info['vice_group_name'];?>，</span><?php  } ?>
			账号有效期限：<span class="text-strong"><?php  echo date('Y-m-d', $_W['user']['starttime'])?> ~~ <?php  if(empty($_W['user']['endtime'])) { ?>无限制<?php  } else { ?><?php  echo date('Y-m-d', $_W['user']['endtime'])?><?php  } ?></span>，
			可创建 <span class="text-strong"><?php  echo $account_info['maxaccount'];?> </span>个公众号，已创建<span class="text-strong"> <?php  echo $account_info['uniacid_num'];?> </span>个，还可创建 <span class="text-strong"><?php  echo $account_info['uniacid_limit'];?> </span>个公众号。
		</div>
		<?php  } ?>
	
	
	<form action="" class="form-inline  pull-left" method="get">
		<input type="hidden" name="c" value="account">
		<input type="hidden" name="a" value="manage">
		<div class="input-group form-group" style="width: 400px;">
			<input type="text" name="keyword" value="<?php  echo $_GPC['keyword'];?>" class="form-control" placeholder="搜索关键字"/>
			<span class="input-group-btn"><button class="btn btn-default"><i class="fa fa-search"></i></button></span>
		</div>
	</form>
	
	<?php  if(!empty($account_info['uniacid_limit']) && (!empty($account_info['founder_uniacid_limit']) && $_W['user']['owner_uid'] || empty($_W['user']['owner_uid'])) || $_W['isfounder'] && !user_is_vice_founder()) { ?>
	<div class="pull-right">
		<a href="<?php  echo url('account/post-step');?>" class="btn btn-primary we7-padding-horizontal">添加公众号</a>
	</div>
	<?php  } ?>
	
	
</div>
<table class="table we7-table table-hover vertical-middle table-manage" id="js-system-account-display" ng-controller="SystemAccountDisplay" ng-cloak>
	<col width="120px" />
	<col/>
	<col width="200px"/>
	<col width="100px"/>
	<col width="260px" />
	<tr>
		<th colspan="5" class="text-left filter">
			<div class="dropdown dropdown-toggle we7-dropdown">
				<a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					时间排序
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu" aria-labelledby="dLabel">
					<li><a href="<?php  echo filter_url('order:asc');?>" class="active">创建时间正序</a></li>
					<li><a href="<?php  echo filter_url('order:desc');?>">创建时间倒序</a></li>
				</ul>
			</div>
			<div class="dropdown dropdown-toggle we7-dropdown">
				<a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					公众号筛选
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu" aria-labelledby="dLabel">
					<li><a href="<?php  echo filter_url('type:all');?>" class="active">全部公众号</a></li>
					<li><a href="<?php  echo filter_url('type:expire');?>" class="active">公众号已到期</a></li>
					<li><a href="<?php  echo filter_url('type:noconnect');?>" class="active">未接入公众号</a></li>
				</ul>
			</div>
		</th>
	</tr>
	<tr>
		<th colspan="2" class="text-left">帐号</th>
		<th>有效期</th>
		<th>短信数(条)</th>
		<th class="text-right">操作</th>
	</tr>
	<tr class="color-gray" ng-repeat="list in lists">
		<td class="text-left td-link">
			<?php  if($role_type) { ?>
			<a ng-href="{{links.post}}&acid={{list.acid}}&uniacid={{list.uniacid}}&account_type={{list.type}}"></a>
			<?php  } else { ?>
			<a href="javascript:;">
			<?php  } ?>
				<img ng-src="{{list.logo}}" class="img-responsive icon">
			</a>
		</td>
		<td class="text-left">
			<p class="color-dark" ng-bind="list.name"></p>
			<span class="color-gray" ng-if="list.level == 1">类型：普通订阅号</span>
			<span class="color-gray" ng-if="list.level == 2">类型：普通服务号</span>
			<span class="color-gray" ng-if="list.level == 3">类型：认证订阅号</span>
			<span class="color-gray" ng-if="list.level == 4" title="认证服务号/认证媒体/政府订阅号">类型：认证服务号</span>
			<span class="color-red" ng-if="list.isconnect == 0" data-toggle="tooltip" data-placement="right" title="公众号接入状态显示“未接入”解决方案：进入微信公众平台，依次选择: 开发者中心 -> 修改配置，然后将对应公众号在平台的url和token复制到微信公众平台对应的选项，公众平台会自动进行检测"><i class="wi wi-error-sign"></i>未接入</span>
			<span class="color-green" ng-if="list.isconnect == 1"><i class="wi wi-right-sign"></i>已接入</span>
		</td>
		<td>
			<p ng-bind="list.end"></p>
		</td>
		<td><p ng-bind="list.sms"></p></td>
		<td class="vertical-middle table-manage-td">
			<div class="link-group">
				<a ng-href="{{links.switch}}uniacid={{list.uniacid}}">进入公众号</a>
				<?php  if($role_type) { ?>
				<a ng-href="{{links.post}}&acid={{list.acid}}&uniacid={{list.uniacid}}&account_type={{list.type}}" ng-show="list.role == 'manager' || list.role == 'owner' || list.role == 'founder'|| list.role == 'vice_founder'">管理设置</a>
				<?php  } ?>
			</div>
			<?php  if($role_type) { ?>
			<div class="manage-option text-right">
				<a href="{{links.post}}&acid={{list.acid}}&uniacid={{list.uniacid}}&account_type={{list.type}}" ng-show="list.role == 'owner' || list.role == 'founder' || list.role == 'vice_founder'">基础信息</a>
				<a href="{{links.post}}&do=sms&uniacid={{list.uniacid}}&acid={{list.acid}}&account_type={{list.type}}" ng-show="list.role == 'owner' || list.role == 'founder' || list.role == 'vice_founder'">短信信息</a>
				<a href="{{links.postUser}}&do=edit&uniacid={{list.uniacid}}&acid={{list.acid}}&account_type={{list.type}}">使用者管理</a>
				<a href="{{links.post}}&do=modules_tpl&uniacid={{list.uniacid}}&acid={{list.acid}}&account_type={{list.type}}">可用应用模板/模块</a>
				<a ng-href="{{links.del}}&acid={{list.acid}}&uniacid={{list.uniacid}}" ng-show="list.role == 'owner' || list.role == 'founder' || list.role == 'vice_founder'" onclick="if(!confirm('确认放入回收站吗？')) return false;" class="del">停用</a>
			</div>
			<?php  } ?>
		</td>
	</tr>
</table>
<div class="text-right">
	<?php  echo $pager;?>
</div>
<script>
	$(function(){
		$('[data-toggle="tooltip"]').tooltip();
	});
	switch_url = "<?php  echo url('account/display/switch')?>";
	angular.module('accountApp').value('config', {
		lists: <?php echo !empty($list) ? json_encode($list) : 'null'?>,
		links: {
			switch: switch_url,
			post: "<?php  echo url('account/post')?>",
			postUser: "<?php  echo url('account/post-user')?>",
			del: "<?php  echo url('account/manage/delete')?>",
		}
	});
	angular.bootstrap($('#js-system-account-display'), ['accountApp']);
</script> -->



<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>