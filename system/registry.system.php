<?php
class Registry {
	private static $instance = null;
	private $properties = array();
	private $isUserAgentMobile = null;
	
	private function Registry() {
		// Singleton-ing :)
		$this->config = parse_ini_file(SITE_PATH.'config.ini',true);
		
		// Set command paths
		$this->systemPath = SITE_PATH."system/";
		$this->controllerPath = SITE_PATH."application/controllers/";
		$this->modelPath = SITE_PATH."application/models/";
		$this->viewPath = SITE_PATH."application/views/";
		
		// Set some defaults
		$this->defaultController = "root";
		$this->defaultAction = "index";
		
		// set common data
		$this->baseUrl = $this->config['site']['base_url'];
	}
	
	private function __clone() {
	// Made private to prevent duplication of the current instance
	}
	
	public static function getInstance() {
		if (Registry::$instance == null) Registry::$instance = new Registry();
		return Registry::$instance;
	}
	
	public function __isset($propertyName) {
		return isset($this->properties[$propertyName]);
	}
	
	public function __get($propertyName) {
		if (isset($this->properties[$propertyName])) return $this->properties[$propertyName];
		throw new Exception('Registry: Property "'.$propertyName.'" not set');
	}
	
	public function __set($propertyName, $propertyValue) {
		if ($propertyName =='config' && isset($this->properties['config'])) {
			throw new Exception('Modifying config not allowed!');
		}
		//echo $propertyName."--\n";
		$this->properties[$propertyName] = $propertyValue;
	}
	
	public function isUserAgentMobile(){
		if ($this->isUserAgentMobile == null) {
			if ($this->config['template']['force_mobile'] == '1') {
				$this->isUserAgentMobile = true;
				return true;
			}
			
			$this->isUserAgentMobile = false;
			if (stripos($_SERVER['HTTP_USER_AGENT'],'mozilla')){
				$this->isUserAgentMobile = true;
				return true;
			}
			
			$arr = array('mobi','mobile');
			foreach ($arr as $r){
				if (stripos($_SERVER['HTTP_USER_AGENT'],$r) > -1) {
					$this->isUserAgentMobile = true;
					break;
				}
			}
		}
		
		return $this->isUserAgentMobile;
	}
	
	public static function checkRole($role) {
		if (!Auth::isLoggedIn()) {
			return false;
		}
		$user_id = Auth::getLoggedInUserId();
		$p = Registry::getInstance()->config['database']['db_prefix'];
		$t = $p.'user_role INNER JOIN '.$p.'role ON '.$p.'user_role.role_id = '.$p.'role.role_id';
		$dt = db::select($t,'*','user_id ='.$user_id.' AND ( role_name ="*" OR role_name = "'.$role.'")');
		if (count($dt) > 0) {
			if ($dt[0]['user_role_grant']  == 1) {
				return true;
			}
			return false;
		}
		$t = $p.'group_role INNER JOIN '.$p.'role ON '.$p.'group_role.role_id = '.$p.'role.role_id INNER JOIN '.$p.'user ON '.$p.'group_role.group_id = '.$p.'user.user_fk_cat_id';
		$dt = db::select($t,'*','user_id ='.$user_id.' AND role_name = "'.$role.'"');
		if (count($dt) > 0) {
			return true;
		}
		return false;
	}
	
	public static function getDate(){
		return date("Y-m-d H:i:s");
	}
	
	public static function getShortDate(){
		return date("Y-m-d");
	}
	
	public static function getUserAvatarUrl($user_id) {
		try {
			$user_media = new user_media('user_media_fk_user_id = '.$user_id);
			return Registry::getInstance()->baseUrl.$user_media->user_media_url_path;
		} catch(Exception $err) {
			return Registry::getInstance()->baseUrl.'application/uploads/user_images/face.jpg';
		}
		
	}
	
	public static function dateDifference($startDate, $endDate) {
		
		$diff = abs(strtotime($endDate) - strtotime($startDate));

		$years = floor($diff / (365*60*60*24));
		$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
		$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
		if ($months == 12 ){
			$years += 1;
			$months = '';
		}
		if ($years == '' && $months == ''){
			return $days.' days';
		} else if ($months == '' && $years != '') {
			return $years.' years, '.$days.' days';
		} else if ($years == ''){
			return $months.' months, '.$days.' days';
		} else {
			return $years.' years, '.$months.' months, '.$days.' days';
		}
	}
	
	public function getSalvageValue($cost, $supplyDate, $dep_rate) {
		$diff = abs(strtotime(Registry::getShortDate()) - strtotime($supplyDate));

		$years = floor($diff / (365*60*60*24));
		$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
		$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
		
		$age = $years + ($months/12);
		$dep = $cost * ($dep_rate/100) * $age;
		
		return ($cost - $dep) < $cost ? money_format('%.2n', ($cost-$dep)) : 0;
	}
}
