<?php
return [
    'id' => 'linkedin_post',
    'name' => 'Linkedin Post',
    'author' => 'SwagatDash',
    'author_uri' => 'https://swagatdash.com',
    'version' => '1.0',
    'desc' => '',
    'icon' => 'fab fa-linkedin',
    'color' => '#0d77b7',
    'menu' => [
        'tab' => 2,
        'position' => 970,
        'name' => 'Linkedin',
        'sub_menu' => [
        	'position' => 1000,
            'id' => 'linkedin_post',
            'name' => 'Post'
        ]
    ],
    'css' => [
		'assets/css/linkedin_post.css'
	]
];