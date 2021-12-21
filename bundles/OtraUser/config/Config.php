<?php
use otra\config\AllConfig;
use const otra\cache\php\BASE_PATH;

define(
  __NAMESPACE__ . '\\OTRA_USER_PATH',
  file_exists(BASE_PATH . 'vendor/otra/user/') ? BASE_PATH . 'vendor/otra/user/' : BASE_PATH
);
AllConfig::$taskFolders[] = constant(__NAMESPACE__ . '\\OTRA_USER_PATH') . 'bundles/OtraUser/tasks/';
