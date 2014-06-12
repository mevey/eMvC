<?php
class MapController extends BaseController {
	public function _beforeAction() {
		$this->template->setCurrentMenu('map');
	}
	
	public function _afterAction() { 
	}
	
	public function index_getAction() {
		$this->template->load('map.html');
	}
	
	public function index_postAction() {
		$this->template->load('map.html');
	}
	
}


