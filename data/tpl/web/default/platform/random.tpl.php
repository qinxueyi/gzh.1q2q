<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>

<table id="demo" lay-filter="test" class="table table-bordered">
    
    <thead>
        <tr>
           
            <th>标题</th>
            <th>查看量</th>
            <th>查看</th>
            <th>编辑</th>
        </tr>
    </thead>
    <tbody id="J_TbData">
        <?php  if(is_array($news_list)) { foreach($news_list as $index => $item) { ?>
            <tr>
                <th><?php  echo $item['title'];?></th>
                <th><?php  echo $item['browse'];?></th>
                <th><a href="./index.php?c=platform&a=material-post&do=news&newsid=<?php  echo $item['attach_id'];?>">查看</a></th>
                <th><a href="./index.php?c=platform&a=material&do=update_news&id=<?php  echo $item['newid'];?>">编辑</a></th>
            </tr>   
        <?php  } } ?>
    </tbody>
</table>
    <?php  echo $pager;?>

