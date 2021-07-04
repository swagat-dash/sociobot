<?php
class reddit_profiles extends MY_Controller {
	
	public $tb_account_manager = "sp_account_manager";
	public $module_name;

	public function __construct(){
		parent::__construct();
        _permission("account_manager_enable");
		$this->load->model(get_class($this).'_model', 'model');
		include get_module_path($this, 'libraries/redditoauth.php', true);

		//
		$this->module_name = get_module_config( $this, 'name' );
		$this->module_icon = get_module_config( $this, 'icon' );
		$this->module_color = get_module_config( $this, 'color' );
		//
        $client_id = get_option("reddit_client_id", "");
        $client_secret = get_option("reddit_client_secret", "");

        if($client_id == "" || $client_secret == ""){
            redirect( get_url("social_network_configuration/index/reddit") );
        }

        $this->reddit = new redditoauth();
	}

	public function index($page = "", $ids = "")
	{
		//
        try {
            //_us("readdit_access_token");
            if(!_s("readdit_access_token")){
                $response = $this->reddit->getAccessToken( post('code') );
                if( isset($response->access_token) ){
                    $access_token = json_encode($response);
                    _ss("readdit_access_token", $access_token);
                }else{
                    $access_token = false;
                    $data = $response;
                }
            }else{
                $access_token = _s("readdit_access_token");
            }

            if($access_token){
                $this->reddit->setAccessToken($access_token);
                $profile = $this->reddit->getUser();

                $result = [];
                $result[] = (object)[
                    'id' => $profile->subreddit->display_name,
                    'name' => $profile->name,
                    'avatar' => $profile->icon_img,
                    'desc' => $profile->name
                ];

                $data = [
                    "status" => "success",
                    "result" => $result
                ];

            }

        } catch (Exception $e) {
            $data = [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }

        $data['module_name'] = $this->module_name;
        $data['module_icon'] = $this->module_icon;
        $data['module_color'] = $this->module_color;

		$views = [
			"subheader" => view( 'main/subheader', [ 'module_name' => $this->module_name, 'module_icon' => $this->module_icon, 'module_color' => $this->module_color ], true ),
			"column_one" => page($this, "pages", "general", $page, $data), 
		];
		
		views( [
			"title" => $this->module_name,
			"fragment" => "fragment_one",
			"views" => $views
		] );
	}

	public function oauth()
	{
        _us("readdit_access_token");
        redirect($this->reddit->getAuthorizeURL());
	}

	public function save()
	{
		try {
            $ids = post('id');
            $team_id = _t("id");

            validate('empty', __('Please select a profile to add'), $ids);

            $access_token = _s("readdit_access_token");

            //
            $this->reddit->setAccessToken($access_token);
            $profile = $this->reddit->getUser();

            if($ids[0] == $profile->subreddit->display_name){
                $item = $this->model->get('*', $this->tb_account_manager, "social_network = 'linkedin' AND team_id = '{$team_id}' AND pid = '{$profile->id}'");
                $avatar = save_img( $profile->icon_img, TMP_PATH.'avatar/' );

                if(!$item){
                    $data = [
                        'ids' => ids(),
                        'social_network' => 'reddit',
                        'category' => 'profile',
                        'login_type' => 1,
                        'can_post' => 1,
                        'team_id' => $team_id,
                        'pid' => $profile->subreddit->display_name,
                        'name' => $profile->name,
                        'username' => $profile->subreddit->display_name,
                        'token' => $access_token,
                        'avatar' => $avatar,
                        'url' => 'https://www.reddit.com/user/'.$profile->name,
                        'data' => NULL,
                        'status' => 1,
                        'changed' => now(),
                        'created' => now()
                    ];

                    check_number_account("reddit", "profile");

                    $this->model->insert($this->tb_account_manager, $data);
                }else{
                    @unlink($item->avatar);

                    $data = [
                        'social_network' => 'reddit',
                        'category' => 'profile',
                        'login_type' => 1,
                        'can_post' => 1,
                        'team_id' => $team_id,
                        'pid' => $profile->subreddit->display_name,
                        'name' => $profile->name,
                        'username' => $profile->subreddit->display_name,
                        'token' => $access_token,
                        'avatar' => $avatar,
                        'url' => 'https://www.reddit.com/user/'.$profile->name,
                        'status' => 1,
                        'changed' => now(),
                    ];

                    $this->model->update($this->tb_account_manager, $data, ['id' => $item->id]);
                }

                _us('readdit_access_token');
                
                ms([
                    "status" => "success",
                    "message" => __("Success")
                ]);
            }else{
                ms([
                    "status" => "error",
                    "message" => __('No profile to add')
                ]);
            }
        } catch (Exception $e) {
            ms([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
        }
	}
}