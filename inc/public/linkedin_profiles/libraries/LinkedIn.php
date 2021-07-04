<?php

namespace myPHPNotes;

use GuzzleHttp\Client;
class LinkedIn  
{
    protected $app_id;
    protected $app_secret;
    protected $callback;
    protected $csrf;
    protected $scopes;
    protected $ssl;
    protected $type;
    public function __construct(string $app_id, string $app_secret, string $callback, string $scopes, bool $ssl = true)
    {
        $this->app_id = $app_id;
        $this->app_secret = $app_secret;
        $this->scopes =  $scopes;
        $this->csrf = random_int(111111,99999999999);
        $this->callback = $callback;
        $this->ssl = $ssl;
        $this->type = "urn:li:person:";
    }

    public function getAuthUrl()
    {
        $_SESSION['linkedincsrf']  = $this->csrf;
        return "https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=". $this->app_id . "&redirect_uri=".$this->callback ."&state=". $this->csrf."&scope=". $this->scopes ;
    }

    public function getAccessToken($code)
    {
        $url = "https://www.linkedin.com/oauth/v2/accessToken";
        $params = [
            'client_id' => $this->app_id,
            'client_secret' => $this->app_secret,
            'redirect_uri' => $this->callback,
            'code' => $code,
            'grant_type' => 'authorization_code',
        ];
        $response = $this->curl($url,http_build_query($params), "application/x-www-form-urlencoded");
        $response = json_decode($response);
        if(isset($response->access_token)){
            $accessToken = $response->access_token;
            return [ "status" => "success", "accessToken" =>  $accessToken ];
        }else{
            return [ "status" => "error", "message" => $response->error_description ];
        }
    }

    public function getPerson($accessToken)
    {
        $url = "https://api.linkedin.com/v2/me?projection=(id,vanityName,firstName,lastName,profilePicture(displayImage~:playableStreams))&oauth2_access_token=" . $accessToken;
        $params = [];
        $response = $this->curl($url,http_build_query($params), "application/x-www-form-urlencoded", false);
        $person = json_decode($response);
        return $person;
    }

    public function getPersonID($accessToken)
    {
        $url = "https://api.linkedin.com/v2/me?oauth2_access_token=" . $accessToken;
        $params = [];
        $response = $this->curl($url,http_build_query($params), "application/x-www-form-urlencoded", false);
        $personID = json_decode($response)->id;
        return $personID;
    }

    public function getCompanyPages($accessToken)
    {

        $company_pages = "https://api.linkedin.com/v2/organizationalEntityAcls?q=roleAssignee&role=ADMINISTRATOR&projection=(elements*(organizationalTarget~(id,localizedName,vanityName,logoV2(original~:playableStreams,cropped~:playableStreams,cropInfo))))&oauth2_access_token=" . trim($accessToken);
        $pages = $this->curl($company_pages,json_encode([]), "application/json", false);
        return json_decode($pages);
        
    }

    public function setType($type){
        $this->type = $type;
    }

    public function linkedInTextPost($accessToken , $person_id,  $message, $visibility = "PUBLIC")
    {
        $post_url = "https://api.linkedin.com/v2/ugcPosts?oauth2_access_token=" .$accessToken;
        $request = [
            "author" => $this->type . $person_id,
            "lifecycleState" => "PUBLISHED",
            "specificContent" => [
                "com.linkedin.ugc.ShareContent" => [
                    "shareCommentary" => [
                        "text" => $message
                    ],
                    "shareMediaCategory" => "NONE",
                ],
                
            ],
            "visibility" => [
                "com.linkedin.ugc.MemberNetworkVisibility" => $visibility,
            ]
        ];
        $post = $this->curl($post_url,json_encode($request), "application/json", true);

        return $post;
    }

    public function linkedInLinkPost($accessToken, $person_id, $message, $link_title, $link_desc, $link_url , $visibility = "PUBLIC")
    {
        $post_url = "https://api.linkedin.com/v2/ugcPosts?oauth2_access_token=" .$accessToken;
        $request = [
            "author" => $this->type . $person_id,
            "lifecycleState" => "PUBLISHED",
            "specificContent" => [
                "com.linkedin.ugc.ShareContent" => [
                    "shareCommentary" => [
                        "text" => $message
                    ],
                    "shareMediaCategory" => "ARTICLE",
                    "media"=> [[
                                            "status" => "READY",
                                            "description"=> [
                                                "text" => substr($link_desc, 0, 200),
                                            ],
                                            "originalUrl" =>  $link_url,
                    
                                            "title" => [
                                                "text" => $link_title,
                                            ],
                                        ]],
                ],
                
            ],
            "visibility" => [
                "com.linkedin.ugc.MemberNetworkVisibility" => $visibility,
            ]
        ];

        $post = $this->curl($post_url,json_encode($request), "application/json", true);

        return $post;
    }

    public function linkedInPhotoPost($accessToken,   $person_id, $message, $image_path,  $image_title, $image_description , $visibility = "PUBLIC")
    {

        $prepareUrl = "https://api.linkedin.com/v2/assets?action=registerUpload&oauth2_access_token=" .$accessToken;
        $prepareRequest =  [
            "registerUploadRequest" => [
                "recipes" => [
                    "urn:li:digitalmediaRecipe:feedshare-image"
                ],
                "owner" => $this->type . $person_id,
                "serviceRelationships" => [
                    [
                        "relationshipType" => "OWNER",
                        "identifier" => "urn:li:userGeneratedContent"
                    ],
                ],
            ],
        ];

        $prepareReponse = $this->curl($prepareUrl,json_encode($prepareRequest), "application/json");
        $prepareReponseParse = json_decode($prepareReponse);
        if(!isset($prepareReponseParse->message)){
            $uploadURL = json_decode($prepareReponse)->value->uploadMechanism->{"com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest"}->uploadUrl;
            $asset_id = json_decode($prepareReponse)->value->asset;

            $client =new Client();
            $response = $client->request('PUT', $uploadURL, [
                'headers' => [ 'Authorization' => 'Bearer ' . $accessToken ],
                'body' => fopen($image_path, 'r'),
                'verify' => $this->ssl
            ]);

            $post_url = "https://api.linkedin.com/v2/ugcPosts?oauth2_access_token=" .$accessToken;
            $request = [
                "author" => $this->type . $person_id,
                "lifecycleState" => "PUBLISHED",
                "specificContent" => [
                    "com.linkedin.ugc.ShareContent" => [
                        "shareCommentary" => [
                            "text" => $message
                        ],
                        "shareMediaCategory" => "IMAGE",
                        "media"=> [[
                                                "status" => "READY",
                                                "description"=> [
                                                    "text" => substr($image_description, 0, 200),
                                                ],
                                                "media" =>  $asset_id,
                        
                                                "title" => [
                                                    "text" => $image_title,
                                                ],
                                            ]],
                    ],
                    
                ],
                "visibility" => [
                    "com.linkedin.ugc.MemberNetworkVisibility" => $visibility ,
                ]
            ];

            $post = $this->curl($post_url,json_encode($request), "application/json");

            return $post;
        }else{
            return $prepareReponse;
        }

    }

    public function linkedInMultiplePhotosPost($accessToken,   $person_id, $message,  array $images , $visibility = "PUBLIC")
    {
        // Posting
        $post_url = "https://api.linkedin.com/v2/ugcPosts?oauth2_access_token=" .$accessToken;
        $request = [
            "author" => $this->type . $person_id,
            "lifecycleState" => "PUBLISHED",
            "specificContent" => [
                "com.linkedin.ugc.ShareContent" => [
                    "shareCommentary" => [
                        "text" => $message
                    ],
                    "shareMediaCategory" => "IMAGE",
                    "media"=> [],
                ],
                
            ],
            "visibility" => [
                "com.linkedin.ugc.MemberNetworkVisibility" => $visibility ,
            ]
        ];

        // Adding Medias
        $media = [];
        foreach ($images as $key => $image) {
            // Preparing Request
            $prepareUrl = "https://api.linkedin.com/v2/assets?action=registerUpload&oauth2_access_token=" .$accessToken;
            $prepareRequest =  [
                "registerUploadRequest" => [
                    "recipes" => [
                        "urn:li:digitalmediaRecipe:feedshare-image"
                    ],
                    "owner" => $this->type . $person_id,
                    "serviceRelationships" => [
                        [
                            "relationshipType" => "OWNER",
                            "identifier" => "urn:li:userGeneratedContent"
                        ],
                    ],
                ],
            ];

            $prepareReponse = $this->curl($prepareUrl,json_encode($prepareRequest), "application/json");
            $prepareReponseParse = json_decode($prepareReponse);
            if(!isset($prepareReponseParse->message)){
                $uploadURL = json_decode($prepareReponse)->value->uploadMechanism->{"com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest"}->uploadUrl;
                $asset_id = json_decode($prepareReponse)->value->asset;
                $images[$key]['asset_id'] = $asset_id;
                $client =new Client();
                $client->request('PUT', $uploadURL, [
                    'headers' => [ 'Authorization' => 'Bearer ' . $accessToken ],
                    'body' => fopen($image['image_path'], 'r'),
                    'verify' => $this->ssl
                ]);
                $media[$key]["status"] = "READY";
                $media[$key]["description"]["text"] = substr($image["desc"], 0, 200);
                $media[$key]["media"] = $asset_id;
                $media[$key]["title"]["text"] = substr($image["title"], 0, 200);
            }else{
                return $prepareReponse;
            }

        }
        $request['specificContent']['com.linkedin.ugc.ShareContent']["media"] = array_values($media);
        $post = $this->curl($post_url,json_encode($request), "application/json");

        return $post;
    }

    public function curl($url, $parameters, $content_type, $post = true)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->ssl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($post) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        }
        curl_setopt($ch, CURLOPT_POST, $post);
        $headers = [];
        $headers[] = "Content-Type: {$content_type}";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        return $result;
    }
}
