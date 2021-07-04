<?php
class youtube_post_model extends MY_Model {
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

		$client_id = get_option('google_youtube_client_id', '');
        $client_secret = get_option('google_youtube_api_secret', '');
        $api_key = get_option('google_youtube_api_key', '');

        if($client_id != "" || $client_secret != "" || $api_key != ""){
	        //
	        $this->client = new Google_Client();
	        $this->client->setAccessType("offline");
	        $this->client->setApprovalPrompt("force");
	        $this->client->setApplicationName($this->module_name);
	        $this->client->setClientId( $client_id );
	        $this->client->setClientSecret( $client_secret );
	        $this->client->setRedirectUri(get_module_url());
	        $this->client->setDeveloperKey( $api_key );
	        $this->client->setScopes(
	            [
	                'https://www.googleapis.com/auth/youtube', 
	                'https://www.googleapis.com/auth/userinfo.email'
	            ]
	        );

	        $this->youtube = new Google_Service_YouTube($this->client);
        }
	}

	public function block_permissions($path = ""){
		$dir = get_directory_block(__DIR__, get_class($this));
		return [
			'position' => 8000,
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
			'tab' => 'youtube',
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
			'position' => 1600,
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
				$errors[] = __("Youtube API not support post text");
				break;

			case 'link':
				$errors[] = __("Youtube API not support post link");
				break;

			case 'photo':
				$errors[] = __("Youtube API not support post image");
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
		
		if($is_schedule)
		{	
			return [
            	"status" => "success",
            	"message" => __('Success'),
            	"type" => $post_type
            ];
		}

		$this->client->setAccessToken($account->token);

		$videoPath = get_file_path($medias[0]);

		try {
			$response = [];
			switch ($post_type)
			{
				case 'video':

					if(isset($advance['title']) && $advance['title'] != ""){
						$title = $advance['title'];
					}else{
						$title = $caption;
						$caption = "";
					}

					$snippet = new Google_Service_YouTube_VideoSnippet();
		            $snippet->setTitle($title);
		            $snippet->setDescription($caption);

		            if(isset($advance['tags']) && $advance['tags'] != ""){
		                $tags = explode(",", $advance['tags']);
		                $snippet->setTags($tags);
		            }

		            if(isset($advance['category'])){
		                $snippet->setCategoryId($advance['category']);
		            }

		            $status = new Google_Service_YouTube_VideoStatus();
		            $status->privacyStatus = "public";

		            $video = new Google_Service_YouTube_Video();
		            $video->setSnippet($snippet);
		            $video->setStatus($status);

		            // Specify the size of each chunk of data, in bytes. Set a higher value for
		            // reliable connection as fewer chunks lead to faster uploads. Set a lower
		            // value for better recovery on less reliable connections.
		            $chunkSizeBytes = 1 * 1024 * 1024;

		            // Setting the defer flag to true tells the client to return a request which can be called
		            // with ->execute(); instead of making the API call immediately.
		            $this->client->setDefer(true);

		            // Create a request for the API's videos.insert method to create and upload the video.
		            $insertRequest = $this->youtube->videos->insert("status,snippet", $video);

		            // Create a MediaFileUpload object for resumable uploads.
		            $media = new Google_Http_MediaFileUpload(
		                $this->client,
		                $insertRequest,
		                'video/*',
		                null,
		                true,
		                $chunkSizeBytes
		            );
		            $media->setFileSize(filesize($videoPath));


		            // Read the media file and upload it chunk by chunk.
		            $status = false;
		            $handle = fopen($videoPath, "rb");
		            while (!$status && !feof($handle)) {
		              $chunk = fread($handle, $chunkSizeBytes);
		              $status = $media->nextChunk($chunk);
		            }

		            fclose($handle);

		            // If you want to make other calls after the file upload, set setDefer back to false
		            $this->client->setDefer(false);

					$response = $status;

					break;
			}

		 	return [
            	"status" => "success",
            	"message" => __('Success'),
            	"id" => $response->getId(),
            	"url" => "https://www.youtube.com/watch?v=".$response->getId(),
            	"type" => $post_type
            ]; 
		} catch (Google_Service_Exception $e) {
			$this->model->update($this->tb_account_manager, [ "status" => 0 ], [ "id" => $account->id ] );
            return [
            	"status" => "error",
            	"message" => __( $e->getMessage() ),
            	"type" => $post_type
            ];
        } catch (Google_Exception $e) {
        	return [
            	"status" => "error",
            	"message" => __( $e->getMessage() ),
            	"type" => $post_type
            ];
        }
	}
}
