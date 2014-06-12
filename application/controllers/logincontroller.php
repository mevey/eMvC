<?php
class LoginController extends BaseController {
	public $requireLogin = false;
	
	public function _beforeAction() {
		$this->template->error_msg = $this->getAndUnsetSession('error_msg');
	}
	
	public function _afterAction() { 
		//$this->template->loadTemplateFile();
	}
	
	public function index_getAction() {
		$this->template->load('index.html');
	}
	
	public function index_postAction() {
		$userName = $_POST['username'];
		$password = sha1($_POST['password']);

		try {
			$user = new user("user_username = '$userName' AND user_password='$password'");
			Auth::setLoggedIn($user->getProperties());
			$this->redirect('dashboard');
		} catch (Exception $err) {
			$this->setSession('error_msg', 'Invalid Login Details Provided');
			$this->redirect('login');
		}
	}
	
}
