<?php
declare(strict_types=1);

namespace bundles\OtraUser\tasks\otraUserInitArchitecture;

use otra\console\TasksManager;
use otra\OtraException;
use phpunit\framework\TestCase;
use const otra\bin\TASK_CLASS_MAP_PATH;
use const otra\console\{CLI_BASE, CLI_GRAY, CLI_INFO, END_COLOR};

/**
 * @runTestsInSeparateProcesses
 */
class OtraUserInitArchitectureHelpTest extends TestCase
{
  private const
    TASK_OTRA_USER_INIT = 'otraUserInitArchitecture',
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
      'Creates the folders and files needed to handle users.' . PHP_EOL .
      END_COLOR
    );

    TasksManager::execute(
      require TASK_CLASS_MAP_PATH,
      self::OTRA_TASK_HELP,
      ['otra.php', self::OTRA_TASK_HELP, self::TASK_OTRA_USER_INIT]
    );
  }
}
