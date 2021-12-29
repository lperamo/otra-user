<?php
declare(strict_types=1);

namespace bundles\OtraUser\tasks\otraUserInit;

use otra\bdd\Sql;
use otra\config\AllConfig;
use otra\console\TasksManager;
use otra\OtraException;
use phpunit\framework\TestCase;
use const otra\bin\TASK_CLASS_MAP_PATH;
use const otra\bin\TEST_PATH;
use const otra\cache\php\APP_ENV;
use const otra\cache\php\BASE_PATH;
use const otra\console\CLI_ERROR;
use const otra\console\CLI_INFO_HIGHLIGHT;
use const otra\console\END_COLOR;

/**
 * @runTestsInSeparateProcesses
 */
class OtraUserInitTaskTest extends TestCase
{
  private const
    TASK_OTRA_USER_INIT = 'otraUserInit',
    TEST_DATABASE_NAME = 'testDB',
    BASE_CONFIG_FILE_PATH = BASE_PATH . 'config/prod/AllConfig.php',
    CONFIG_BACKUP_PATH = BASE_PATH . 'config/prod/AllConfig-old.php';

  // fixes issues like when AllConfig is not loaded while it should be
  protected $preserveGlobalState = FALSE;

  protected function setUp(): void
  {
    parent::setUp();
    $_SERVER[APP_ENV] = 'prod';
  }

  /**
   * @throws OtraException
   */
  protected function tearDown(): void
  {
    parent::tearDown();

    // Restores the good config' file
    if (file_exists(self::CONFIG_BACKUP_PATH))
    {
      file_put_contents(
        self::BASE_CONFIG_FILE_PATH,
        file_get_contents(self::CONFIG_BACKUP_PATH)
      );
      unlink(self::CONFIG_BACKUP_PATH);
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

    $db = Sql::getDb('otraUser', false);
    $db->beginTransaction();
    $statement = $db->prepare('
      SELECT SCHEMA_NAME
      FROM INFORMATION_SCHEMA.SCHEMATA
      WHERE SCHEMA_NAME = :database'
    );
    $test = $statement->execute(['database' => self::TEST_DATABASE_NAME]);

    try
    {
      $db->commit();
    } catch (\PDOException $exception)
    {
      echo 'PDO exception! ' . $exception->getMessage();
    }
//    var_dump($test);die;
  }

  /**
   * @author Lionel Péramo
   * @throws OtraException
   */
  public function test_noDefaultConnection() : void
  {
    // testing
    $this->expectException(OtraException::class);
    self::expectOutputString(
      CLI_ERROR . 'There is no default database connection key in your ' . CLI_INFO_HIGHLIGHT . 'defaultConnection' .
      CLI_ERROR . ' configuration parameter!' . END_COLOR . PHP_EOL);

    // launching
    TasksManager::execute(
      require TASK_CLASS_MAP_PATH,
      self::TASK_OTRA_USER_INIT,
      ['otra.php', self::TASK_OTRA_USER_INIT]
    );
  }

  /**
   * @author Lionel Péramo
   * @throws OtraException
   */
  public function test_nonExistentDatabaseConnectionKey() : void
  {
    // context
    define(__NAMESPACE__ . '\\WRONG_DATABASE_CONNECTION_KEY', 'dada');

    // testing
    $this->expectException(OtraException::class);
    self::expectOutputString(
      CLI_ERROR . 'There is no database connection key ' . CLI_INFO_HIGHLIGHT . WRONG_DATABASE_CONNECTION_KEY .
      CLI_ERROR . ' in your ' . CLI_INFO_HIGHLIGHT . 'dbConnections' . CLI_ERROR . ' configuration parameter!' .
      END_COLOR . PHP_EOL
    );

    // launching
    TasksManager::execute(
      require TASK_CLASS_MAP_PATH,
      self::TASK_OTRA_USER_INIT,
      ['otra.php', self::TASK_OTRA_USER_INIT, WRONG_DATABASE_CONNECTION_KEY]
    );
  }

  /**
   * @author Lionel Péramo
   * @throws OtraException
   */
  public function test_() : void
  {
    // context
    copy(self::BASE_CONFIG_FILE_PATH, self::CONFIG_BACKUP_PATH);
    file_put_contents(
      self::BASE_CONFIG_FILE_PATH,
      file_get_contents(TEST_PATH . 'config/ProdAllConfigNoDbConnections.php')
    );
    define(__NAMESPACE__ . '\\WRONG_DATABASE_CONNECTION_KEY', 'dada');

    // testing
    $this->expectException(OtraException::class);
    self::expectOutputString(
      CLI_ERROR . 'There is no database connection key ' . CLI_INFO_HIGHLIGHT . WRONG_DATABASE_CONNECTION_KEY .
      CLI_ERROR . ' in your ' . CLI_INFO_HIGHLIGHT . 'dbConnections' . CLI_ERROR . ' configuration parameter!' .
      END_COLOR . PHP_EOL
    );

    // launching
    TasksManager::execute(
      require TASK_CLASS_MAP_PATH,
      self::TASK_OTRA_USER_INIT,
      ['otra.php', self::TASK_OTRA_USER_INIT, WRONG_DATABASE_CONNECTION_KEY]
    );
  }
}
