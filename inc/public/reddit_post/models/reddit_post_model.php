<?php
class reddit_post_model extends MY_Model {
	public $tb_account_manager = "sp_account_manager";

	public function __construct(){
		parent::__construct();
		$module_path = get_module_directory(__DIR__);
		
		if(!class_exists("redditoauth")){
			include $module_path.'libraries/redditoauth.php';
		}

		//
		$this->module_name = get_module_config( $module_path, 'name' );
		$this->module_icon = get_module_config( $module_path, 'icon' );
		$this->module_color = get_module_config( $module_path, 'color' );
		//
		
		$this->client_id = get_option("reddit_client_id", "");
        $this->client_secret = get_option("reddit_client_secret", "");

        $this->reddit = new redditoauth();
	}

	public function block_permissions($path = ""){
		$dir = get_directory_block(__DIR__, get_class($this));
		return [
			'position' => 8300,
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
			'tab' => 'reddit',
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
			'position' => 1300,
			'name' => $this->module_name,
			'color' => $this->module_color,
			'icon' => $this->module_icon, 
			'id' => str_replace("_model", "", get_class($this)),
			'preview' => view( $dir.'pages/preview', ['path' => $path], true, $this ),
		];
	}

	public function post( $data ){
		$post_type = $data["post_type"];
		$account = $data["account"];
		$medias = $data["medias"];
		$link = $data["link"];
		$advance = $data["advance"];
		$caption = spintax( $data["caption"] );
		$is_schedule = $data["is_schedule"];

		if($is_schedule)
		{	
			return [
            	"status" => "success",
            	"message" => __('Success'),
            	"type" => $post_type
            ];
		}
		
		$access_token = $this->renew_access_token($account->token);
		if(is_array($access_token)){
			$this->model->update($this->tb_account_manager, [ "status" => 0 ], [ "id" => $account->id ] );
			return [
            	"status" => "error",
            	"message" => $access_token['message'],
            	"type" => $post_type
            ];
		}

		$this->reddit->setAccessToken($access_token);

		if(isset($advance["title"])){
			$title = $advance["title"];
			$description = $caption;
		}else{
			$title = $this->cut_text( $caption, 300 );
			$description = "";
		}

		switch ($post_type)
		{
			case 'photo':
				$medias[0] = watermark($medias[0], $account->team_id, $account->id);
				$response = $this->reddit->createStory($title, $medias[0], $account->pid, $description, "image");
				break;

			case 'link':
				$response = $this->reddit->createStory($title, $link, $account->pid, $description, "link");
				break;

			case 'text':
				$response = $this->reddit->createStory($title, null, $account->pid, $description, "self");
				break;
		}

		if($response->success == 1){
			$url = "";
            switch ($post_type) {
                case 'link':
                    $url = substr($response->jquery[16][3][0], 0, -1);
                    break;

                case 'text':
                   	$url = substr($response->jquery[10][3][0], 0, -1);
                    break;
                
                case 'photo':
                   	$url = substr($response->jquery[10][3][0], 0, -1);
                    break;
            }

            $post_id = explode("/comments/", $url);
            if(count($post_id) == 2){
            	$post_id = str_replace("/new_post", "", end($post_id));
            }else{
            	$post_id = "";
            }

            return [
            	"status" => "success",
            	"message" => __('Success'),
            	"id" => $post_id,
            	"url" => $url,
            	"type" => $post_type
            ]; 

        }else{
            if(isset($response->jquery[14][3][0])){
                return array(
                    "status" => "error",
                    "message" => __( ucfirst($response->jquery[14][3][0]) ),
                    "type" => $post_type
                );
            }

            if(isset($response->jquery[22][3][0])){
                return array(
                    "status" => "error",
                    "message" => __( ucfirst( $response->jquery[22][3][0] ) ),
                    "type" => $post_type
                );
            }
        }
	}

	public function renew_access_token($access_token){
		$access_token = json_decode($access_token);

        $curl = curl_init('https://www.reddit.com/api/v1/access_token');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_USERPWD, $this->client_id . ':' . $this->client_secret);
        curl_setopt(
            $curl, CURLOPT_POSTFIELDS, [
                'grant_type' => 'refresh_token',
                'refresh_token' => $access_token->refresh_token,
            ]
        );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);

        $access_token = json_decode($response);
        if(isset($access_token->access_token)){
        	return $response;
        }else{
        	return ["status" => "error", "message" => $access_token->message];
        }
    }

    public function cut_text($text, $n = 280){ 
		if(strlen($text) <= $n){
			return $text;
		}
		
		$text= substr($text, 0, $n);
		if($text[$n-1] == ' '){
			return trim($text)."...";
		}

		$x  = explode(" ", $text);
		$sz = sizeof($x);

		if($sz <= 1){
			return $text."...";
		}

		$x[$sz-1] = '';

		return trim(implode(" ", $x))."...";
	}
}
