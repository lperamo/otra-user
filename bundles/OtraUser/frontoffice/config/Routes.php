<?php
declare(strict_types=1);
const OTRA_USER_PREFIX = 'otra\\user';
return [
  'login' => [
    'chunks' => ['/login', 'OtraUser', 'frontoffice', 'index', 'LoginFormAction'],
    'prefix' => OTRA_USER_PREFIX,
    'resources' => [
      'template' => true,
      'app_js' => ['jsRouting'],
      'module_css' => ['pages/login/login'],
      'module_js' => ['loginForm', 'userLogin']
    ]
  ],
  'loginCheck' => [
    'chunks' => ['/login-check', 'OtraUser', 'frontoffice', 'index', 'LoginCheckAction'],
    'prefix' => OTRA_USER_PREFIX,
    'resources' => [
      'template' => true
    ]
  ],
  'logout' => [
    'chunks' => ['/logout', 'OtraUser', 'frontoffice', 'index', 'LogoutAction'],
    'prefix' => OTRA_USER_PREFIX
  ]
];
