<?php
define('IN_SHAFFER_FRAME', true);
require_once "system/function/core.php";
require_once 'system/common.inc.php';
require_once 'system/class/widget_password.php';


// md5('zxf123456') == '5949d48b3e54c09db970fb9de152db0d'
$info=array('username'=>'zxf','password'=>'5949d48b3e54c09db970fb9de152db0d');
echo Widget_Password::encrypt($info, $info['password']);



$str = '';
var_dump(empty($str));

$str = "1' or '1' = '1";
echo daddslashes($str);
echo "<br />";

$CookieVersion = 1;

$uid='1';
$user['username']='zxf';

$login_exp = 1361330598 - 90000;
$password_hash = substr(md5('e10adc3949ba59abbe56e057f20f883e'), 8, 8);

$uid='3';
$user['username']='zxf1';

$login_exp = 1461330598 + 900000;
$password_hash = substr(md5('0df995f74be7077b1e6701e958fad01a'), 8, 8);

$token = authcode("{$CookieVersion}\t{$uid}\t{$user['username']}\t{$login_exp}\t{$password_hash}", 'ENCODE');
var_dump($token);


function daddslashes($string, $force = 0, $strip = FALSE) {
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
?>