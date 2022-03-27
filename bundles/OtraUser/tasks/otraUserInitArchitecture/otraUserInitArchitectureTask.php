<?php
/**
 * @author  Lionel Péramo
 */
declare(strict_types=1);

namespace otra\user\bundles\OtraUser\tasks;

use otra\console\TasksManager;
use otra\OtraException;
use const otra\bin\CACHE_PHP_INIT_PATH;
use const otra\cache\php\{BASE_PATH, BUNDLES_PATH, CORE_PATH};
use const otra\console\{CLI_BASE, CLI_ERROR, CLI_SUCCESS, END_COLOR, ERASE_SEQUENCE};
use function otra\console\deployment\genClassMap\genClassMap;
use function otra\console\deployment\updateConf\updateConf;
use function otra\tools\{cliCommand, copyFileAndFolders};

const
  OTRA_USER_CONFIG_FOLDER_CHUNK = 'config/',
  OTRA_USER_BACKOFFICE_FOLDER_CHUNK = 'backoffice/',
  OTRA_USER_FRONTOFFICE_FOLDER_CHUNK = 'frontoffice/',
  OTRA_USER_RESOURCES_FOLDER_CHUNK = 'resources/',
  OTRA_USER_DEVJS_CHUNK = 'resources/devJs/',
  OTRA_USER_LOGOUT_TS_CHUNK = 'userLogout.ts',
  OTRA_USER_LOGIN_FORM_TS_CHUNK = 'loginForm.ts',
  OTRA_USER_VENDOR_BUNDLE_PATH = BASE_PATH . 'vendor/otra/user/bundles/OtraUser/',
  OTRA_USER_VENDOR_STARTERS_PATH = OTRA_USER_VENDOR_BUNDLE_PATH . 'tasks/starters/',
  OTRA_USER_MAIN_PATH = BASE_PATH . 'bundles/OtraUser/',
  OTRA_USER_BACKOFFICE_PATH = OTRA_USER_MAIN_PATH . OTRA_USER_BACKOFFICE_FOLDER_CHUNK,
  OTRA_USER_FRONTOFFICE_PATH = OTRA_USER_MAIN_PATH . OTRA_USER_FRONTOFFICE_FOLDER_CHUNK,
  OTRA_USER_CONFIG_PATH = OTRA_USER_MAIN_PATH . OTRA_USER_CONFIG_FOLDER_CHUNK,
  OTRA_USER_VIEWS_PATH = OTRA_USER_MAIN_PATH . 'views/',
  BASE_PATH_THEMES_PATH = BUNDLES_PATH . OTRA_USER_RESOURCES_FOLDER_CHUNK . 'scss/' . 'configuration/' . 'components/' . 'themes/',
  SELECT_SCSS = '_select.scss',
  STYLED_BUTTONS_SCSS = '_styledButtons.scss',
  TABLE_SCSS = '_table.scss';

/**
 * @throws OtraException
 */
function otraUserInitArchitecture()
{
  /** MAIN STRUCTURE */
  echo 'Creating main structure...', PHP_EOL;

  if (!file_exists(OTRA_USER_BACKOFFICE_PATH))
    mkdir(OTRA_USER_BACKOFFICE_PATH, 0777, true);

  if (!file_exists(OTRA_USER_FRONTOFFICE_PATH))
    mkdir(OTRA_USER_FRONTOFFICE_PATH, 0777, true);

  echo ERASE_SEQUENCE, 'Main structure created', CLI_SUCCESS, ' ✔', END_COLOR, PHP_EOL;

  /** CONFIGURATION */
  echo 'Copying configuration files...', PHP_EOL;
  require CORE_PATH . 'tools/copyFilesAndFolders.php';

  copyFileAndFolders(
    [
      OTRA_USER_VENDOR_BUNDLE_PATH . OTRA_USER_CONFIG_FOLDER_CHUNK,
      OTRA_USER_VENDOR_BUNDLE_PATH . OTRA_USER_BACKOFFICE_FOLDER_CHUNK . OTRA_USER_CONFIG_FOLDER_CHUNK,
      OTRA_USER_VENDOR_BUNDLE_PATH . OTRA_USER_FRONTOFFICE_FOLDER_CHUNK . OTRA_USER_CONFIG_FOLDER_CHUNK
    ],
    [
      OTRA_USER_CONFIG_PATH,
      OTRA_USER_BACKOFFICE_PATH . OTRA_USER_CONFIG_FOLDER_CHUNK,
      OTRA_USER_FRONTOFFICE_PATH . OTRA_USER_CONFIG_FOLDER_CHUNK
    ]
  );

  echo ERASE_SEQUENCE, 'Configuration files copied', CLI_SUCCESS, ' ✔', END_COLOR, PHP_EOL;

  /** CONTROLLERS, SERVICES AND VIEWS */
  echo 'Copying controller, services, views, JavaScript and SASS files...', PHP_EOL;

  copyFileAndFolders(
    [
      OTRA_USER_VENDOR_STARTERS_PATH . OTRA_USER_BACKOFFICE_FOLDER_CHUNK,
      OTRA_USER_VENDOR_STARTERS_PATH . OTRA_USER_FRONTOFFICE_FOLDER_CHUNK,
      OTRA_USER_VENDOR_STARTERS_PATH . OTRA_USER_RESOURCES_FOLDER_CHUNK,
      OTRA_USER_VENDOR_STARTERS_PATH . OTRA_USER_BACKOFFICE_FOLDER_CHUNK . OTRA_USER_RESOURCES_FOLDER_CHUNK,
      OTRA_USER_VENDOR_STARTERS_PATH . OTRA_USER_FRONTOFFICE_FOLDER_CHUNK . OTRA_USER_RESOURCES_FOLDER_CHUNK,
      OTRA_USER_VENDOR_STARTERS_PATH . 'views/'
    ],
    [
      OTRA_USER_BACKOFFICE_PATH,
      OTRA_USER_FRONTOFFICE_PATH,
      OTRA_USER_MAIN_PATH . OTRA_USER_RESOURCES_FOLDER_CHUNK,
      OTRA_USER_MAIN_PATH . OTRA_USER_BACKOFFICE_FOLDER_CHUNK . OTRA_USER_RESOURCES_FOLDER_CHUNK,
      OTRA_USER_MAIN_PATH . OTRA_USER_FRONTOFFICE_FOLDER_CHUNK . OTRA_USER_RESOURCES_FOLDER_CHUNK,
      OTRA_USER_VIEWS_PATH
    ]
  );

  if (!file_exists(BASE_PATH_THEMES_PATH))
    mkdir(
      BASE_PATH_THEMES_PATH,
      0777,
      true
    );

  if (!file_exists(BASE_PATH_THEMES_PATH . SELECT_SCSS))
    copy(
      OTRA_USER_VENDOR_STARTERS_PATH . 'baseResources/' . SELECT_SCSS,
      BASE_PATH_THEMES_PATH . SELECT_SCSS
    );

  if (!file_exists(BASE_PATH_THEMES_PATH . STYLED_BUTTONS_SCSS))
    copy(
      OTRA_USER_VENDOR_STARTERS_PATH . 'baseResources/' . STYLED_BUTTONS_SCSS,
      BASE_PATH_THEMES_PATH . STYLED_BUTTONS_SCSS
    );

  if (!file_exists(BASE_PATH_THEMES_PATH . TABLE_SCSS))
    copy(
      OTRA_USER_VENDOR_STARTERS_PATH . 'baseResources/' . TABLE_SCSS,
      BASE_PATH_THEMES_PATH . TABLE_SCSS
    );

  echo ERASE_SEQUENCE, 'Controller, services and views files copied', CLI_SUCCESS, ' ✔', END_COLOR, PHP_EOL;
  echo 'Adding symbolic links to TypeScript files...', PHP_EOL;

  if (
    !file_exists(OTRA_USER_BACKOFFICE_PATH . OTRA_USER_DEVJS_CHUNK)
    && !mkdir(OTRA_USER_BACKOFFICE_PATH . OTRA_USER_DEVJS_CHUNK)
  )
  {
    echo CLI_ERROR, 'Cannot create folder ', CLI_INFO_HIGHLIGHT, OTRA_USER_BACKOFFICE_PATH, OTRA_USER_DEVJS_CHUNK,
      CLI_ERROR, '.', END_COLOR, PHP_EOL;
    throw new OtraException(code: 1, exit: true);
  }

  symlink(
    OTRA_USER_VENDOR_BUNDLE_PATH . OTRA_USER_BACKOFFICE_FOLDER_CHUNK . OTRA_USER_DEVJS_CHUNK .
    OTRA_USER_LOGOUT_TS_CHUNK,
    OTRA_USER_BACKOFFICE_PATH . OTRA_USER_DEVJS_CHUNK . OTRA_USER_LOGOUT_TS_CHUNK
  );

  if (
    !file_exists(OTRA_USER_FRONTOFFICE_PATH . OTRA_USER_DEVJS_CHUNK)
    && !mkdir(OTRA_USER_FRONTOFFICE_PATH . OTRA_USER_DEVJS_CHUNK)
  )
  {
    echo CLI_ERROR, 'Cannot create folder ', CLI_INFO_HIGHLIGHT, OTRA_USER_FRONTOFFICE_PATH, OTRA_USER_DEVJS_CHUNK,
      CLI_ERROR, '.', END_COLOR, PHP_EOL;
    throw new OtraException(code: 1, exit: true);
  }

  symlink(
    OTRA_USER_VENDOR_BUNDLE_PATH . OTRA_USER_FRONTOFFICE_FOLDER_CHUNK . OTRA_USER_DEVJS_CHUNK .
    OTRA_USER_LOGIN_FORM_TS_CHUNK,
    OTRA_USER_FRONTOFFICE_PATH . OTRA_USER_DEVJS_CHUNK . OTRA_USER_LOGIN_FORM_TS_CHUNK
  );

  echo ERASE_SEQUENCE, 'Symbolic links to TypeScript files added', CLI_SUCCESS, ' ✔', PHP_EOL;
  echo CLI_BASE, 'Regenerating the class map...', PHP_EOL;
  $tasksClassMap = require CACHE_PHP_INIT_PATH . 'tasksClassMap.php';
  require $tasksClassMap['genClassMap'][TasksManager::TASK_CLASS_MAP_TASK_PATH] . '/genClassMapTask.php';
  genClassMap([]);

  echo 'Launching configuration files update...', PHP_EOL;
  require_once $tasksClassMap['updateConf'][TasksManager::TASK_CLASS_MAP_TASK_PATH] . '/updateConfTask.php';
  updateConf('7');

  echo CLI_BASE, '> otra buildDev', END_COLOR, PHP_EOL;
  require CORE_PATH . 'tools/cli.php';
  [,$output] = cliCommand(
    'php ' . BASE_PATH . 'bin/otra.php buildDev',
    CLI_ERROR . 'There was a problem during the assets transcompilation.' . END_COLOR . PHP_EOL
  );

  echo CLI_BASE, $output, PHP_EOL;
}
