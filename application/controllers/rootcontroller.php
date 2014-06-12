<?php
class RootController extends BaseController {
	public function _beforeAction() {
	}
	
	public function _afterAction() {
		
	}
	
	public function index_getAction() {
		$user = new user();
		$this->template->load('index.html');
	}
	
	public function index_postAction() {
		$this->template->load('index.html');
	}
	
	public function help_getAction() {
		
	}
	
}
