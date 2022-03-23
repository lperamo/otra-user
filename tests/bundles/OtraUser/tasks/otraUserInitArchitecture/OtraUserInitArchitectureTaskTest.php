<?php
declare(strict_types=1);

namespace bundles\OtraUser\tasks\otraUserInitArchitecture;

use otra\OtraException;
use phpunit\framework\TestCase;
use const otra\bin\TEST_PATH;
use const otra\cache\php\{BASE_PATH, BUNDLES_PATH, CORE_PATH};
use const otra\console\
{CLI_BASE, CLI_ERROR, CLI_INFO_HIGHLIGHT, CLI_SUCCESS, CLI_TABLE, CLI_WARNING, END_COLOR, ERASE_SEQUENCE, SUCCESS};
use function otra\tools\{copyFileAndFolders, delTree};

/**
 * @runTestsInSeparateProcesses
 */
class OtraUserInitArchitectureTaskTest extends TestCase
{
  private const
    BUNDLES_PATH_CONFIG_FOR_COPY = BUNDLES_PATH . 'config',
    BUNDLES_PATH_CONFIG = self::BUNDLES_PATH_CONFIG_FOR_COPY . '/',

    BUNDLES_PATH_OTRA_USER_FOR_COPY = BUNDLES_PATH . 'OtraUser',
    BUNDLES_PATH_OTRA_USER = self::BUNDLES_PATH_OTRA_USER_FOR_COPY . '/',

    VENDOR_OTRA_USER = BASE_PATH . 'vendor/otra/user/',

    VENDOR_BUNDLE_OTRA_USER_FOR_COPY = BASE_PATH . 'vendor/otra/user/bundles/OtraUser',
    VENDOR_BUNDLE_OTRA_USER = self::VENDOR_BUNDLE_OTRA_USER_FOR_COPY . '/',

    VENDOR_OTRA_USER_CONFIG_FOR_COPY = BASE_PATH . 'vendor/otra/user/bundles/config',
    VENDOR_OTRA_USER_CONFIG = self::VENDOR_OTRA_USER_CONFIG_FOR_COPY . '/',

    COPY_FILES_AND_FOLDERS_PATH = CORE_PATH . 'tools/copyFilesAndFolders.php',
    DELETE_TREE_PATH = CORE_PATH . 'tools/deleteTree.php';

  // It fixes issues like when AllConfig is not loaded while it should be
  protected $preserveGlobalState = FALSE;

  private static bool $workAroundOddBug = false;

  /**
   * @throws OtraException
   */
  public static function setUpBeforeClass(): void
  {
    if (!self::$workAroundOddBug)
    {
      parent::setUpBeforeClass();

      // context
      require self::COPY_FILES_AND_FOLDERS_PATH;
      require self::DELETE_TREE_PATH;
      copyFileAndFolders(
        [
          self::BUNDLES_PATH_CONFIG,
          self::BUNDLES_PATH_OTRA_USER
        ],
        [
          self::VENDOR_OTRA_USER_CONFIG,
          self::VENDOR_BUNDLE_OTRA_USER
        ]
      );
      delTree(BUNDLES_PATH);
    }
  }

  /**
   * @throws OtraException
   */
  public static function tearDownAfterClass(): void
  {
    if (!self::$workAroundOddBug)
    {
//      $test = fopen(BASE_PATH . 'test.txt', 'a+');
//      fwrite($test, 'coucou');
//      fclose($test);
//      fwrite(STDOUT, 'called' . PHP_EOL);
//    }
//      parent::tearDownAfterClass();
      self::restoring();
      $workAroundOddBug = true;
    }
  }

  public function testExists() : void
  {
    $this->assertFileExists(self::VENDOR_OTRA_USER_CONFIG);
    $this->assertFileExists(self::VENDOR_BUNDLE_OTRA_USER);
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

    // Truncating bundles/config
    if (file_exists(self::BUNDLES_PATH_CONFIG))
      delTree(self::BUNDLES_PATH_CONFIG);

    mkdir(self::BUNDLES_PATH_CONFIG, 0777, true);

    // Truncating bundles/OtraUser
    if (file_exists(self::BUNDLES_PATH_OTRA_USER))
      delTree(self::BUNDLES_PATH_OTRA_USER);

    mkdir(self::BUNDLES_PATH_OTRA_USER, 0777, true);

    copyFileAndFolders(
      [
        self::VENDOR_OTRA_USER_CONFIG_FOR_COPY,
        self::VENDOR_BUNDLE_OTRA_USER_FOR_COPY
      ],
      [
        self::BUNDLES_PATH_CONFIG_FOR_COPY,
        self::BUNDLES_PATH_OTRA_USER_FOR_COPY
      ]
    );

    if (file_exists(self::VENDOR_OTRA_USER))
      delTree(self::VENDOR_OTRA_USER);
  }

  /**
   * We do not specify any key.
   *
   * @author Lionel Péramo
   * @throws OtraException
   */
  public function testAllGood() : void
  {
    // context
    mkdir(BUNDLES_PATH . 'config/', 0777, true);
    copy(TEST_PATH . 'config/Routes.php', BUNDLES_PATH . 'config/Routes.php');
    require TEST_PATH . 'config/AllConfig.php';

    // launching
    $argv = [];
    ob_start();
    require_once self::VENDOR_BUNDLE_OTRA_USER . 'tasks/otraUserInitArchitecture/otraUserInitArchitectureTask.php';
    $output = ob_get_clean();

    // testing
    self::assertEquals(
      'Creating main structure...' . PHP_EOL .
      ERASE_SEQUENCE . 'Main structure created' . SUCCESS .

      'Copying configuration files...' . PHP_EOL .
      ERASE_SEQUENCE . 'Configuration files copied' . SUCCESS .

      'Copying controller, services, views and SASS files...' . PHP_EOL .
      ERASE_SEQUENCE . 'Controller, services and views files copied' . SUCCESS .

      'Adding symbolic links to TypeScript files...' . PHP_EOL .
      ERASE_SEQUENCE . 'Symbolic links to TypeScript files added' . CLI_SUCCESS . ' ✔' . PHP_EOL .

      CLI_BASE . '> otra buildDev' . END_COLOR . PHP_EOL .
      CLI_BASE .
      CLI_WARNING . 'The production configuration is used for this task.' . END_COLOR . PHP_EOL .
      'Class mapping finished' . SUCCESS .
      'Launching routes update...' . PHP_EOL .
      CLI_TABLE . 'BASE_PATH + ' . CLI_INFO_HIGHLIGHT . 'bundles/config/Routes.php' . CLI_BASE .
      ' updated' . SUCCESS .
      CLI_BASE . 'Files have been generated' . SUCCESS . END_COLOR . PHP_EOL,
      $output
    );
//    self::assertFileExists();
  }

  /**
   * No 'bundles' folder at the root of the project
   *
   * @author Lionel Péramo
   * @throws OtraException
   */
  public function testNoBundles() : void
  {
    // testing
    $this->expectException(OtraException::class);
    self::expectOutputString(CLI_ERROR . 'There is no bundles in your project!' . END_COLOR . PHP_EOL);

    // launching
    $argv = [];
    require_once self::VENDOR_BUNDLE_OTRA_USER . 'tasks/otraUserInitArchitecture/otraUserInitArchitectureTask.php';
  }

  /**
   * @doesNotPerformAssertions
   */
  public function testNothing(): void
  {

  }
}
