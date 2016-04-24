<?php
error_reporting(E_ALL);
define('IN_SHAFFER_FRAME', true);
require_once './system/common.inc.php';
require_once SYSTEM_ROOT.'function/core.php';
require_once SYSTEM_ROOT.'class/widget_password.php';
// 调试开始

// $add_user = json_decode($_POST['adduser'],true);
// var_dump($add_user);
// exit();
// 调试结束

if(!isset($_SERVER['HTTP_NICP_ACCESS_TOKEN'])) showMessage(401,'登陆信息无效','error');
else{
	$token = $_SERVER['HTTP_NICP_ACCESS_TOKEN'];
	// var_dump($token); // 调试用
	$uid = $isTokenValid = isTokenValid($token);
	if(!$isTokenValid) showMessage(406,'token无效','error');
	doLogin($uid);
}
$DB = new DB;
$_info = $DB->fetch_first("SELECT * FROM member WHERE uid='$uid'");
unset($DB);

if(intval(filter($_info['role']))!==0) showUserInfo($uid);// role不是0，即非管理员

if(isset($_POST['adduser'])){

	$add_user = json_decode($_POST['adduser'],true);
	$add_user['username'] = filter($add_user['username']);
	$add_user['password'] = filter($add_user['password']);
	if(isUserExisit($add_user['username'])) showMessage(405,'用户已存在','error');
	else{
		$Widget_Password = new Widget_Password;
		$add_user['password'] = $Widget_Password->encrypt($add_user,$add_user['password']);
		$add_user['role'] = intval(filter($add_user['role']));
		$add_user['token'] = authcode(implode("\t", $add_user),'ENCODE');
		$add_uid = addUser($add_user);
		showUserInfo($add_uid);
	}
}elseif(isset($_GET['id'])){
	$viewid=intval(filter($_GET['id']));
	showUserInfo($add_uid);
}else{
	showUserInfo('ALL');
}
?>