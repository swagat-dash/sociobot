<?php
class youtube_post extends MY_Controller {
	
	public $tb_account_manager = "sp_account_manager";

	public function __construct(){
		parent::__construct();
		_permission(get_class($this)."_enable");
		$this->load->model(get_class($this).'_model', 'model');

		//
		$this->module_name = get_module_config( $this, 'name' );
		$this->module_icon = get_module_config( $this, 'icon' );
		$this->module_color = get_module_config( $this, 'color' );
		//

		$client_id = get_option('google_youtube_client_id', '');
        $client_secret = get_option('google_youtube_api_secret', '');
        $api_key = get_option('google_youtube_api_key', '');

        if($client_id == "" || $client_secret == "" || $api_key == ""){
            redirect( get_url("social_network_configuration/index/youtube") );
        }
	}

	public function index($page = "", $ids = "")
	{
		$post_types = [
			[
				"id" => "video",
				"name" => __("Video"),
				"icon" => "fas fa-video"
			]
		];

		$block_post_type = Modules::run("post/block_post_type", $post_types);
		$block_file_video = Modules::run("file_manager/block_file", "single", "video", "upload_video");
		$block_group = Modules::run("group_manager/block_group");
		$block_preview = Modules::run("post/block_preview", [get_class($this)]);
		$block_accounts = Modules::run("post/block_accounts", "youtube");
		$block_link = Modules::run("post/block_link");
		$block_caption = Modules::run("post/block_caption");
		$block_schedule = Modules::run("post/block_schedule");
		Modules::run(get_class($this)."/block");

		$views = [
			"subheader" => view( 'main/subheader', [ 'module_name' => $this->module_name, 'module_icon' => $this->module_icon, 'module_color' => $this->module_color ], true ),
			"column_one" => $block_accounts,
			"column_two" => view("pages/general", [ 'file_manager_video' => $block_file_video, 'block_post_type' => $block_post_type, 'block_caption' => $block_caption, 'block_schedule' => $block_schedule ] ,true), 
			"column_three" => $block_preview, 
		];
		
		views( [
			"title" => $this->module_name,
			"fragment" => "fragment_three",
			"views" => $views
		] );
	}

	public function block(){}
}