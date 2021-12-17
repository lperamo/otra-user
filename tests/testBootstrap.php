<?php
declare(strict_types=1);

namespace otra\bin;

use const otra\cache\php\init\CLASSMAP;
use const otra\cache\php\{CACHE_PATH, CONSOLE_PATH};

define(__NAMESPACE__ . '\\OTRA_PROJECT', str_contains(__DIR__, 'vendor'));
require __DIR__ . '/../config/constants.php';

const
  CACHE_PHP_INIT_PATH = CACHE_PATH . 'php/init/',
  TASK_CLASS_MAP_PATH = CACHE_PHP_INIT_PATH . 'tasksClassMap.php',
  TEST_PATH = __DIR__ . '/';

if (file_exists(CACHE_PHP_INIT_PATH . 'ClassMap.php'))
{
  require CACHE_PHP_INIT_PATH . 'ClassMap.php';

  spl_autoload_register(function (string $className)
  {
    if (!isset(CLASSMAP[$className]))
    {
      // Handle the particular test configuration
      if ('AllConfig' === $className)
        require TEST_PATH . 'config/AllConfig.php';
      else
        echo PHP_EOL, 'Path not found for the class name : ', $className, PHP_EOL;
    } else
      require CLASSMAP[$className];
  });

  require CONSOLE_PATH . 'colors.php';
}
