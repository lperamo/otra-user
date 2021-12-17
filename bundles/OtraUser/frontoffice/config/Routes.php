<?php
declare(strict_types=1);
return [
  'login' => [
    'chunks' => ['/login', 'OtraUser', 'frontoffice', 'index', 'LoginFormAction'],
    'resources' => [
      'template' => true,
      'module_css' => ['pages/login/login'],
      'module_js' => ['loginForm']
    ]
  ],
  'notAjaxLogin' => [
    'chunks' => ['/notAjax/login', 'OtraUser', 'frontoffice', 'index', 'NotAjaxLoginFormAction'],
    'resources' => [
      'template' => true,
      'module_css' => ['pages/login/login'],
      'app_js' => ['jsRouting'],
      'module_js' => ['loginForm']
    ]
  ],
  'loginCheck' => [
    'chunks' => ['/login-check', 'OtraUser', 'frontoffice', 'index', 'LoginCheckAction'],
    'resources' => [
      'template' => true
    ]
  ],
  'logout' => [
    'chunks' => ['/logout', 'OtraUser', 'frontoffice', 'index', 'LogoutAction']
  ]
];
