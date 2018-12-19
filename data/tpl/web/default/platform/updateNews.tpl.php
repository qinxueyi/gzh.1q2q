<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
    <style>
    #pagecount{
        text-align: center;
        margin-bottom: 5px;
        padding:10px 10px 20px 0;
    }
    #pagecount span{
        display: inline-block;
        background: #ddd;
        margin:0 0 0 5px;
        text-align: center;
        padding:2px 8px 2px 8px;
        border:1px solid #eee;
        color: #666;
    }
    #pagecount span a{
        color:#333;
    }
    #pagecount span a:hover{
        text-decoration: underline;
    }
         
    </style>
    <div style="margin-top:15px;">
        <!-- BEGIN 添加图片 -->
    <!-- <div class="" ng-module="materialApp" ng-controller="materialAdd" id="main" ng-cloak> -->
        <a  href="javascript:void(0);" class="btn btn-default" onclick="select_mediaid('image');" style="margin-left:15px;">选择图片</a>
        <ul class="keywords-list">
        </ul>
        <input type="hidden" name="img_url" id="image_url" value="<?php  echo $result['imgUrl'];?>">
        <script type="text/javascript">
        var image_array =  $('#image_url').val();
        var data_array = new Array();
        if(image_array){
                data_array=image_array.toString().split(",");
                for (var i = data_array.length - 1; i >= 0; i--) {
                    $('.keywords-list').append(
                    '<li>'+
                    '<div class="del-image-media">'+
                    '   <div class="desc">'+
                    '       <div class="media-content">'+
                    '           <div class="appmsgSendedItem">'+
                    '               <a class="title-wrp" href="javascript:;">'+
                    '                    <span class="icon cover" style="background-image:url('+data_array[i]+');"></span>'+
                    '                   <span class="title">[图片]</span>'+
                    '               </a>'+
                    '           </div>'+
                    '       </div>'+
                    '   </div>'+
                    '   <div class="opr">'+
                    '       <a href="javascript:;" class="del-gray" onclick="delmedia(this,'+"'"+data_array[i]+"'"+')">删除</a>'+
                    '   </div>'+
                    '</div>'+
                    '</li>'
                     );
                }
        }
        window.select_mediaid = function(type, otherVal){
        var option = {
            type: type,
            isWechat : true, // 默认显示微信
            needType : 3, //  除了图文 其他只能微信
            otherVal : otherVal,//
            others: {
                image: {
                    needType : 1
                },
                video : {
                    needType : 1
                },
                voice : {
                    needType : 3
                }
            }
        };
        util.material(function(material){
            var val_data = new Array();
            for (var i = material.length - 1; i >= 0; i--) {
                    var image_url=$('#image_url').val();
                    if(image_url){
                        val_data=image_url.toString().split(",");
                    }
                    val_data.push(material[i].url);
                    image_url= val_data.join(',');
                    $('#image_url').val(image_url);
                    $('.keywords-list').append(
                    '<li>'+
                    '<div class="del-'+type+'-media">'+
                    '   <div class="desc">'+
                    '       <div class="media-content">'+
                    '           <div class="appmsgSendedItem">'+
                    '               <a class="title-wrp" href="javascript:;">'+
                    '                    <span class="icon cover" style="background-image:url('+material[i].url+');"></span>'+
                    '                   <span class="title">[图片]</span>'+
                    '               </a>'+
                    '           </div>'+
                    '       </div>'+
                    '   </div>'+
                    '   <div class="opr">'+
                    '       <a href="javascript:;" data-media="'+material[i].media_id+'" class="del-gray" onclick="delmedia(this,'+"'"+material[i].url+"'"+')">删除</a>'+
                    '   </div>'+
                    '</div>'+
                    '</li>'
                );  
            }


            //显示隐藏-start
 
            //显示隐藏-end
        }, option);
    };
    function delmedia(a,url){
        var val = new Array();
        var image_url = $('#image_url').val();
        if(image_url){
            val=image_url.toString().split(",");
        }
        val.remove(url); 
        image_url = val.join(',');
        $('#image_url').val(image_url);
        $(a).parent().parent().parent().hide();
    }
    </script>

        <input type="hidden" name="attach_id_array" id="news_id" value="<?php  echo $result['attach_id_array'];?>">
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
            <input type="hidden" name="id" value="<?php  echo $id;?>" id="content_id">
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
            <table id="demo" lay-filter="test" style="display:none" class="table table-bordered">
                <thead>
                    <tr>
                        <th>序号</th>
                        <th>标题</th>
                    </tr>
                </thead>
                <tbody id="J_TbData">
                </tbody>
            </table>
                <ul class="theme_body"></div><div id="pagecount"class="pagecount"></div>
                <button type="button" class="btn btn-success">提交</button>
        </div>  
    </div>
    <!-- END 添加随机链接 -->
    <script type="text/javascript">
        function in_array(stringToSearch, arrayToSearch) {
             for (s = 0; s < arrayToSearch.length; s++) {
              thisEntry = arrayToSearch[s].toString();
              if (thisEntry == stringToSearch) {
               return true;
              }
             }
             return false;
        }
        $('#ex_select').change(function(){

            getData()
            
        });

        function getData(page=''){
            uniacid = $('#ex_select').val(); //公众号Id
            if(!uniacid){
                util.message('未选中公众号', '', 'error');
                return false;
            }
            $.post("<?php  echo url('platform/material-post/getContent_material')?>", {'uniacid':uniacid,'page':page},function(data) {
                    data = $.parseJSON(data);
                    if(data.code!=0){
                        var $trTemp = $("<tr></tr>");
                        //往行里面追加 td单元格
                        $trTemp.append('<td width="50" colspan=2 style="text-align:center">暂无数据</td>');
                        $trTemp.appendTo("#J_TbData");  
                        return false;
                    }
                    var datas = data.data.data;
                    var page = data.data.page;
                    var total = data.data.total;
                    var totalPage = data.data.totalPage;
                    if(datas){
                        $(function(){
                            //第二种： 动态创建表格的方式，使用动态创建dom对象的方式
                            //清空所有的子节点
                            $("#J_TbData").empty();

                            //自杀
                            // $("#J_TbData").remove();
                            var news_id = $('#news_id').val();
                            if(news_id){
                                var val_news = new Array();
                                val=news_id.toString().split(",");

                            }
                            for( var i = 0; i < datas.length; i++ ) {
                                //动态创建一个tr行标签,并且转换成jQuery对象
                                var $trTemp = $("<tr></tr>");
                                //往行里面追加 td单元格
                                if(in_array(datas[i].newid,val)){
                                    $trTemp.append('<td width="50"><input type="checkbox" value="'+datas[i].newid+'" onclick="checkboxOnclick(this)" checked></td>');
                                }else{
                                   $trTemp.append('<td width="50"><input type="checkbox" value="'+datas[i].newid+'" onclick="checkboxOnclick(this)"></td>'); 
                                }
                                
                                $trTemp.append("<td>"+ datas[i].title +"</td>");
                                // $("#J_TbData").append($trTemp);
                                $trTemp.appendTo("#J_TbData");
                            }
                        });
                        getPageBar(page,total,totalPage);
                        $('#demo').show();      
                    }

            });
        }
        $("#pagecount").on('click','span a',function(){
            var rel = $(this).attr("rel");
            if(rel){
                getData(rel);
            }
        });
        function getPageBar(page,total,totalPage){
                $("#pagecount").find('*').remove();
                //页码大于最大页数
                if(page>totalPage) page=totalPage;
                //页码小于1
                if(page<1) curPage=1;
                pageStr = "<span>共"+total+"条</span><span>"+page+"/"+totalPage+"</span>";
                 
                //如果是第一页
                if(page==1){
                    pageStr += "<span>首页</span><span>上一页</span>";
                }else{
                    pageStr += "<span><a href='javascript:void(0)' rel='1'>首页</a></span><span><a href='javascript:void(0)' rel='"+(page-1)+"'>上一页</a></span>";
                }
                 
                //如果是最后页
                if(page>=totalPage){
                    pageStr += "<span>下一页</span><span>尾页</span>";
                }else{
                    pageStr += "<span><a href='javascript:void(0)' rel='"+(parseInt(page)+1)+"'>下一页</a></span><span><a href='javascript:void(0)' rel='"+totalPage+"'>尾页</a></span>";
                }  
                $("#pagecount").append(pageStr);
            }
        Array.prototype.indexOf = function (val) {
              for(var i = 0; i < this.length; i++){
                if(this[i] == val){return i;}
              }
              return -1;
            }
        Array.prototype.remove = function (val) {
          var index = this.indexOf(val);
          if(index > -1){this.splice(index,1);}
        }
        function checkboxOnclick(a){
            if($(a).is(':checked')) { 
                var val = new Array();
                var new_id=$('#news_id').val();
                if(new_id){
                    val=new_id.toString().split(",");
                }
                val.push($(a).val());
                b = val.join(',');
                $('#news_id').val(b)
             }else{
                var val = new Array();
                var new_id=$('#news_id').val();
                if(new_id){
                    val=new_id.toString().split(",");
                }
                val.remove($(a).val()); 
                b = val.join(',');
                $('#news_id').val(b)    
             }
        }

        $('.btn-success').click(function(){
            var content_id = $('#content_id').val();
            var news_id = $('#news_id').val();
            var imgurl = $('#image_url').val();
            $.post("<?php  echo url('platform/material/setContent_material')?>", {'content_id':content_id,'news_id':news_id,'imgurl':imgurl},function(data) {
                    data = $.parseJSON(data);
                   if(data.message.errno==0){
                        util.message(data.message.message, "", 'success');    
                    }else{
                        util.message(data.message.message, '', 'error');
                    }

            });
        });

    </script>