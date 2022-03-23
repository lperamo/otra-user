<?php
declare(strict_types=1);

namespace bundles\OtraUser\tasks\otraUserInitDatabase;

use otra\bdd\Sql;
use otra\config\AllConfig;
use otra\console\TasksManager;
use otra\OtraException;
use PDO;
use phpunit\framework\TestCase;
use const otra\bin\TASK_CLASS_MAP_PATH;
use const otra\cache\php\{APP_ENV, BASE_PATH, PROD};
use const otra\console\{CLI_ERROR, CLI_INFO_HIGHLIGHT, END_COLOR};

/**
 * @runTestsInSeparateProcesses
 */
class OtraUserInitDatabaseTaskTest extends TestCase
{
  private const
    OTRA_BINARY = 'otra.php',
    TASK_OTRA_USER_INIT_DATABASE = 'otraUserInitDatabase',
    BASE_CONFIG_FILE_PATH = BASE_PATH . 'config/prod/AllConfig.php',
    CONFIG_BACKUP_PATH = BASE_PATH . 'config/prod/AllConfig-old.php',
    DATABASE_CONNECTION = 'otraUser',
    FIRST_NAME_AND_LAST_NAME = 1,
    ROLES_ENABLED = 1;

  // It fixes issues like when AllConfig is not loaded while it should be
  protected $preserveGlobalState = FALSE;

  /**
   * @throws OtraException
   */
  private static function cleanBdd(): void
  {
    $db = Sql::getDb(self::DATABASE_CONNECTION, false);
    $db->beginTransaction();
    $statement = $db->prepare('
      USE otra_user;
      DROP TABLE IF EXISTS user;
      DROP TABLE IF EXISTS role;'
    );
    $statement->execute();
    $statement->closeCursor();

    try
    {
      $db->commit();
    } catch(\PDOException $exception)
    {
      var_dump($exception->getMessage());
      $db->rollBack();
    }
  }

  /**
   * @throws OtraException
   */
  protected function setUp(): void
  {
    parent::setUp();
    $_SERVER[APP_ENV] = PROD;
    self::cleanBdd();
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

    AllConfig::$dbConnections[self::DATABASE_CONNECTION] = array_merge(
      AllConfig::$dbConnections[self::DATABASE_CONNECTION],
      [
        'host' => $_SERVER['DATABASE_HOST'],
        'db' => $_SERVER['DATABASE_NAME'],
        'login' => $_SERVER['DATABASE_LOGIN'],
        'password' => $_SERVER['DATABASE_PASSWORD'],
      ]
    );

    self::cleanBdd();
  }

  /**
   * We do not specify any key.
   *
   * @author Lionel Péramo
   * @throws OtraException
   */
  public function test_noDefaultConnection() : void
  {
    // context
    // removing the default connection key
    AllConfig::$defaultConn = '';

    // testing
    $this->expectException(OtraException::class);
    self::expectOutputString(
      CLI_ERROR . 'There is no default database connection key in your ' . CLI_INFO_HIGHLIGHT . 'defaultConnection' .
      CLI_ERROR . ' configuration parameter!' . END_COLOR . PHP_EOL);

    // launching
    TasksManager::execute(
      require TASK_CLASS_MAP_PATH,
      self::TASK_OTRA_USER_INIT_DATABASE,
      [self::OTRA_BINARY, self::TASK_OTRA_USER_INIT_DATABASE]
    );
  }

  /**
   * The key we use does not exist.
   *
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
    require(
      (require(TASK_CLASS_MAP_PATH))[self::TASK_OTRA_USER_INIT_DATABASE][0] . '/' . self::TASK_OTRA_USER_INIT_DATABASE .
      'Task.php'
    );
    TasksManager::execute(
      require TASK_CLASS_MAP_PATH,
      self::TASK_OTRA_USER_INIT_DATABASE,
      [self::OTRA_BINARY, self::TASK_OTRA_USER_INIT_DATABASE, WRONG_DATABASE_CONNECTION_KEY]
    );
  }

  /**
   * @author Lionel Péramo
   * @throws OtraException
   */
  public function test_basicUserTable() : void
  {
    // launching
    TasksManager::execute(
      require TASK_CLASS_MAP_PATH,
      self::TASK_OTRA_USER_INIT_DATABASE,
      [
        self::OTRA_BINARY,
        self::TASK_OTRA_USER_INIT_DATABASE
      ]
    );

    // testing
    $db = Sql::getDb();
    $statement = $db->query('SHOW COLUMNS FROM otra_user.user', PDO::FETCH_ASSOC);
    self::assertEquals(
      [
        [
          'Field' => 'id',
          'Type' => 'int unsigned',
          'Null' => 'NO',
          'Key' => 'PRI',
          'Default' => null,
          'Extra' => 'auto_increment'
        ],
        [
          'Field' => 'username',
          'Type' => 'varchar(255)',
          'Null' => 'YES',
          'Key' => '',
          'Default' => null,
          'Extra' => ''
        ],
        [
          'Field' => 'email',
          'Type' => 'varchar(320)',
          'Null' => 'NO',
          'Key' => '',
          'Default' => null,
          'Extra' => ''
        ],
        [
          'Field' => 'password',
          'Type' => 'varchar(255)',
          'Null' => 'NO',
          'Key' => '',
          'Default' => null,
          'Extra' => ''
        ]
      ],
      $statement->fetchAll()
    );
  }

  /**
   * @author Lionel Péramo
   * @throws OtraException
   */
  public function test_fullDatabase() : void
  {
    var_dump(AllConfig::$defaultConn, AllConfig::$dbConnections);
    // launching
    TasksManager::execute(
      require TASK_CLASS_MAP_PATH,
      self::TASK_OTRA_USER_INIT_DATABASE,
        [
        self::OTRA_BINARY,
        self::TASK_OTRA_USER_INIT_DATABASE,
        self::DATABASE_CONNECTION,
        self::FIRST_NAME_AND_LAST_NAME,
        self::ROLES_ENABLED
      ]
    );

    // testing
    $db = Sql::getDb();
    $statement = $db->query('SHOW COLUMNS FROM otra_user.user', PDO::FETCH_ASSOC);
    self::assertEquals(
      [
        [
          'Field' => 'id',
          'Type' => 'int unsigned',
          'Null' => 'NO',
          'Key' => 'PRI',
          'Default' => null,
          'Extra' => 'auto_increment'
        ],
        [
          'Field' => 'username',
          'Type' => 'varchar(255)',
          'Null' => 'YES',
          'Key' => '',
          'Default' => null,
          'Extra' => ''
        ],
        [
          'Field' => 'email',
          'Type' => 'varchar(320)',
          'Null' => 'NO',
          'Key' => '',
          'Default' => null,
          'Extra' => ''
        ],
        [
          'Field' => 'password',
          'Type' => 'varchar(255)',
          'Null' => 'NO',
          'Key' => '',
          'Default' => null,
          'Extra' => ''
        ],
        [
          'Field' => 'first_name',
          'Type' => 'varchar(255)',
          'Null' => 'YES',
          'Key' => '',
          'Default' => null,
          'Extra' => ''
        ],
        [
          'Field' => 'last_name',
          'Type' => 'varchar(255)',
          'Null' => 'YES',
          'Key' => '',
          'Default' => null,
          'Extra' => ''
        ],
        [
          'Field' => 'fk_id_role',
          'Type' => 'int unsigned',
          'Null' => 'NO',
          'Key' => 'MUL',
          'Default' => null,
          'Extra' => ''
        ]
      ],
      $statement->fetchAll()
    );

    $statement = $db->query('SHOW COLUMNS FROM otra_user.role', PDO::FETCH_ASSOC);
    self::assertEquals(
      [
        [
          'Field' => 'id',
          'Type' => 'int unsigned',
          'Null' => 'NO',
          'Key' => 'PRI',
          'Default' => null,
          'Extra' => 'auto_increment'
        ],
        [
          'Field' => 'mask',
          'Type' => 'tinyint unsigned',
          'Null' => 'NO',
          'Key' => 'UNI',
          'Default' => null,
          'Extra' => ''
        ],
        [
          'Field' => 'name',
          'Type' => 'varchar(255)',
          'Null' => 'NO',
          'Key' => 'UNI',
          'Default' => null,
          'Extra' => ''
        ]
      ],
      $statement->fetchAll()
    );
  }
}
