<?php
declare(strict_types=1);

namespace bundles\OtraUser\tasks\otraUserInitDatabase;

use otra\console\TasksManager;
use otra\OtraException;
use phpunit\framework\TestCase;
use const otra\bin\TASK_CLASS_MAP_PATH;
use const otra\console\{CLI_BASE, CLI_GRAY, CLI_INFO, CLI_INFO_HIGHLIGHT, END_COLOR};

/**
 * @runTestsInSeparateProcesses
 */
class OtraUserInitDatabaseHelpTest extends TestCase
{
  private const
    TASK_OTRA_USER_INIT = 'otraUserInitDatabase',
    OTRA_TASK_HELP = 'help';

  /**
   * @author Lionel Péramo
   * @throws OtraException
   */
  public function testVersionHelp()
  {
    $this->expectOutputString(
      CLI_BASE .
      str_pad(self::TASK_OTRA_USER_INIT, TasksManager::PAD_LENGTH_FOR_TASK_TITLE_FORMATTING) .
      CLI_GRAY . ': ' . CLI_INFO .
      'Creates the tables needed to handle users.' .
      PHP_EOL . CLI_INFO_HIGHLIGHT .
      '   + ' . str_pad('databaseKey', TasksManager::PAD_LENGTH_FOR_TASK_OPTION_FORMATTING) .
      CLI_GRAY . ': ' . CLI_INFO_HIGHLIGHT . '(' . TasksManager::OPTIONAL_PARAMETER .
      ') ' . CLI_INFO . 'The database key from your configuration files. If not precised, we will take the default one' . PHP_EOL .
      CLI_INFO_HIGHLIGHT .
      '   + ' . str_pad('name', TasksManager::PAD_LENGTH_FOR_TASK_OPTION_FORMATTING) .
      CLI_GRAY . ': ' . CLI_INFO_HIGHLIGHT . '(' . TasksManager::OPTIONAL_PARAMETER .
      ') ' . CLI_INFO . 'Put 1 to add first name and last name. Defaults to 0' . PHP_EOL .
      CLI_INFO_HIGHLIGHT .
      '   + ' . str_pad('roles', TasksManager::PAD_LENGTH_FOR_TASK_OPTION_FORMATTING) .
      CLI_GRAY . ': ' . CLI_INFO_HIGHLIGHT . '(' . TasksManager::OPTIONAL_PARAMETER .
      ') ' . CLI_INFO . 'Put 1 to add roles management. Defaults to 0' . PHP_EOL .
      END_COLOR
    );

    TasksManager::execute(
      require TASK_CLASS_MAP_PATH,
      self::OTRA_TASK_HELP,
      ['otra.php', self::OTRA_TASK_HELP, self::TASK_OTRA_USER_INIT]
    );
  }
}
