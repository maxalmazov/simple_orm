<?php

return [
    'home' => [
        'pattern' => '/',
        'controller' => 'University::index',
        'method' => 'GET',
    ],
    'university_add' => [
        'pattern' => '/university_add',
        'controller' => 'University::add',
        'method' => 'GET|POST',
    ],
    'university_edit' => [
        'pattern' => '/university_edit/{:num}',
        'controller' => 'University::edit',
        'method' => 'GET|POST',
    ],
];
