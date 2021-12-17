<?php
declare(strict_types=1);
return [
  'users' => [
    'chunks' => ['/admin/users', 'OtraUser', 'backoffice', 'index', 'UsersAction'],
    'resources' => [
      'module_css' => ['pages/users/ajaxUsers'],
      'module_js' => ['userLogout'],
      'template' => true
    ]
  ],
  'notAjaxUsers' => [
    'chunks' => ['/admin/notAjax/users', 'OtraUser', 'backoffice', 'index', 'NotAjaxUsersAction'],
    'resources' => [
      'module_css' => ['pages/users/users'],
      'app_js' => ['jsRouting'],
      'module_js' => ['userLogout'],
      'template' => true
    ]
  ]
];
