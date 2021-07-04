<?php
use DirkGroenen\Pinterest\Pinterest;
use seregazhuk\PinterestBot\Factories\PinterestBot;

class pinterest_boards extends MY_Controller {
	
	public $tb_account_manager = "sp_account_manager";
	public $module_name;

	public function __construct(){
		parent::__construct();
        _permission("account_manager_enable");
		$this->load->model(get_class($this).'_model', 'model');
		include get_module_path($this, 'libraries/vendor/autoload.php', true);

		//
		$this->module_name = get_module_config( $this, 'name' );
		$this->module_icon = get_module_config( $this, 'icon' );
		$this->module_color = get_module_config( $this, 'color' );
		//

        $this->app_id = get_option('pinterest_app_id', '');
        $this->app_secret = get_option('pinterest_app_secret', '');
        $this->login_button = get_option('pinterest_login_button', 1);

        if($this->login_button && ($this->app_id == "" || $this->app_secret == "")){
            redirect( get_url("social_network_configuration/index/pinterest") );
        }

        $this->bot = PinterestBot::create();
	}

	public function index($page = "", $ids = "")
	{
        $result = [];

        switch ($page) {
            case 'oauth':

                break;
            
            default:
                $login_type = _s("pinterest_login_type");
                switch ($login_type) {
                    case 2:
                        try {
                            $pinterest = new Pinterest( _s("pinterest_app_id"), _s("pinterest_app_secret") );
                            if(!_s("pinterest_access_token")){
                                $response = $pinterest->auth->getOAuthToken( post("code") );
                                $access_token = $response->access_token;
                                _ss("pinterest_access_token", $response->access_token);
                            }else{
                                $access_token = _s("pinterest_access_token");
                            }

                            $pinterest->auth->setOAuthToken($access_token);

                            $boards = $pinterest->users->getMeBoards();  
                            $boards = json_encode($boards);
                            $boards = json_decode($boards);

                            if(!empty($boards->data)){
                                $data = array();
                                foreach ($boards->data as $board) {
                                    $board_id = str_replace("https://www.pinterest.com/", "", $board->url );
                                    $board_id = explode("/", $board_id);
                                    array_pop($board_id);
                                    $board_id = implode('/', $board_id); 
                                    $board_id = urldecode($board_id);

                                    $result[] = (object)[
                                        'id' => $board_id,
                                        'name' => $board->name,
                                        'avatar' => "https://ui-avatars.com/api?name=".$board->name."&size=128&background=cd2029&color=fff",
                                        'desc' => $board->description
                                    ];                 
                                }

                                $data = [
                                    "status" => "success",
                                    "result" => $result
                                ];
                            }else{
                                $data = [
                                    "status" => "error",
                                    "message" => __('No profile to add')
                                ];
                            }
                        } catch (Exception $e) {
                            $data = [
                                "status" => "error",
                                "message" => $e->getMessage()
                            ];
                        }
                        break;

                     case 3:
                        try {
                            $username = _s("pinterest_username");
                            $password = _s("pinterest_password");
                            $proxy = _s("pinterest_proxy");

                            $this->proxy($proxy);
                            $this->bot->auth->login($username, $password);
                            if ($this->bot->auth->isLoggedIn()) {
                                $profile = $this->bot->user->profile();
                                if(!empty($profile)){

                                    $profile = (object)$profile;
                                    $boards = $this->bot->boards->forUser($profile->username);

                                    if(empty($boards)){
                                        $data = [
                                            "status" => "error",
                                            "message" => __("Could not find boards")
                                        ];
                                    }

                                    foreach ($boards as $board) {
                                        $board = (object)$board;
                                        $result[] = (object)[
                                            'id' => $board->id,
                                            'name' => $board->name,
                                            'avatar' => "https://ui-avatars.com/api?name=".$board->name."&size=128&background=cd2029&color=fff",
                                            'desc' => $board->description
                                        ];                 
                                    }

                                    $data = [
                                        "status" => "success",
                                        "result" => $result
                                    ];
                                }else{
                                    $data = [
                                        "status" => "error",
                                        "message" => __("Login required")
                                    ];
                                }
                            }else{
                                $data = [
                                    "status" => "error",
                                    "message" => $this->bot->getLastError()
                                ];
                            }
                        } catch (Exception $e) {
                            $data = [
                                "status" => "error",
                                "message" => $e->getMessage()
                            ];
                        }
                        break;
                    
                    default:
                        try {
                            $pinterest = new Pinterest($this->app_id, $this->app_secret);
                            if(!_s("pinterest_access_token")){
                                $response = $pinterest->auth->getOAuthToken( post("code") );
                                $access_token = $response->access_token;
                                _ss("pinterest_access_token", $response->access_token);
                            }else{
                                $access_token = _s("pinterest_access_token");
                            }

                            $pinterest->auth->setOAuthToken($access_token);

                            $boards = $pinterest->users->getMeBoards();  
                            $boards = json_encode($boards);
                            $boards = json_decode($boards);

                            if(!empty($boards->data)){
                                $data = array();
                                foreach ($boards->data as $board) {
                                    $board_id = str_replace("https://www.pinterest.com/", "", $board->url );
                                    $board_id = explode("/", $board_id);
                                    array_pop($board_id);
                                    $board_id = implode('/', $board_id); 
                                    $board_id = urldecode($board_id);

                                    $result[] = (object)[
                                        'id' => $board_id,
                                        'name' => $board->name,
                                        'avatar' => "https://ui-avatars.com/api?name=".$board->name."&size=128&background=cd2029&color=fff",
                                        'desc' => $board->description
                                    ];                 
                                } 

                                $data = [
                                    "status" => "success",
                                    "result" => $result
                                ];
                            }else{
                                $data = [
                                    "status" => "error",
                                    "message" => __('No profile to add')
                                ];
                            }
                        } catch (Exception $e) {
                            $data = [
                                "status" => "error",
                                "message" => $e->getMessage()
                            ];
                        }
                        break;
                }

                break;
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
        redirect(  get_module_url("index/oauth") );   
	}

    public function oauth_button()
    {
        _ss("pinterest_login_type", 1);
        _us("pinterest_access_token");
        $pinterest = new Pinterest($this->app_id, $this->app_secret);
        $url = $pinterest->auth->getLoginUrl( get_module_url(), array('read_public,write_public,read_relationships,write_relationships'));
        redirect($url);
    }

    public function oauth_app()
    {
        _ss("pinterest_login_type", 2);
        _us("pinterest_access_token");
        if(is_ajax() || (!_s("pinterest_app_id") || !_s("pinterest_app_secret")) ){
            _ss("pinterest_app_id", post("app_id"));
            _ss("pinterest_app_secret", post("app_secret"));

            ms([
                "status" => "success"
            ]);
        }else{
            $pinterest = new Pinterest( _s("pinterest_app_id"), _s("pinterest_app_secret") );
            $url = $pinterest->auth->getLoginUrl( get_module_url(), array('read_public,write_public,read_relationships,write_relationships'));
            redirect($url);
        }
    }

     public function oauth_user()
    {
        _ss("pinterest_login_type", 3);
        $this->username = post("username");
        $this->password = post("password");

        _ss("pinterest_username", $this->username);
        _ss("pinterest_password", $this->password);

        try {
            $this->proxy(post("proxy"));
            $this->bot->auth->login($this->username, $this->password);
            if (!$this->bot->auth->isLoggedIn()) {
                ms([
                    "status" => "error",
                    "message" => $this->bot->getLastError()
                ]);
            }

            $profile = $this->bot->user->profile();
            if(empty($profile)){
                ms([
                    "status" => "error",
                    "message" => __("Login required")
                ]);
            }

            $profile = (object)$profile;

            $boards = $this->bot->boards->forUser($profile->username);

            if(empty($boards)){
                ms(array(
                    "status" => "error",
                    "message" => __("Could not find boards")
                ));
            }

            ms([
                "status" => "success"
            ]);
        } catch (Exception $e) {
            ms(array(
                "status" => "error",
                "message" => $e->getMessage()
            ));
        }
    }

    public function proxy($proxy){
        if($proxy == ""){
            $system_proxy = add_proxy("pinterest");
            $proxy = $system_proxy->proxy;
            _ss("pinterest_proxy", $system_proxy->id);
        }else{
            _ss("pinterest_proxy", $proxy);
        }


        if($proxy != "" || $proxy != 0){
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

	public function save()
    {
        $ids = post('id');
        $team_id = _t("id");

        validate('empty', __('Please select a profile to add'), $ids);

        $login_type = _s("pinterest_login_type");

        $result = [];
        $save_access_token = false;
        $save_login_type = 0;
        switch ($login_type) {
            case 2:
                try {
                    $access_token = _s("pinterest_access_token");
                    $save_login_type = 3;
                    $save_access_token = json_encode([
                        "access_token" => $access_token,
                        "app_id" => _s("pinterest_app_id"),
                        "app_secret" => _s("pinterest_app_secret")
                    ]);

                    $pinterest = new Pinterest( _s("pinterest_app_id"), _s("pinterest_app_secret") );
                    $pinterest->auth->setOAuthToken($access_token);

                    $boards = $pinterest->users->getMeBoards();  
                    $boards = json_encode($boards);
                    $boards = json_decode($boards);

                    if(!empty($boards->data)){
                        $data = array();
                        foreach ($boards->data as $board) {
                            $board_id = str_replace("https://www.pinterest.com/", "", $board->url );
                            $board_id = explode("/", $board_id);
                            array_pop($board_id);
                            $board_id = implode('/', $board_id); 
                            $board_id = urldecode($board_id);

                            $result[] = (object)[
                                'id' => $board_id,
                                'name' => $board->name,
                                'avatar' => "https://ui-avatars.com/api?name=".$board->name."&size=128&background=cd2029&color=fff",
                                'url' => $board->url
                            ];                 
                        }
                    }
                } catch (Exception $e) {
                    ms([
                        "status" => "error",
                        "message" => $e->getMessage()
                    ]);
                }
                break;

            case 3:
                try {
                    $username = _s("pinterest_username");
                    $password = _s("pinterest_password");
                    $proxy = _s("pinterest_proxy");
                    $save_login_type = 2;
                    
                    $this->proxy($proxy);
                    $this->bot->auth->login($username, $password);
                    if ($this->bot->auth->isLoggedIn()) {
                        $profile = $this->bot->user->profile();
                        if(!empty($profile)){

                            $profile = (object)$profile;
                            $boards = $this->bot->boards->forUser($profile->username);

                            $save_access_token = json_encode([
                                "username" => $profile->username,
                                "password" => encrypt_encode($password)
                            ]);

                            if(empty($boards)){
                                $data = [
                                    "status" => "error",
                                    "message" => __("Could not find boards")
                                ];
                            }

                            foreach ($boards as $board) {
                                $board = (object)$board;
                                $result[] = (object)[
                                    'id' => $board->id,
                                    'name' => $board->name,
                                    'avatar' => "https://ui-avatars.com/api?name=".$board->name."&size=128&background=cd2029&color=fff",
                                    'url' => "https://www.pinterest.com".$board->url
                                ];                 
                            }
                        }else{
                            ms([
                                "status" => "error",
                                "message" => __("Login required")
                            ]);
                        }
                    }else{
                        ms([
                            "status" => "error",
                            "message" => $this->bot->getLastError()
                        ]);
                    }
                } catch (Exception $e) {
                    ms([
                        "status" => "error",
                        "message" => $e->getMessage()
                    ]);
                }
                break;
            
            default:
                try {
                    $access_token = _s("pinterest_access_token");
                    $save_login_type = 1;
                    $save_access_token = json_encode([
                        "access_token" => $access_token,
                        "app_id" => "",
                        "app_secret" => ""
                    ]);

                    $pinterest = new Pinterest( $this->app_id, $this->app_secret );
                    $pinterest->auth->setOAuthToken($access_token);

                    $boards = $pinterest->users->getMeBoards();  
                    $boards = json_encode($boards);
                    $boards = json_decode($boards);

                    if(!empty($boards->data)){
                        $data = array();
                        foreach ($boards->data as $board) {
                            $board_id = str_replace("https://www.pinterest.com/", "", $board->url );
                            $board_id = explode("/", $board_id);
                            array_pop($board_id);
                            $board_id = implode('/', $board_id); 
                            $board_id = urldecode($board_id);

                            $result[] = (object)[
                                'id' => $board_id,
                                'name' => $board->name,
                                'avatar' => "https://ui-avatars.com/api?name=".$board->name."&size=128&background=cd2029&color=fff",
                                'url' => $board->url
                            ];                 
                        }
                    }
                } catch (Exception $e) {
                    ms([
                        "status" => "error",
                        "message" => $e->getMessage()
                    ]);
                }
                break;
        }

        if(!empty($result)){
            foreach ($result as $row) {
                $row = (object)$row;

                $url = $row->url;
                $url = str_replace("https://www.pinterest.com/", "", $url);
                $url_parese = explode("/", $url);
                $username = $url_parese[0];

                if( in_array($row->id, $ids) ){

                    $item = $this->model->get('*', $this->tb_account_manager, "social_network = 'pinterest' AND team_id = '{$team_id}' AND pid = '{$row->id}'");
                    $avatar = save_img( $row->avatar, TMP_PATH.'avatar/' );
                    if(!$item){
                        $data = [
                            'ids' => ids(),
                            'social_network' => 'pinterest',
                            'category' => 'board',
                            'login_type' => $save_login_type,
                            'can_post' => 1,
                            'team_id' => $team_id,
                            'pid' => $row->id,
                            'name' => $row->name,
                            'username' => $username,
                            'token' => $save_access_token,
                            'avatar' => $avatar,
                            'url' => $row->url,
                            'data' => NULL,
                            'proxy' => _s("pinterest_proxy")?_s("pinterest_proxy"):NULL,
                            'status' => 1,
                            'changed' => now(),
                            'created' => now()
                        ];

                        check_number_account("pinterest", "board");

                        $this->model->insert($this->tb_account_manager, $data);
                    }else{
                        @unlink($item->avatar);
                        $data = [
                            'social_network' => 'pinterest',
                            'category' => 'board',
                            'login_type' => $save_login_type,
                            'can_post' => 1,
                            'team_id' => $team_id,
                            'pid' => $row->id,
                            'name' => $row->name,
                            'username' => $username,
                            'token' => $save_access_token,
                            'avatar' => $avatar,
                            'url' => $row->url,
                            'proxy' => _s("pinterest_proxy")?_s("pinterest_proxy"):NULL,
                            'status' => 1,
                            'changed' => now(),
                        ];

                        $this->model->update($this->tb_account_manager, $data, ['id' => $item->id]);
                    }

                    _us("pinterest_proxy");
                }
            }
        }else{
            ms([
                "status" => "error",
                "message" => __('No profile to add')
            ]);
        }

        _us("pinterest_access_token");
        _us("pinterest_username");
        _us("pinterest_password");
        _us("pinterest_app_id");
        _us("pinterest_app_secret");
        _us("pinterest_proxy");
        _us("pinterest_login_type");

        ms([
            "status" => "success",
            "message" => __("Success")
        ]);
	}

	public function get($params, $accessToken){

		try {
            $response = $this->fb->get($params, $accessToken);
            return json_decode($response->getBody()); 
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            return $e->getMessage();
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            return $e->getMessage();
        }

	}

}