<?php
class ReadMemoController extends BaseController {
	public function _beforeAction() {
	}
	
	public function _afterAction() { 
	}
	
	public function index_getAction() {
		$this->template->load('ajax/blog/readmemo.html');
	}
	
	public function index_postAction() {
		$this->template->load('ajax/blog/readmemo.html');
	}
	
}

