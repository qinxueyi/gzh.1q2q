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

