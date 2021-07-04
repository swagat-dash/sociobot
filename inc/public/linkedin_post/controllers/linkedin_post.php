<?php
class linkedin_post extends MY_Controller {
	
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

		$app_id = get_option('linkedin_api_key', '');
        $app_secret = get_option('linkedin_api_secret', '');
        $app_callback = get_url( get_class($this) );
        $app_scopes = "r_emailaddress r_basicprofile r_liteprofile w_member_social w_organization_social r_organization_social rw_organization_admin";
        $ssl = false;

        if($app_id == "" || $app_secret == ""){
            redirect( get_url("social_network_configuration/index/linkedin") );
        }
	}

	public function index($page = "", $ids = "")
	{
		$post_types = [
			[
				"id" => "photo",
				"name" => __("Photo"),
				"icon" => "fas fa-images"
			],
			[
				"id" => "link",
				"name" => __("Link"),
				"icon" => "fas fa-link"
			],
			[
				"id" => "text",
				"name" => __("Text"),
				"icon" => "far fa-file-alt"
			]
		];

		$block_post_type = Modules::run("post/block_post_type", $post_types);
		$block_file_photo = Modules::run("file_manager/block_file", "multi", "image", "upload_photo");
		$block_group = Modules::run("group_manager/block_group");
		$block_preview = Modules::run("post/block_preview", [get_class($this)]);
		$block_accounts = Modules::run("post/block_accounts", "linkedin");
		$block_link = Modules::run("post/block_link");
		$block_caption = Modules::run("post/block_caption");
		$block_schedule = Modules::run("post/block_schedule");
		Modules::run(get_class($this)."/block");

		$views = [
			"subheader" => view( 'main/subheader', [ 'module_name' => $this->module_name, 'module_icon' => $this->module_icon, 'module_color' => $this->module_color ], true ),
			"column_one" => $block_accounts,
			"column_two" => view("pages/general", [ 'file_manager_photo' => $block_file_photo, 'block_post_type' => $block_post_type, 'block_link' => $block_link, 'block_caption' => $block_caption, 'block_schedule' => $block_schedule ] ,true), 
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