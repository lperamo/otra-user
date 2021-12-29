<?php
/**
 * @author  Lionel Péramo
 */
declare(strict_types=1);

namespace OtraUser\bundles\OtraUser\tasks;

use otra\bdd\Sql;
use otra\config\AllConfig;
use otra\OtraException;
use const otra\console\
{CLI_BASE, CLI_ERROR, CLI_INFO_HIGHLIGHT, END_COLOR};

const
  OTRA_USER_INIT_ARG_DB_CONNECTION_KEY = 2,
  OTRA_USER_INIT_ARG_NAME = 3,
  OTRA_USER_INIT_ARG_ROLES = 4,
  DATABASE_NAME_REGEX = '^[0-9a-zA-Z$_]+$';

if (!isset(AllConfig::$dbConnections))
{
  echo CLI_ERROR, 'The parameter ' . CLI_INFO_HIGHLIGHT, 'dbConnections', CLI_ERROR,
    ' does not exist in your production configuration file!', END_COLOR, PHP_EOL;
  throw new OtraException('', 1, '', null, [], true);
}

if (empty(AllConfig::$dbConnections))
{
  echo CLI_ERROR, 'No database connections in your ', CLI_INFO_HIGHLIGHT, 'dbConnections', CLI_ERROR,
    ' configuration parameter!', END_COLOR, PHP_EOL;
  throw new OtraException('', 1, '', null, [], true);
}

if (isset($argv[OTRA_USER_INIT_ARG_DB_CONNECTION_KEY]))
{
  if (!isset(AllConfig::$dbConnections[$argv[OTRA_USER_INIT_ARG_DB_CONNECTION_KEY]]))
  {
    echo CLI_ERROR, 'There is no database connection key ', CLI_INFO_HIGHLIGHT,
      $argv[OTRA_USER_INIT_ARG_DB_CONNECTION_KEY], CLI_ERROR, ' in your ', CLI_INFO_HIGHLIGHT, 'dbConnections',
      CLI_ERROR, ' configuration parameter!', END_COLOR, PHP_EOL;
    throw new OtraException('', 1, '', null, [], true);
  }

  define(__NAMESPACE__ . '\\DB', AllConfig::$dbConnections[$argv[OTRA_USER_INIT_ARG_DB_CONNECTION_KEY]]['db']);
  $db = Sql::getDb(AllConfig::$dbConnections[$argv[OTRA_USER_INIT_ARG_DB_CONNECTION_KEY]]['db'], false);
} else
{
  if (empty(AllConfig::$defaultConn))
  {
    echo CLI_ERROR, 'There is no default database connection key in your ', CLI_INFO_HIGHLIGHT, 'defaultConnection',
    CLI_ERROR, ' configuration parameter!', END_COLOR, PHP_EOL;
    throw new OtraException('', 1, '', null, [], true);
  }

  define(__NAMESPACE__ . '\\DB', AllConfig::$dbConnections[AllConfig::$defaultConn]['db']);
  $db = Sql::getDb(null, false);
}

if (preg_match('@' . DATABASE_NAME_REGEX . '@', DB, $matches) === 0)
{
  echo CLI_ERROR, 'Your database name ' . CLI_INFO_HIGHLIGHT . DB . CLI_ERROR .
    ' is not correct. It must follow this regular expression ', CLI_INFO_HIGHLIGHT, DATABASE_NAME_REGEX, CLI_ERROR, '!',
    END_COLOR, PHP_EOL;
  throw new OtraException('', 1, '', null, [], true);
}

define(
  __NAMESPACE__ . '\\NAME',
  isset($argv[OTRA_USER_INIT_ARG_NAME])
    ? intval($argv[OTRA_USER_INIT_ARG_NAME])
    : 0
);

define(
  __NAMESPACE__ . '\\ROLES',
  isset($argv[OTRA_USER_INIT_ARG_ROLES])
    ? intval($argv[OTRA_USER_INIT_ARG_ROLES])
    : 0
);

$db->beginTransaction();

// alternative is to separate username and domain name by putting domains in
// their own table to minimize storage
// 320 seems for ...64 for the username + 1 for @ + 255 for the domain name
$queryUserTable = 'CREATE TABLE user(
  id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  username NVARCHAR(255),
  email NVARCHAR(320) NOT NULL,
  password VARCHAR(255) NOT NULL';

if (NAME === 1)
  $queryUserTable .= ',
    first_name NVARCHAR(255),
    last_name NVARCHAR(255)';

$queryRightsTable = '';

if (ROLES === 1)
{
  $queryUserTable .= ',
    fk_id_role INT UNSIGNED NOT NULL,
    FOREIGN KEY (fk_id_role)
      REFERENCES role(id)';

  $queryRightsTable .= '
  CREATE TABLE role(
    id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    mask TINYINT UNSIGNED UNIQUE NOT NULL,
    name NVARCHAR(255) UNIQUE NOT NULL
  );';
}

$statement = $db->prepare(
//  'CREATE DATABASE IF NOT EXISTS `:database`;'
  'CREATE DATABASE IF NOT EXISTS `' . DB . '`;
  USE ' . DB . ';' . $queryRightsTable . $queryUserTable .');'
);

$statement->execute();
$statement->closeCursor();
try
{
  $db->commit();
} catch(\PDOException $exception)
{
  echo CLI_ERROR . 'Cannot commit!' . $exception->getMessage() . END_COLOR . PHP_EOL;
}

echo CLI_BASE . 'Table ' . CLI_INFO_HIGHLIGHT . 'user' . CLI_BASE . ' created.';

if (ROLES === 1)
  echo PHP_EOL . 'Table ' . CLI_INFO_HIGHLIGHT . 'role' . CLI_BASE . ' created.';

echo END_COLOR . PHP_EOL;
