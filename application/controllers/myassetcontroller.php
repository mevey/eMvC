<?php
class MyAssetController extends BaseController {
	public function _beforeAction() {
		$this->template->setCurrentMenu('myasset');
	
	}
	
	public function _afterAction() {
		
	}

	
	public function index_getAction() {
		$user_id = Auth::getLoggedInUserId();
		$p = Registry::getInstance()->config['database']['db_prefix'];
		
		$t = $p.'asset INNER JOIN '.$p.'allocation ON '.$p.'asset.asset_id = '.$p.'allocation.allocation_fk_asset_id';
		$this->template->list_data = db::select($t,'*','allocation_fk_user_id='.$user_id,'','');
		
		$this->template->category_data = db::select('asset_category','*','','','');
		
		$this->template->load('myassets.html');
	}
	public function index_postAction() {
		
	}
	
	public function view_my_asset_getAction() {
		try {
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$asset = new asset($_GET['id']);
			$this->template->asset_data = $asset->getProperties();
			
			$allocation = new allocation('allocation_fk_asset_id='.$_GET['id'].' and allocation_fk_user_id='.Auth::getLoggedInUserId());
			$this->template->allocation_data = $allocation->getProperties();
			
			
			$this->template->media = db::select('asset_media','*','asset_media_fk_asset_id = '.$_GET['id'],'','');
			
			$asset_category = new asset_category('asset_category_id='.$this->template->asset_data['asset_fk_cat_id']);
			$this->template->category_data = $asset_category->getProperties();
			
			$this->template->load('myasset_view.html');
			
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			$this->redirect('myasset');
			return;
		}
	}
	
	public function schedule_maintenance_postAction() {
		$dateTime = Registry::getDate();
		$maintenance = new maintenance();
		
		$maintenance->maintenance_fk_asset_id =$_POST['asset_id'];
		$maintenance->maintenance_fk_user_id =$_POST['user_id'];
		$maintenance->maintenance_fk_cat_id =$_POST['cat_id'];
		$maintenance->maintenance_fk_dept_id =$_POST['dept_id'];
		$maintenance->maintenance_problem_date = $dateTime;
		$maintenance->maintenance_problem = $_POST['problem'];
		
		$d = $maintenance->save();
		
		$asset = new asset($_POST['asset_id']);
		$user = new user($_POST['user_id']);
		$name = $user->user_surname.' '.$user->user_othername;
		$department = new department($_POST['dept_id']);
		$d = $department->department_name;
		
		$log = new log();
		$log->log_date = Registry::getDate();
		$log->log_by_user_id = Auth::getLoggedInUserId();
		$log->log_to_dept_id = 6;
		$log->log_to_asset_id = $maintenance->maintenance_fk_asset_id;
		$log->log_description = Notifications::schedule_maintenance($asset->asset_model, $asset->asset_tag_id,$name , $d);
		$log->save();
		$this->setSuccessMessage('Your request for maintenance has been delivered');
		$this->redirect('myasset');
	}
	
	public function return_asset_getAction() {
		try {
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$c = db::getRowCount('returned_asset','returned_asset_asset_id = '.$_GET['id']);
			if ($c > 0) {
				$this->setInfoMessage('Sorry, You have already requested for a return of this asset. It is awaiting processing from your head of department.');
				$this->redirect('myasset');
			}
			$returned_asset = new returned_asset();
			$returned_asset->returned_asset_user_id = Auth::getLoggedInUserId();
			$returned_asset->returned_asset_asset_id = $_GET['id'];
			$returned_asset->returned_asset_dept_id = Auth::getLoggedInUserData('user_fk_dept_id');
			$returned_asset->save();
			
			$log = new log();
			$log->log_date = Registry::getDate();
			$log->log_by_user_id = Auth::getLoggedInUserId();
			$log->log_to_dept_id = Auth::getLoggedInUserData('user_fk_dept_id');
			$log->log_to_asset_id = $_GET['id'];
			$name = Auth::getLoggedInUserData('user_surname').' '.Auth::getLoggedInUserData('user_othername');
			$log->log_description = Notifications::return_user_asset($asset->asset_model, $asset->asset_tag_id, $name);
			$log->save();
			
			$this->setSuccessMessage('Your request has been successfully sent. It is pending approval from your department head.');
			$this->redirect('myasset');
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			$this->redirect('myasset');
		}
	}
	
	public function make_report_postAction() {
		$report = new report();
		$report->report_fk_user_id = $_POST['user_id'];
		$report->report_fk_dept_id = $_POST['dept_id'];
		$report->report_fk_asset_id = $_POST['asset_id'];
		$report->report_remarks = $_POST['report'];
		$report->save();
		
		$this->setSuccessMessage('The report has been made successfully');
		$this->redirect('myasset');
	}
}




