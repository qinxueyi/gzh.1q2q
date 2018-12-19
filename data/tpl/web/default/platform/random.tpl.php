<<<<<<< HEAD
<?php defined('IN_IA') or exit('Access Denied');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
    <title></title>
</head>
<body>
<a href="javascript:" onclick="jump()"><img src="<?php  echo $imgUrl ?>" alt=""></a>
<input type="hidden" value="<?php  echo $news_list[0]['attach_id_array'] ?>" id="attach_id">

</body>
</html>
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script>
    function jump() {
        var attach_ids = $('#attach_id').val().split(",");
        var attach_id = attach_ids[Math.trunc(Math.random()*attach_ids.length)];
        $.ajax({
            url: "http://1q2q.chaotuozhe.com/random.php",
            type: 'post',
            data: {'jump':'different','attach_id':attach_id},
            dataType: 'text',
            success: function (data) {
                window.location.href=data;
            }
        })
    }
</script>

=======
<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>

<table id="demo" lay-filter="test" class="table table-bordered">
    
    <thead>
        <tr>
           
            <th>标题</th>
            <th>查看</th>
            <th>编辑</th>
        </tr>
    </thead>
    <tbody id="J_TbData">
        <?php  if(is_array($news_list)) { foreach($news_list as $index => $item) { ?>
            <tr>
                <th><?php  echo $item['title'];?></th>
                <th><a href="./index.php?c=platform&a=material-post&do=news&newsid=<?php  echo $item['attach_id'];?>">查看</a></th>
                <th><a href="./index.php?c=platform&a=material&do=update_news&id=<?php  echo $item['newid'];?>">编辑</a></th>
            </tr>   
        <?php  } } ?>
    </tbody>
</table>
    <?php  echo $pager;?>

>>>>>>> 5dde9ff2d487619e7dcba2002a77f20c59149e04
