<?php
use otra\config\AllConfig;
use const otra\cache\php\BASE_PATH;

enum Roles: int
{
  case ROLE_ADMIN = 1;
  case ROLE_MODERATOR = 2;
}

AllConfig::$taskFolders[] = constant(__NAMESPACE__ . '\\OTRA_USER_PATH') . 'bundles/OtraUser/tasks/';
// We use constant here as the BASE_PATH is different in each project where we use this bundle
AllConfig::$sassLoadPaths = [
  ...AllConfig::$sassLoadPaths,
  BASE_PATH,
  BASE_PATH . 'vendor/ecocomposer/ecocomposer/',
  constant(__NAMESPACE__ . '\\OTRA_USER_PATH') . 'bundles/OtraUser/resources/scss/',
  constant(__NAMESPACE__ . '\\OTRA_USER_PATH') . 'bundles/OtraUser/backoffice/resources/scss/',
  constant(__NAMESPACE__ . '\\OTRA_USER_PATH') . 'bundles/OtraUser/frontoffice/resources/scss/'
];
