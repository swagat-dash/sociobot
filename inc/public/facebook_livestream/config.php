<?php
return [
    'id' => 'facebook_livestream',
    'name' => 'Facebook Livestream',
    'author' => 'SwagatDash',
    'author_uri' => 'https://swagatdash.com',
    'version' => '1.0',
    'desc' => '',
    'icon' => 'fab fa-facebook-square',
    'color' => '#3b5998',
    'menu' => [
        'tab' => 2,
        'position' => 990,
        'name' => 'Facebook',
        'sub_menu' => [
        	'position' => 1000,
            'id' => 'facebook_livestream',
            'name' => 'Livestream'
        ]
    ],
    'css' => [
		'assets/css/facebook_livestream.css'
	]
];