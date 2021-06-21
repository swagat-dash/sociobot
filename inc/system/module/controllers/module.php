<?php
class module extends MY_Controller {
	
	public $tb_purchase_manager = "sp_purchase_manager";
	public $module_name;

	public function __construct(){
		parent::__construct();
		_permission(get_class($this)."_enable");
		$this->load->model(get_class($this).'_model', 'model');

		//
		$this->module_name = get_module_config( $this, 'name' );
		$this->module_icon = get_module_config( $this, 'icon' );
		//

		$this->endpoint = "https://swagatdash.com/";
	}

	public function index($page = "", $category = "")
	{
		$categories = @file_get_contents($this->endpoint."category");
		$categories = json_decode($categories);

		$page_type = is_ajax()?false:true;

		//
		$data = [];
		switch ($page) {
			case 'product':
				$purchases = $this->model->fetch("*", $this->tb_purchase_manager);
				$purchase_array = [];
				if( !empty( $purchases ) ){
					foreach ($purchases as  $row) {
						$purchase_array[$row->item_id] = $row->version;
					}
				}

				$result = @file_get_contents($this->endpoint."product/".$category."?domain=".urlencode( base_url() )."&purchases=".serialize($purchase_array));
				$data['result'] = $result;
				break;
		}

		$page = page($this, "pages", "general", $page, $data, $page_type);
		//

		if( !is_ajax() ){

			$views = [
				"subheader" => view( 'main/subheader', [ 'result' => $categories, 'module_name' => $this->module_name, 'module_icon' => $this->module_icon ], true ),
				"column_one" => view("main/sidebar", [ 'result' => $categories, 'module_name' => $this->module_name, 'module_icon' => $this->module_icon ], true ),
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

	public function do_install(){
		$purchase_code = urlencode( trim( post("purchase_code") ) );
		$domain = base_url();

		$purchase = $this->model->get("*", $this->tb_purchase_manager, "purchase_code = '{$purchase_code}'");

		if(!empty($purchase)){
			ms([
				"status" => "error",
				"message" => __("This modules or themes is already installed")
			]);
		}

		$params = [
			"domain" => urlencode( $domain ),
			"purchase_code" => $purchase_code,
			"is_main" => 0
		];

		$result = @file_get_contents( $this->endpoint."install?".http_build_query( $params ) );
		
		if(!$result){
			ms([
				"status" => "error",
				"message" => __("There is a problem on your request. Please make sure your server enabled enough permission to can install.")
			]);
		}

		$result_array = json_decode( $result , 1 );
		if( is_array( $result_array ) && isset( $result_array['status'] ) && $result_array['status'] == "error"){
			ms($result);
		}

		$result = base64_decode( $result );
		$result = explode("{|}", $result);

		if( count( $result ) != 5 ){
			ms([
				"status" => "error",
				"message" => __("There was a problem during installation")
			]);
		}

	    if (!extension_loaded('zip')) {
	    	ms([
				"status" => "error",
				"message" => __("Please enable zip extension on your server to can install")
			]);
	    }

		$status = $result[0];
		$item_id = $result[1];
		$install_path = $result[2];
		$version = $result[3];
		$data = $result[4];
		$file = TMP_PATH.ids().".temp";

	    $fp = @fopen($file, 'w');
	    @fwrite( $fp, base64_decode( $data ) );
	    @fclose($fp);

		if(!is_file($file) || !is_readable(TMP_PATH)){
		    ms([
				"status" => "error",
				"message" => __("Can't read input")
			]);
		}

		if(!is_dir(TMP_PATH) || !is_writable(TMP_PATH)){
		    ms([
				"status" => "error",
				"message" => __("Can't write to target")
			]);
		}

		//Extract file
    	$zip = new ZipArchive;
		$response = @$zip->open($file);
		$file_count = @$zip->numFiles;
		if ($response === FALSE) {
			ms([
				"status" => "error",
				"message" => __("There was a problem during installation")
			]);
		}

		if(!$file_count){
			ms([
				"status" => "error",
				"message" => __("There was a problem during installation")
			]);
		}

		@$zip->extractTo($install_path);
		@$zip->close();

		//Insert data
		$save = array(
			"ids" => ids(),
			"item_id" => $item_id,
			"purchase_code" => $purchase_code,
			"version" => $version
		);

		$this->db->insert($this->tb_purchase_manager , $save);

		if( file_exists( $install_path."database.sql" ) ){
			$sql = @file_get_contents($install_path."database.sql");
			$sql_querys = explode(';', $sql);
			array_pop($sql_querys);

			foreach($sql_querys as $sql_query){
			    $sql_query = $sql_query . ";";
			    $this->db->query($sql_query);   
			}
		}

		//Remove Install
		@unlink($file);
		@unlink($install_path."database.sql");

		ms(array(
			"status" => "success",
			"message" => __("Success")
		));
	}

	public function do_update($item_id = "", $version = ""){
		$purchase = $this->model->get("*", $this->tb_purchase_manager, "item_id = '".$item_id."'");
		if( !$purchase ){
			ms([
				"status" => "error",
				"message" => __("This products does not exist")
			]);
		}

		$params = [
			"domain" => urlencode( base_url() ),
			"purchase_code" => $purchase->purchase_code,
			"version" => $version
		];

		$result = @file_get_contents( $this->endpoint."update?".http_build_query( $params ) );

		if(!$result){
			ms([
				"status" => "error",
				"message" => __("There is a problem on your request. Please make sure your server enabled enough permission to can install.")
			]);
		}

		$result_array = json_decode( $result , 1 );
		if( is_array( $result_array ) && isset( $result_array['status'] ) && $result_array['status'] == "error"){
			ms($result);
		}

		$result = base64_decode( $result );
		$result = explode("{|}", $result);

		if( count( $result ) != 5 ){
			ms([
				"status" => "error",
				"message" => __("There was a problem during installation")
			]);
		}

	    if (!extension_loaded('zip')) {
	    	ms([
				"status" => "error",
				"message" => __("Please enable zip extension on your server to can install")
			]);
	    }

	    $status = $result[0];
		$item_id = $result[1];
		$install_path = $result[2];
		$version = $result[3];
		$data = $result[4];

		$file = TMP_PATH.ids().".temp";

	    $fp = @fopen($file, 'w');
	    @fwrite( $fp, base64_decode( $data ) );
	    @fclose($fp);

		if(!is_file($file) || !is_readable(TMP_PATH)){
		    ms([
				"status" => "error",
				"message" => __("Can't read input")
			]);
		}

		if(!is_dir(TMP_PATH) || !is_writable(TMP_PATH)){
		    ms([
				"status" => "error",
				"message" => __("Can't write to target")
			]);
		}

		//Extract file
    	$zip = new ZipArchive;
		$response = @$zip->open($file);
		$file_count = @$zip->numFiles;
		if ($response === FALSE) {
			ms([
				"status" => "error",
				"message" => __("There was a problem during installation")
			]);
		}

		if(!$file_count){
			ms([
				"status" => "error",
				"message" => __("There was a problem during installation")
			]);
		}

		@$zip->extractTo($install_path);
		@$zip->close();

		$save = array(
			"version" => $version
		);

		$this->db->update($this->tb_purchase_manager , $save, [ "id" => $purchase->id ]);

		if( file_exists( $install_path."database.sql" ) ){
			$sql = @file_get_contents($install_path."database.sql");
			$sql_querys = explode(';', $sql);
			array_pop($sql_querys);

			foreach($sql_querys as $sql_query){
			    $sql_query = $sql_query . ";";
			    @$this->db->query($sql_query);   
			}
		}

		//Remove Install
		@unlink($file);
		@unlink($install_path."database.sql");

		ms(array(
			"status" => "success",
			"message" => __("Success")
		));
	}
}