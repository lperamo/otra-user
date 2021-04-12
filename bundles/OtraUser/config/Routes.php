<?php
declare(strict_types=1);
return [
  'login' => [
    'chunks' => ['/login', 'OtraUser', 'frontend', 'index', 'LoginAction'],
    'resources' => [
      'template' => true,
      '_css' => ['pages/login/login'],
      '_js' => ['user']
    ]
  ],
  'loginCheck' => [
    'chunks' => ['/login-check', 'OtraUser', 'frontend', 'index', 'LoginCheckAction'],
    'resources' => [
      'template' => true
    ]
  ]
];

