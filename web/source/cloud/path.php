<?php 
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
require_once './../../../framework/bootstrap.inc.php';

load()->func('communication');
load()->model('cloud');
load()->func('file');
load()->func('up');


$path = $_GET['path'];

	$pathl = IA_ROOT;
	$hosturl = $_SERVER['HTTP_HOST'];
	$updatehost = 'http://we7.rocrm.cn/update.php';
    $updatedir = IA_ROOT.'/data/update';
	$backdir = IA_ROOT.'/data/patch';

if($_GET['type'] == 'file'){
  
  	$paths = array('file' => $path );
  	$file = SendCurl($updatehost.'?a=file&u='.$hosturl,$paths);
  	$filterl = file_back($pathl, $file, $backdir, $path);

	echo $filterl;
}

if($_GET['type'] == 'module'){
  	$mname = $_GET['mname'];
  	$pathl = IA_ROOT."/addons/".$mname;
  	$paths = array('file' => $path );
  	$file = SendCurl($updatehost.'?a=mfile&u='.$hosturl.'&m='.$mname,$paths);
  	$filterl = file_back($pathl, $file, $backdir, $path);

	echo $filterl;
}

if($_GET['type'] == 'db'){
  
	echo '1111';
}

if($_GET['type'] == 'del'){
  	$updatedir = $updatedir.'/map.json';
  	unlink($updatedir);
}