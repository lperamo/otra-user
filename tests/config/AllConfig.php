<?php
declare(strict_types=1);

namespace otra\config
{
  use const otra\cache\php\{BASE_PATH, CACHE_PATH};

  const
    VERSION = '1.0.0-alpha.2.5.1',
    RESOURCE_FILE_MIN_SIZE = 21000, // n characters
    CACHE_TIME = 300; // 5 minutes(5*60)

  /**
   * Class AllConfig
   *
   * @package config
   */
  class AllConfig
  {
    public static int $verbose = 0;
    public static string
      /* In order to not make new AllConfig::$foo before calling CACHE_PATH, use directly AllConfig::$cachePath in this
      case
      (if we not use AllConfig::$foo it will not load AllConfig even if it's in the use statement so the "defines" aren't
      accessible ) */
      $cachePath = CACHE_PATH,
      $version = 'v1',
      $defaultConn = 'otraUser'; // mandatory in order to modify it later if needed
    public static array
      $dbConnections = [
        'otraUser' => [
          'driver' => 'Pdomysql',
          'host' => 'localhost',
          'port' => '',
          'db' => 'otra_user',
          'motor' => 'InnoDB'
        ]
      ], // mandatory in order to modify it later if needed
      $debugConfig = [
        'barPosition' => 'bottom',
        'maxChildren' => 130,
        'maxData' => 514,
        'maxDepth' => 6
      ],
      $taskFolders = [],
      $sassLoadPaths = [];
  }

  define(
    __NAMESPACE__ . '\\OTRA_USER_PATH',
    file_exists(BASE_PATH . 'vendor/otra/user/') ? BASE_PATH . 'vendor/otra/user/' : BASE_PATH
  );
  AllConfig::$taskFolders[] = constant(__NAMESPACE__ . '\\OTRA_USER_PATH') . 'bundles/OtraUser/tasks/';
// We use constant here as the BASE_PATH is different in each project where we use this bundle
  AllConfig::$sassLoadPaths = [
    ...AllConfig::$sassLoadPaths,
    BASE_PATH . 'vendor/ecocomposer/ecocomposer/',
    constant(__NAMESPACE__ . '\\OTRA_USER_PATH') . 'bundles/OtraUser/resources/scss',
    constant(__NAMESPACE__ . '\\OTRA_USER_PATH') . 'bundles/OtraUser/backoffice/resources/scss',
    constant(__NAMESPACE__ . '\\OTRA_USER_PATH') . 'bundles/OtraUser/frontoffice/resources/scss'
  ];

  AllConfig::$dbConnections['otraUser'] = array_merge(
    AllConfig::$dbConnections['otraUser'],
    [
      'host' => $_SERVER['DATABASE_HOST'],
      'db' => $_SERVER['DATABASE_NAME'],
      'login' => $_SERVER['DATABASE_LOGIN'],
      'password' => $_SERVER['DATABASE_PASSWORD'],
    ]
  );

  /*
    array(4) {
      [0] =>
      string(61) "/var/www/html/perso/otra-user/vendor/ecocomposer/ecocomposer/"
      [1] =>
      string(78) "/var/www/html/perso/otra-user/vendor/otra/user/bundles/OtraUser/resources/scss"
      [2] =>
      string(89) "/var/www/html/perso/otra-user/vendor/otra/user/bundles/OtraUser/backoffice/resources/scss"
      [3] =>
      string(90) "/var/www/html/perso/otra-user/vendor/otra/user/bundles/OtraUser/frontoffice/resources/scss"
    }
   */
}

namespace bundles\config {
  enum Roles: int
  {
    case ROLE_ADMIN=1;case ROLE_MODERATOR=2;
  }
}
