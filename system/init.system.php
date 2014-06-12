<?php
include SITE_PATH.'system/registry.system.php';
include SITE_PATH.'system/urlhandler.system.php';
include SITE_PATH.'system/pdo_db.system.php';
include SITE_PATH.'system/db.system.php';
include SITE_PATH.'system/controllerdispatcher.system.php';
include SITE_PATH.'system/basecontroller.system.php';
include SITE_PATH.'system/template.system.php';
include SITE_PATH.'system/auth.system.php';
include SITE_PATH.'system/roles.system.php';
include SITE_PATH.'system/notifications.system.php';

Registry::getInstance();
pdo_db::getInstance();

function t($varName){
	echo Template::getInstance()->__get($varName);
}

function __autoload($className){
	// look for module file first, then model
	$moduleFilePath = SITE_PATH.'application/modules/'.strtolower($className).'/'.strtolower($className).'.class.php';
	if (is_readable($moduleFilePath)) {
		include $moduleFilePath;
		return;
	}
	
	$modelFilePath = SITE_PATH.'application/models/'.strtolower($className).'.model.php';
	if (!is_readable($modelFilePath)) {
		eval("class $className extends db{}");
	} else {
		include $modelFilePath;
	}
}

/** Sanitizing all user-supplied data. */
if(isset($_POST) && is_array($_POST)){
	foreach($_POST as $key=>$value){
		$_POST[$key] = mysql_escape_string($value);
	}
}
if(isset($_GET) && is_array($_GET)){
	foreach($_GET as $key=>$value){
		$_GET[$key] = mysql_escape_string($value);
	}
}
