<?php
use myPHPnotes\LinkedIn;

class linkedin_profiles extends MY_Controller {
	
	public $tb_account_manager = "sp_account_manager";
	public $module_name;

	public function __construct(){
		parent::__construct();
        _permission("account_manager_enable");
		$this->load->model(get_class($this).'_model', 'model');
        include get_module_path($this, 'libraries/vendor/autoload.php', true);
		include get_module_path($this, 'libraries/LinkedIn.php', true);

		//
		$this->module_name = get_module_config( $this, 'name' );
		$this->module_icon = get_module_config( $this, 'icon' );
		$this->module_color = get_module_config( $this, 'color' );
		//
        $app_id = get_option('linkedin_api_key', '');
        $app_secret = get_option('linkedin_api_secret', '');
        $app_callback = get_url( get_class($this) );
        if(post("error") == "unauthorized_scope_error"){
            _ss("linkedin_scopes", "r_emailaddress r_liteprofile w_member_social");
            redirect( get_module_url("oauth") );
        }else{
            $app_scopes = "r_emailaddress r_basicprofile r_liteprofile w_member_social";
            if(_s("linkedin_scopes")){
                 $app_scopes = _s("linkedin_scopes");
            }
        }
        
        $ssl = false;

        if($app_id == "" || $app_secret == ""){
            redirect( get_url("social_network_configuration/index/linkedin") );
        }

        $this->linkedin = new LinkedIn($app_id, $app_secret, $app_callback, $app_scopes, $ssl);
	}

	public function index($page = "", $ids = "")
	{
		//
        try {
            if(!_s("linkedin_access_token")){
                $response = $this->linkedin->getAccessToken( post('code') );
                if ( $response['status'] == "success" ) {
                    $access_token = $response['accessToken'];
                    _ss("linkedin_access_token", $response['accessToken']);
                }else{
                    $data = $response;
                    $access_token = false;
                }
            }else{
                $access_token = _s("linkedin_access_token");
            }

            if($access_token){
                $profile = $this->linkedin->getPerson($access_token);

                $firstName_param = (array)$profile->firstName->localized;
                $lastName_param = (array)$profile->lastName->localized;

                $firstName = reset($firstName_param);
                $lastName = reset($lastName_param);
                $fullname = $firstName." ".$lastName;

                $avatar = (array)$profile->profilePicture; 
                $avatar = $avatar['displayImage~']->elements[0]->identifiers[0]->identifier;

                $result = [];
                $result[] = (object)[
                    'id' => $profile->id,
                    'name' => $fullname,
                    'avatar' => $avatar,
                    'desc' => $fullname
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
        _us("linkedin_access_token");
        redirect($this->linkedin->getAuthUrl());
	}

	public function save()
	{
		try {
            $ids = post('id');
            $team_id = _t("id");

            validate('empty', __('Please select a profile to add'), $ids);

            $access_token = _s("linkedin_access_token");

            //
            $profile = $this->linkedin->getPerson($access_token);

            $vanityName = "";
            if(isset($profile->vanityName)){
                $vanityName = $profile->vanityName;
            }

            $firstName_param = (array)$profile->firstName->localized;
            $lastName_param = (array)$profile->lastName->localized;

            $firstName = reset($firstName_param);
            $lastName = reset($lastName_param);
            $fullname = $firstName." ".$lastName;

            $avatar = (array)$profile->profilePicture; 
            $avatar = $avatar['displayImage~']->elements[0]->identifiers[0]->identifier;

            if($ids[0] == $profile->id){
                $item = $this->model->get('*', $this->tb_account_manager, "social_network = 'linkedin' AND team_id = '{$team_id}' AND pid = '{$profile->id}'");
                $avatar = save_img( $avatar, TMP_PATH.'avatar/' );

                if(!$item){
                    $data = [
                        'ids' => ids(),
                        'social_network' => 'linkedin',
                        'category' => 'profile',
                        'login_type' => 1,
                        'can_post' => 1,
                        'team_id' => $team_id,
                        'pid' => $profile->id,
                        'name' => $fullname,
                        'username' => $fullname,
                        'token' => $access_token,
                        'avatar' => $avatar,
                        'url' => 'https://linkedin.com/in/'.$vanityName,
                        'data' => NULL,
                        'proxy' => $profile->id,
                        'status' => 1,
                        'changed' => now(),
                        'created' => now()
                    ];

                    check_number_account("linkedin", "profile");

                    $this->model->insert($this->tb_account_manager, $data);
                }else{
                    @unlink($item->avatar);

                    $data = [
                        'social_network' => 'linkedin',
                        'category' => 'profile',
                        'login_type' => 1,
                        'can_post' => 1,
                        'team_id' => $team_id,
                        'pid' => $profile->id,
                        'name' => $fullname,
                        'username' => $fullname,
                        'token' => $access_token,
                        'avatar' => $avatar,
                        'url' => 'https://linkedin.com/in/'.$vanityName,
                        'proxy' => $profile->id,
                        'status' => 1,
                        'changed' => now(),
                    ];

                    $this->model->update($this->tb_account_manager, $data, ['id' => $item->id]);
                }

                $items = $this->model->fetch('*', $this->tb_account_manager, "social_network = 'linkedin' AND team_id = '{$team_id}' AND proxy = '{$profile->id}'");
                if(!empty($items)){
                    foreach ($items as $key => $value) {
                        $this->model->update($this->tb_account_manager, ["token" => $access_token], ['id' => $value->id]);
                    }
                }

                _us('linkedin_access_token');
                _us("linkedin_scopes");
                
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