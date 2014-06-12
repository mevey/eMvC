<?php
abstract class BaseController {
	protected $registry = null;
	protected $template = null;
	protected $baseUrl = null;
	protected $controllerName = null;
	public $requireLogin = true;
	
	abstract function _beforeAction();
	abstract function _afterAction();
	abstract function index_getAction();
	
	public function BaseController() {
		$this->registry = Registry::getInstance();
		$this->template = Template::getInstance();
		$this->baseUrl = $this->registry->config['site']['base_url'];
		$this->controllerName = get_class($this);
		if (Auth::isLoggedIn()) {
			$this->template->unreadMemos = db::getRowCount('memo', 'memo_to_fk_user_id = '.Auth::getLoggedInUSerId().' AND memo_read = 0');
		
			$this->template->unreadForHodRequests = db::getRowCount('request', 'request_for_hod = 1 AND request_read = 0 AND request_fk_dept_id='.Auth::getLoggedInUserData('user_fk_dept_id'));
		
			$this->template->unreadForDvcRequests = db::getRowCount('request', 'request_for_dvc = 1 AND request_read = 0 ');
			
			$this->template->department_request_count = db::getRowCount('request','request_fk_allocation_id = 0 AND request_dvc_approve = 1 AND request_for_dvc = 1');
			
			$this->template->user_request_count = db::getRowCount('request','request_fk_allocation_id = 0 AND request_hod_approve = 1 AND request_for_hod = 1  AND request_fk_dept_id='.Auth::getLoggedInUserData('user_fk_dept_id'));
		}
		
	}
	
	protected function checkRole($roleName) {
		if (!Registry::checkRole($roleName)) {
			$this->template->role_name = $roleName;
			$this->template->load('forbidden.html');
			exit;
		}
	}
	
	public function redirect($url, $fullUrl = false) {
		$this->template->persistMessagesToSession();
		if ($fullUrl) {
			header('location: '.$url);
		} else {
			header('location: '.$this->baseUrl.$url);
		}
		exit;
	}
	
	protected function setSession($key, $value) {
		$_SESSION[$this->controllerName][$key] = $value;
	}
	
	protected function getSession($key) {
		return !isset($_SESSION[$this->controllerName][$key]) ?
				'' : $_SESSION[$this->controllerName][$key];
	}
	
	protected function getAndUnsetSession($key) {
		$dt = !isset($_SESSION[$this->controllerName][$key]) ?
				'' : $_SESSION[$this->controllerName][$key];
		unset($_SESSION[$this->controllerName][$key]);
		return $dt;
	}
	
	protected function unsetSession() {
		unset($_SESSION[$this->controllerName]);
	}
	
	protected function setSuccessMessage($msg) {
		$this->template->setSuccessMessage($msg);
	}
	
	protected function setErrorMessage($msg) {
		$this->template->setErrorMessage($msg);
	}
	
	protected function setInfoMessage($msg) {
		$this->template->setInfoMessage($msg);
	}
	
	protected function setAlertMessage($msg) {
		$this->template->setAlertMessage($msg);
	}
	
	protected function setAnnouncementMessage($msg) {
		$this->template->setAnnouncementMessage($msg);
	}
}
