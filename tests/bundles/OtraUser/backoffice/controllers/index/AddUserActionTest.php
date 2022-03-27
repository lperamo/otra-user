<?php
declare(strict_types=1);

namespace bundles\OtraUser\backoffice\controllers\index;

use otra\bdd\Sql;
use otra\
{console\TasksManager, OtraException, Router, Session, user\bundles\OtraUser\backoffice\services\UserService};
use phpunit\framework\TestCase;
use ReflectionException;
use const otra\cache\php\{APP_ENV, BASE_PATH, PROD};
use const otra\bin\CACHE_PHP_INIT_PATH;
use function otra\console\database\sqlCreateDatabase\sqlCreateDatabase;
use function otra\console\database\sqlCreateFixtures\sqlCreateFixtures;

/**
 * @runTestsInSeparateProcesses
 * It fixes issues like when AllConfig is not loaded while it should be
 * @preserveGlobalState disabled
 */
class AddUserActionTest extends TestCase
{
  private const
    DATABASE_NAME = 'otra_user',
    OTRA_BINARY = 'bin/otra.php',
    PARAMETER_USER_ID = 'userId',
    ROUTE_ADD_USER = 'addUser',
    TASK_END_FILE_NAME = 'Task.php',
    TASK_SQL_CREATE_DATABASE = 'sqlCreateDatabase',
    TASK_SQL_CREATE_FIXTURES = 'sqlCreateFixtures';

  /**
   * @throws OtraException
   */
  public static function setUpBeforeClass() : void
  {
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

    $db = Sql::getDb();
    $query = 'DELETE FROM `' . UserService::TABLE_USER . '` WHERE id = 3';

    $statement = $db->prepare($query);

    if (!$statement->execute())
      throw new OtraException('Cannot execute the query<br>' . $query);
  }

  /**
   * @throws OtraException
   * @throws ReflectionException
   */
  public function testAdd() : void
  {
    // context
    session_name('__Secure-LPSESSID');
    session_start([
      'cookie_secure' => true,
      'cookie_httponly' => true,
      'cookie_samesite' => 'strict'
    ]);
    Session::init();
    Session::sets(
      [
        self::PARAMETER_USER_ID => 1,
        'userRoleMask' => 1,
        'timeout' => 300
      ]
    );
    $_POST = [
      $_POST,
      ...[
        'fkIdRole' => 1,
        'mail' => 'contact@test.com',
        'pwd' => 'pwd',
        'pseudo' => 'pseudo',
        'firstName' => 'firstName',
        'lastName' => 'lastName'
      ]
    ];

    // testing
    ob_start();

    // launching
    Router::get(self::ROUTE_ADD_USER);

    // testing
    self::assertEquals(
      json_encode([
        'success' => true,
        'html' => ''
      ]),
      ob_get_clean()
    );
  }

  /**
   * @throws OtraException
   * @throws ReflectionException
   */
  public function testWrongEmailSyntax() : void
  {
    // context
    session_name('__Secure-LPSESSID');
    session_start([
      'cookie_secure' => true,
      'cookie_httponly' => true,
      'cookie_samesite' => 'strict'
    ]);
    Session::init();
    Session::sets(
      [
        self::PARAMETER_USER_ID => 1,
        'userRoleMask' => 1,
        'timeout' => 300
      ]
    );
    $_POST = [
      $_POST,
      ...[
        'fkIdRole' => 1,
        'mail' => 'contact',
        'pwd' => 'pwd',
        'pseudo' => 'pseudo',
        'firstName' => 'firstName',
        'lastName' => 'lastName'
      ]
    ];

    // testing
    ob_start();

    // launching
    Router::get(self::ROUTE_ADD_USER);

    // testing
    self::assertEquals(
      json_encode([
        'success' => false,
        'message' => 'Wrong email syntax'
      ]),
      ob_get_clean()
    );
  }

  /**
   * @throws OtraException
   * @throws ReflectionException
   */
  public function testWrongRights() : void
  {
    // context
    session_name('__Secure-LPSESSID');
    session_start([
      'cookie_secure' => true,
      'cookie_httponly' => true,
      'cookie_samesite' => 'strict'
    ]);
    Session::init();
    Session::sets(
      [
        self::PARAMETER_USER_ID => 1,
        'userRoleMask' => 3,
        'timeout' => 300
      ]
    );

    // testing
    $this->expectException(OtraException::class);
    $this->expectExceptionMessage('Rights not sufficient.');

    // launching
    Router::get(
      self::ROUTE_ADD_USER,
      [self::PARAMETER_USER_ID => 2]
    );
  }

  public function testNotAjaxUserNotConnected() : void
  {
    // testing
    $this->expectException(OtraException::class);

    // launching
    Router::get(
      self::ROUTE_ADD_USER,
      [self::PARAMETER_USER_ID => 2]
    );
  }
}
