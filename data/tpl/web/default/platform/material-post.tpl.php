<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<style>
.appmsg-edit-item.title .form-control {
    font-size: 15px;
    height: 36px;
    line-height: 31px;
    border: 1px solid #e7e7eb!important;
	border-radius: 0;
    width: calc(100% - 30px);
    margin: auto;
}
.tz_select_pintures{    margin-top: 14px;margin-left: 14px;}
</style>
<div class="" ng-module="materialApp" ng-controller="materialAdd" id="main" ng-cloak>
	<div class="penel we7-panel panel-special">
		<div class="panel-heading">
			<ol class="breadcrumb we7-breadcrumb">
				<a href="<?php  echo url('platform/material')?>"><i class="wi wi-back-circle"></i> </a>
				<li>
					<a href="<?php  echo url('platform/material')?>">素材库</a>
				</li>
				<li>
					<?php  if(!empty($news_list)) { ?>编辑<?php  } else { ?>新建<?php  } ?>图文素材
				</li>
			</ol>
		</div>
		<div class="panel-body">
			<div class="appmsg-edit-box">
				<div class="appmsg-preview-area">
					<div class="appmsg-edit-container appmsg-preview-container js-aside">
						<div class="appmsg-container-hd">
							<h4 class="appmsg-container-title">图文列表</h4>
						</div>
						<div class="appmsg-container-bd">
							<div class="material-appmsg-item multi">
								<div class="appmsg-content">
									<div ng-repeat="material in materialList" ng-click="changeIndex($index)" ng-class="material.class">
										<h4 class="appmsg-title">
											<a href="" target="-blank">{{ material.title }}</a>
										</h4>
										<div class="appmsg-thumb" ng-style="{'background-image' : 'url('+material.thumb+')'}">
										</div>
										<a href="javascript:;" ng-if="$index == 0" ng-click="exportFromCms()">导入文章</a>
										<div class="appmsg-edit-mask">
											<a onclick="return false;" class="sort-up" href="javascript:;" ng-click="changeOrder('up', $index)" data-toggle="tooltip" data-placement="bottom" title="上移" ng-if="$index != 0"><i class="wi wi-stick-sign"></i></a>
											<a onclick="return false;" class="sort-down" href="javascript:;" ng-click="changeOrder('down', $index)" data-toggle="tooltip" data-placement="bottom" title="下移" ng-if="$index != (materialList.length - 1)"><i class="wi wi-down-sign"></i></a>
											<a href="javascript:;" ng-click="exportFromCms()">导入文章</a>
											<a onclick="return false;" class="del" href="javascript:;" ng-if="$index != 0 && (operate == 'add' || model == 'local')" ng-click="deleteMaterial($index)" data-toggle="tooltip" data-placement="bottom" title="删除"><i class="wi wi-delete2"></i></a>
										</div>
									</div>
								</div>
							</div>
							<a title="添加一篇图文" ng-click="addMaterial()" ng-show="(materialList.length < 8 && (operate == 'add' || operate == 'edit'))" class="appmsg-add" href="javascript:void(0);" style="display: block;">
								<i class="add-gray">+</i>
							</a>
						</div>
					</div>
				</div>
				<div class="appmsg-input-area" id="edit-container">
					<div class="reply" ng-if="new_type == 'reply'">
						<!--标题-->
						<div class="appmsg-edit-item title">
							<label for="title" class="" style="display:none">请在这里输入标题</label>
							<input id="title" placeholder="请在这里输入标题" class="form-control" name="title" id="title" ng-model="materialList[activeIndex].title" max-length="64" type="text">
							<em class="form-control-append hidden">0/64</em>
						</div>
						<!--作者-->
						<div class="appmsg-edit-item author">
							<label for="author" class="" style="display:none">请输入作者</label>
							<input id="author" placeholder="请输入作者" class="form-control" ng-model="materialList[activeIndex].author" name="author" max-length="8" type="text">
							<em class="form-control-append hidden">0/8</em>
						</div>
						<!-- 阅读原文链接 -->
						<div we7-linker we7-my-url="materialList[activeIndex].content_source_url"></div>
						<!--正文-->
						<div class="editor-area" ng-my-upurl="<?php  echo url('utility/file/upload')?>" ng-my-editor ng-my-value="materialList[activeIndex].content" >
							<textarea ></textarea>
						</div>
					</div>
					<div class="link" ng-if="new_type == 'link'">
						<div class="appmsg-edit-function-area ">
							<!-- 标题 -->
							<div class="appmsg-edit-item title">
								<label for="title" class="" style="display:none">请在这里输入标题</label>
								<input id="title" placeholder="请在这里输入标题" class="form-control" name="title" id="title" ng-model="materialList[activeIndex].title" max-length="64" type="text">
								<em class="form-control-append hidden">0/64</em>
							</div>
							<!-- END 标题 -->
							<!-- BEGIN 跳转链接 -->
<!-- 							<div class="we7-form appmsg-edit-item origin-url-area">
								<div class="form-group">
									<div class="col-sm-12 form-control-box">
										<div we7-linker we7-my-url="materialList[activeIndex].content_source_url" we7-my-title="materialList[activeIndex].content_source_url"></div>
									</div>
									 <button ng-click="multiGraph()" class="btn btn-default tz_select_pintures">选择图片</button> -->
					<!-- 				<image ng-repeat="imgsrc in thumbList"  ng-src="{{ imgsrc}}" style="max-height: 200px;"></image>
								</div>
							</div> -->
							<!-- END 跳转链接 -->
							<!-- BEGIN 判断是不是外链接 -->
							<div>
								<label>是否是外链接</label>
								<select id="select_urls">
									<option value="false">否</option>
									<option value="true">是</option>
								</select>
							</div>
							<!-- END 判断是不是外链接 -->
							<div style="margin-top:15px;display:none;" id="outerJoin">
								<!-- BEGIN 添加图片 -->
								<a  href="javascript:void(0);" class="btn btn-default" ng-click="multiGraph()" style="margin-left:15px;">选择图片</a>
								<image ng-src="{{ materialList[activeIndex].imgurl}}" style="max-height: 100px;max-width:150px;"></image>
								<!-- END 添加图片 -->
								<!-- BEGIN 添加随机链接 -->
								<style type="text/css">
									.ex_left{
										float:left;
										height:34px;
										margin-left:15px;
									}
									.ex_text{
										line-height: 34px;
									}
								</style>
								<div style="margin-top:20px;">
									<div class="ex_left ex_text">微信公众号选择:</div>
									<div class="ex_left">
										<select id="ex_select">
											<?php  if(is_array($_W['tag'])) { foreach($_W['tag'] as $index => $item) { ?>
												<?php  if(is_array($item['account'])) { foreach($item['account'] as $index1 => $item1) { ?>
													<option value="<?php  echo $item1['uniacid'];?>" <?php  if($item1['uniacid'] == $_W['uniacid']) { ?>selected<?php  } ?>><?php  echo $item1['name'];?></option>	
												<?php  } } ?>
											<?php  } } ?>
										</select>
									</div>
									<table id="demo" lay-filter="test" style="display:none;" class="table table-bordered">
										<thead>
											<tr>
								                <th>序号</th>
								                <th>标题</th>
								            </tr>
							            </thead>
							            <tbody id="J_TbData">
        								</tbody>
									</table>
								</div>	
								<!-- END 添加随机链接 -->
								<script type="text/javascript">
									$('#select_urls').change(function(){
										if($('#select_urls').val()=="true"){
											$('#outerJoin').show();
										}else{
											$('#outerJoin').hide();
										}
									});

									$('#ex_select').change(function(){

								        uniacid = $('#ex_select').val(); //公众号Id
								        if(!uniacid){
								        	util.message('未选中公众号', '', 'error');
								        	return false;
								        }
								        $.post("<?php  echo url('platform/material-post/getContent_material')?>", {'uniacid':uniacid},function(data) {
								        		data = $.parseJSON(data);
								        		var datas = data.data;
								        		if(datas){
								        			$(function(){
											            //第二种： 动态创建表格的方式，使用动态创建dom对象的方式
											            //清空所有的子节点
											            $("#J_TbData").empty();

											            //自杀
											            // $("#J_TbData").remove();

											            for( var i = 0; i < datas.length; i++ ) {
											                //动态创建一个tr行标签,并且转换成jQuery对象
											                var $trTemp = $("<tr></tr>");

											                //往行里面追加 td单元格
											                $trTemp.append('<td width="50"><input type="checkbox" value="'+datas[i].id+'" onclick="select()"></td>');
											                $trTemp.append("<td>"+ datas[i].title +"</td>");
											                // $("#J_TbData").append($trTemp);
											                $trTemp.appendTo("#J_TbData");
											            }
											        });
											      	$('#demo').show();		
								        		}

								        });
										
									});

									function select(){
										alert(1111);

									}
								</script>
							</div>
						</div>
					</div>
				</div>
				<div class="appmsg-edit-highlight-area">
					<div class="appmsg-edit-title">发布样式编辑</div>
					<!-- EBGIN 封面 -->
					<div class="appmsg-edit-item gap-left we7-form">
						<div class="form-group">
							<label class="col-sm-12 control-label">封面<br><span class="color-gray">建议尺寸：<br>大图片：900 * 500（单图文或多图文第一篇）<br>小图片200 * 200（多图文第一篇图文下面）</span></label>
							<div>
								<a  href="javascript:void(0);" class="btn btn-default" ng-click="pickPicture('local')">本地图片</a>
								<a  href="javascript:void(0);" class="btn btn-default we7-margin-left" ng-click="pickPicture('wechat')">微信图片</a>
								<div style="margin: 20px 0 10px 0;">
									<input type="checkbox" ng-checked="{{ materialList[activeIndex].show_cover_pic }}" ng-click="updateSelection()" id="display-cover"/>
									<label for="display-cover">在正文顶部显示封面图</label>
								</div>
								<image ng-src="{{ materialList[activeIndex].thumb }}" style="max-height: 200px;"></image>
							</div>
						</div>




						<div class="form-group">
							<label class="col-sm-12 control-label" ng-click="zhaiyao()">摘要<span class="color-gray">(选填，如果不填写会默认抓取正文前54个字)</span></label>
							<div>
								<span class="form-textarea-box">
									<textarea class="form-textarea" ng-model="materialList[activeIndex]['digest']" name="digest" max-length="120" rows="4" ></textarea>
									<em class="form-control-append hidden">0/120</em>
								</span>
							</div>
						</div>
					</div>
					<!-- END 封面 -->
				</div>
			</div>
			<nav class="navbar navbar-wxapp-bottom navbar-fixed-bottom" role="navigation">
				<div class="container">
					<div class="pager">
						<a type="button" class="btn btn-default pull-left hidden">收起正文</a>
						<div class="pull-right hidden">
							<a type="button" class="btn btn-primary" ng-click="saveNews()">保存</a>
							<a type="button" class="btn btn-default">预览</a>
							<a type="button" class="btn btn-default">保存并群发</a>
						</div>
						<a type="button" class="btn btn-primary" ng-if="model == 'local' || operate == 'add'" ng-click="saveNews('local')">保存到本地</a>
						<a id="savewechat" type="button" class="btn btn-primary" ng-click="saveNews('wechat')" ng-show="(!hidenbutton && materialList.length <= 8) || operate == 'add' && new_type == 'reply'">保存并上传到微信</a>
					</div>
				</div>
			</nav>
		</div>
	</div>

</div>
<script>
	require(['underscore', 'fileUploader'], function() {
		angular.module('materialApp').value('material', {
			'materialList' : <?php  echo json_encode($news_list)?>,
			'type' : '<?php  echo $type;?>',
			'news_rid' : '<?php  echo $reply_news_id;?>',
			'operate' : <?php  if(!empty($newsid)) { ?>'edit'<?php  } else { ?>'add'<?php  } ?>,
			'model' : <?php  if(!empty($attachment['model'])) { ?>'<?php  echo $attachment['model'];?>'<?php  } else { ?>''<?php  } ?>,
			'url' : "<?php  echo url('platform/material-post/tomedia')?>",
			'newsUpload_url' : "<?php  echo url('platform/material-post/addnews')?>",
			'msg_url' : "<?php  echo url('platform/material/display')?>",
			'upload_thumb_url' : "<?php  echo url('platform/material-post/thumb_upload')?>",
			'upload_iamge_url' : "<?php  echo url('platform/material-post/image_upload')?>",
			'replace_url' : "<?php  echo url('platform/material-post/replace_image_url')?>",
			'num_limit' : "<?php  echo $upload_limit['num'];?>",
			'image_limit' : "<?php  echo $upload_limit['image'];?>",
			'voice_limit' : "<?php  echo $upload_limit['voice'];?>",
			'video_limit' : "<?php  echo $upload_limit['video'];?>",
			'new_type' : "<?php  echo $new_type;?>"
		});
		angular.bootstrap($('#main'), ['materialApp']);
	});
	$('[data-toggle="tooltip"]').tooltip();
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>