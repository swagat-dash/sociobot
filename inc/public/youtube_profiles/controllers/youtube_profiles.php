<?php
class youtube_profiles extends MY_Controller {
	
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

        $client_id = get_option('google_youtube_client_id', '');
        $client_secret = get_option('google_youtube_api_secret', '');
        $api_key = get_option('google_youtube_api_key', '');

        if($client_id == "" || $client_secret == "" || $api_key == ""){
            redirect( get_url("social_network_configuration/index/youtube") );
        }

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

	public function index($page = "", $ids = "")
	{
		//
        try {
            if( !_s("youtube_access_token") ){
                $this->client->authenticate( post("code") );
                $oauth2 = new Google_Service_Oauth2($this->client);
                $access_token = $this->client->getAccessToken();
                _ss("youtube_access_token", $access_token);
            }else{
                $access_token = _s("youtube_access_token");
            }
            
            $this->client->setAccessToken($access_token);

            $part = 'brandingSettings,status,id,snippet,contentDetails,contentOwnerDetails,statistics';
            $optionalParams = array(
                'mine' => true
            );
            $channel = $this->youtube->channels->listChannels($part, $optionalParams);

            $result = [];
            if(!empty($channel->items))
            {
                foreach ($channel->items as $key => $row)
                {
                    $result[] = (object)[
                        'id' => $row->getId(),
                        'name' => $row->getSnippet()->getLocalized()->getTitle(),
                        'avatar' => $row->getSnippet()->getThumbnails()->getDefault()->getUrl(),
                        'desc' => $row->getSnippet()->getLocalized()->getDescription()
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
        _us("youtube_access_token");
        $url = $this->client->createAuthUrl();
        redirect($url);
	}

	public function save()
	{
        try {
            $ids = post('id');
            $team_id = _t("id");

            validate('empty', __('Please select a profile to add'), $ids);

            $access_token = _s("youtube_access_token");
            $this->client->setAccessToken($access_token);

            $part = 'brandingSettings,status,id,snippet,contentDetails,contentOwnerDetails,statistics';
            $optionalParams = array(
                'mine' => true
            );
            $channel = $this->youtube->channels->listChannels($part, $optionalParams);
            
            if(!empty($channel->items))
            {
                foreach ($channel->items as $key => $row)
                {
                    if(in_array($row->getId(), $ids))
                    {
                        $avatar = $row->getSnippet()->getThumbnails()->getDefault()->getUrl();
                        $avatar = save_img( $avatar, TMP_PATH.'avatar/' );

                        $item = $this->model->get('*', $this->tb_account_manager, "social_network = 'youtube' AND team_id = '{$team_id}' AND pid = '{$row->getId()}'");

                        if(!$item){
                            $data = [
                                'ids' => ids(),
                                'social_network' => 'youtube',
                                'category' => 'profile',
                                'login_type' => 1,
                                'can_post' => 1,
                                'team_id' => $team_id,
                                'pid' => $row->getId(),
                                'name' => $row->getSnippet()->getLocalized()->getTitle(),
                                'username' => $row->getSnippet()->getLocalized()->getTitle(),
                                'token' => json_encode($access_token),
                                'avatar' => $avatar,
                                'url' => 'https://www.youtube.com/channel/'.$row->getId(),
                                'data' => NULL,
                                'status' => 1,
                                'changed' => now(),
                                'created' => now()
                            ];

                            check_number_account("youtube", "profile");

                            $this->model->insert($this->tb_account_manager, $data);
                        }else{
                            @unlink($item->avatar);

                            $data = [
                                'social_network' => 'youtube',
                                'category' => 'profile',
                                'login_type' => 1,
                                'can_post' => 1,
                                'team_id' => $team_id,
                                'pid' => $row->getId(),
                                'name' => $row->getSnippet()->getLocalized()->getTitle(),
                                'username' => $row->getSnippet()->getLocalized()->getTitle(),
                                'token' => json_encode($access_token),
                                'avatar' => $avatar,
                                'url' => 'https://www.youtube.com/channel/'.$row->getId(),
                                'status' => 1,
                                'changed' => now(),
                            ];

                            $this->model->update($this->tb_account_manager, $data, ['id' => $item->id]);
                        }
                    }
                }
                _us('youtube_access_token');

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