<?php
declare(strict_types=1);

namespace bundles\OtraUser\backoffice\controllers\index;

use otra\bdd\Sql;
use otra\
{console\TasksManager, OtraException, Router, Session, user\bundles\OtraUser\backoffice\services\UserService};
use phpunit\framework\TestCase;
use ReflectionException;
use const otra\bin\CACHE_PHP_INIT_PATH;
use const otra\cache\php\{APP_ENV, BASE_PATH, PROD};
use function otra\console\database\sqlCreateDatabase\sqlCreateDatabase;
use function otra\console\database\sqlCreateFixtures\sqlCreateFixtures;

/**
 * @runTestsInSeparateProcesses
 * It fixes issues like when AllConfig is not loaded while it should be
 * @preserveGlobalState disabled
 */
class RemoveUserActionTest extends TestCase
{
  private const
    DATABASE_NAME = 'otra_user',
    OTRA_BINARY = 'bin/otra.php',
    PARAMETER_USER_ID = 'userId',
    ROUTE_REMOVE_USER = 'removeUser',
    TASK_END_FILE_NAME = 'Task.php',
    TASK_SQL_CREATE_DATABASE = 'sqlCreateDatabase',
    TASK_SQL_CREATE_FIXTURES = 'sqlCreateFixtures';

  protected function setUp(): void
  {
    parent::setUp();
    $_SERVER[APP_ENV] = PROD;
    $_SERVER['REMOTE_ADDR'] = '::1';
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['HTTP_HOST'] = 'dev.otra-user.tech';
  }

  /**
   * @throws OtraException
   */
  public static function setUpBeforeClass() : void
  {
    $tasksClassMap = require CACHE_PHP_INIT_PATH . 'tasksClassMap.php';
    require_once BASE_PATH . 'tests/config/AllConfig.php';
    require $tasksClassMap[self::TASK_SQL_CREATE_DATABASE][TasksManager::TASK_CLASS_MAP_TASK_PATH] . '/' .
      self::TASK_SQL_CREATE_DATABASE . self::TASK_END_FILE_NAME;
    sqlCreateDatabase([self::OTRA_BINARY, self::TASK_SQL_CREATE_DATABASE, self::DATABASE_NAME, 'true']);

    require $tasksClassMap[self::TASK_SQL_CREATE_FIXTURES][TasksManager::TASK_CLASS_MAP_TASK_PATH] . '/' .
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
    // beware, INSERT IGNORE works with MySQL only
    $query = 'INSERT IGNORE INTO `' . UserService::TABLE_USER .
      '` (`id`, `fk_id_role`, `mail`, `pwd`, `pseudo`, `first_name`, `last_name`, `token`) VALUES(2, 2, \'contact@website2.com\', \'$2y$10$Vps8t9vwk/cgIQdagIf16eXbJujb3luT0k3byEdC4Yxdk0N.bhW.m\', \'MrPaul\', \'Paul\', \'Brown\', NULL)';

    $statement = $db->prepare($query);

    if (!$statement->execute())
      throw new OtraException('Cannot execute the query<br>' . $query);
  }

  /**
   * @throws OtraException
   * @throws ReflectionException
   */
  public function testRemove() : void
  {
    // context
    session_name('__Secure-LPSESSID');
    session_start([
      'cookie_secure' => true,
      'cookie_httponly' => true,
      'cookie_samesite' => 'strict'
    ]);
    require BASE_PATH . 'tests/config/AllConfig.php';
    Session::init();
    Session::sets(
      [
        self::PARAMETER_USER_ID => 1,
        'userRoleMask' => 1,
        'timeout' => 300
      ]
    );

    // testing
    ob_start();

    // launching
    Router::get(
      self::ROUTE_REMOVE_USER,
      [self::PARAMETER_USER_ID => 2]
    );

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
  public function testWrongRights() : void
  {
    // context
    require BASE_PATH . 'tests/config/AllConfig.php';
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
      self::ROUTE_REMOVE_USER,
      [self::PARAMETER_USER_ID => 2]
    );
  }

  public function testNotAjaxUserNotConnected() : void
  {
    // context
    require BASE_PATH . 'tests/config/AllConfig.php';

    // testing
    $this->expectException(OtraException::class);

    // launching
    Router::get(
      self::ROUTE_REMOVE_USER,
      [self::PARAMETER_USER_ID => 2]
    );
  }
}
