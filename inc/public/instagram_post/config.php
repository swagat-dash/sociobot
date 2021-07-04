<?php
return [
    'id' => 'instagram_post',
    'name' => 'Instagram Post',
    'author' => 'NulledByBabiatoMember',
    'author_uri' => 'https://babiato.co/members/hiy0104.109135/#about',
    'version' => '1.0',
    'desc' => '',
    'icon' => 'fab fa-instagram',
    'color' => '#d62976',
    'menu' => [
        'tab' => 2,
        'position' => 990,
        'name' => 'Instagram',
        'sub_menu' => [
        	'position' => 1000,
            'id' => 'instagram_post',
            'name' => 'Post'
        ]
    ],
    'css' => [
		'assets/css/instagram_post.css'
	],
    'js' => [
        'assets/js/instagram_post.js'
    ]
];