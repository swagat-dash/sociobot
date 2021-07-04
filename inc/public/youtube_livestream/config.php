<?php
return [
    'id' => 'youtube_livestream',
    'name' => 'Youtube Livestream',
    'author' => 'SwagatDash',
    'author_uri' => 'https://swagatdash.com',
    'version' => '1.0',
    'desc' => '',
    'icon' => 'fab fa-youtube',
    'color' => '#c4302b',
    'menu' => [
        'tab' => 2,
        'position' => 900,
        'name' => 'Youtube',
        'sub_menu' => [
        	'position' => 1000,
            'id' => 'youtube_livestream',
            'name' => 'Livestream'
        ]
    ],
    'css' => [
		'assets/css/youtube_livestream.css'
	]
];