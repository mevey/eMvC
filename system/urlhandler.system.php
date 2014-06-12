<?php
class UrlHandler{
	
	public static function dispatch(){
		$requestMethod = null;
		if(isset($_SERVER['REQUEST_METHOD'])) {
			$requestMethod = strtolower($_SERVER['REQUEST_METHOD']);
		} else {
			$requestMethod = 'get';
		}
		
		$controller = null;
		$action = null;
		$registry = Registry::getInstance();
		if (isset($_SERVER['PATH_INFO'])) {
			$urlParts = explode('.',$_SERVER['PATH_INFO']);
			if (isset($urlParts[1]) && strtolower($urlParts[1]) == 'json') {
				Registry::getInstance()->respondWithJson = true;
			}
			$pathParts = explode('/',$urlParts[0]);
			
			if (!empty($pathParts[1])) {
				$controller = $pathParts[1];
			} else {
				$controller = $registry->defaultController;
			}
			
			if (!empty($pathParts[2])) {
				$action = $pathParts[2];
			} else {
				$action = $registry->defaultAction;
			}
		} else {
			$controller = $registry->defaultController;
			$action = $registry->defaultAction;
		}
		
		$controllerDispatcher = new ControllerDispatcher($controller, $action, $requestMethod);
		$controllerDispatcher->dispatchToController();
	}
}
