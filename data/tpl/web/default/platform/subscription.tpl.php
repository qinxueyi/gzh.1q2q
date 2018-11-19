<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="we7-page-title">
	一次性订阅消息
</div>
<ul class="we7-page-tab">
	<li class="active">
	<a href="<?php  echo url('platform/subscription')?>">一次性订阅消息</a>
</ul>
<!-- <div class="clearfix we7-margin-bottom"> -->
	<!-- <form action="" class="form-inline  pull-left" method="get">
		<input type="hidden" name="c" value="account">
		<input type="hidden" name="a" value="recycle">
		<div class="input-group form-group" style="width: 400px;">
			<input type="text" name="keyword" value="<?php  echo $_GPC['keyword'];?>" class="form-control" placeholder="搜索关键字"/>
			<span class="input-group-btn"><button class="btn btn-default"><i class="fa fa-search"></i></button></span>
		</div>
	</form> -->
<!-- </div> -->

<div class="modal fade" id="new_type" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="we7-modal-dialog modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<div class="modal-title">添加标签</div>
			</div>
			<div class="modal-body">
				<div class="reply" ng-click="createNew('reply')">
					<a href="javascript:;">
						<div class="content">
							<form id="reply-form" class="form-horizontal form" action="<?php  echo url('platform/tag/add')?>" method="post" enctype="multipart/form-data" id="delay-form">
								<div class="title" style="float:left;line-height:34px;margin-right:15px;">标签名字 </div>
								<input type="text" class="form-control" name="tag_name" style="width:80%;">
								<div style="text-align:center;margin-top:20px;">
									<input type="submit" name="submit" value="发布" class="reply-form-submit btn btn-primary">
								</div>
							</form>
						</div>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>


<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>