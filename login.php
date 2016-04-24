<?php
error_reporting(E_ALL);
define('IN_SHAFFER_FRAME', true);
require_once './system/common.inc.php';
require_once 'system/function/core.php';

if(isset($_SERVER['HTTP_NICP_ACCESS_TOKEN'])){
	$token = $_SERVER['HTTP_NICP_ACCESS_TOKEN'];
	$uid = $isTokenValid = isTokenValid($token);
	if(!$isTokenValid) showMessage(406,'token无效','error');
	doLogin($uid);
	showUserInfo($uid,1,true);// showUserInfo($uid,$page,$outputToken)
}elseif(isset($_POST['info'])){// 没有token的话，只要POST帐号密码正确也可登陆

	try{
		$info = json_decode(urldecode($_POST['info']),true);
	}catch (Exception $e){
		showMessage(404,'非法操作','error');
	}
	$info['username'] = filter($info['username']);
	$info['password'] = filter($info['password']);
	$DB=new DB;
	$_info = $DB->fetch_first("SELECT * FROM member WHERE username ='{$info['username']}'");
	unset($DB);
	if($_info == false) showMessage(402,'无此用户名','error');
	
	$Widget_Password = new Widget_Password;
	$isPwValid = $Widget_Password->verify($_info, $info['password']);
	unset($Widget_Password);
	if(!$isPwValid) showMessage(403,'密码错误','error');

	$uid = intval($_info['uid']);
	doLogin($uid);
	showUserInfo($uid,1,true);
	exit();
}
showMessage(401,'登陆信息无效','error');
?>