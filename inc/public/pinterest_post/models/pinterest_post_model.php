<?php
use DirkGroenen\Pinterest\Pinterest;
use seregazhuk\PinterestBot\Factories\PinterestBot;
class pinterest_post_model extends MY_Model {
	public $tb_account_manager = "sp_account_manager";

	public function __construct(){
		parent::__construct();
		$module_path = get_module_directory(__DIR__);
		include $module_path.'libraries/vendor/autoload.php';

		//
		$this->module_name = get_module_config( $module_path, 'name' );
		$this->module_icon = get_module_config( $module_path, 'icon' );
		$this->module_color = get_module_config( $module_path, 'color' );
		//
		
		$this->app_id = get_option('pinterest_app_id', '');
        $this->app_secret = get_option('pinterest_app_secret', '');
        $this->bot = PinterestBot::create();
	}

	public function block_permissions($path = ""){
		$dir = get_directory_block(__DIR__, get_class($this));
		return [
			'position' => 8600,
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
			'tab' => 'pinterest',
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
			'position' => 1000,
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
			case 'text':
				$errors[] = __("Pinterest API not support post video");
				break;

			case 'link':
				if(empty($data['medias'])){
					$errors[] = __("Pinterest requires an image");
				}
				break;

			case 'video':
				$errors[] = __("Pinterest API not support post video");
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
		$caption = spintax( $data["caption"] );
		$is_schedule = $data["is_schedule"];
		$endpoint = "statuses/update";
		
		if($is_schedule)
		{	
			return [
            	"status" => "success",
            	"message" => __('Success'),
            	"type" => $post_type
            ];
		}
		
		switch ($account->login_type) {
			case 1:
				$access_token = json_decode($account->token);

				$pinterest = new Pinterest( $this->app_id, $this->app_secret );
                $pinterest->auth->setOAuthToken($access_token->access_token);

                 try {
                 	$medias[0] = watermark($medias[0], $account->team_id, $account->id);
                 	$params = [
                 		"image_url"  => $medias[0],
	                    "note"       => $caption,
	                    "board"      => $account->pid,
                 	];

                 	if($link != ""){
                 		$params["link"] = $link;
                 	}

	                $response = $pinterest->pins->create($params);
	                $response = (object)$response;
	                return [
		            	"status" => "success",
		            	"message" => __('Success'),
		            	"id" => $response->id,
		            	"url" => $response->url,
		            	"type" => $post_type
		            ]; 
	            } catch (Exception $e) {
	            	if($e->getCode() == 401){
	            		$this->model->update($this->tb_account_manager, [ "status" => 0 ], [ "id" => $account->id ] );
	            	}

	            	$error = explode(": ", $e->getMessage());
	                return array(
	                    "status"  => "error",
	                    "message" => end($error),
	                    "type" => $post_type
	                );
	            }
				break;

			case 2:

				try {
					$this->proxy($account->proxy);
					$access_token = json_decode($account->token);
					$this->bot->auth->login($access_token->username, encrypt_decode($access_token->password));
				} catch (Exception $e) {
					$this->model->update($this->tb_account_manager, [ "status" => 0 ], [ "id" => $account->id ] );
					return array(
	                    "status"  => "error",
	                    "message" => $e->getMessage(),
	                    "type" => $post_type
	                );
				}
				
				try {
					
					$medias[0] = watermark($medias[0], $account->team_id, $account->id);
	                $response = $this->bot->pins->create(
	                    $medias[0],
	                    $account->pid,
	                    $caption,
	                    $link
	                );

	                if(!empty($response)){
	                	$response = (object)$response;
	                    return [
			            	"status" => "success",
			            	"message" => __('Success'),
			            	"id" => $response->id,
			            	"url" => "https://www.pinterest.com/pin/".$response->id,
			            	"type" => $post_type
			            ]; 
	                }

	                return array(
	                    "status"  => "error",
	                    "message" => __("Image does not exist"),
	                    "type" => $post_type
	                );
	                
	            } catch (Exception $e) {
	                return array(
	                    "status"  => "error",
	                    "message" => $e->getMessage(),
	                    "type" => $post_type
	                );
	            }

				break;

			case 3:

				$access_token = json_decode($account->token);

				$pinterest = new Pinterest( $access_token->app_id, $access_token->app_secret );
                $pinterest->auth->setOAuthToken($access_token->access_token);

                 try {
                 	$medias[0] = watermark($medias[0], $account->team_id, $account->id);
                 	$params = [
                 		"image_url"  => $medias[0],
	                    "note"       => $caption,
	                    "board"      => $account->pid,
                 	];

                 	if($link != ""){
                 		$params["link"] = $link;
                 	}

	                $response = $pinterest->pins->create($params);
	                $response = (object)$response;
	                unlink_watermark($medias);
	                return [
		            	"status" => "success",
		            	"message" => __('Success'),
		            	"id" => $response->id,
		            	"url" => $response->url,
		            	"type" => $post_type
		            ]; 
	            } catch (Exception $e) {
	            	if($e->getCode() == 401){
	            		$this->model->update($this->tb_account_manager, [ "status" => 0 ], [ "id" => $account->id ] );
	            	}

	            	$error = explode(": ", $e->getMessage());
	                return array(
	                    "status"  => "error",
	                    "message" => end($error),
	                    "type" => $post_type
	                );
	            }

				break;
		}

		return array(
            "status"  => "error",
            "message" => __("Unknown error"),
            "type" => $post_type
        );
	}

	public function proxy($proxy){
        $proxy = get_proxy($proxy);

        if($proxy != "" && $proxy != 0){
            $proxy = str_replace("https://", "", $proxy);
            $proxy = str_replace("http://", "", $proxy);
            $ip = "";
            $port = "";
            $auth = "";
            
            if(strripos($proxy, "@") !== false){

                $proxy_arr = explode("@", $proxy);
                $auth = $proxy_arr[0];
                $proxy_none = explode(":", $proxy_arr[1]);
                $ip = $proxy_none[0];
                $port = $proxy_none[1];
                $this->bot->getHttpClient()->useProxy($ip, $port, $auth);
                
            }else{

                $proxy_none = explode(":", $proxy);
                $ip = $proxy_none[0];
                $port = $proxy_none[1];
                $this->bot->getHttpClient()->useProxy($ip, $port);

            }
        }else{
            $this->bot->getHttpClient()->dontUseProxy();
        }
    }
}
