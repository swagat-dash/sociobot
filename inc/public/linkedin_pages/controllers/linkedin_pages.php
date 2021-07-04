<?php
use myPHPnotes\LinkedIn;

class linkedin_pages extends MY_Controller {
    
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
        $app_scopes = "r_emailaddress r_basicprofile r_liteprofile w_member_social w_organization_social r_organization_social rw_organization_admin";
        $ssl = false;

        if($app_id == "" || $app_secret == ""){
            redirect( get_url("social_network_configuration/index/linkedin") );
        }

        $this->linkedin = new LinkedIn($app_id, $app_secret, $app_callback, $app_scopes, $ssl);
    }

    public function index($page = "", $ids = "")
    {
        //
        if(!_s("linkedin_access_token")){
            if( !post("error_description") ){
                $response = $this->linkedin->getAccessToken( post('code') );
                if ( $response['status'] == "success" ) {
                    $access_token = $response['accessToken'];
                    _ss("linkedin_access_token", $response['accessToken']);
                }else{
                    $data = $response;
                    $access_token = false;
                }
            }else{
                $access_token = false;
                $data = [
                    "status" => "error",
                    "message" => post("error_description")
                ];
            }
        }else{
            $access_token = _s("linkedin_access_token");
        }

        if($access_token){
            $result = [];
            $profiles = $this->linkedin->getCompanyPages($access_token);
            $this->linkedin->getPerson($access_token);
            _ss("linkedin_company_pages", serialize($profiles));
            if(isset($profiles->elements)){

                $profiles = $profiles->elements;

                if($profiles)
                {
                    foreach ($profiles as $profile) {
                        $profile = (array)$profile;
                        $profile = $profile['organizationalTarget~'];
                        $avatar = (array)$profile->logoV2;
                        $avatar = $avatar['original~'];
                        $avatar = $avatar->elements[0]->identifiers[0]->identifier;

                        $result[] = (object)[
                            'id' => $profile->id,
                            'name' => $profile->localizedName,
                            'avatar' => $avatar,
                            'desc' => $profile->vanityName
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

            }else{
                $data = [
                    "status" => "error",
                    "message" => $profiles->message
                ];
            }
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
        $ids = post('id');
        $team_id = _t("id");

        validate('empty', __('Please select a profile to add'), $ids);

        $access_token = _s("linkedin_access_token");
        $profiles = _s("linkedin_company_pages");
        $profiles = unserialize($profiles);
        $userinfo = $this->linkedin->getPerson($access_token);

        if(!isset($profiles->elements)){
            ms([
                "status" => "error",
                "message" => $profiles->message
            ]);
        }

        $profiles = $profiles->elements;

        if($profiles)
        {
            foreach ($profiles as $profile){
                $profile = (array)$profile;
                $profile = $profile['organizationalTarget~'];

                if(in_array($profile->id, $ids)){

                    $avatar = (array)$profile->logoV2;
                    $avatar = $avatar['original~'];
                    $avatar = $avatar->elements[0]->identifiers[0]->identifier;
                    $avatar = save_img( $avatar, TMP_PATH.'avatar/' );
                    $item = $this->model->get('*', $this->tb_account_manager, "social_network = 'linkedin' AND team_id = '{$team_id}' AND pid = '{$profile->id}'");

                    if(!$item){
                        $data = [
                            'ids' => ids(),
                            'social_network' => 'linkedin',
                            'category' => 'page',
                            'login_type' => 1,
                            'can_post' => 1,
                            'team_id' => $team_id,
                            'pid' => $profile->id,
                            'name' => $profile->localizedName,
                            'username' => $profile->vanityName,
                            'token' => $access_token,
                            'avatar' => $avatar,
                            'url' => 'https://linkedin.com/company/'.$profile->id,
                            'data' => NULL,
                            'proxy' => $userinfo->id,
                            'status' => 1,
                            'changed' => now(),
                            'created' => now()
                        ];

                        check_number_account("linkedin", "page");

                        $this->model->insert($this->tb_account_manager, $data);
                    }else{
                        @unlink($item->avatar);

                        $data = [
                            'social_network' => 'linkedin',
                            'category' => 'page',
                            'login_type' => 1,
                            'can_post' => 1,
                            'team_id' => $team_id,
                            'pid' => $profile->id,
                            'name' => $profile->localizedName,
                            'username' => $profile->vanityName,
                            'token' => $access_token,
                            'avatar' => $avatar,
                            'url' => 'https://linkedin.com/company/'.$profile->id,
                            'proxy' => $userinfo->id,
                            'status' => 1,
                            'changed' => now(),
                        ];

                        $this->model->update($this->tb_account_manager, $data, ['id' => $item->id]);
                    }
                }

            }

            $items = $this->model->fetch('*', $this->tb_account_manager, "social_network = 'linkedin' AND team_id = '{$team_id}' AND proxy = '{$userinfo->id}'");
            if(!empty($items)){
                foreach ($items as $key => $value) {
                    $this->model->update($this->tb_account_manager, ["token" => $access_token], ['id' => $value->id]);
                }
            }

            _us('linkedin_access_token');
            _us("linkedin_company_pages");
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
    }
}