<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/10/23
 * Time: 13:15
 */
$json='{"articles":[{"title":"${name}\uff0c\u505a\u4e00\u4e2a\u9ad8\u60c5\u5546\u7537\u4eba\uff0c\u6210\u4e3a\u66f4\u597d\u7684\u81ea\u5df1","description":null,"url":"https:\/\/shuyuan.v5.com\/?d=DISB8532116515236200163F00574F26","picurl":"http:\/\/1q2q.chaotuozhe.com\/attachment\/images\/8\/2018\/12\/iaVqP8JqVJPk4YD8SklqQQQdQq44ja.jpg"}]}';
$rep_str='${name}';
echo str_replace($rep_str,"Franky",$json);