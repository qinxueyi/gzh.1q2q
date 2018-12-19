<?php 
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
load()->func('communication');
load()->model('cloud');
load()->func('file');
load()->func('up');

global $_W,$_GPC;


$step = $_GPC['step'];
$steps = array('files','filespro', 'schemas', 'scripts');
$step = in_array($step, $steps) ? $step : 'files';

	$hosturl = $_SERVER['HTTP_HOST'];
	$updatehost = 'http://we7.rocrm.cn/update.php';
	$pathl = IA_ROOT;
    $updatedir = IA_ROOT.'/data/update';
	$backdir = IA_ROOT.'/data/patch';
	$pathlist = to_md5($pathl);



if ($step == 'files' && $_GPC['m'] == 'prepare') {
  
        $mapfiles = $updatedir.'/res.json';
        $result = file_get_contents($mapfiles);
        $result = json_decode($result,TRUE);  
        $return = SendCurl($updatehost.'?a=update&u='.$hosturl.'&v='.$ver,$result);

        if($return == 5858){
            itoast(' &nbsp;未授权域名，请联系客服：<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=675069166&site=qq&menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=2:675069166:51" alt="联系若晨社区授权" title="联系若晨社区授权"/></a> ',create_url('cloud/upgrade',array('op' => 'display')),'error');
            die;
        }
        if($return == 7474){
            itoast(' &nbsp;授权IP错误，请联系客服：<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=675069166&site=qq&menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=2:675069166:51" alt="联系若晨社区授权" title="联系若晨社区授权"/></a> ',create_url('cloud/upgrade',array('op' => 'display')),'error');
            die;
        }
        if($return == 6161){
            itoast(' &nbsp;授权已到期，请联系客服：<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=675069166&site=qq&menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=2:675069166:51" alt="联系若晨社区授权" title="联系若晨社区授权"/></a> ',create_url('cloud/upgrade',array('op' => 'display')),'error');
            die;
        } 
      	$upgrade = $return;
      
		$filelist = array_filter($upgrade['B']);


      	$dbcheck = array_filter($upgrade['ret']);
      
        if(!empty($upgrade)){
          if(!is_dir($updatedir)) {
            mkdirs($updatedir,0777);
            chmod($updatedir,0777);
          }
          $upgrade['D'] = $filelist;
          $upgrade['E'] = $dbcheck;
          $mapfile = $updatedir.'/map.json';
          $content = json_encode($upgrade);
          $ret = file_put_contents($mapfile, $content, TRUE);
		  if(!$ret){
		  	itoast('系统错误！',create_url('cloud/upgrade',array('op' => 'display')),'error');
            die;
		  }
        }  
  
  
    if($_GPC['f'] == 0 && $_GPC['d'] == 0){
		itoast('没有要更新的内容！',create_url('cloud/upgrade',array('op' => 'display')),'error');
      	die;
    }
  
    if($_GPC['f'] == 0 && $_GPC['d'] != 0){
		//header("Location: ".create_url('cloud/process',array("step"=>"dbpro")) );
        $mapfile = $updatedir.'/map.json';
        $returns = file_get_contents($mapfile);
        $returns = json_decode($returns,TRUE);
      	pdo_query($returns['sqls']);
      	//header("Location: ".create_url('cloud/upgrade',array()) );
      	itoast('更新成功！',create_url('cloud/upgrade',array('op' => 'display')),'success');
      	exit;
    }
  
      
      	$back = date("Ymdhis");
      	$back = $backdir.'/'.$back;
      	if(!mkdirs($back)) {
          itoast('创建回滚目录失败，请返回重新尝试！',create_url('cloud/upgrade',array()),'error');
      	  die;
        }
    	header("Location: ".create_url('cloud/process',array("step"=>"filespro")) );
      	exit;
}

if (!empty($_GPC['m'])) {
	$m = $_GPC['m'];

  
} elseif (!empty($_GPC['d'])) {
  
	$dbcount = $_GPC['d'];
  
} elseif (!empty($_GPC['w'])) {

} else {
	$m = '';
    $mapfile = $updatedir.'/map.json';
    $returns = file_get_contents($mapfile);
  	$returns = json_decode($returns,TRUE);
  	$lastver = $returns['C']['name'];
	$packet = $returns;

}

if ($step == 'filespro') {
  
  $newver = "<?php return array ('ver' => '$lastver');?>";
  $ver = fopen(IA_ROOT.'/web/version.php','w+');
  fwrite($ver,$newver);
  fclose($ver);
  if($packet['sqls']){
  	pdo_query($packet['sqls']);
  }
}

if ($step == 'dbpro') {
  
  	//pdo_query($packet['sqls']);
    die;
  
}


template('cloud/process');