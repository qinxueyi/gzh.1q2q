<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/11/14
 * Time: 10:23
 */

getUserAuthorization();
function getUserAuthorization()
{

    $template_id = "hviz35sN0-zViDRLAqtvm1zeTE3zlq5ySGItBEXXBMo";
    $url = "https://mp.weixin.qq.com/mp/subscribemsg?action=get_confirm&appid=wxbd393edabf09e2d7&scene=1000&template_id=$template_id&redirect_url=http%3a%2f%2fsupport.qq.com&reserved=test#wechat_redirect";
    header('Location:' . $url);

}