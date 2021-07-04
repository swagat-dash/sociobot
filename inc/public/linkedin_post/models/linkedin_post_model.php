<?php
class linkedin_post_model extends MY_Model {
	public $tb_account_manager = "sp_account_manager";

	public function __construct(){
		parent::__construct();
		$module_path = get_module_directory(__DIR__);
		include $module_path.'libraries/vendor/autoload.php';
		include $module_path.'libraries/LinkedIn.php';

		//
		$this->module_name = get_module_config( $module_path, 'name' );
		$this->module_icon = get_module_config( $module_path, 'icon' );
		$this->module_color = get_module_config( $module_path, 'color' );
		//
		
		$app_id = get_option('linkedin_api_key', '');
        $app_secret = get_option('linkedin_api_secret', '');
        $app_callback = get_url( get_class($this) );
        $app_scopes = "r_emailaddress r_basicprofile r_liteprofile w_member_social rw_company_admin w_share";
        $ssl = false;

        $this->linkedin = new LinkedIn($app_id, $app_secret, $app_callback, $app_scopes, $ssl);
	}

	public function block_permissions($path = ""){
		$dir = get_directory_block(__DIR__, get_class($this));
		return [
			'position' => 8700,
			'name' => $this->module_name,
			'color' => $this->module_color,
			'icon' => $this->module_icon, 
			'id' => str_replace("_model", "", get_class($this)),
			'html' => view( $dir.'pages/block_permissions', ['path' => $path], true, $this ),
		];
	}

	public function block_report($path = ""){
		$dir = get_directory_block(__DIR__, get_class($this));
		return [
			'tab' => 'linkedin',
			'position' => 1000,
			'name' => $this->module_name,
			'color' => $this->module_color,
			'icon' => $this->module_icon, 
			'id' => str_replace("_model", "", get_class($this)),
			'html' => view( $dir.'pages/block_report', ['path' => $path], true, $this ),
		];
	}

	public function block_post_preview($path = ""){
		$dir = get_directory_block(__DIR__, get_class($this));
		return [
			'position' => 1100,
			'name' => $this->module_name,
			'color' => $this->module_color,
			'icon' => $this->module_icon, 
			'id' => str_replace("_model", "", get_class($this)),
			'preview' => view( $dir.'pages/preview', ['path' => $path], true, $this ),
		];
	}

	public function post_validator($data){
		$errors = array();

		switch ($data['post_type']) {

			case 'video':
				$errors[] = __("Linkedin API not support post video");
				break;
		}

		return $errors;
	}

	public function post( $data ){
		$post_type = $data["post_type"];
		$account = $data["account"];
		$medias = $data["medias"];
		$link = $data["link"];
		$advance = $data["advance"];
		$caption =  spintax( $data["caption"] );
		$is_schedule = $data["is_schedule"];
		$access_token = $account->token;
		$response = json_encode(["message" => __("Unknown error")]);

		if($is_schedule)
		{	
			return [
            	"status" => "success",
            	"message" => __('Success'),
            	"type" => $post_type
            ];
		}
		
		$params = [];

		if($account->category == "page"){
			$this->linkedin->setType("urn:li:organization:");
		}

		switch ($post_type)
		{
			case 'photo':
				if(count($medias) == 1){
					$medias[0] = watermark($medias[0], $account->team_id, $account->id);
					$response = $this->linkedin->linkedInPhotoPost($access_token, $account->pid, $caption, get_file_path($medias[0]), "", "");
				}else{
					$media_paths = [];
					foreach ($medias as $key => $media) {
						$media = watermark($media, $account->team_id, $account->id);
						$media_paths[] = [
							"title" => "",
							"desc" => "",
							"image_path" => get_file_path($media)
						];
					}

					$response = $this->linkedin->linkedInMultiplePhotosPost($access_token, $account->pid, $caption, $media_paths);
					unlink_watermark($medias);
				}
				break;

			case 'link':
				$link_info = get_link_info($link);
				$response = $this->linkedin->linkedInLinkPost($access_token, $account->pid, $caption, $link_info['title'], $link_info['description'], $link);
				break;

			case 'text':

				$response = $this->linkedin->linkedInTextPost($access_token, $account->pid, $caption);
				break;
			
		}

		$response = json_decode($response);
		if( isset($response->id) ){

			return [
            	"status" => "success",
            	"message" => __('Success'),
            	"id" => $response->id,
            	"url" => "https://www.linkedin.com/feed/update/".$response->id,
            	"type" => $post_type
            ]; 

		}else{
			if(isset($response->status) &&  ($response->status == 401 || $response->status == 403) ){
				$this->model->update($this->tb_account_manager, [ "status" => 0 ], [ "id" => $account->id ] );
			}

			$error = explode(" :: ", $response->message);
			$error = end($error);
			$error = str_replace("\"", "", $error);

			return [
            	"status" => "error",
            	"message" => __( $error ),
            	"type" => $post_type
            ];
		}
	}
}
