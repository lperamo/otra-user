<?php
declare(strict_types=1);

namespace bundles\OtraUser\backoffice\controllers\index;

use otra\{console\TasksManager, OtraException, Router, Session};
use phpunit\framework\TestCase;
use ReflectionClass;
use ReflectionException;
use const otra\bin\CACHE_PHP_INIT_PATH;
use const otra\cache\php\{APP_ENV, BASE_PATH, CORE_PATH, PROD};
use function otra\console\database\sqlCreateDatabase\sqlCreateDatabase;
use function otra\console\database\sqlCreateFixtures\sqlCreateFixtures;
use function otra\tools\files\returnLegiblePath2;
use function tests\tools\normalizingHtmlResponse;

/**
 * It fixes issues like when AllConfig is not loaded while it should be
 * @preserveGlobalState disabled
 */
class NotAjaxUsersActionTest extends TestCase
{
  private const
    DATABASE_NAME = 'otra_user',
    OTRA_BINARY = 'bin/otra.php',
    TASK_SQL_CREATE_DATABASE = 'sqlCreateDatabase',
    TASK_SQL_CREATE_FIXTURES = 'sqlCreateFixtures',
    TASK_END_FILE_NAME = 'Task.php',
    USERS_ACTION_EXAMPLE_TEMPLATE = BASE_PATH . 'tests/examples/usersAction.phtml';

  /**
   * @throws OtraException
   */
  public static function setUpBeforeClass(): void
  {
    parent::setUpBeforeClass();
    $_SERVER[APP_ENV] = PROD;
    $_SERVER['REMOTE_ADDR'] = '::1';
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['HTTP_HOST'] = 'dev.otra-user.tech';
    $tasksClassMap = require CACHE_PHP_INIT_PATH . 'tasksClassMap.php';
    require_once BASE_PATH . 'tests/config/AllConfig.php';
    require_once $tasksClassMap[self::TASK_SQL_CREATE_DATABASE][TasksManager::TASK_CLASS_MAP_TASK_PATH] . '/' .
      self::TASK_SQL_CREATE_DATABASE . self::TASK_END_FILE_NAME;
    sqlCreateDatabase([self::OTRA_BINARY, self::TASK_SQL_CREATE_DATABASE, self::DATABASE_NAME, 'true']);

    require_once $tasksClassMap[self::TASK_SQL_CREATE_FIXTURES][TasksManager::TASK_CLASS_MAP_TASK_PATH] . '/' .
      self::TASK_SQL_CREATE_FIXTURES . self::TASK_END_FILE_NAME;
    sqlCreateFixtures([self::OTRA_BINARY, self::TASK_SQL_CREATE_FIXTURES, self::DATABASE_NAME, '2']);
  }

  /**
   * @throws OtraException
   * @throws ReflectionException
   */
  protected function tearDown(): void
  {
    parent::tearDown();
    Session::init();
    Session::clean();
  }

  /**
   * @throws OtraException
   * @throws ReflectionException
   */
  public function testNotAjaxSuccess() : void
  {
    // context
    Session::init();
    Session::sets(
      [
        'userId' => 1,
        'userRoleMask' => 1,
        'timeout' => 300
      ]
    );

    // testing
    // launching
    $response = Router::get(
      'notAjaxUsers',
      []
    );

    // testing
    require BASE_PATH . 'tests/tools/normalizingHtmlResponse.php';
    require CORE_PATH . '/tools/files/returnLegiblePath.php';
    self::assertEquals(
      file_get_contents(self::USERS_ACTION_EXAMPLE_TEMPLATE),
      normalizingHtmlResponse((new ReflectionClass($response))->getProperty('response')->getValue($response)),
      'Comparing template with ' . returnLegiblePath2(self::USERS_ACTION_EXAMPLE_TEMPLATE)
    );
  }

  /**
   * @throws ReflectionException
   * @throws OtraException
   */
  public function testNotAjaxUserWrongRights() : void
  {
    // context
    Session::init();
    Session::sets(
      [
        'userId' => 1,
        'userRoleMask' => 3,
        'timeout' => 300
      ]
    );

    // testing
    $this->expectException(OtraException::class);
    $this->expectExceptionMessage('Rights not sufficient.');

    // launching
    Router::get(
      'notAjaxUsers',
      []
    );
  }

  /**
   * @runInSeparateProcess
   * @return void
   */
  public function testNotAjaxUserNotConnected() : void
  {
    // testing
    $this->expectException(OtraException::class);

    // launching
    Router::get(
      'notAjaxUsers',
      []
    );
  }
}
