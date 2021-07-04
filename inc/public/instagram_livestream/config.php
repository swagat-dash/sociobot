<?php
return [
    'id' => 'instagram_livestream',
    'name' => 'Instagram Livestream',
    'author' => 'SwagatDash',
    'author_uri' => 'https://swagatdash.com',
    'version' => '1.0',
    'desc' => '',
    'icon' => 'fab fa-instagram',
    'color' => '#d62976',
    'menu' => [
        'tab' => 2,
        'position' => 990,
        'name' => 'Instagram',
        'sub_menu' => [
        	'position' => 2000,
            'id' => 'instagram_livestream',
            'name' => 'Livestream'
        ]
    ],
    'css' => [
		'assets/css/instagram_livestream.css'
	]
];