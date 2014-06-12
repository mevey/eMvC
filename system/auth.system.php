<?php
class Auth {
	public static function checkRole($roleName) {
		
	}
	
	public static function getLoggedInUserId() {
		if (self::isLoggedIn()){
			return $_SESSION['user_session']['user_id'];
		}
		throw new exception('User not logged in');
	}
	
	public static function getLoggedInUserName() {
		return self::getLoggedInUserData('user_username');
	}
	
	public static function getLoggedInUserData($key) {
		if (self::isLoggedIn()){
			return $_SESSION['user_session'][$key];
		}
		throw new exception('User not logged in');
	}
	
	public static function setLoggedIn($userData) {
		if (!isset($userData['user_id'])) throw new exception('User-Data required with user_id');
		$_SESSION['user_session'] = $userData;
	}
	
	public static function setLoggedOut() {
		$_SESSION['user_session']['user_id'] = 0;
		unset($_SESSION['user_session']);
	}
	
	public static function isLoggedIn() {
		if (empty($_SESSION['user_session']['user_id'])) {
			return false;
		}
		return true;
	}
}
