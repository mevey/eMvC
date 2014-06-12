<?php
class MaintenanceController extends BaseController {
	public function _beforeAction() {
		$this->template->setCurrentMenu('maintenance');
		if (isset($_GET['page_number'])) {
			$this->setSession('page_number',$_GET['page_number']);
		}
		if (isset($_GET['items_per_page'])) {
			$this->setSession('items_per_page',$_GET['items_per_page']);
		}
		if (isset($_GET['search'])) {
			$this->setSession('search',$_GET['search']);
		}
		if (isset($_GET['order_by'])) {
			if ($this->getSession('order_by') == $_GET['order_by']) {
				if ( $this->getSession('order_direction') == 'ASC') {
					$this->setSession('order_direction','DESC');
				} else {
					$this->setSession('order_direction','ASC');
				}
			}
			$this->setSession('order_by',$_GET['order_by']);
		}
		if (isset($_GET['order_direction'])) {
			$this->setSession('order_direction',$_GET['order_direction']);
		}
	}
	
	public function _afterAction() {
		
	}
	
	private function searchClause() {
		$search =$this->getSession('search');
		if (empty($search)) {
			return '';
		}
		$search = pdo_db::getInstance()->quote('%'.$search.'%');
		return ' AND asset_tag_id LIKE '.$search.' OR department_name LIKE '.$search.' OR user_surname LIKE '.$search.' OR user_othername LIKE '.$search.'OR asset_model LIKE '.$search.'';
	}
	
	private function orderClause() {
		$order_by = $this->getSession('order_by');
		$order_direction = strtoupper($this->getSession('order_direction'));
		if (empty($order_by)) {
			return 'maintenance_id ASC';
		} else if (!empty($order_direction) && ($order_direction == 'ASC' || $order_direction == 'DESC')){
			$order_by = $order_by.' '.$order_direction;
		}
		
		return $order_by;
	}
	
	public function index_getAction() {
		$maintenance = new maintenance();
		
		$p = Registry::getInstance()->config['database']['db_prefix'];
		$t = $p.'maintenance as A INNER JOIN '.$p.'asset as B ON A.maintenance_fk_asset_id = B.asset_id INNER JOIN '.$p.'user AS C ON A.maintenance_fk_user_id = C.user_id INNER JOIN '.$p.'department AS D ON A.maintenance_fk_dept_id = D.department_id INNER JOIN '.$p.'asset_category as E ON A.maintenance_fk_cat_id= E.asset_category_id';
		
		$this->template->list_data = $maintenance->getRows($t,'*','maintenance_done = 0 '.$this->searchClause(),$this->orderClause(),$this->getSession('items_per_page'),$this->getSession('page_number'));
		
		$this->template->search_data = $this->getSession('search');
		$this->template->order_direction = $this->getSession('order_direction');
		$this->template->order_by = $this->getSession('order_by');
		
		$this->template->load('maintenance.html');
	}
	
	public function index_postAction() {
		$this->template->load('maintenance.html');
	}
	
	public function help_getAction() {
		
	}
	
	public function past_repairs_getAction() {
		$maintenance = new maintenance();
		
		$p = Registry::getInstance()->config['database']['db_prefix'];
		$t = $p.'maintenance as A INNER JOIN '.$p.'asset as B ON A.maintenance_fk_asset_id = B.asset_id INNER JOIN '.$p.'user AS C ON A.maintenance_fk_user_id = C.user_id INNER JOIN '.$p.'department AS D ON A.maintenance_fk_dept_id = D.department_id INNER JOIN '.$p.'asset_category as E ON A.maintenance_fk_cat_id= E.asset_category_id';
		
		$this->template->list_data = $maintenance->getRows($t,'*','maintenance_done = 1 '.$this->searchClause(),$this->orderClause(),$this->getSession('items_per_page'),$this->getSession('page_number'));
		$this->template->search_data = $this->getSession('search');
		$this->template->order_direction = $this->getSession('order_direction');
		$this->template->order_by = $this->getSession('order_by');
		
		$this->template->load('maintenance_past.html');
	}
	
	public function send_report_postAction() {
		try{
			if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
				throw new exception();
			}
			$maintenance = new maintenance('maintenance_id='.$_GET['id']);
			$maintenance->maintenance_report = $_POST['report'];
			$maintenance->maintenance_report_date = Registry::getDate();
			$maintenance->maintenance_user_id = Auth::getLoggedInUserId();
			$maintenance->maintenance_done = 1;
			$maintenance->save();
			
			$asset = new asset($maintenance->maintenance_fk_asset_id);
			$user = new user($maintenance->maintenance_user_id);
			$name = $user->user_surname.' '.$user->user_othername;
			
			$log = new log();
			$log->log_date = Registry::getDate();
			$log->log_by_user_id = Auth::getLoggedInUserId();
			$log->log_to_user_id = $maintenance->maintenance_fk_user_id;
			$log->log_to_asset_id = $maintenance->maintenance_fk_asset_id;
			$log->log_description = Notifications::repair_report($asset->asset_model, $asset->asset_tag_id,$name , $_GET['id']);
			$log->log_asset_description = Notifications::asset_repair_report($name, $maintenance->maintenance_problem, $maintenance->maintenance_report);
			$log->save();
			
			$this->setSuccessMessage('The report has been posted successfully.');
			$this->redirect('maintenance/past_repairs');
			
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, something went wrong. Please try again.');
			return;
		}
	}
	
	public function search_asset_getaction() {
		$this->template->flag = 0;
		$this->template->load('maintenance_asset_view.html');
	}
	
	public function view_asset_postAction() {
		try{
			$asset = new asset('asset_tag_id = "'.$_POST['tag'].'"');
			
			$this->template->media = db::select('asset_media','*','asset_media_fk_asset_id = '.$asset->asset_id);

			$asset_category = new asset_category($asset->asset_fk_cat_id);
			$this->template->category_name = $asset_category->asset_category_name;
			$this->template->data = $asset;
			$this->template->history = db::select('log','*','log_to_asset_id='.$asset->asset_id,'log_id DESC');
			
			$this->template->flag = 1;
			$this->template->tag = $_POST['tag'];
			$this->template->load('maintenance_asset_view.html');
			
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, the asset with that tag ID does not exist.'.$err);
			$this->template->tag = $_POST['tag'];
			$this->redirect('maintenance/search_asset');
			return;
		}
	}
}





