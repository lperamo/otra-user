<?php
declare(strict_types=1);
return [
  'addUser' => [
    'chunks' => ['/admin/users/add', 'OtraUser', 'backoffice', 'index', 'AddUserAction'],
    'prefix' => OTRA_USER_PREFIX
  ],
  'editUser' => [
    'chunks' => ['/admin/users/edit', 'OtraUser', 'backoffice', 'index', 'EditUserAction'],
    'prefix' => OTRA_USER_PREFIX
  ],
  'removeUser' => [
    'chunks' => ['/admin/users/remove/{userId}', 'OtraUser', 'backoffice', 'index', 'RemoveUserAction'],
    'prefix' => OTRA_USER_PREFIX
  ],
  'users' => [
    'chunks' => ['/admin/users', 'OtraUser', 'backoffice', 'index', 'UsersAction'],
    'prefix' => OTRA_USER_PREFIX,
    'resources' => [
//      'module_css' => ['pages/users/ajaxUsers'],
      'module_js' => ['userLogout']
    ]
  ],
  'notAjaxUsers' => [
    'chunks' => ['/admin/notAjax/users', 'OtraUser', 'backoffice', 'index', 'NotAjaxUsersAction'],
    'prefix' => OTRA_USER_PREFIX,
    'resources' => [
      'module_css' => ['pages/users/users'],
      'app_js' => ['jsRouting', 'spaCall'],
      'module_js' => ['userLogout']
    ]
  ]
];
