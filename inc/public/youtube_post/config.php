<?php
return [
    'id' => 'youtube_post',
    'name' => 'Youtube Post',
    'author' => 'SwagatDash',
    'author_uri' => 'https://swagatdash.com',
    'version' => '1.0',
    'desc' => '',
    'icon' => 'fab fa-youtube',
    'color' => '#c4302b',
    'menu' => [
        'tab' => 2,
        'position' => 910,
        'name' => 'Youtube',
        'sub_menu' => [
        	'position' => 1000,
            'id' => 'youtube_post',
            'name' => 'Post'
        ]
    ],
    'css' => [
		'assets/css/youtube_post.css'
	]
];