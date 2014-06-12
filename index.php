<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors','on');

header('X-Powered-By: Evey');
header('Server: minty');

define('SITE_PATH',getcwd().'/');

include SITE_PATH."system/init.system.php";

UrlHandler::dispatch();
