<?php
class DashBoardController extends BaseController {
	public function _beforeAction() {
		$this->template->setCurrentMenu('dash');
	}
	
	public function _afterAction() {
		
	}	
	
	public function index_getAction() {
		$log = new log();
		$where = 'log_to_user_id = '.Auth::getLoggedInUserId().' OR log_to_dept_id = '.Auth::getLoggedInUserData('user_fk_dept_id').' OR log_to_group_id ='.Auth::getLoggedInUserData('user_fk_cat_id');
		$this->template->notifications = db::select('log','*',$where,'log_id DESC');
		
		$this->template->news_data = db::select('news','*','news_publish = 1','news_id DESC');
		$this->template->load('dashboard.html');
	}
	
	public function index_postAction() {
		$this->template->load('dashboard.html');
	}
	
	public function help_getAction() {
		
	}
	
}

