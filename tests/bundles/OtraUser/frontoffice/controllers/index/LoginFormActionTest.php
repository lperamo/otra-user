<?php
declare(strict_types=1);

namespace bundles\OtraUser\frontoffice\controllers\index;

use otra\{OtraException, Router, Session};
use phpunit\framework\TestCase;
use ReflectionException;
use const otra\cache\php\{APP_ENV, BASE_PATH, CORE_PATH, PROD};
use function otra\tools\files\returnLegiblePath2;
use function tests\tools\normalizingHtmlResponse;

/**
 * @runTestsInSeparateProcesses
 */
class LoginFormActionTest extends TestCase
{
  // It fixes issues like when AllConfig is not loaded while it should be
  protected $preserveGlobalState = FALSE;
  private const
    LOGIN_FORM_ACTION_EXAMPLE_TEMPLATE = BASE_PATH . 'tests/examples/loginFormAction.phtml',
    LOGIN_FORM_ACTION_ALREADY_CONNECTED_EXAMPLE_TEMPLATE = BASE_PATH . 'tests/examples/loginFormActionAlreadyConnected.phtml';

  /**
   * @throws OtraException
   * @throws ReflectionException
   */
  protected function setUp(): void
  {
    parent::setUp();
    $_SERVER[APP_ENV] = PROD;
    require BASE_PATH . 'tests/config/AllConfig.php';
    $_SERVER['REMOTE_ADDR'] = '::1';
    Session::init();
    Session::clean();
    require BASE_PATH . 'tests/tools/normalizingHtmlResponse.php';
  }

  /**
   * @throws OtraException
   * @throws ReflectionException
   */
  protected function tearDown(): void
  {
    parent::tearDown();
    unset($_SESSION['sid']);
    Session::init();
    Session::clean();
  }

  /**
   * @throws OtraException
   * @throws ReflectionException
   */
  public function test() : void
  {
    // testing
    ob_start();

    // launching
    Router::get(
      'login',
      []
    );

    // testing
    require CORE_PATH . '/tools/files/returnLegiblePath.php';
    self::assertEquals(
      file_get_contents(self::LOGIN_FORM_ACTION_EXAMPLE_TEMPLATE),
      normalizingHtmlResponse(ob_get_clean()),
      'Comparing template with ' . returnLegiblePath2(self::LOGIN_FORM_ACTION_EXAMPLE_TEMPLATE)
    );
  }

  /**
   * @throws OtraException
   * @throws ReflectionException
   */
  public function testAlreadyConnected() : void
  {
    // context
    $_SESSION['sid'] = 1;
    Session::init();
    Session::sets(
      [
        'userId' => 1,
        'userRoleMask' => 1,
        'timeout' => 300
      ]
    );

    // testing
    ob_start();

    // launching
    Router::get(
      'login',
      []
    );

    // testing
    require CORE_PATH . '/tools/files/returnLegiblePath.php';
    self::assertEquals(
      file_get_contents(self::LOGIN_FORM_ACTION_ALREADY_CONNECTED_EXAMPLE_TEMPLATE),
      normalizingHtmlResponse(ob_get_clean()),
      'Comparing template with ' . returnLegiblePath2(self::LOGIN_FORM_ACTION_ALREADY_CONNECTED_EXAMPLE_TEMPLATE)
    );
  }
}
