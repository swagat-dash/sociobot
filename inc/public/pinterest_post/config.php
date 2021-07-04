<?php
return [
    'id' => 'pinterest_post',
    'name' => 'Pinterest Post',
    'author' => 'SwagatDash',
    'author_uri' => 'https://swagatdash.com',
    'version' => '1.0',
    'desc' => '',
    'icon' => 'fab fa-pinterest',
    'color' => '#cd2029',
    'menu' => [
        'tab' => 2,
        'position' => 960,
        'name' => 'Pinterest',
        'sub_menu' => [
        	'position' => 1000,
            'id' => 'pinterest_post',
            'name' => 'Post'
        ]
    ],
    'css' => [
		'assets/css/pinterest_post.css'
	]
];