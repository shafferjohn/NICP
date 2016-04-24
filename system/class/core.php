<?php
class core{
	function __construct(){
		$this->init();
	}
	function init() {
		global $_config;
		if(!$_config) require_once SYSTEM_ROOT.'./config.cfg.php';
		$this->init_syskey();
	}
	function init_syskey(){
		if(SYS_KEY){
			define('ENCRYPT_KEY', SYS_KEY);
		}elseif(!getSetting('SYS_KEY')){
			$key = random(32);
			saveSetting('SYS_KEY', $key);
			define('ENCRYPT_KEY', $key);
		}else{
			define('ENCRYPT_KEY', getSetting('SYS_KEY'));
		}
	}
}
?>