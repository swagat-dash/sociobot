<?php
return [
    'id' => 'reddit_post',
    'name' => 'Reddit Post',
    'author' => 'SwagatDash',
    'author_uri' => 'https://swagatdash.com',
    'version' => '1.0',
    'desc' => '',
    'icon' => 'fab fa-reddit',
    'color' => '#4267b2',
    'menu' => [
        'tab' => 2,
        'position' => 930,
        'name' => 'Reddit',
        'sub_menu' => [
        	'position' => 1000,
            'id' => 'reddit_post',
            'name' => 'Post'
        ]
    ],
    'css' => [
		'assets/css/reddit_post.css'
	],
    'js' => [
        'assets/js/reddit_post.js'
    ]
];