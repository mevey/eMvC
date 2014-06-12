<?php
class ReportController extends BaseController {
	public function _beforeAction() {
		$this->template->setCurrentMenu('report');
	}
	
	public function _afterAction() {
		
	}
	
	public function index_getAction() {
		$this->template->load('reports.html');
	}
	
	public function index_postAction() {
		$this->template->load('reports.html');
	}
	
	public function help_getAction() {
		
	}
	
	public function assets_schedule_postAction() {
		
		$this->template->category_data = db::select('asset_category','*');
		$this->template->date_from = $_POST['from_date'];
		$this->template->date_to = $_POST['to_date'];
		
		$asset_category = new asset_category();
		$this->template->cat_count = $asset_category->getRowCount('asset_category','');
		$this->template->startSum = $this->asAtStart($_POST['from_date']);
		$this->template->additions = $this->addDate($_POST['from_date'],$_POST['to_date']);
		$this->template->disposals = $this->disposals($_POST['from_date'],$_POST['to_date']);
		
		$c = $this->template->load('assets_schedule_report.html');
	}
	
	public function assets_schedule_csv_getAction() {
		$this->template->category_data = db::select('asset_category','*');
		$this->template->date_from = $_POST['from_date'];
		$this->template->date_to = $_POST['to_date'];
		
		$asset_category = new asset_category();
		$this->template->cat_count = $asset_category->getRowCount('asset_category','');
		$this->template->startSum = $this->asAtStart($_POST['from_date']);
		$this->template->additions = $this->addDate($_POST['from_date'],$_POST['to_date']);
		$this->template->disposals = $this->disposals($_POST['from_date'],$_POST['to_date']);
		$this->template->load('assets_schedule_report_csv.html');
	}
	
	public function assets_schedule_pdf_postAction() {
		
		$this->template->category_data = db::select('asset_category','*');
		$this->template->date_from = $_POST['from_date'];
		$this->template->date_to = $_POST['to_date'];
		
		$asset_category = new asset_category();
		$this->template->cat_count = $asset_category->getRowCount('asset_category','');
		$this->template->startSum = $this->asAtStart($_POST['from_date']);
		$this->template->additions = $this->addDate($_POST['from_date'],$_POST['to_date']);
		$this->template->disposals = $this->disposals($_POST['from_date'],$_POST['to_date']);
		
		try {
			$c = $this->template->load('assets_schedule_report_pdf.html', true);
			$html2pdf = new HTML2PDF('L', 'A4', 'en');
			//$html2pdf->setModeDebug();
			$html2pdf->setDefaultFont('Dejavusans');
			$html2pdf->writeHTML($c, isset($_GET['vuehtml']));
			$html2pdf->Output('Fixed Assets Schedule.pdf');
		} catch(HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
	}
	
	public function asset_view_report_postAction() {
		$id = $_POST['tag_id'];
		$asset = new asset('asset_tag_id = "'.$id.'"');
		$this->template->media = db::select('asset_media','*','asset_media_fk_asset_id = '.$asset->asset_id);
		
		$asset_category = new asset_category($asset->asset_fk_cat_id);
		$this->template->category_name = $asset_category->asset_category_name;
		
		$this->template->data = $asset;
		$this->template->history = db::select('log','*','log_to_asset_id='.$asset->asset_id,'log_id DESC');
		$this->template->load('asset_view_report.html');
	}
	
	public function charts_getAction() {
		$this->template->dept_data = db::select('department','*');
		$this->template->load('charts.html');
	}
	
	//////////////////////////////////////////////////////////////////////////////////
	private function asAtStart($startDate) {
		$arr = array();
		$cat = db::select('asset_category', 'asset_category_id');
		foreach ($cat as $c) {
			$asset = db::select('asset','SUM(asset_cost) AS sum','asset_supply_date <= "'.$startDate.'" AND asset_state  <> 2 AND asset_fk_cat_id ='.$c['asset_category_id']);
			$arr[$c['asset_category_id']] = empty($asset[0]['sum']) ? 0 : $asset[0]['sum'];
		}
		return $arr;
	}
	
	private function addDate($startDate, $endDate) {
		$arr = array();
		$cat = db::select('asset_category', 'asset_category_id');
		foreach ($cat as $c) {
			$asset = db::select('asset','SUM(asset_cost)','asset_supply_date > "'.$startDate.'" AND asset_supply_date > "'.$endDate.'" AND asset_state <> 2');
			$arr[$c['asset_category_id']] = empty($asset[0]['sum']) ? 0 : $asset[0]['sum'];
		}
		return $arr;
	}
	
	private function disposals($startDate, $endDate) {
		$arr = array();
		$cat = db::select('asset_category', 'asset_category_id');
		foreach ($cat as $c) {
			$asset = db::select('asset','SUM(asset_cost)','asset_supply_date > "'.$startDate.'" AND asset_supply_date > "'.$endDate.'" AND asset_state = 2');
			$arr[$c['asset_category_id']] = empty($asset[0]['sum']) ? 0 : $asset[0]['sum'];
		}
		return $arr;
	}
	
	public function assets_register_getAction() {
		$this->redirect('report');
	}
	
	public function assets_register_postAction() {
		try{
			$asset = new asset('asset_tag_id = "'.$_POST['tag'].'"');
			
			$this->template->media = db::select('asset_media','*','asset_media_fk_asset_id = '.$asset->asset_id);
			
			$asset_category = new asset_category($asset->asset_fk_cat_id);
			$this->template->category_name = $asset_category->asset_category_name;
			
			$this->template->data = $asset;
			$this->template->asset_meta_data = db::select('asset_meta','*','asset_meta_fk_id = '.$asset->asset_id);
			$this->template->history = db::select('log','*','log_to_asset_id='.$asset->asset_id,'log_id DESC');
			
			$this->template->flag = 1;
			$this->template->tag = $_POST['tag'];
			$this->template->load('asset_register_report.html');
			
		} catch(Exception $err) {
			$this->setErrorMessage('Sorry, the asset with that tag ID does not exist.');
			$this->template->tag = $_POST['tag'];
			$this->redirect('report');
			return;
		}
	}
}







