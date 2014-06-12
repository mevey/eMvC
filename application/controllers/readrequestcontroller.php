<?php
class ReadRequestController extends BaseController {
	public function _beforeAction() {
	}
	
	public function _afterAction() { 
	}
	
	public function index_getAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$request = new request($_GET['id']);
			$this->template->request_data = $request;
			$user = new user('user_id = '.$this->template->request_data->request_fk_user_id);
			$this->template->user_data = $user;
			$asset_category = new asset_category('asset_category_id = '.$this->template->request_data->request_fk_asset_cat_id);
			$this->template->cat_data = $asset_category;
			$this->template->load('ajax/blog/readrequest.html');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
	
	public function index_postAction() {
		$this->template->load('ajax/blog/readrequest.html');
	}
	
	public function send_dvc_postAction() {
		$request = new request();
		$request->request_fk_asset_cat_id = $_POST['category'];
		$request->request_model = $_POST['model'];
		$request->request_reference = 'REF/'.microtime();
		$request->request_title = $_POST['title'];
		$request->request_body = $_POST['body'];
		$request->request_date= $_POST['date'];
		$request->request_to_dvc = 1;
		$request->save();
		
		$this->setSuccessMessage('Your request has been sent successfully.');
		$this->redirect('userrequest');
		
	}
	
	public function send_hod_postAction() {
		$request = new request();
		$request->request_fk_asset_cat_id = $_POST['category'];
		$request->request_model = $_POST['model'];
		$request->request_reference = 'REF/'.microtime();
		$request->request_title = $_POST['title'];
		$request->request_body = $_POST['body'];
		$request->request_date= $_POST['date'];
		$request->request_to_hod = 1;
		$request->save();
		
		$this->setSuccessMessage('Your request has been sent successfully.');
		$this->redirect('userrequest');
		
	}
	
	public function send_hod_getAction() {
		$this->send_hod_postAction();
	}
	
	public function send_dvc_getAction() {
		$this->send_dvc_postAction();
	}
	
}

