<?php
class NotFoundController extends BaseController {
	public function _beforeAction() {
	}
	
	public function _afterAction() { 
	}
	
	public function index_getAction() {
		$this->template->load('notfound.html');
	}
	
	public function index_postAction() {
		$this->template->load('notfound.html');
	}
	
}

