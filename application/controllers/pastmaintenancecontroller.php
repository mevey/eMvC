<?php
class PastMaintenanceController extends BaseController {
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
			return 'maintenance_done = 1 ';
		}
		$search = pdo_db::getInstance()->quote('%'.$search.'%');
		return 'maintenance_done = 1 AND ( asset_tag_id LIKE '.$search.' OR department_name LIKE '.$search.' OR user_surname LIKE '.$search.' OR user_othername LIKE '.$search.'OR asset_model LIKE '.$search.' OR maintenance_id ='.$search.')';
	}
	
	private function orderClause() {
		$order_by = $this->getSession('order_by');
		$order_direction = strtoupper($this->getSession('order_direction'));
		if (empty($order_by)) {
			return 'maintenance_id DESC';
		} else if (!empty($order_direction) && ($order_direction == 'ASC' || $order_direction == 'DESC')){
			$order_by = $order_by.' '.$order_direction;
		}
		
		return $order_by;
	}
	
	public function index_getAction() {
		$maintenance = new maintenance();
		
		$p = Registry::getInstance()->config['database']['db_prefix'];
		$t = $p.'maintenance as A INNER JOIN '.$p.'asset as B ON A.maintenance_fk_asset_id = B.asset_id INNER JOIN '.$p.'user AS C ON A.maintenance_fk_user_id = C.user_id INNER JOIN '.$p.'department AS D ON A.maintenance_fk_dept_id = D.department_id INNER JOIN '.$p.'asset_category as E ON A.maintenance_fk_cat_id= E.asset_category_id';
		
		$this->template->list_data = $maintenance->getRows($t,'*',$this->searchClause(),$this->orderClause(),$this->getSession('items_per_page'),$this->getSession('page_number'));
		$this->template->search_data = $this->getSession('search');
		$this->template->order_direction = $this->getSession('order_direction');
		$this->template->order_by = $this->getSession('order_by');
		
		$this->template->load('maintenance_past.html');
	}
	
	public function index_postAction() {
		$this->template->load('maintenance_past.html');
	}
}





