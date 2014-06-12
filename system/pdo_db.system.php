<?php
class pdo_db {
	private static $pdoInstance = null;
	
	private function pdo_db(){}
	private function __clone(){}
	
	public static function getInstance() {
		if (!self::$pdoInstance) {
			$db_type = Registry::getInstance()->config['database']['db_type'];
			$db_host = Registry::getInstance()->config['database']['db_hostname'];
			$db_name = Registry::getInstance()->config['database']['db_name'];
			$db_user = Registry::getInstance()->config['database']['db_username'];
			$db_pass = Registry::getInstance()->config['database']['db_password'];
			
			self::$pdoInstance = new PDO($db_type.':host='.$db_host.';dbname='.$db_name, $db_user,$db_pass);
			
			self::$pdoInstance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		return self::$pdoInstance;
	}
}
