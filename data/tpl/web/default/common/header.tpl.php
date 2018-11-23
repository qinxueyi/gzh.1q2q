<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header-base', TEMPLATE_INCLUDEPATH)) : (include template('common/header-base', TEMPLATE_INCLUDEPATH));?>
<div data-skin="default" class="skin-default <?php  if($_GPC['main-lg']) { ?> main-lg-body <?php  } ?>">
<?php  $frames = buildframes(FRAME);_calc_current_frames($frames);?>
<style>
	.head ul li{
		display: inline;
	}
	
</style>
<div class="head" style="position:fixed;top:0;width:100%;z-index:999">
	<nav class="navbar navbar-default" role="navigation">
		<div class="container <?php  if(!empty($frames['section']['platform_module_menu']['plugin_menu'])) { ?>plugin-head<?php  } ?>">
			<div class="navbar-header">
				<a class="navbar-brand" href="<?php  echo $_W['siteroot'];?>">
					<img src="<?php  if(!empty($_W['setting']['copyright']['blogo'])) { ?><?php  echo tomedia($_W['setting']['copyright']['blogo'])?><?php  } else { ?>./resource/images/logo/logo.png<?php  } ?>" class="pull-left" width="110px" height="35px">
					<span class="version hidden"><?php echo IMS_VERSION;?></span>
				</a>
			</div>
			<?php  if(!empty($_W['uid'])) { ?>
			<div class="collapse navbar-collapse">
				
				<ul class="nav navbar-nav navbar-right">
					<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header-notice', TEMPLATE_INCLUDEPATH)) : (include template('common/header-notice', TEMPLATE_INCLUDEPATH));?>
					<li class="dropdown">
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"><i class="wi wi-user color-gray"></i><?php  echo $_W['user']['username'];?> <span class="caret"></span></a>
						<ul class="dropdown-menu color-gray" role="menu">
							<li>
								<a href="<?php  echo url('user/profile');?>" target="_blank"><i class="wi wi-account color-gray"></i> 我的账号</a>
							</li>
							<li class="divider"></li>
							<?php  if(permission_check_account_user('see_system_upgrade')) { ?><li><a href="<?php  echo url('cloud/upgrade');?>" target="_blank"><i class="wi wi-update color-gray"></i> 自动更新</a></li><?php  } ?>
							<li><a href="<?php  echo url('system/updatecache');?>" target="_blank"><i class="wi wi-cache color-gray"></i> 更新缓存</a></li>
							<li class="divider"></li>
							<li>
								<a href="<?php  echo url('user/logout');?>"><i class="fa fa-sign-out color-gray"></i> 退出系统</a>
							</li>
						</ul>
					</li>
				</ul>
			</div>
			<?php  } else { ?>
			<div class="collapse navbar-collapse">
				<ul class="nav navbar-nav navbar-right">
					<li class="dropdown"><a href="<?php  echo url('user/register');?>">注册</a></li>
					<li class="dropdown"><a href="<?php  echo url('user/login');?>">登录</a></li>
				</ul>
			</div>
			<?php  } ?>
		</div>
	</nav>
</div>
<?php  if(empty($_COOKIE['check_setmeal']) && !empty($_W['account']['endtime']) && ($_W['account']['endtime'] - TIMESTAMP < (6*86400))) { ?>
<div class="system-tips we7-body-alert" id="setmeal-tips">
	<div class="container text-right">
		<div class="alert-info">
			<a href="<?php  if($_W['isfounder']) { ?><?php  echo url('user/edit', array('uid' => $_W['account']['uid']));?><?php  } else { ?>javascript:void(0);<?php  } ?>">
				该公众号管理员服务有效期：<?php  echo date('Y-m-d', $_W['account']['starttime']);?> ~ <?php  echo date('Y-m-d', $_W['account']['endtime']);?>.
				<?php  if($_W['account']['endtime'] < TIMESTAMP) { ?>
				目前已到期，请联系管理员续费
				<?php  } else { ?>
				将在<?php  echo floor(($_W['account']['endtime'] - strtotime(date('Y-m-d')))/86400);?>天后到期，请及时付费
				<?php  } ?>
			</a>
			<span class="tips-close" onclick="check_setmeal_hide();"><i class="wi wi-error-sign"></i></span>
		</div>
	</div>
</div>
<script>
	function check_setmeal_hide() {
		util.cookie.set('check_setmeal', 1, 1800);
		$('#setmeal-tips').hide();
		return false;
	}
</script>
<?php  } ?>
<style type="text/css">
	.skin-default{
		background-color:#424957;
	}
	.menu-self *{
		background-color:#424957!important;
	}
	.text-over{
		color:#D2D4D8!important;
		background-color:#4A5264!important;
		width:210px;
	}
	.text-over i{
		background-color:#4A5264!important;
	}
	.two-menu{
		background-color:#3C424E!important;
	}
	.two-menu i{
		background-color:#3C424E!important;
	}
</style>

<div class="main" style="margin-left:179px;width:calc( 100% - 200px);margin-top:56px;height:calc( 100% - 10px);">
<?php  if(!defined('IN_MESSAGE')) { ?>

<div class="container">
	<?php  if(in_array(FRAME, array('account', 'system', 'advertisement', 'wxapp', 'site', 'store', 'webapp', 'phoneapp')) && !in_array($_GPC['a'], array('news-show', 'notice-show')) &&!in_array($_GPC['c'], array('account'))) { ?>
	<style>
	.gundong{
		height:100%;	
		width:99%;
		border:1px solid #eee;
	}
	.zuigao{
		height:100%;
		overflow:auto;
		width:300px;
	}
	.zuigao::-webkit-scrollbar {
	     width: 4px;    
	     height: 4px;
	}
	.zuigao::-webkit-scrollbar-thumb {
	     border-radius: 5px;
	     -webkit-box-shadow: inset 0 0 5px rgba(0,0,0,0.2);
	     background: rgba(0,0,0,0.2);
	}
	.zuigao::-webkit-scrollbar-track {
	     -webkit-box-shadow: inset 0 0 5px rgba(0,0,0,0.2);
	     border-radius: 0;
	     background: rgba(0,0,0,0.1);
	}

	</style>
	<div class="zuigao" style="position:absolute;">
		<div class="gundong">
			<!-- <form action="" method="post">
				<div class="input-group" style="width:155px;margin:auto;margin-top:20px;">
				    <input type="search" class="form-control" id="keyword" placeholder="请输入公众号名" style="font-size:10px;">
				    <span class="input-group-btn">
				        <button class="btn btn-default" type="button"><span class="glyphicon glyphicon-search"></span></button>
				    </span>
				</div>
			</form> -->
				<?php  
					load()->model('user');
					$account_table = table('account');
					$account_list1 = $account_table->searchAccountList();
					$account_list1 = array_values($account_list1);
					foreach ($_W['tag'] as $k => $v) {
						foreach ($account_list1 as $k1 => $v1) {
							$account = uni_fetch($v1['uniacid']);
							$tag = pdo_get('account_tag_link',array('uniacid'=>$account['uniacid']));
							if($tag){
								$tag_array = explode(',',$tag['tag_id']);
								if(in_array($v['id'],$tag_array)){
									$_W['tag'][$k]['account'][$k1]['name'] = $account['name'];
									$_W['tag'][$k]['account'][$k1]['uniacid'] = $account['uniacid'];
									$_W['tag'][$k]['account'][$k1]['level'] = $account['level'];
								}
							}
						}
					}
					$c = $_GPC['c'];
					$a = $_GPC['a'];
					$do = $_GPC['do'];
					$href = "$c/$a/$do";
				?>
			<!-- 折叠菜单 begin -->
			<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true" style="width:250px;margin:auto;margin-top:20px;">
			<?php  if(is_array($_W['tag'])) { foreach($_W['tag'] as $index => $item) { ?>
			  <div class="panel panel-default" style="border:0px;">
			    <div class="panel-heading" role="tab" id="heading<?php  echo $item['id'];?>" style="background-color:white;cursor:pointer;" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php  echo $item['id'];?>" aria-expanded="true" aria-controls="collapse<?php  echo $item['id'];?>">
			        <a role="button" class="qiehuan">
			          <?php  echo $item['tag_name'];?>(<?php  echo count($item['account'])?>)
			        </a>
			        <div style="float:right;">
			      		<span class="glyphicon glyphicon-chevron-down"></span>
			        </div>
			    </div>
			    <style type="text/css">
			    	.acti{
			    		background-color:#EEF9FF;
			    	}
				</style>
			    <div id="collapse<?php  echo $item['id'];?>" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading<?php  echo $item['id'];?>" >
			    	<?php  if(is_array($item['account'])) { foreach($item['account'] as $index1 => $item1) { ?>
				      <div class="panel-body <?php  if($_W['uniacid'] == $item1['uniacid']) { ?> acti <?php  } ?>" >
				        <a href="<?php  echo url($href,array('uniacid'=>$item1['uniacid']))?>"><?php  echo $item1['name'];?></a>
				        <?php  if($item1['level'] == 1 || $item1['level'] == 3) { ?>
				        <span class="label label-primary">订阅号</span>
				        <?php  } ?>
				        <?php  if($item1['level'] == 2 || $item1['level'] == 4) { ?>
				        <span class="label label-success">服务号</span>
				        <?php  } ?>
				      </div>
			    	<?php  } } ?>
			    </div>
			  </div>
			  <?php  } } ?>
			  
			</div>
			<!-- 折叠菜单 end -->
		</div>
	</div>
	<script type="text/javascript">
		$('.qiehuan').click(function(){
			if($(this).next().children().hasClass('glyphicon-chevron-down')){
				$('.glyphicon').children('glyphicon-chevron-down');
				$(this).next().children().removeClass('glyphicon-chevron-down');
				$('.glyphicon').addClass('glyphicon-chevron-up');
			}else{
				$(this).next().children().removeClass('glyphicon-chevron-up');
				$(this).next().children().addClass('glyphicon-chevron-down');
			}
		})
	</script>
	<?php  } ?>

	
	<!-- <a href="javascript:;" class="js-big-main button-to-big color-gray" title="加宽"><?php  if($_GPC['main-lg']) { ?>正常<?php  } else { ?>宽屏<?php  } ?></a> -->
	<?php  if(in_array(FRAME, array('account', 'system', 'advertisement', 'wxapp', 'site', 'store', 'webapp', 'phoneapp')) && !in_array($_GPC['a'], array('news-show', 'notice-show'))) { ?>
	<div class="panel panel-content main-panel-content <?php  if(!empty($frames['section']['platform_module_menu']['plugin_menu'])) { ?>panel-content-plugin<?php  } ?>">
		<?php  if(in_array(FRAME, array('account', 'system', 'advertisement', 'wxapp', 'site', 'store', 'webapp', 'phoneapp')) && !in_array($_GPC['a'], array('news-show', 'notice-show')) &&!in_array($_GPC['c'], array('account'))) { ?>
		<div class="content-head panel-heading main-panel-heading" style="margin-left:300px;">
		<?php  } else { ?>
		<div class="content-head panel-heading main-panel-heading">
		<?php  } ?>
			<?php  if(($_GPC['c'] != 'cloud' && !empty($_GPC['m']) && !in_array($_GPC['m'], array('keyword', 'special', 'welcome', 'default', 'userapi', 'service','delay'))) || defined('IN_MODULE')) { ?>
				<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header-module', TEMPLATE_INCLUDEPATH)) : (include template('common/header-module', TEMPLATE_INCLUDEPATH));?>
			<?php  } else { ?>
				<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header-' . FRAME, TEMPLATE_INCLUDEPATH)) : (include template('common/header-' . FRAME, TEMPLATE_INCLUDEPATH));?>
			<?php  } ?>
		</div>


	<div class="panel-body clearfix main-panel-body <?php  if(!empty($_W['setting']['copyright']['leftmenufixed'])) { ?>menu-fixed<?php  } ?>">
		<div class="left-menu menu-self" style="position:fixed;left:0;top:57px;font-size:16px;" >
			<div class="left-menu-content" style="height:100%;">

				<div class="panel panel-menu" >
					<ul class="list-group" id="left-accordion" role="tablist" aria-multiselectable="true">
						<li class="list-group-item <?php  if($menu['active']) { ?>active<?php  } ?>">
							<a href="<?php  echo url('account/manage');?>" class="text-over">
								<i class="wi wi-wechat"></i>
								公众号管理
							</a>
						</li>
						<li class="list-group-item <?php  if($menu['active']) { ?>active<?php  } ?>">
							<a href="<?php  echo url('platform/material');?>" class="text-over">
								<i class="wi wi-redact"></i>
								素材管理
							</a>
						</li>
						<li class="list-group-item <?php  if($menu['active']) { ?>active<?php  } ?>">
							<a href="<?php  echo url('article/news');?>" class="text-over">
								<i class="wi wi-article"></i>
								文章库
								<span style="color:red">x</span>
							</a>
						</li>
						<li class="list-group-item <?php  if($menu['active']) { ?>active<?php  } ?>">
							<a href="<?php  echo url('platform/reply');?>" class="text-over">
								<i class="wi wi-reply"></i>
								自动回复
							</a>
						</li>
					      <li class="list-group-item <?php  if($menu['active']) { ?>active<?php  } ?>" role="button" data-toggle="collapse" data-parent="#left-accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne" role="tab" id="headingOne">
							<a href="javascript:;" class="text-over">
								<i class="wi wi-wechat"></i>
								营销工具
								<i class="glyphicon glyphicon-chevron-down" style="margin-left:30px;"></i>
							</a>
							</li>
					        <ul id="collapseOne" class="collapse" role="tabpanel" aria-labelledby="headingOne">
								<li class="list-group-item <?php  if($menu['active']) { ?>active<?php  } ?>">
									<a href="javascript:;" class="text-over two-menu">
										<i class="wi wi-wechat"></i>
										随机链接
										<span style="color:red">x</span>
									</a>
								</li>
								<li class="list-group-item <?php  if($menu['active']) { ?>active<?php  } ?>">
									<a href="javascript:;" class="text-over two-menu">
										<i class="wi wi-wechat"></i>
										裂变活动
										<span style="color:red">x</span>
									</a>
								</li>
								<li class="list-group-item <?php  if($menu['active']) { ?>active<?php  } ?>">
									<a href="<?php  echo url('platform/reply',array('m'=>'delay'));?>" class="text-over two-menu">
										<i class="wi wi-wechat"></i>
										互动延时消息
									</a>
								</li>
								<li class="list-group-item <?php  if($menu['active']) { ?>active<?php  } ?>">
									<a href="javascript:;" class="text-over two-menu">
										<i class="wi wi-wechat"></i>
										参数二维码
										<span style="color:red">x</span>
									</a>
								</li>
							</ul>


						<li class="list-group-item <?php  if($menu['active']) { ?>active<?php  } ?>">
							<a href="<?php  echo url('platform/menu/post');?>" class="text-over">
								<i class="wi wi-custommenu"></i>
								菜单配置
							</a>
						</li>
						<li class="list-group-item <?php  if($menu['active']) { ?>active<?php  } ?>" role="button" data-toggle="collapse" data-parent="#left-accordion" href="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo" role="tab" id="headingTwo">
							<a href="javascript:;" class="text-over">
								<i class="wi wi-reply"></i>
								消息管理
								<i class="glyphicon glyphicon-chevron-down" style="margin-left:30px;"></i>
							</a>
						</li>
						<ul id="collapseTwo" class="collapse" role="tabpanel" aria-labelledby="headingTwo">
							<li class="list-group-item <?php  if($menu['active']) { ?>active<?php  } ?>">
								<a href="javascript:;" class="text-over two-menu">
									<i class="wi wi-wechat"></i>
									模板消息
									<span style="color:red">x</span>
								</a>
							</li>
							<li class="list-group-item <?php  if($menu['active']) { ?>active<?php  } ?>">
								<a href="javascript:;" class="text-over two-menu">
									<i class="wi wi-wechat"></i>
									客服消息
									<span style="color:red">x</span>
								</a>
							</li>
							<li class="list-group-item <?php  if($menu['active']) { ?>active<?php  } ?>">
								<a href="javascript:;" class="text-over two-menu">
									<i class="wi wi-wechat"></i>
									群发消息
									<span style="color:red">x</span>
								</a>
							</li>
							<li class="list-group-item <?php  if($menu['active']) { ?>active<?php  } ?>">
								<a href="javascript:;" class="text-over two-menu">
									<i class="wi wi-wechat"></i>
									一次性订阅消息
									<span style="color:red">x</span>
								</a>
							</li>
						</ul>
						<li class="list-group-item <?php  if($menu['active']) { ?>active<?php  } ?>" role="button" data-toggle="collapse" data-parent="#left-accordion" href="#collapseThree" aria-expanded="true" aria-controls="collapseThree" role="tab" id="headingThree">
							<a href="javascript:;" class="text-over">
								<i class="wi wi-pc"></i>
								广告管理
								<i class="glyphicon glyphicon-chevron-down" style="margin-left:30px;"></i>
							</a>
						</li>
						<ul id="collapseThree" class="collapse" role="tabpanel" aria-labelledby="headingThree">
							<li class="list-group-item <?php  if($menu['active']) { ?>active<?php  } ?>">
								<a href="javascript:;" class="text-over two-menu">
									<i class="wi wi-wechat"></i>
									广告代售管理
									<span style="color:red">x</span>
								</a>
							</li>
							<li class="list-group-item <?php  if($menu['active']) { ?>active<?php  } ?>">
								<a href="javascript:;" class="text-over two-menu">
									<i class="wi wi-wechat"></i>
									广告订单列表
									<span style="color:red">x</span>
								</a>
							</li>
							<li class="list-group-item <?php  if($menu['active']) { ?>active<?php  } ?>">
								<a href="javascript:;" class="text-over two-menu">
									<i class="wi wi-wechat"></i>
									投放计划管理
									<span style="color:red">x</span>
								</a>
							</li>
						</ul>


						<li class="list-group-item <?php  if($menu['active']) { ?>active<?php  } ?>">
							<a href="<?php  echo url('mc/fans');?>" class="text-over">
								<i class="wi wi-fansmanage"></i>
								粉丝管理
							</a>
						</li>
						<li class="list-group-item <?php  if($menu['active']) { ?>active<?php  } ?>">
							<a href="javascript" class="text-over">
								<i class="wi wi-user"></i>
								团队管理
								<span style="color:red">x</span>
							</a>
						</li>
						<li class="list-group-item <?php  if($menu['active']) { ?>active<?php  } ?>" role="button" data-toggle="collapse" data-parent="#left-accordion" href="#collapseFour" aria-expanded="true" aria-controls="collapseFour" role="tab" id="headingFour">
							<a href="javascript:;" class="text-over">
								<i class="wi wi-user-group"></i>
								数据管理
								<i class="glyphicon glyphicon-chevron-down" style="margin-left:30px;"></i>
							</a>
						</li>
						<ul id="collapseFour" class="collapse" role="tabpanel" aria-labelledby="headingFour">
							<li class="list-group-item <?php  if($menu['active']) { ?>active<?php  } ?>">
								<a href="<?php  echo url('account/fan-list');?>" class="text-over two-menu">
									<i class="wi wi-wechat"></i>
									粉丝数据
								</a>
							</li>
							<li class="list-group-item <?php  if($menu['active']) { ?>active<?php  } ?>">
								<a href="<?php  echo url('account/article-list');?>" class="text-over two-menu">
									<i class="wi wi-wechat"></i>
									图文数据
								</a>
							</li>
						</ul>
						<li class="list-group-item <?php  if($menu['active']) { ?>active<?php  } ?>" role="button" data-toggle="collapse" data-parent="#left-accordion" href="#collapseFive" aria-expanded="true" aria-controls="collapseFive" role="tab" id="headingFive">
							<a href="javascript:;" class="text-over">
								<i class="wi wi-update"></i>
								分销管理
								<i class="glyphicon glyphicon-chevron-down" style="margin-left:30px;"></i>
							</a>
						</li>
						<ul id="collapseFive" class="collapse" role="tabpanel" aria-labelledby="headingFive">
							<li class="list-group-item <?php  if($menu['active']) { ?>active<?php  } ?>">
								<a href="javascript:;" class="text-over two-menu">
									<i class="wi wi-wechat"></i>
									分销列表
									<span style="color:red">x</span>
								</a>
							</li>
							<li class="list-group-item <?php  if($menu['active']) { ?>active<?php  } ?>">
								<a href="javascript:;" class="text-over two-menu">
									<i class="wi wi-wechat"></i>
									订单管理
									<span style="color:red">x</span>
								</a>
							</li>
							<li class="list-group-item <?php  if($menu['active']) { ?>active<?php  } ?>">
								<a href="javascript:;" class="text-over two-menu">
									<i class="wi wi-wechat"></i>
									收益中心
									<span style="color:red">x</span>
								</a>
							</li>
						</ul>
					</ul>



				</div>
			</div>
		</div>




		<?php  if(in_array(FRAME, array('account', 'system', 'advertisement', 'wxapp', 'site', 'store', 'webapp', 'phoneapp')) && !in_array($_GPC['a'], array('news-show', 'notice-show')) &&!in_array($_GPC['c'], array('account'))) { ?>
		<div class="right-content" style="margin-left:300px;">
		<?php  } else { ?>
		<div class="right-content">
		<?php  } ?>
	<?php  } ?>
<?php  } ?>





