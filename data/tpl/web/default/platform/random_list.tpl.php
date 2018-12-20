<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<a  href="javascript:void(0);" class="btn btn-default" onclick="window.history.back()">返回</a>
 <a href="<?php  echo url('platform/material/random_add')?>"><button class="layui-btn layui-btn-normal" style="margin-bottom:20px;float:right;">添加图链接</button></a>   
<table id="demo" lay-filter="test" class="table table-bordered">
    
    <thead>
        <tr>
            <th>图片</th>
            <th>链接</th>
            <th>阅读量</th>
            <th>编辑</th>
        </tr>
    </thead>
    <tbody id="J_TbData">
        <?php  if($result) { ?>
            <?php  if(is_array($result)) { foreach($result as $index => $item) { ?>
                <tr>
                    <th width="80" height="80"><img src="<?php  echo $item['imgurl'];?>" style="width:100%;height:100%"></th>
                    <th style="width:500px;word-break: break-all;"><span style="word-break: break-all;"><?php  echo $item['url'];?></span></th>
                    <th style="vertical-align: middle;"><?php  echo $item['browse'];?></th>
                    <th style="vertical-align: middle;">    
                        <a href="./index.php?c=platform&a=material&do=random_add&id=<?php  echo $item['id'];?>" class="btn btn-default">编辑</a>
                        <a href="javascript:void(0);" class="btn btn-default" onclick="del(<?php  echo $item['id'];?>,this)">删除</a>
                    </th>
                </tr>   
            <?php  } } ?>
        <?php  } else { ?>
                <tr>
                    <th colspan=3>暂无数据</th>
                </tr>   
        <?php  } ?>
    </tbody>
</table>
<?php  echo $pager;?>

<script type="text/javascript">
    function del(id,a){
        $.post("<?php  echo url('platform/material/random_detele')?>", {'id':id},function(data) {
                data = $.parseJSON(data);
                if(data.message.errno==0){
                    $(a).parent().parent().hide();
                    util.message(data.message.message, "", 'success');
                }else{
                     util.message(data.message.message, '', 'error');  
                }       

        });
    }
</script>