<?php
class ControllerDispatcher {
	protected $controller = null;
	protected $action = null;
	protected $requestMethod = null;
	protected $template = null;
		
	public function ControllerDispatcher($controller, $action, $requestMethod) {
		$this->controller = strtolower($controller);
		$this->action = $action;
		$this->requestMethod = $requestMethod;
		$this->template = Template::getInstance();
		
		$forceDesktop = Registry::getInstance()->config['template']['force_desktop'];
		if (isset($_GET['force_desktop']) && $_GET['force_desktop'] == 'rem' && $forceDesktop == '0') {
			unset($_SESSION['force_desktop']);
		} else if ($forceDesktop == '1' || isset($_GET['force_desktop']) || isset($_SESSION['force_desktop'])) {
			$_SESSION['force_desktop'] = 1;
			$this->template->forceDesktop(true);
		}
		
	}
	
	public function dispatchToController(){
		$controllerFilePath = Registry::getInstance()->controllerPath . $this->controller .'controller.php';
		
		if (is_readable($controllerFilePath)) {
			
			include $controllerFilePath;
			
			$controllerClassName = $this->controller.'Controller';
			$controller = new $controllerClassName();
			
			$actionName = $this->action.'_'.$this->requestMethod.'Action';
			if (is_callable(array($controller, $actionName))) {
				$baseUrl = Registry::getInstance()->config['site']['base_url'];
				// $secureBaseUrl = Registry::getInstance()->config['site']['secure_base_url'];
				if ($controller->requireLogin) {
					if (!Auth::isLoggedIn()) {
						header('location: '.$baseUrl.'login');
						exit;
					}
				} 
				$controller->_beforeAction();
				$controller->$actionName();
				$controller->_afterAction();
			} else if (is_callable(array($controller, '_404Handler'))) {
				$controller->_404Handler();
			} else {
				$this->_404Handler();
			}
		} else {
			$this->_404Handler();
		}
	}
	
	public function _404Handler() {
		$this->template->load('notfound.html');
	}
	
}
