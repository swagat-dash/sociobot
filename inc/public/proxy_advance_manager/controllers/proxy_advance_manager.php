<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class proxy_advance_manager extends MY_Controller {
	
	public $tb_proxy_manager = "sp_proxy_manager";
	public $tb_package_manager = "sp_package_manager";
	public $tb_account_manager = "sp_account_manager";
	public $module_name;

	public function __construct(){
		parent::__construct();
		_permission(get_class($this)."_enable");
		$this->load->model(get_class($this).'_model', 'model');

		$module_path = get_module_directory(__DIR__);
		include $module_path.'libraries/vendor/autoload.php';

		//
		$this->module_name = get_module_config( $this, 'name' );
		$this->module_icon = get_module_config( $this, 'icon' );
		//
	}

	public function index($page = "", $ids = "")
	{	
		$result = $this->model->fetch("*", $this->tb_proxy_manager);
		$page_type = is_ajax()?false:true;

		//
		$data = [];
		switch ($page) {
			case 'update':
				$item = $this->model->get("*", $this->tb_proxy_manager, "ids = '{$ids}'");
				$packages = $this->model->fetch("*", $this->tb_package_manager);
				$data['result'] = $item;
				$data['packages'] = $packages;
				break;

			case 'import':
				$packages = $this->model->fetch("*", $this->tb_package_manager);
				$data['packages'] = $packages;
				break;

			case 'assign':
				$data = $this->model->get_data();
				break;
		}

		$page = page($this, "pages", "general", $page, $data, $page_type);
		//

		if( !is_ajax() ){

			$views = [
				"subheader" => view( 'main/subheader', [ 'result' => $result, 'module_name' => $this->module_name, 'module_icon' => $this->module_icon ], true ),
				"column_one" => view("main/sidebar", [ 'result' => $result, 'module_name' => $this->module_name, 'module_icon' => $this->module_icon ], true ),
				"column_two" => view("main/content", [ 'view' => $page ] ,true), 
			];
			
			views( [
				"title" => $this->module_name,
				"fragment" => "fragment_two",
				"views" => $views
			] );

		}else{
			_e( $page, false );
		}

	}

	public function save($ids = "")
	{
		$status = post('status');
		$proxy = post('proxy');
		$location = post('location');
		$limit = post('limit');
		$packages = post('packages');

		$item = $this->model->get("*", $this->tb_proxy_manager, "ids = '{$ids}'");
		if(!$item){
			$item = $this->model->get("*", $this->tb_proxy_manager, "address = '{$proxy}'");
			validate('null', __('Proxy'), $proxy);
			validate('null', __('Location'), $location);
			validate('not_empty', __('This proxy already exists'), $item);

			$this->model->insert($this->tb_proxy_manager , [
				"ids" => ids(),
				"address" => $proxy,
				"location" => $location,
				"limit" => $limit,
				"packages" => json_encode($packages),
				"status" => $status,
				"changed" => now(),
				"created" => now()
			]);
		}else{
			$item = $this->model->get("*", $this->tb_proxy_manager, "ids != '{$ids}' AND address = '{$proxy}'");
			validate('null', __('Proxy'), $proxy);
			validate('null', __('Location'), $location);
			validate('not_empty', __('This proxy already exists'), $item);

			$this->model->update(
				$this->tb_proxy_manager, 
				[
					"address" => $proxy,
					"location" => $location,
					"limit" => $limit,
					"packages" => json_encode($packages),
					"status" => $status,
					"changed" => now()
				], 
				array("ids" => $ids)
			);
		}

		ms([
			"status" => "success",
			"message" => __('Success')
		]);
	}

	public function assign($ids = ""){
		$proxies = $this->model->fetch("*", $this->tb_proxy_manager, "", "id", "ASC");
		return view("pages/assign_modal", [ "proxies" => $proxies ] , false);
	}

	public function do_assign($ids = ""){
		$proxy_id = addslashes( post("proxy") );

		$account = $this->model->get("*", $this->tb_account_manager, "ids = '{$ids}'");
		validate('empty', __('Cannot find account to assign proxy'), $account);

		$proxy = $this->model->get("*", $this->tb_proxy_manager, "ids = '{$proxy_id}'");
		validate('empty', __('This proxy does not exist'), $proxy);

		$this->db->update(
			$this->tb_account_manager, 
			["proxy" => $proxy->id], 
			[ "id" => $account->id ]
		);

		ms([
			"status" => "success",
			"message" => __('Success')
		]);
	}

	public function cancel_assign($ids = ""){
		$account = $this->model->get("*", $this->tb_account_manager, "ids = '{$ids}'");
		validate('empty', __('Cannot find account to cancel assign proxy'), $account);

		$this->db->update(
			$this->tb_account_manager, 
			["proxy" => ""], 
			[ "id" => $account->id ]
		);

		ms([
			"status" => "success",
			"message" => __('Success')
		]);
	}

	public function do_export(){

		$limit = post("limit");
		$packages = post("packages");



		// Create new Spreadsheet object
	  	$spreadsheet = new Spreadsheet();
	  	$sheet = $spreadsheet->getActiveSheet();

		// add style to the header
	    $styleArray = array(
	      'font' => array(
	        'bold' => true,
	      ),
	      'alignment' => array(
	        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
	        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
	      ),
	      'borders' => array(
	          'bottom' => array(
	              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
	              'color' => array('rgb' => '5578eb'),
	          ),
	      ),
	      'fill' => array(
	        'type'       => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
	        'rotation'   => 90,
	        'startcolor' => array('rgb' => '0d0d0d'),
	        'endColor'   => array('rgb' => 'f2f2f2'),
	      ),
	    );
	    $spreadsheet->getActiveSheet()->getStyle('A1:C1')->applyFromArray($styleArray);
	    
	    // auto fit column to content
		foreach(range('A', 'C') as $columnID) {
	      $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
	    }

		// set the names of header cells
		$sheet->setCellValue('A1', 'Proxy');
		$sheet->setCellValue('B1', 'Packages');
		$sheet->setCellValue('C1', 'Limit');


  	 	$sheet->setCellValue('A2', "user:password@ip:port");
        $sheet->setCellValue('B2', $packages?json_encode($packages):"");
        $sheet->setCellValue('C2', $limit);

		//Create file excel.xlsx
 	 	$writer = new Xlsx($spreadsheet);

 	 	header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="proxy_import_template.xlsx"');
		$writer->save("php://output");
		//End Function index
	}

	public function do_import(){
		$config['upload_path']          = TMP_PATH;
        $config['allowed_types']        = 'xlsx';
        $config['encrypt_name']         = FALSE;

        $this->load->library('upload', $config);
        
        if(!empty($_FILES)){
	        $files = $_FILES;
		    for($i=0; $i< count($_FILES['files']['name']); $i++){  
		        $_FILES['files']['name']= $files['files']['name'][$i];
		        $_FILES['files']['type']= $files['files']['type'][$i];
		        $_FILES['files']['tmp_name']= $files['files']['tmp_name'][$i];
		        $_FILES['files']['error']= $files['files']['error'][$i];
		        $_FILES['files']['size']= $files['files']['size'][$i];
		        
		        $this->upload->initialize($config);

		        if (!$this->upload->do_upload("files"))
		        {
	                ms([
	                	"status"  => "error",
	                	"message" => $this->upload->display_errors()
	                ]);
		        }
		        else
		        {
		        	$info = (object)$this->upload->data();

		        	$inputFileName = $info->full_path;
		        	$inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);
					$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
					$spreadsheet = $reader->load($inputFileName);
					$rows = $spreadsheet->getActiveSheet()->toArray();

					if(count($rows) < 2){
						ms([
		                	"status"  => "error",
		                	"message" => __("Empty data")
		                ]);
					}

					$success = 0;
					$total = count($rows) - 1;

					foreach ( $rows as $key => $row ){
						if( $key != 0 ){
							if( count($row) == 3 ){
								$check = $this->model->get("id", $this->tb_proxy_manager, "address = '".addslashes($row[0])."'");
								if(!$check){

									$ip = "";
									$proxy = $row[0];
									$proxy_parse = explode("@", $proxy);

							        if(count($proxy_parse) > 1){
							            $ipport = explode(":", $proxy_parse[1]);
							            if(count($ipport) == 2){
							                $ip = $ipport[0];
							            }
							        }else{
							            $ipport = explode(":", $proxy_parse[0]);
							            if(count($ipport) == 2){
							                $ip = $ipport[0];
							            }
							        }

							        if($ip != ""){

										$result = get_curl("http://ip-api.com/json/".$ip);
										$result = json_decode($result);

										if(isset($result->status) && $result->status == 'success'){
											$this->model->insert($this->tb_proxy_manager , [
												"ids" => ids(),
												"address" => $row[0],
												"location" => $result->countryCode,
												"limit" => $row[2]?$row[2]:NULL,
												"packages" => is_array( json_decode($row[1], false) )?$row[1]:NULL,
												"status" => 1,
												"changed" => now(),
												"created" => now()
											]);

											$success++;
										}
							        }
								}
							}
						}
					}

					ms([
	                	"status"  => "success",
	                	"message" => sprintf( __("Added success %d of %d proxies"), $success, $total )
	                ]);
		        }
		    }
        }else{
        	load_404();
        }
	}

	public function proxy_info(){

		$ip = "";
		$proxy = post("proxy");
		$proxy_parse = explode("@", $proxy);

        if(count($proxy_parse) > 1){
            $ipport = explode(":", $proxy_parse[1]);
            if(count($ipport) == 2){
                $ip = $ipport[0];
            }
        }else{
            $ipport = explode(":", $proxy_parse[0]);
            if(count($ipport) == 2){
                $ip = $ipport[0];
            }
        }

        if($ip == ""){
        	ms([
        		"status" => "error",
        		"message" => __("Invalid or bad proxy")
        	]);
        }

		$result = get_curl("http://ip-api.com/json/".$ip);
		$result = json_decode($result);

		if($result->status == 'success'){
			ms([
				'status' => 'success',
				'code' => $result->countryCode
			]);
		}

		ms([
			'status' => 'error'
		]);
	}

	public function export(){
		export_csv($this->tb_proxy_manager);
	}

	public function delete(){
		$ids = post('id');

		if( empty($ids) ){
			ms([
				"status" => "error",
				"message" => __('Please select an item to delete')
			]);
		}

		if( is_array($ids) ){
			foreach ($ids as $id) {
				$this->model->delete($this->tb_proxy_manager, ['ids' => $id]);
			}
		}
		elseif( is_string($ids) )
		{
			$this->model->delete($this->tb_proxy_manager, ['ids' => $ids]);
		}

		ms([
			"status" => "success",
			"message" => __('Success')
		]);
	}
}