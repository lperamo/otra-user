<?php
declare(strict_types=1);

namespace bundles\OtraUser\tasks\otraUserInitArchitecture;

use otra\console\TasksManager;
use otra\OtraException;
use phpunit\framework\TestCase;
use const otra\bin\{CACHE_PHP_INIT_PATH,TEST_PATH};
use const otra\cache\php\{APP_ENV, BASE_PATH, BUNDLES_PATH, CORE_PATH, DEV, PROD};
use const otra\console\
{CLI_BASE, CLI_INFO_HIGHLIGHT, CLI_SUCCESS, CLI_TABLE, CLI_WARNING, END_COLOR, ERASE_SEQUENCE, SUCCESS};
use function otra\console\deployment\genClassMap\genClassMap;
use function otra\tools\{copyFileAndFolders, delTree};
use function otra\user\bundles\OtraUser\tasks\otraUserInitArchitecture;

class OtraUserInitArchitectureTaskTest extends TestCase
{
  private const
    BUNDLES_PATH_CONFIG_FOR_COPY = BUNDLES_PATH . 'config',
    BUNDLES_PATH_CONFIG = self::BUNDLES_PATH_CONFIG_FOR_COPY . '/',

    BUNDLES_PATH_OTRA_USER_FOR_COPY = BUNDLES_PATH . 'OtraUser',
    BUNDLES_PATH_OTRA_USER = self::BUNDLES_PATH_OTRA_USER_FOR_COPY . '/',

    BUNDLES_PATH_OTRA_USER_RESOURCES_FOR_COPY = BUNDLES_PATH . 'resources',
    BUNDLES_PATH_OTRA_USER_RESOURCES = self::BUNDLES_PATH_OTRA_USER_RESOURCES_FOR_COPY . '/',

    VENDOR_OTRA_USER = BASE_PATH . 'vendor/otra/user/',

    VENDOR_BUNDLE_OTRA_USER_FOR_COPY = self::VENDOR_OTRA_USER . 'bundles/OtraUser',
    VENDOR_BUNDLE_OTRA_USER = self::VENDOR_BUNDLE_OTRA_USER_FOR_COPY . '/',

    VENDOR_OTRA_USER_CONFIG_FOR_COPY = self::VENDOR_OTRA_USER . 'bundles/config',
    VENDOR_OTRA_USER_CONFIG = self::VENDOR_OTRA_USER_CONFIG_FOR_COPY . '/',

    VENDOR_OTRA_USER_RESOURCES_FOR_COPY = self::VENDOR_OTRA_USER . 'bundles/resources',
    VENDOR_OTRA_USER_RESOURCES = self::VENDOR_OTRA_USER_RESOURCES_FOR_COPY . '/',

    COPY_FILES_AND_FOLDERS_PATH = CORE_PATH . 'tools/copyFilesAndFolders.php',
    DELETE_TREE_PATH = CORE_PATH . 'tools/deleteTree.php',
    CACHE_PHP_SECURITY = 'cache/php/security/',
    HELLO_WORLD_PHP = '/HelloWorld.php';

  // It fixes issues like when AllConfig is not loaded while it should be
  protected $preserveGlobalState = FALSE;

  /**
   * @throws OtraException
   */
  public function setUp(): void
  {
    parent::setUp();

    // tools
    require self::COPY_FILES_AND_FOLDERS_PATH;
    require self::DELETE_TREE_PATH;

    if (!file_exists(self::BUNDLES_PATH_CONFIG)) {
      var_dump(self::BUNDLES_PATH_CONFIG);
    }

    // backups
    copyFileAndFolders(
      [
        self::BUNDLES_PATH_CONFIG,
        self::BUNDLES_PATH_OTRA_USER,
        self::BUNDLES_PATH_OTRA_USER_RESOURCES
      ],
      [
        self::VENDOR_OTRA_USER_CONFIG,
        self::VENDOR_BUNDLE_OTRA_USER,
        self::VENDOR_OTRA_USER_RESOURCES
      ]
    );
    delTree(BUNDLES_PATH);
  }

  /**
   * @throws OtraException
   */
  public function tearDown(): void
  {
    parent::tearDown();
    self::restoring();
    require_once (require CACHE_PHP_INIT_PATH . 'tasksClassMap.php')['genClassMap'][TasksManager::TASK_CLASS_MAP_TASK_PATH] .
      '/genClassMapTask.php';
    genClassMap([]);
  }

  /**
   * @param string $folder
   *
   * @return void
   */
  private static function truncateFolder(string $folder): void
  {
    if (file_exists($folder))
      delTree($folder);

    mkdir($folder, 0777, true);
  }

  /**
   * @throws OtraException
   */
  public static function restoring()
  {
    // Strangely these functions are sometimes ...unloaded! (due to the use of `afterClass` and/or `beforeClass`?)
    if (!defined('otra\tools\copyFileAndFolders'))
      require_once self::COPY_FILES_AND_FOLDERS_PATH;

    if (!defined('otra\tools\deleteTree.php'))
      require_once self::DELETE_TREE_PATH;

    self::truncateFolder(self::BUNDLES_PATH_CONFIG);
    self::truncateFolder(self::BUNDLES_PATH_OTRA_USER);

    copyFileAndFolders(
      [
        self::VENDOR_OTRA_USER_CONFIG_FOR_COPY,
        self::VENDOR_BUNDLE_OTRA_USER_FOR_COPY,
        self::VENDOR_OTRA_USER_RESOURCES_FOR_COPY
      ],
      [
        self::BUNDLES_PATH_CONFIG_FOR_COPY,
        self::BUNDLES_PATH_OTRA_USER_FOR_COPY,
        self::BUNDLES_PATH_OTRA_USER_RESOURCES_FOR_COPY
      ]
    );

    if (file_exists(self::VENDOR_OTRA_USER))
      delTree(self::VENDOR_OTRA_USER);
  }

  /**
   * We do not specify any key.
   * @runInSeparateProcess
   *
   * @author Lionel Péramo
   * @throws OtraException
   */
  public function testAllGood() : void
  {
    // context
    mkdir(BUNDLES_PATH . 'config/', 0777, true);
    copy(TEST_PATH . 'config/Routes.php', BUNDLES_PATH . 'config/Routes.php');
    require_once TEST_PATH . 'config/AllConfig.php';
    $_SERVER[APP_ENV] = PROD;

    // launching
    $argv = [];
    ob_start();
    require_once self::VENDOR_BUNDLE_OTRA_USER . 'tasks/otraUserInitArchitecture/otraUserInitArchitectureTask.php';
    otraUserInitArchitecture();
    $output = ob_get_clean();

    // testing
    self::assertEquals(
      'Creating main structure...' . PHP_EOL .
      ERASE_SEQUENCE . 'Main structure created' . SUCCESS .

      'Copying configuration files...' . PHP_EOL .
      ERASE_SEQUENCE . 'Configuration files copied' . SUCCESS .

      'Copying controller, services, views, JavaScript and SASS files...' . PHP_EOL .
      ERASE_SEQUENCE . 'Controller, services and views files copied' . SUCCESS .

      'Adding symbolic links to TypeScript files...' . PHP_EOL .
      ERASE_SEQUENCE . 'Symbolic links to TypeScript files added' . CLI_SUCCESS . ' ✔' . PHP_EOL .

      CLI_BASE .  'Regenerating the class map...' .  PHP_EOL .
      'Class mapping finished' . SUCCESS .

      'Launching configuration files update...' . PHP_EOL .
      CLI_TABLE . 'BASE_PATH + ' . CLI_INFO_HIGHLIGHT . 'bundles/config/Config.php' . CLI_BASE .
      ' updated' . CLI_SUCCESS . ' ✔' . END_COLOR . PHP_EOL .
      CLI_TABLE . 'BASE_PATH + ' . CLI_INFO_HIGHLIGHT . 'bundles/config/Routes.php' . CLI_BASE .
      ' updated' . CLI_SUCCESS . ' ✔' . END_COLOR . PHP_EOL .
      CLI_TABLE . 'BASE_PATH + ' . CLI_INFO_HIGHLIGHT . self::CACHE_PHP_SECURITY . DEV . self::HELLO_WORLD_PHP . CLI_BASE .
      ' updated' . CLI_SUCCESS . ' ✔' . END_COLOR . PHP_EOL .
      CLI_TABLE . 'BASE_PATH + ' . CLI_INFO_HIGHLIGHT . self::CACHE_PHP_SECURITY . PROD . self::HELLO_WORLD_PHP . CLI_BASE .
      ' updated' . CLI_SUCCESS . ' ✔' . END_COLOR . PHP_EOL .

      CLI_BASE . '> otra buildDev' . END_COLOR . PHP_EOL .
      CLI_BASE .
      CLI_WARNING . 'The production configuration is used for this task.' . END_COLOR . PHP_EOL .
      'Class mapping finished' . SUCCESS .
      'Launching routes update...' . PHP_EOL .
      CLI_TABLE . 'BASE_PATH + ' . CLI_INFO_HIGHLIGHT . 'bundles/config/Routes.php' . CLI_BASE .
      ' updated' . SUCCESS .
      CLI_BASE . 'Files have been generated' . SUCCESS,
      $output
    );
  }
}
