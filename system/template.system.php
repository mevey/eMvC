<?php
class Template {
	private static $instance = null;
	private $properties = array();
	private $templateName = null;
	private $templateFilePath = null;
	private $reg = null;
	private $forceDesktop = false;
	private $menuItems = array();
	
	private function Template() {
		// Singleton-ing :)
		$this->reg = Registry::getInstance();
		
		//Define the menu items
		$this->menuItems['asset'] = '';
		$this->menuItems['myasset'] = '';
		$this->menuItems['cat'] = '';
		$this->menuItems['dash'] = '';
		$this->menuItems['user'] = '';
		$this->menuItems['memo'] = '';
		$this->menuItems['request'] = '';
		$this->menuItems['employeerequest'] = '';
		$this->menuItems['dept_asset'] = '';
		$this->menuItems['departmentrequest'] = '';
		$this->menuItems['log'] = '';
		$this->menuItems['maintenance'] = '';
		$this->menuItems['myprofile'] = '';
		$this->menuItems['reports'] = '';
		$this->menuItems['map'] = '';
		
		// load messages saved to session
		$this->loadMessagesFromSession();
	}
	
	public function setCurrentMenu($menuItemName) {
		$this->menuItems[$menuItemName] = 'class="current"';
	}
	
	private function __clone() {
	// Made private to prevent duplication of the current instance
	}
	
	public static function getInstance() {
		if (Template::$instance == null) Template::$instance = new Template();
		return Template::$instance;
	}
	
	public function __get($propertyName) {
		if (isset($this->properties[$propertyName])) return $this->properties[$propertyName];
		throw new Exception('Template: Property "'.$propertyName.'" not set');
	}
	
	public function __set($propertyName, $propertyValue) {
		$this->properties[$propertyName] = $propertyValue;
	}
	
	public function __isset($propertyName) {
		return isset($this->properties[$propertyName]);
	}
	
	public function load($templateName, $bufferOutput = false) {
		$this->templateFilePath = SITE_PATH.'application/views/';
		
		$this->baseUrl = $this->reg->config['site']['base_url'];
		$this->secureBaseUrl = $this->reg->config['site']['secure_base_url'];
		
		$desktopTemplate = $this->reg->config['template']['desktop_name'];
		$mobileTemplate = $this->reg->config['template']['mobile_name'];
		if ($this->reg->isUserAgentMobile() && !$this->forceDesktop){
			$templateFileName = $this->templateFilePath.'mobile/'.$mobileTemplate.'/'.$templateName;
			$this->templatePathUrl = $this->reg->config['site']['site_path_url'].'application/views/mobile/'.$mobileTemplate.'/';
			$this->secureTemplatePathUrl = $this->reg->config['site']['secure_site_path_url'].'application/views/mobile/'.$mobileTemplate.'/';
		} else {
			$templateFileName = $this->templateFilePath.'desktop/'.$desktopTemplate.'/'.$templateName;
			$this->templatePathUrl = $this->reg->config['site']['site_path_url'].'application/views/desktop/'.$desktopTemplate.'/';
			$this->secureTemplatePathUrl = $this->reg->config['site']['secure_site_path_url'].'application/views/desktop/'.$desktopTemplate.'/';
		}
		
		if (isset(Registry::getInstance()->respondWithJson) && Registry::getInstance()->respondWithJson == true) {
			echo json_encode($this->properties);
			return;
		}
		
		if (is_readable($templateFileName)) {
			foreach ($this->properties as $key=>$value) {
				$$key = $value;
			}
			
			if ($bufferOutput) {
				ob_start();
				include $templateFileName;
				return ob_get_clean();
			} else {
				include $templateFileName;
			}
			
		} else {
			throw new Exception('Template file "'.$templateFileName.'" not readable or doesn\'t exist');
		}
	}
	
	public function setTemplateFile($templateName) {
		$this->templateName = $templateName;
	}
	
	public function loadTemplateFile() {
		if (empty($this->templateName)) {
			Throw new Exception('Template file not set.');
		} else {
			$this->load($this->templateName);
		}
	}
	
	public function forceDesktop($force){
		$this->forceDesktop = $force;
	}
	
	public function setSuccessMessage($msg) {
		$this->success_messages = $msg;
	}
	
	public function setErrorMessage($msg) {
		$this->error_messages = $msg;
	}
	
	public function setInfoMessage($msg) {
		$this->info_messages = $msg;
	}
	
	public function setAlertMessage($msg) {
		$this->alert_messages = $msg;
	}
	
	public function setAnnouncementMessage($msg) {
		$this->announcement_messages = $msg;
	}
	
	public function persistMessagesToSession() {
		$_SESSION['template']['success_messages'] = $this->success_messages;
		$_SESSION['template']['error_messages'] = $this->error_messages;
		$_SESSION['template']['info_messages'] = $this->info_messages;
		$_SESSION['template']['alert_messages'] = $this->alert_messages;
		$_SESSION['template']['announcement_messages'] = $this->announcement_messages;
	}
	
	private function loadMessagesFromSession() {
		$this->success_messages = isset($_SESSION['template']['success_messages']) ? $_SESSION['template']['success_messages'] : '';
		$this->error_messages = isset($_SESSION['template']['error_messages']) ? $_SESSION['template']['error_messages'] : '';
		$this->info_messages = isset($_SESSION['template']['info_messages']) ? $_SESSION['template']['info_messages'] : '';
		$this->alert_messages = isset($_SESSION['template']['alert_messages']) ? $_SESSION['template']['alert_messages'] : '';
		$this->announcement_messages = isset($_SESSION['template']['announcement_messages']) ? $_SESSION['template']['announcement_messages'] : '';
		unset($_SESSION['template']);
	}
}
