<?php
declare(strict_types=1);

/** THE framework production config
 *
 * @author Lionel Péramo
 */

namespace otra\config;

use const otra\cache\php\{BASE_PATH, CACHE_PATH};

const CACHE_TIME = 300; // 5 minutes(5*60)

/**
 * @package config
 */
abstract class AllConfig
{
  public static int $verbose = 0;
  public static bool $cssSourceMaps = false;
  public static string
    /* In order to not make new AllConfig::$foo before calling CACHE_PATH, use directly AllConfig::$cachePath in this
    case (if we not use AllConfig::$foo it will not load AllConfig even if it's in the use statement so the "defines"
     aren't accessible ) */
    $cachePath = CACHE_PATH,
    $version = '1.0.0-alpha.2.5.0',
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
      'maxChildren' => 256, // 128
      'maxData' => 4096, //512
      'maxDepth' => 6 // 3
    ],
    $deployment = [
      'domainName' => 'otra-user.tech',
      'server' => 'lionelp@vps812032.ovh.net',
      'port' => 49153,
      //    'folder' => '/var/www/html/perso/otra-user/',
      'folder' => '/var/www/html/otra-user/',
      'privateSshKey' => '~/.ssh/id_rsa',
      'gcc' => true
    ],
    $pathsToAvoidForBuild = [
      BASE_PATH . 'bundles/tasks/componentTasks/starters'
    ],
    $taskFolders = [],
    $sassLoadPaths = [];
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
