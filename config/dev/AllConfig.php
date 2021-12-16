<?php
declare(strict_types=1);

/** THE framework development config
 *
 * @author Lionel Péramo */
namespace otra\config;

use const otra\cache\php\CACHE_PATH;

define('otra\\cache\\php\\CACHE_TIME', 300); // 5 minutes(5*60)

/**
 * @package config
 */
abstract class AllConfig
{
  public static int $verbose = 1;
  public static bool
    $cache = false,
    $cssSourceMaps = false;
  public static string
    /* In order to not make new AllConfig::foo before calling CACHE_PATH, use directly AllConfig::$cachePath in this
    case (if we not use AllConfig::foo it will not load AllConfig even if it's in the use statement so the "defines"
     aren't accessible ) */
    $cachePath = CACHE_PATH,
    $defaultConn = 'otraUser';
  public static array
    $dbConnections = [
      'otraUser' => [
        'driver' => 'Pdomysql',
        'host' => 'localhost',
        'port' => '',
        'db' => 'otra_user',
        'motor' => 'InnoDB'
      ],
    ],
    $debugConfig = [
      'autoLaunch' => true,
      'barPosition' => 'bottom',
      'maxChildren' => 128,
      'maxData' => 512,
      'maxDepth' => 3
    ],
    $tasksFolders = [];
}

AllConfig::$dbConnections['otraUser'] = array_merge(
  AllConfig::$dbConnections['otraUser'],
  [
    'host' => $_SERVER['DATABASE_HOST'],
    'db' => $_SERVER['DATABASE_NAME'],
    'login' => $_SERVER['DATABASE_LOGIN'],
    'password' => $_SERVER['DATABASE_PASSWORD'],
  ]
);
