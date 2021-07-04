<?php
class instagram_livestream extends MY_Controller {
	
	public $tb_account_manager = "sp_account_manager";
	public $tb_posts = "sp_posts";

	public function __construct(){
		parent::__construct();
		_permission(get_class($this)."_enable");
		$this->load->model(get_class($this).'_model', 'model');

		//
		$this->module_name = get_module_config( $this, 'name' );
		$this->module_icon = get_module_config( $this, 'icon' );
		$this->module_color = get_module_config( $this, 'color' );
		//
	}

	public function index($page = "", $ids = "")
	{
		$post_types = [
			[
				"id" => "video",
				"name" => __("Select video"),
				"icon" => "fas fa-video"
			],
		];

		$block_post_type = Modules::run("post/block_post_type", $post_types);
		$block_file = Modules::run("file_manager/block_file", "single", "video");
		Modules::run(get_class($this)."/block");

		//Sidebar
		$team_id = _t("id");
		$result = $this->model->fetch('*', $this->tb_account_manager, " status = '1' AND team_id = '{$team_id}' AND social_network = 'instagram' AND login_type = 2", "social_network, category", "ASC");
		$ids = addslashes(post("edit"));
		$post = $this->model->get("*", $this->tb_posts, "ids = '{$ids}'");
		//End sidebar

		$views = [
			"subheader" => view( 'main/subheader', [ 'module_name' => $this->module_name, 'module_icon' => $this->module_icon, 'module_color' => $this->module_color ], true ),
			"column_one" => view("main/sidebar", [ 'post' => $post, 'result' => $result, 'module_name' => $this->module_name, 'module_icon' => $this->module_icon ], true ),
			"column_two" => view("pages/general", [ 'file_manager' => $block_file, 'block_post_type' => $block_post_type ] ,true), 
			"column_three" => view("pages/preview", [ 'module_name' => $this->module_name, 'module_icon' => $this->module_icon, 'module_color' => $this->module_color ], true ), 
		];
		
		views( [
			"title" => $this->module_name,
			"fragment" => "fragment_three",
			"views" => $views
		] );
	}

	public function block(){}

	public function save($skip_validate = false){

		$post_type = post("post_type");
		$accounts = post("account");
		$medias = post("media");
		$link = post("link");
		$caption = post("caption");
		$time_post = timestamp_sql(post("time_post"));
		$is_schedule = post("is_schedule");
		$interval_per_post = (int)0;
		$repost_frequency = (int)post("repost_frequency");
		$repost_until = timestamp_sql(post("repost_until"));
		$advance = post("advance");

		@exec("ffmpeg -v 2>&1", $check_ffmpeg);
        if(count($check_ffmpeg) <= 2){
        	ms([
            	"status" => "error",
            	"message" => __("Please install FFMPEG and FFPROBE on your server"),
            	"type" => $post_type
            ]);
        }

		validate('empty', __('Please select at least a profile'), $accounts);
		validate('empty', __('Please select at least one video'), $medias);

		validate('null', __('Time post'), $time_post);
		validate('repost_frequency', __('Repost frequency'), $repost_frequency, 0);

		if($repost_frequency > 0)
		{
			validate('null', __('Repost until'), $repost_until);
		}

		if($repost_frequency > 0 && $time_post > $repost_until){
			ms([
				"status" => "error",
				"message" => __("Time post must be smaller than repost until")
			]);
		}

		$data = [
			"post_type" => $post_type,
			"accounts" => $accounts,
			"medias" => $medias,
			"link" => $link,
			"caption" => $caption,
			"time_post" => $time_post,
			"is_schedule" => $is_schedule,
			"interval_per_post" => $interval_per_post,
			"repost_frequency" => $repost_frequency,
			"repost_until" => $repost_until,
			"advance" => $advance
		];

		$result = $this->model->post($data);
		ms($result);
	}

	public function cron(  )
	{

		$posts = $this->model->get_posts();
		if(!$posts){ 
			_e("Empty schedule");
			exit(0);
		}

		foreach ($posts as $post) {
			
			$accounts = [
				$post->social_network."__".$post->account_ids
			];

			$data_posts = json_decode($post->data);

			$id = $post->id;
			$ids = $post->ids;
			$team_id = $post->team_id;
			$account_id = $post->account_id;
			$category = $post->cate;
			$social_network = $post->social_network;
			$type = $post->type;
			$data = $post->data;
			$medias = $data_posts->medias;
			$link = $data_posts->link;
			$caption = $data_posts->caption;
			$advance = json_decode($data_posts->advance, true);
			$time_post = $post->time_post;
			$time_delete = $post->time_delete;
			$delay = $post->delay;
			$repost_frequency = $post->repost_frequency;
			$repost_until = $post->repost_until;
			$status = $post->status;
			$changed = $post->changed;
			$created = $post->created;
			
			$result = $this->model->post([
				"id" => $id,
				"post_type" => $type,
				"accounts" => $accounts,
				"medias" => $medias,
				"link" => $link,
				"caption" => $caption,
				"time_post" => $time_post,
				"is_schedule" => false,
				"interval_per_post" => $delay,
				"repost_frequency" => $repost_frequency,
				"repost_until" => $repost_until,
				"advance" => $advance
			]);

			//Repost
			if($repost_frequency != 0){
				$next_time = $repost_frequency*86400;

				$result_data = $this->model->get("result", $this->tb_posts, "id = '{$id}'")->result;
				$this->model->update( $this->tb_posts, [
					"ids" => ids(),
					"team_id" => $team_id,
					"account_id" => $account_id,
					"social_network" => $social_network,
					"category" => $category,
					"type" => $type,
					"data" => $data,
					"time_post" => $time_post,
					"time_delete" => $time_delete,
					"delay" => $delay,
					"repost_frequency" => 0,
					"repost_until" => NULL,
					"result" => $result_data,
					"status" => $result['status']=="success"?3:4,
					"changed" => now(),
					"created" => $created,
				], [ "id" => $id ]);

				if($time_post < $repost_until){
					$time_post += $next_time;

					$this->model->insert( $this->tb_posts, [
						"ids" => $ids,
						"team_id" => $team_id,
						"account_id" => $account_id,
						"social_network" => $social_network,
						"category" => $category,
						"type" => $type,
						"data" => $data,
						"time_post" => $time_post,
						"time_delete" => $time_delete,
						"delay" => $delay,
						"repost_frequency" => $repost_frequency,
						"repost_until" => $repost_frequency? $repost_until :NULL,
						"status" => 1,
						"changed" => $changed,
						"created" => $created
					]);
				}
			}

			_e( strtoupper( __( ucfirst($result['status']) ) ).": ".__( $result['message']) . "<br/>" , false);
		}

	}

}