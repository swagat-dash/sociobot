<?php
class facebook_livestream_model extends MY_Model {
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

		$app_id = get_option('facebook_client_id', '');
        $app_secret = get_option('facebook_client_secret', '');
        $app_version = get_option('facebook_app_version', 'v4.0');

        if($app_id != "" && $app_secret != "" && $app_version != ""){
			$this->fb = new \Facebook\Facebook([
	            'app_id' => $app_id,
	            'app_secret' => $app_secret,
	            'default_graph_version' => $app_version,
	        ]);
        }
	}

	public function block_report($path = ""){
		$dir = get_directory_block(__DIR__, get_class($this));
		return [
			'tab' => 'facebook',
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
			'position' => 9000,
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
			'position' => 15000,
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
		/*$ffmpeg = FCPATH."/ffmpeg/ffmpeg.exe";
		$ffprobe = FCPATH."/ffmpeg/ffprobe.exe";*/
		$ffmpeg = "ffmpeg";
		$ffprobe = "ffprobe";
		$file_path = realpath(FCPATH.get_file_path($medias[0]));
		$loop_file = FCPATH.TMP_PATH.ids().".txt";

		switch ($account->category) {
            case 'profile':
                $endpoint = "/me/live_videos";
                break;
            
            default:
                $endpoint = "/".$account->pid."/live_videos";
                break;
        }

		if($is_schedule)
		{	
			if(isset($advance['show'])){
				try {
	                if(isset($result->id) && $result->id != ""){
	                	$params['planned_start_time'] = $data['time_post'];
	                	$response = $this->fb->post($endpoint."/".$result->id, $params, $account->token)->getDecodedBody();
	                }else{
	                	$params['status'] = "SCHEDULED_UNPUBLISHED";
	                	$params['planned_start_time'] = $data['time_post'];
	                	$response = $this->fb->post($endpoint, $params, $account->token)->getDecodedBody();
	                }

	                return [
		            	"status" => "success",
		            	"message" => __('Success'),
		            	"id" => $response['id'],
		            	"url" => "",
		            	"type" => $post_type
		            ];
				} catch(Facebook\Exceptions\FacebookResponseException $e) {
		           	if($e->getCode() == 190){
		            	$this->model->update($this->tb_account_manager, [ "status" => 0 ], [ "id" => $account->id ] );
	                }
		            return [
		            	"status" => "error",
		            	"message" => __( $e->getMessage() ),
		            	"id" => "",
		            	"url" => "",
		            	"type" => $post_type
		            ];
		        } catch(Facebook\Exceptions\FacebookSDKException $e) {
		            return [
		            	"status" => "error",
		            	"message" => __( $e->getMessage() ),
		            	"id" => "",
		            	"url" => "",
		            	"type" => $post_type
		            ];
		        }
			}

			return [
            	"status" => "success",
            	"message" => __('Success'),
            	"id" => "",
            	"url" => "",
            	"type" => $post_type
            ];
		}
		
		switch ($post_type)
		{
			case 'video':
				
				$params = ['title' => $advance['title']];

                if($caption != ""){
                    $params['description'] = $caption;
                }

				break;
		}

		//Create watermark
        @exec($ffprobe." -v quiet -show_entries stream=width,height -of default=noprint_wrappers=1 ".$file_path." 2>&1", $resolution);
		$watermark = $this->watermark($resolution, $account);

		try
		{
            $params['status'] = "LIVE_NOW";

            //Create Live Video
            $response = $this->fb->post($endpoint, $params, $account->token)->getDecodedBody();

            $video_id = $response['id'];
            $stream_url = $response['stream_url'];

            if($params['status'] == "LIVE_NOW"){

            	$loop_times = 1;
            	if(isset($advance['loop']) && (int)$advance['loop'] > 0){
            		$loop_times = (int)$advance['loop'];
            	}

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
            }

            $attempts = 0;
            do {
	            $attempts++;
	            sleep(2);
	            $response = $this->fb->get('/'.$video_id, $account->token)->getDecodedBody();
	            if($response['status'] != 'UNPUBLISHED') {
	                break;
	            }
	        } while($attempts <= 5);

	        @unlink($loop_file);

            if($response['status'] != "UNPUBLISHED"){

				preg_match('/src="([^"]+)"/', $response['embed_html'], $match);
				$url = $match[1];
				$url = explode("?href=", $url);
				$url = urldecode( $url[1] );
				$url = str_replace("/&width=0", "", $url);
				$url = explode("/&width", $url);
				$url = $url[0];

				$post_id = explode("/", $url);
				$post_id = end($post_id);

	            return [
	            	"status" => "success",
	            	"message" => __('Success'),
	            	"id" => $post_id,
	            	"url" => $url,
	            	"type" => $post_type
	            ]; 
            }else{
            	return [
	            	"status" => "error",
	            	"message" => __( "Live stream unpublished, Please try again later" ),
	            	"type" => $post_type
	            ];
            }
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            $this->model->update($this->tb_account_manager, [ "status" => 0 ], [ "id" => $account->id ] );
            return [
            	"status" => "error",
            	"message" => __( $e->getMessage() ),
            	"type" => $post_type
            ];
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            return [
            	"status" => "error",
            	"message" => __( $e->getMessage() ),
            	"type" => $post_type
            ];
        }
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
		$this->db->where(" a.time_post <= '".time()."' AND a.category = 'facebook_livestream'");
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
