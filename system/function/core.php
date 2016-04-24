<?php
if (!defined('IN_SHAFFER_FRAME')) exit();
require_once dirname(__FILE__).'/../config.inc.php';
require_once dirname(__FILE__).'/../config.cfg.php';
require_once dirname(__FILE__).'/../class/db.php';
require_once dirname(__FILE__).'/../class/error.php';
require_once dirname(__FILE__).'/../class/core.php';
require_once dirname(__FILE__).'/../class/widget_password.php';
$core = new core;

global $CookieVersion;
$CookieVersion = 1;
function RandomStr($len=1)
{
	$str='';
	for ($i=0; $i <$len; $i++) { 
		$str .= chr(rand(0,2)?rand(0,1)?rand(65,90):rand(97,122):rand(48,57));
	}
	return $str;
	
}
function getSetting($k, $force = false){
	$DB = new DB;
	if($force) return $setting_k = $DB->result_first("SELECT v FROM setting WHERE k='{$k}'");
	unset($DB);
	// $force==false 从cache里获取
	// $cache = CACHE::get('setting');
	// return $cache[$k];
}
function saveSetting($k, $v){
	$DB = new DB;
	$DB->query("REPLACE INTO setting SET v='{$v}', k='{$k}'");
	unset($DB);
	// if($cache_cleaned) return;
	// CACHE::clean('setting');
	// $cache_cleaned = true;
}
function showMessage($code = 400, $msg = '', $type='error'){
	switch ($type) {
		case 'user':
			$result = array($msg);
			break;
		case 'login':
			$result = array('id' => $msg['uid'], 'token' => $msg['token']);
			break;
		case 'error':
		default:
			$result = array('code' => $code, 'msg' => $msg);
			break;
	}
	echo json_encode($result);
	exit();
	
}
function showUserInfo($uid = 'ALL', $page = 1, $outputToken = false){
	if($outputToken){
		$DB = new DB;
		$result = $DB->fetch_first("SELECT uid,token FROM member WHERE uid='$uid'");
		unset($DB);
		showMessage(0, $result, 'login');
	}
	if($uid == 'ALL'){
		$DB = new DB;
		$result = $DB->fetch_all("SELECT uid,username,role FROM member");
		unset($DB);
		showMessage(0, $result, 'user');
	}else{
		$uid = intval($uid);
		$DB = new DB;
		$result = $DB->fetch_first("SELECT uid,username,role FROM member WHERE uid='$uid'");
		unset($DB);
		showMessage(0, $result, 'user');
	}
}
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	$ckey_length = 4;
	$key = md5($key ? $key : ENCRYPT_KEY);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);
	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);
	$result = '';
	$box = range(0, 255);
	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}
	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}
	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}
	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}
function isTokenValid($token = ''){
	global $CookieVersion;
	if(empty($_COOKIE['token']) && empty($token)) return false;
	$token = empty($token) ? $_COOKIE['token'] : $token;
	
	list($cv, $uid, $username, $exptime, $password) = explode("\t", authcode($token, 'DECODE'));

	if (!$uid || $cv != $CookieVersion || $exptime < TIMESTAMP) return false;
	elseif ($exptime > TIMESTAMP){
		$DB = new DB;
		$user = $DB->fetch_first("SELECT username,password FROM member WHERE uid='$uid'");
		unset($DB);
		$_password = substr(md5($user['password']), 8, 8);
		if ($user && $password == $_password) {
			$exptime = TIMESTAMP + 900;
			updateCookie('token', authcode("{$CookieVersion}\t{$uid}\t{$user['username']}\t{$exptime}\t{$password}", 'ENCODE'));
			return $uid;
		}else{
			updateCookie('token');
			return false;
		}
	}
}
function isUserExisit($username = ''){
	$DB = new DB;
	$isExisit = $DB->fetch_first("SELECT uid FROM member WHERE username ='$username'");
	unset($DB);
	return $isExisit['uid'];
}
function addUser($user){
	$DB = new DB;
	return $uid = $DB->insert('member',$user);
	unset($DB);
}
function doLogin($uid){
	global $CookieVersion;
	$DB = new DB;
	$user = $DB->fetch_first("SELECT username,password FROM member WHERE uid='$uid'");
	$password_hash = substr(md5($user['password']), 8, 8);
	$login_exp = TIMESTAMP + 900;
	$token = authcode("{$CookieVersion}\t{$uid}\t{$user['username']}\t{$login_exp}\t{$password_hash}", 'ENCODE');
	updateCookie('token',$token);
	$DB->query("UPDATE member SET token='{$token}' WHERE uid='$uid'");
	unset($DB);
	// showUserInfo($uid);
	return true;
}
function filter($string, $force = 0, $strip = FALSE) {
	!defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
	if(!MAGIC_QUOTES_GPC || $force) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = daddslashes($val, $force, $strip);
			}
		} else {
			$string = addslashes($strip ? stripslashes($string) : $string);
		}
	}
	return $string;
}
function updateCookie($name, $value = '', $exp = 2592000){
	$exp = $value ? TIMESTAMP + $exp : '1';
	setcookie($name, $value, $exp, '/');
}
?>