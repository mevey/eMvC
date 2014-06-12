<?php
class ReportedAssetsController extends BaseController {
	public $requireLogin = true;
	
	public function _beforeAction() {
		$this->template->setCurrentMenu('departmentasset');
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
	
	public function _afterAction() {}
	
	private function searchClause() {
		$search =$this->getSession('search');
		if (empty($search)) {
			return '';
		}
		$search = pdo_db::getInstance()->quote('%'.$search.'%');
		return 'report_remarks LIKE '.$search;
	}
	
	private function orderClause() {
		$order_by = $this->getSession('order_by');
		$order_direction = strtoupper($this->getSession('order_direction'));
		if (empty($order_by)) {
			return '';
		} else if (!empty($order_direction) && ($order_direction == 'ASC' || $order_direction == 'DESC')){
			$order_by = $order_by.' '.$order_direction;
		}
		
		return $order_by;
	}
	
	public function index_getAction() {
		$order = new order();
		
		$p = Registry::getInstance()->config['database']['db_prefix'];
		$t = $p.'report AS A INNER JOIN '.$p.'user AS B on A.report_fk_user_id = B.user_id INNER JOIN '.$p.'asset AS C ON A.report_fk_asset_id = C.asset_id INNER JOIN '.$p.'department as D ON A.request_fk_dept_id = D.department_id ';
		
		$this->template->list_data = $order->getRows($t,'*',$this->searchClause(),$this->orderClause(),$this->getSession('items_per_page'),$this->getSession('page_number'));
		$this->template->search_data = $this->getSession('search');
		$this->template->order_direction = $this->getSession('order_direction');
		$this->template->order_by = $this->getSession('order_by');
		
		$this->template->load('reports.html');
	}
	
	public function index_postAction() {
		$this->template->load('reports.html');
	}
	
}

