<?php 
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
load()->func('communication');
load()->model('cloud');
load()->func('file');
load()->func('up');
load()->func('db');

global $_W,$_GPC;


$step = $_GPC['step'];
$steps = array('files','filespro', 'schemas', 'scripts');
$step = in_array($step, $steps) ? $step : 'files';
$res =array();
$m = $_GPC['m'];


	$hosturl = $_SERVER['HTTP_HOST'];
	$updatehost = 'http://we7.rocrm.cn/update.php';

	$mpathl = IA_ROOT.'/addons/'.$m;

    $updatedir = IA_ROOT.'/data/update';
	$backdir = IA_ROOT.'/data/patch';



if ($step == 'files' && $_GPC['d'] == 'prepare') {
  

      if(is_dir($mpathl)){
          $mpathlist = to_md5($mpathl);
      }else{
          $mpathlist = $res;
      }
      $mresult['mpath'] = json_encode($mpathlist);

      if(strlen($m) > 8){
          $tablelike = substr($m, 0, -2);
      }else{
          $tablelike = $m;
      }

      $msql = "show tables like '%".$tablelike."%'";
      $mtable = pdo_fetchall($msql,array());
      $mtable = to_arr($mtable);

      $remtable = SendCurl($updatehost.'?a=mtable&u='.$hosturl.'&m='.$m,$res);
      $mdifftable = array_diff($remtable,$mtable);
      $mresult['mdifftable'] = json_encode($mdifftable);

      $mloctable = array_diff($remtable,$mdifftable);

      $mupdatetable = local_tablelist($mloctable);
      $mresult['mupdatetable'] =  json_encode($mupdatetable);

      $mresult['md'] = json_encode(module_fetch($m));

      $mresult['mdbud'] = json_encode(module_buildlist($m));

      $content = json_encode($mresult);


        $result = json_decode($content,TRUE);  
        $return = SendCurl($updatehost.'?a=mupdate&u='.$hosturl.'&m='.$m,$result);  
 

        if($return == 5858){
            itoast('未授权域名，请联系客服QQ: ',create_url('cloud/redirect',array()),'error');
            die;
        }
        if($return == 7474){
            itoast('授权IP错误，请联系客服QQ:  ',create_url('cloud/redirect',array()),'error');
            die;
        }
        if($return == 6161){
            itoast('授权已经到期，请联系客服QQ: ',create_url('cloud/redirect',array()),'error');
            die;
        } 
      	$upgrade = $return;
      
    $filelist = array_filter($upgrade['B']);      
    if(!empty($upgrade)){
      if(!is_dir($updatedir)) {
        mkdirs($updatedir,0777);
        chmod($updatedir,0777);
      }
      $upgrade['m'] = $m;
      $upgrade['D'] = $filelist;
      $mapfile = $updatedir.'/moduleup.json';
      $content = json_encode($upgrade);
      $ret = file_put_contents($mapfile, $content, TRUE);
      if(!$ret){
        itoast('系统错误！',create_url('cloud/redirect',array()),'error');
        die;
      }
    } 
  
    if(empty($filelist)){
		$mapfile = $updatedir.'/moduleup.json';
        $returns = file_get_contents($mapfile);
        $returns = json_decode($returns,TRUE);
        $packet = $returns;
        $modname = $returns['m'];
      
        if(!empty($packet['newmod'])){
          pdo_insert('modules', $packet['newmod']);
          //cache_build_module_info($modname);
        }
        if(!empty($packet['upmod'])){
          $mid = module_mid($modname);
          pdo_update('modules', $packet['upmod'], array('mid' => $mid));
          //cache_build_module_info($modname);
        }
      
      
        if(!empty($packet['buddiff'])){
          $arry = $packet['buddiff'];
          $arr1 = array();
          $arr2 = array();
          if(!empty($arry['IN'])){
            foreach($arry['IN'] as  $key => $val){
              pdo_insert('modules_bindings', $val);
            }
          }
          if(!empty($arry['UP'])){
            foreach($arry['UP'] as  $keys => $value){
              $eid = array_search($value,$arry['UP']);
              pdo_update('modules_bindings', $value, array('eid' => $eid));
            }
          }
          //cache_build_module_info($modname);
        }
      cache_build_account_modules();
      cache_build_uninstalled_module();
      cache_build_cloud_upgrade_module();
      cache_build_module_info($modname);   
        if($packet['sqls']){
          pdo_query($packet['sqls']);
        }
      itoast('更新成功！',create_url('cloud/redirect',array()),'success');
    }
  
    if($_GPC['f'] == 0 && $_GPC['d'] != 0){

    }
  
      
      	$back = date("Ymdhis");
      	$back = $backdir.'/'.$back.'/addons/'.$m;
      	if(!mkdirs($back)) {
          itoast('创建回滚目录失败，请返回重新尝试！',create_url('cloud/redirect',array()),'error');
      	  die;
        }
    	header("Location: ".create_url('cloud/moduleup',array("step"=>"filespro")));
      	exit;
}



if(!empty($_GPC['m'])){
	//$m = $_GPC['m']; 
}elseif(!empty($_GPC['w'])) {

}else{
    $mapfile = $updatedir.'/moduleup.json';
    $returns = file_get_contents($mapfile);
  	$returns = json_decode($returns,TRUE);
	$packet = $returns;
  	$modname = $returns['m'];
}


if ($step == 'filespro') {
  
    if(!empty($packet['newmod'])){
      pdo_insert('modules', $packet['newmod']);
      //cache_build_module_info($modname);
    }
    if(!empty($packet['upmod'])){
      $mid = module_mid($modname);
      pdo_update('modules', $packet['upmod'], array('mid' => $mid));
      //cache_build_module_info($modname);
    }


    if(!empty($packet['buddiff'])){
      $arry = $packet['buddiff'];
      $arr1 = array();
      $arr2 = array();
      if(!empty($arry['IN'])){
        foreach($arry['IN'] as  $key => $val){
          pdo_insert('modules_bindings', $val);
        }
      }
      if(!empty($arry['UP'])){
        foreach($arry['UP'] as  $keys => $value){
          $eid = array_search($value,$arry['UP']);
          pdo_update('modules_bindings', $value, array('eid' => $eid));
        }
      }
      //cache_build_module_info($modname);
    }
  cache_build_account_modules();
  cache_build_uninstalled_module();
  cache_build_cloud_upgrade_module();
  cache_build_module_info($modname);
    if($packet['sqls']){
      pdo_query($packet['sqls']);
    }
}

if ($step == 'dbpro') {
  
  	//pdo_query($packet['sqls']);
    die;
  
}


template('cloud/moduleup');