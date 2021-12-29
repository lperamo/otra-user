<?php
/**
 * @author  Lionel Péramo
 */
declare(strict_types=1);

namespace OtraUser\bundles\OtraUser\tasks;

use otra\console\TasksManager;

return [
  'Creates the tables needed to handle users.',
  [
    'databaseKey' => 'The database key from your configuration files. If not precised, we will take the default one',
    'name' => 'Put 1 to add first name and last name. Defaults to 0',
    'roles' => 'Put 1 to add roles management. Defaults to 0',
  ],
  [
    TasksManager::OPTIONAL_PARAMETER,
    TasksManager::OPTIONAL_PARAMETER,
    TasksManager::OPTIONAL_PARAMETER
  ],
  'OTRA User management'
];
