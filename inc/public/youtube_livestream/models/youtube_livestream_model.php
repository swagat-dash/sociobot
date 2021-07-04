<?php
class youtube_livestream_model extends MY_Model {
	public $tb_account_manager = "sp_account_manager";
	public $tb_posts = "sp_posts";

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
	        $this->live_broadcast_snippet = new Google_Service_YouTube_LiveBroadcastSnippet;
	        $this->live_broadcast_status = new Google_Service_YouTube_LiveBroadcastStatus;
	        $this->live_broadcast = new Google_Service_YouTube_LiveBroadcast;
	        $this->live_stream_snippet = new Google_Service_YouTube_LiveStreamSnippet;
	        $this->cdn_settings = new Google_Service_YouTube_CdnSettings;
	        $this->live_stream = new Google_Service_YouTube_LiveStream;
	    }
	}

	public function block_report($path = ""){
		$dir = get_directory_block(__DIR__, get_class($this));
		return [
			'tab' => 'youtube',
			'position' => 2000,
			'name' => $this->module_name,
			'color' => $this->module_color,
			'icon' => $this->module_icon, 
			'id' => str_replace("_model", "", get_class($this)),
			'html' => view( $dir.'pages/block_report', ['path' => $path], true, $this ),
		];
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

	public function block_cronjobs($path = ""){
		$dir = get_directory_block(__DIR__, get_class($this));
		return [
			'position' => 25000,
			'name' => $this->module_name,
			'color' => $this->module_color,
			'icon' => $this->module_icon, 
			'id' => str_replace("_model", "", get_class($this)),
			'cronjobs' => [
				[
					"name" => $this->module_name,
					"time" => __("Once/minute"),
					"command_line" => "curl ". get_url( str_replace("_model", "", get_class($this) )."/cron?key=" . get_option("cron_key", uniqid() ) ) ." >/dev/null 2>&1"
				]
			]
		];
	}

	public function post( $data ){
		$team_id = _t("id");
		$post_id = isset( $data["id"] )?$data["id"]:0;
		$post_type = $data["post_type"];
		$accounts = $data["accounts"];
		$medias = $data["medias"];
		$link = $data["link"];
		$caption = $data["caption"];
		$time_post = $data["time_post"];
		$is_schedule = $data["is_schedule"];
		$interval_per_post = $data["interval_per_post"];
		$repost_frequency = $data["repost_frequency"];
		$repost_until = $data["repost_until"];
		$advance = $data["advance"];

		$count_error = 0;
		$count_success = 0;
		$count_schedule = 0; 
		$message = ""; 

		foreach ($accounts as $key => $account)
		{
			$result = [];
			$account_arr = explode("__", $account);
			if(count($account_arr) == 2)
			{
				$social_network = $account_arr[0];
				$ids = $account_arr[1];

				if(!post("ids"))
				{
					$item = $this->model->get("*", $this->tb_account_manager, "ids = '".$ids."' AND status = 1");
					if( $item )
					{
						$data['account'] = $item;
						$response = $this->run( $data );

						if( !$post_id )
						{
							$time_post += $count_schedule*$interval_per_post*60;
							$result = [
								"ids" => ids(),
								"team_id" => $team_id,
								"account_id" => $item->id,
								"social_network" => $social_network,
								"category" => $social_network."_livestream",
								"type" => "livestream",
								"data" => json_encode([
											"caption" => $caption,
											"link" => $link,
											"medias" => $medias,
											"advance" => json_encode($advance)
								]),
								"time_post" => $time_post,
								"time_delete" => NULL,
								"delay" => $interval_per_post,
								"repost_frequency" => $repost_frequency,
								"repost_until" => $repost_frequency? $repost_until :NULL,
								"status" => 1,
								"changed" => now(),
								"created" => now()
							];
						}

						if(!$is_schedule)
						{
							if( $response['status'] == "success" )
							{
								$count_success++;
								$message = $response["message"];
								$result['status'] = 3;
								$result['result'] = json_encode([
									"id" => $response["id"],
									"url" => $response["url"],
									"message" => $response["message"]
								]);
							}
							else
							{
								$count_error++;
								$message = $response["message"];

								$result['status'] = 4;
								$result['result'] = json_encode([
									"message" => $response["message"]
								]);
							}

						}
						else
						{
							$count_schedule++;
							$result['result'] = json_encode([
								"id" => $response["id"],
								"url" => $response["url"],
								"message" => $response["message"]
							]);
						}


						if( $post_id )
						{
							$this->db->update($this->tb_posts, $result, [ "id" => $post_id ]);
						}
						else
						{
							$this->db->insert($this->tb_posts, $result);
						}
					}else{
						$count_error++;
						$message = __("This profile not exist");

						//Update
						if( $post_id )
						{
							$result['status'] = 4;
							$result['result'] = json_encode([
								"message" => $message
							]);
							$this->db->update($this->tb_posts, $result, [ "id" => $post_id ]);
						}
					}

				}else{

					$ids = addslashes(post("ids"));
					$item = $this->model->get("*", $this->tb_posts, "ids = '".$ids."'");

					if($item){
						$account = $this->model->get("*", $this->tb_account_manager, "id = '".$item->account_id."'");
						$data['account'] = $account;
						$data['is_schedule'] = 1;
						$data['result'] = json_decode($item->result);
						$response = $this->run( $data );
						
						$result = [
							"type" => $response["type"],
							"data" => json_encode([
										"caption" => $caption,
										"link" => $link,
										"medias" => $medias,
										"advance" => json_encode($advance)
							]),
							"time_post" => $time_post,
							"time_delete" => NULL,
							"delay" => $interval_per_post,
							"repost_frequency" => $repost_frequency,
							"repost_until" => $repost_frequency? $repost_until :NULL,
							"result" => json_encode([
								"id" => isset($response["id"])?$response["id"]:"",
								"url" => isset($response["url"])?$response["url"]:"",
								"message" => $response["message"]
							]),
							"changed" => now()
						];

						$this->db->update($this->tb_posts, $result, [ "id" => $item->id ]);
						ms([
							"status" => "success",
							"message" => __("Success")
						]);
					}
					else
					{
						ms([
							"status" => "error",
							"message" => __("Can't update this post")
						]);
					}

				}
			}else{
				$count_error++;
				$message = __("This profile not exist");

				//Update
				if( $post_id )
				{
					$result['status'] = 4;
					$result['result'] = json_encode([
						"message" => $message
					]);
					$this->db->update($this->tb_posts, $result, [ "id" => $post_id ]);
				}
			}
		}

		if(!$is_schedule)
		{
			if($count_error == 0)
			{
				return [
					"status"  => "success",
					"message" => sprintf(__("Content is being published on %d profiles"), $count_success)
				];
			}
			else
			{
				if($count_error == 1 && $count_success == 0)
				{
					return [
						"status"  => "error",
						"message" => $message
					];
				}
				else
				{
					return [
						"status"  => "error",
						"message" => sprintf(__("Content is being published on %d profiles and %d profiles unpublished"), $count_success, $count_error)
					];
				}
			}
		}
		else
		{
			return [
				"status"  => "success",
				"message" => __("Content successfully scheduled")
			];
		}
	}

	public function run( $data ){
		$post_type = $data["post_type"];
		$account = $data["account"];
		$medias = $data["medias"];
		$link = $data["link"];
		$advance = $data["advance"];
		$caption = spintax( $data["caption"] );
		$is_schedule = $data["is_schedule"];
		$params = [];
		$ffmpeg = "ffmpeg";
		$ffprobe = "ffprobe";
		$file_path = realpath(FCPATH.get_file_path($medias[0]));
		$loop_file = FCPATH.TMP_PATH.ids().".txt";
		$test_video = realpath(FCPATH.get_module_path($this, "", true)."assets/video/test.mp4");
		$videoInfo = self::videoInfo($file_path);

		$this->client->setAccessToken($account->token);

		try{
			if($is_schedule && !isset($advance['show']))
			{	
				if(isset($data['result']->url) && isset($data['result']->id) && $data['result']->url != "" && $data['result']->id != ""){
					self::deleteEvent($data['result']->id);
				}

				return [
	            	"status" => "success",
	            	"message" => __('Success'),
	            	"id" => "",
	            	"url" => "",
	            	"type" => $post_type
	            ];
			}

			if($is_schedule && isset($data['result']->url) && isset($data['result']->id) && $data['result']->url != "" && $data['result']->id != ""){

				$response = self::updateBroadcast($data);
				return [
	            	"status" => "success",
	            	"message" => __('Success'),
	            	"id" => $data['result']->id,
	            	"url" => $data['result']->url,
	            	"type" => $post_type
	            ];

			}


            $response = [];
            if((!isset($data['result']->url) && !isset($data['result']->id)) || $is_schedule){
                /** 
                 * Create an object for the liveBroadcast resource [specify snippet's title, scheduled start time, and scheduled end time]
                 */
                $startdt = gmdate("Y-m-d\TH:i:s\Z", $data['time_post']);
                $this->live_broadcast_snippet->setTitle($advance['title']);
                $this->live_broadcast_snippet->setDescription($caption);
                $this->live_broadcast_snippet->setScheduledStartTime($startdt);

                /**
                 * object for the liveBroadcast resource's status ["private, public or unlisted"]
                 */
                $privacy_status = "public";
	            if(isset($advance['privacy_status']) && $advance['privacy_status'] == "private"){
	            	$privacy_status = "private";
	            }
                $this->live_broadcast_status->setPrivacyStatus($privacy_status);

                /** 
                 * API Request [inserts the liveBroadcast resource]
                 */
                $this->live_broadcast->setSnippet($this->live_broadcast_snippet);
                $this->live_broadcast->setStatus($this->live_broadcast_status);
                $this->live_broadcast->setKind('youtube#liveBroadcast');

                /**
                 * Execute Insert LiveBroadcast Resource Api [return an object that contains information about the new broadcast]
                 */
                $broadcastsResponse = $this->youtube->liveBroadcasts->insert('snippet,status', $this->live_broadcast, array());
                $response['broadcast_response'] = $broadcastsResponse;
                $youtube_event_id = $broadcastsResponse['id'];

                /**
                 * set thumbnail to the event
                 */
                if($advance['thumbnail'] != ""){
                    $thumbnail = get_file_path($advance['thumbnail']);
                    $thumb = self::uploadThumbnail($thumbnail, $youtube_event_id);
                }

                /**
                 * Call the API's videos.list method to retrieve the video resource.
                 */
                $listResponse = $this->youtube->videos->listVideos("snippet", array('id' => $youtube_event_id));
                $video = $listResponse[0]; 

                /**
                 * update the tags and language via video resource
                 */
                $videoSnippet = $video['snippet'];
                $videoSnippet['tags'] = "";
                $videoSnippet['categoryId'] = $advance['category'];
                $videoSnippet['description'] = $caption;
                $video['snippet'] = $videoSnippet;
                if(isset($advance['tags']) && $advance['tags'] != ""){
	            	$videoSnippet['tags'] = explode(",", $advance['tags']);   
	            }

                /** 
                 * Update video resource [videos.update() method.]
                 */
                $updateResponse = $this->youtube->videos->update("snippet", $video);
                $response['video_response'] = $updateResponse;

                /**
                 * object of livestream resource [snippet][title]
                 */
                $this->live_stream_snippet->setTitle($advance['title']);

                /**
                 * object for content distribution  [stream's format,ingestion type.]
                 */
                $this->cdn_settings->setFormat($videoInfo->format);
                $this->cdn_settings->setIngestionType('rtmp');
                $this->cdn_settings->setResolution('1080p');
                $this->cdn_settings->setFrameRate('30fps');
                $this->cdn_settings->setIngestionType('rtmp');
                $response['video_info'] = $videoInfo;

                /** 
                 * API request [inserts liveStream resource.]
                 */
                $this->live_stream->setSnippet($this->live_stream_snippet);
                $this->live_stream->setCdn($this->cdn_settings);
                $this->live_stream->setKind('youtube#liveStream');

                /*
                 * execute the insert request [return an object that contains information about new stream]
                 */
                $streamsResponse = $this->youtube->liveStreams->insert('snippet,cdn', $this->live_stream, array());
                $response['stream_response'] = $streamsResponse;

                /**
                 * Bind the broadcast to the live stream
                 */
                $bindBroadcastResponse = $this->youtube->liveBroadcasts->bind(
                    $broadcastsResponse['id'],'id,contentDetails',
                    array(
                        'streamId' => $streamsResponse['id'],
                    ));
                $response['bind_broadcast_response'] = $bindBroadcastResponse;

                $broadcast_id = $broadcastsResponse['id'];
                $stream_name = $streamsResponse->getCdn()->getIngestionInfo()->getStreamName();
                $stream_url = $streamsResponse->getCdn()->getIngestionInfo()->getIngestionAddress();
                $stream_url = $stream_url."/".$stream_name;

                if($is_schedule && isset($advance['show'])){
                    return [
		            	"status" => "success",
		            	"message" => __('Success'),
		            	"id" => $broadcast_id,
		            	"url" => $stream_url,
		            	"type" => $post_type
		            ];
                }

            } else {

                $broadcast_id = $livestream_result->id;
                $stream_url = $livestream_result->rtmp;

            }

            //Create watermark
	        @exec($ffprobe." -v quiet -show_entries stream=width,height -of default=noprint_wrappers=1 ".$file_path." 2>&1", $resolution);
			$watermark = $this->watermark($resolution, $account);

            /**************************/
            /*  PROCCESS LIVE STREAM  */
            /**************************/
            $stream_url = preg_replace(
                '#^rtmps://([^/]+?):443/#ui',
                'rtmp://\1:80/',
                $stream_url
            );
            
            //START LIVE
        	$loop_times = 1;
        	if(isset($advance['loop']) && (int)$advance['loop'] > 0){
        		$loop_times = (int)$advance['loop'];
        	}

        	//$loop = "file '".$test_video."'\n";
        	$loop = "";
        	for ($i=0; $i < $loop_times; $i++) { 
        		$loop .= "file '".$file_path."'\n";
        	}
        	
        	file_put_contents($loop_file, $loop);

            $livestream_code = sprintf(
                $ffmpeg.' -rtbufsize 256M -re -f concat -safe 0 -i "'.$loop_file.'" %s -acodec libmp3lame -ar 44100 -b:a 128k -pix_fmt yuv420p -profile:v baseline -bufsize 6000k -vb 4500k -maxrate 4500k -deinterlace -vcodec libx264 -preset veryfast -g 30 -r 30 -f flv "%s" > /dev/null &', 
                $watermark,
                $stream_url
            );

            @exec($livestream_code);
            //END START LIVE

            $attempts = 0;
            do {
                $attempts++;
                sleep(5);
                $transitionTesting = self::transitionEvent($broadcast_id, 'testing');
                if(!is_string($transitionTesting)){
                    break;
                }
            } while($attempts <= 20);

            if(is_string($transitionTesting)){
            	$error = json_decode($transitionTesting, true);
	            return [
	            	"status" => "error",
	            	"message" => __( $error['error']['errors'][0]['message'] ),
	            	"type" => $post_type
	            ];
            }

            $attempts = 0;
            do {
                $attempts++;
                sleep(10);
                $transitionLive = self::transitionEvent($broadcast_id, 'live');
                if(!is_string($transitionLive)){
                    break;
                }
            } while($attempts <= 6);

            if(is_string($transitionLive)){
            	$error = json_decode($transitionLive, true);
	            return [
	            	"status" => "error",
	            	"message" => __( $error['error']['errors'][0]['message'] ),
	            	"type" => $post_type
	            ];
            }

            @unlink($loop_file);

            $video_id = $response['broadcast_response']->getId();

            return [
            	"status" => "success",
            	"message" => __('Success'),
            	"id" => $video_id,
            	"url" => "https://www.youtube.com/watch?v=".$video_id,
            	"type" => $post_type
            ]; 

        } catch ( Google_Service_Exception $e ) {
        	$error = $e->getMessage();
			$error = json_decode($error, true);
            return [
            	"status" => "error",
            	"message" => __( $error['error']['errors'][0]['message'] ),
            	"type" => $post_type
            ];
        } catch ( Google_Exception $e ) {
        	$error = $e->getMessage();
			$error = json_decode($error, true);
            return [
            	"status" => "error",
            	"message" => __( $error['error']['errors'][0]['message'] ),
            	"type" => $post_type
            ];
        } catch(Exception $e) {
        	$error = $e->getMessage();
			$error = json_decode($error, true);
            return [
            	"status" => "error",
            	"message" => __( $error['error']['errors'][0]['message'] ),
            	"type" => $post_type
            ];
        }
	}

    public function updateBroadcast($data = "")
    {

        $post_type = $data["post_type"];
		$account = $data["account"];
		$medias = $data["medias"];
		$link = $data["link"];
		$advance = $data["advance"];
		$caption = spintax( $data["caption"] );
		$is_schedule = $data["is_schedule"];
		$params = [];
		$ffmpeg = "ffmpeg";
		$ffprobe = "ffprobe";
		$file_path = realpath(FCPATH.get_file_path($medias[0]));
		$loop_file = FCPATH.TMP_PATH.ids().".txt";
        $livestream_result = isset($data->result)? json_decode($data->result) : array();

        $file = FCPATH.get_file_path($medias[0]);

        $videoInfo = self::videoInfo($file);

        try{
        	$youtube_event_id = $data['result']->id;
            /**
             * Create an object for the liveBroadcast resource's snippet [snippet's title, scheduled start time, and scheduled end time.]
             */
            $startdt = gmdate("Y-m-d\TH:i:s\Z", $data['time_post']);
            $this->live_broadcast_snippet->setTitle($advance['title']);
            $this->live_broadcast_snippet->setDescription($caption);
            $this->live_broadcast_snippet->setScheduledStartTime($startdt);
            
            /** 
             * Create an object for the liveBroadcast resource's status ["private, public or unlisted".]
             */
            $privacy_status = "public";
            if(isset($advance['privacy_status']) && $advance['privacy_status'] == "private"){
            	$privacy_status = "private";
            }
            $this->live_broadcast_status->setPrivacyStatus($privacy_status);

            /**
             * Create the API request  [inserts the liveBroadcast resource.]
             */
            $this->live_broadcast->setSnippet($this->live_broadcast_snippet);
            $this->live_broadcast->setStatus($this->live_broadcast_status);
            $this->live_broadcast->setKind('youtube#liveBroadcast');
            $this->live_broadcast->setId($youtube_event_id);

            /** 
             * Execute the request [return info about the new broadcast ]
             */
            $broadcastsResponse = $this->youtube->liveBroadcasts->update(
                'snippet,status',
                $this->live_broadcast, 
                array()
            );

            /**
             * set thumbnail
             */
            if($advance['thumbnail'] != ""){
                $thumbnail = get_file_path($advance['thumbnail']);
                $thumb = self::uploadThumbnail($thumbnail, $youtube_event_id);
            }

            /** 
             * Call the API's videos.list method [retrieve the video resource]
             */
            $listResponse = $this->youtube->videos->listVideos(
                "snippet",
                array(
                    'id' => $youtube_event_id
                )
            );

            $video = $listResponse[0]; 
            $videoSnippet = $video['snippet'];
            if(isset($advance['tags']) && $advance['tags'] != ""){
            	$videoSnippet['tags'] = explode(",", $advance['tags']);   
            }

            /** 
             * set Language and other details
             */
            /*if(!is_null($language)){
                $temp = isset($this->yt_language[$language]) ? $this->yt_language[$language] : "en"; 
                $videoSnippet['defaultAudioLanguage'] = $temp; 
                $videoSnippet['defaultLanguage'] = $temp;  
            }*/
            $videoSnippet['title'] = $advance['title']; 
            $videoSnippet['description'] = $caption; 
            $videoSnippet['categoryId'] = $advance['category'];
            $videoSnippet['scheduledStartTime'] = $startdt; 
            $video['snippet'] = $videoSnippet;

            /** 
             * Update the video resource  [call videos.update() method]
             */
            $updateResponse = $this->youtube->videos->update(
                "snippet", 
                $video
            );

            $response['broadcast_response'] = $updateResponse;
            $youtube_event_id = $updateResponse['id'];
            
            $this->live_stream_snippet->setTitle($advance['title']);

            /**
             * object for content distribution  [stream's format,ingestion type.]
             */
            $this->cdn_settings->setFormat($videoInfo->format);
            $this->cdn_settings->setIngestionType('rtmp');
            $this->cdn_settings->setResolution('1080p');
            $this->cdn_settings->setFrameRate('30fps');
            $response['video_info'] = $videoInfo;

            /** 
             * API request [inserts liveStream resource.]
             */
            $this->live_stream->setSnippet($this->live_stream_snippet);
            $this->live_stream->setCdn($this->cdn_settings);
            $this->live_stream->setKind('youtube#liveStream');

            /**
             * execute the insert request [return an object that contains information about new stream]
             */
            $streamsResponse = $this->youtube->liveStreams->insert(
                'snippet,cdn', 
                $this->live_stream, 
                array()
            );

            $response['stream_response'] = $streamsResponse;

            /**
             * Bind the broadcast to the live stream
             */
            $bindBroadcastResponse = $this->youtube->liveBroadcasts->bind(
                $updateResponse['id'],'id,contentDetails',
                array(
                    'streamId' => $streamsResponse['id'],
                )
            );

            $response['bind_broadcast_response'] = $bindBroadcastResponse;

            return $response;

        } catch ( Google_Service_Exception $e ) {
            return $e->getMessage();
        } catch ( Google_Exception $e ) {
            return $e->getMessage();
        } catch(Exception $e) {
			pr(11,1);

            return $e->getMessage();
        }
    }

    public function uploadThumbnail($url = '', $videoId)
    {
        try{
            $videoId = $videoId;
            $imagePath = $url;
            /**
             * size of chunk to be uploaded  in bytes [default  1 * 1024 * 1024] (Set a higher value for reliable connection as fewer chunks lead to faster uploads)
             */             
            $chunkSizeBytes = 1 * 1024 * 1024;
            $this->client->setDefer(true);
            /**
             * Setting the defer flag to true tells the client to return a request which can be called with ->execute(); instead of making the API call immediately
             */
            $setRequest = $this->youtube->thumbnails->set($videoId);
            /**
             * MediaFileUpload object [resumable uploads]
             */
            $media = new Google_Http_MediaFileUpload(
                $this->client,
                $setRequest,
                'image/png',
                null,
                true,
                $chunkSizeBytes
            );
            $media->setFileSize(filesize($imagePath));
            /** 
             * Read the media file [to upload chunk by chunk]
             */
            $status = false;
            $handle = fopen($imagePath, "rb");
            while (!$status && !feof($handle)) {
              $chunk = fread($handle, $chunkSizeBytes);
              $status = $media->nextChunk($chunk);
            }
            fclose($handle);
            /**
             * set defer to false [to make other calls after the file upload]
             */
            $this->client->setDefer(false);
            $thumbnailUrl = $status['items'][0]['default']['url'];
            return $thumbnailUrl;
        } catch( Google_Exception $e ) {
            return $e->getMessage();
        }
    }

    public function transitionEvent($youtube_event_id, $broadcastStatus)
    {
        try{

            $part = "status, id, snippet";
            $liveBroadcasts = $this->youtube->liveBroadcasts;
            $transition = $liveBroadcasts->transition($broadcastStatus, $youtube_event_id, $part);

            return $transition;

        } catch(Google_Exception $e ) {
            return $e->getMessage();
        } catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function deleteEvent($youtube_event_id)
    {
        try {
            $deleteBroadcastsResponse = $this->youtube->liveBroadcasts->delete($youtube_event_id);
            return $deleteBroadcastsResponse;
                
        } catch ( Google_Service_Exception $e ) {
            return $e->getMessage();
        } catch ( Google_Exception $e ) {
            return $e->getMessage();
        } catch(Exception $e) {
            return $e->getMessage();
        }
    }

    public function videoInfo($videoPath = ""){
        @exec('ffprobe -show_streams -i "'.$videoPath.'"', $output, $statusCode);
        $format = "240p";
        $result = array();

        if($statusCode == 0 && !empty($output)){

            $result = array();
            foreach ($output as $value) {
                
                $parse_value = explode("=", $value);
                if(count($parse_value) == 2){
                    $key = $parse_value[0];
                    $val = $parse_value[1];

                    $result[$key] = $val;

                }

            }

            $result = (object)$result;

            if(!empty($result)){

                $w = $result->width;
                $h = $result->height;

                if($w >= 1920 && $h >= 1080){
                    $format = "1080p";
                }elseif ($w >= 1280 && $h >= 720) {
                    $format = "720p";
                }elseif ($w >= 854 && $h >= 480) {
                    $format = "480p";
                }elseif ($w >= 640 && $h >= 360) {
                    $format = "360p";
                }elseif ($w >= 426 && $h >= 240) {
                    $format = "240p";
                }
            }

        }

        $result->format = $format;

        return $result;

    }

	public function watermark($resolution = [], $account){
		$uid = _t("owner", $account->team_id);

		$opacity = _gd("watermark_opacity", 70)/100;
		$size = _gd("watermark_size", 30)/100;
		$position = _gd("watermark_position", "lb", $uid);
		$watermark = _gd("watermark_mask", "", $uid);

		$data = json_decode($account->data);
		if(is_object($data)){
			if(isset($data->watermark_opacity)){
				$opacity = $data->watermark_opacity/100;
			}

			if(isset($data->watermark_size)){
				$size = $data->watermark_size/100;
			}


			if(isset($data->watermark_position)){
				$position = $data->watermark_position;
			}

			if(isset($data->watermark_mask)){
				$watermark = $data->watermark_mask;
			}
		}

		if($watermark != "")
		{
			$width = explode("=", $resolution[0]);
			$width = $width[1];

			$height = explode("=", $resolution[1]);
			$height = $height[1];

			if($width < $height){
				$scale = '[w]scale=' . $width . '*' . $size . ':-1[wm]';
			}else{
				$scale = '[w]scale=-1:' . $height . '*' . $size . '[wm]';
			}

			$watermark = BASE.$watermark;
            $padding = 0;
            $cmd = '-i '.$watermark.' -filter_complex ';
            switch ($position) {
                case 'lt':
                    $position = ' "[1]format=argb,colorchannelmixer=aa=' . $opacity . '[w];' . $scale . ';[0][wm]overlay=x=' . $padding . ':' . $padding . '" ';
                    break;

                case 'rt':
                    $position = ' "[1]format=argb,colorchannelmixer=aa=' . $opacity . '[w];' . $scale . ';[0][wm]overlay=x=(main_w-overlay_w)-' . $padding . ':' . $padding . '" ';
                    break;

                case 'lb':
                    $position = ' "[1]format=argb,colorchannelmixer=aa=' . $opacity . '[w];' . $scale . ';[0][wm]overlay=x=' . $padding . ':(main_h-overlay_h)-' . $padding . '" ';
                    break;

                case 'rb':
                    $position = ' "[1]format=argb,colorchannelmixer=aa=' . $opacity . '[w];' . $scale . ';[0][wm]overlay=x=(main_w-overlay_w)-' . $padding . ':(main_h-overlay_h)-' . $padding . '" ';
                    break;

                case 'ct':
                    $position = ' "[1]format=argb,colorchannelmixer=aa=' . $opacity . '[w];' . $scale . ';[0][wm]overlay=x=(main_w-overlay_w)/2:' . $padding . '" ';
                    break;

                case 'bc':
                    $position = ' "[1]format=argb,colorchannelmixer=aa=' . $opacity . '[w];' . $scale . ';[0][wm]overlay=x=(main_w-overlay_w)/2:(main_h-overlay_h)-' . $padding . '" ';
                    break;

                case 'cc':
                    $position = ' "[1]format=argb,colorchannelmixer=aa=' . $opacity . '[w];' . $scale . ';[0][wm]overlay=x=(main_w-overlay_w)/2:(main_h-overlay_h)/2" ';
                    break;

                case 'lc':
                    $position = ' "[1]format=argb,colorchannelmixer=aa=' . $opacity . '[w];' . $scale . ';[0][wm]overlay=x=' . $padding . '/2:(main_h-overlay_h)/2" ';
                    break;

                case 'rc':
                    $position = ' "[1]format=argb,colorchannelmixer=aa=' . $opacity . '[w];' . $scale . ';[0][wm]overlay=x=(main_w-overlay_w)-' . $padding . ':(main_h-overlay_h)/2" ';
                    break;
            }

            $cmd .= $position;

            return $cmd;
		}
		else
		{
			return "";
		}
	}

	public function get_posts(  ){

		$this->db->select("
			a.id,
			a.ids,
			a.team_id,
			a.account_id,
			a.social_network,
			a.category as cate,
			a.type,
			a.data,
			a.time_post,
			a.time_delete,
			a.delay,
			a.repost_frequency,
			a.repost_until,
			a.result,
			a.status,
			a.changed,
			a.created,
			b.category,
			b.login_type,
			b.name,
			b.username,
			b.token,
			b.avatar,
			b.url,
			b.data as account_data,
			b.ids as account_ids,
		");

		$this->db->from($this->tb_posts." as a");
		$this->db->join($this->tb_account_manager." as b", "a.account_id = b.id");
		$this->db->where(" a.status = 1 ");
		$this->db->where(" a.time_post <= '".time()."' AND a.category = 'youtube_livestream'");
		$this->db->order_by(" a.time_post ", " ASC ");
		$this->db->limit(5, 0);
		$query = $this->db->get();

		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
}
