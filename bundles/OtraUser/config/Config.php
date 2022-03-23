<?php
use otra\config\AllConfig;
use const otra\cache\php\BASE_PATH;

define(
  __NAMESPACE__ . '\\OTRA_USER_PATH',
  file_exists(BASE_PATH . 'vendor/otra/user/') ? BASE_PATH . 'vendor/otra/user/' : BASE_PATH
);
AllConfig::$taskFolders[] = constant(__NAMESPACE__ . '\\OTRA_USER_PATH') . 'bundles/OtraUser/tasks/';
// We use constant here as the BASE_PATH is different in each project where we use this bundle
AllConfig::$sassLoadPaths = [
  ...AllConfig::$sassLoadPaths,
  BASE_PATH . 'vendor/ecocomposer/ecocomposer/',
  constant(__NAMESPACE__ . '\\OTRA_USER_PATH') . 'bundles/OtraUser/resources/scss/',
  constant(__NAMESPACE__ . '\\OTRA_USER_PATH') . 'bundles/OtraUser/backoffice/resources/scss/',
  constant(__NAMESPACE__ . '\\OTRA_USER_PATH') . 'bundles/OtraUser/frontoffice/resources/scss/'
];
