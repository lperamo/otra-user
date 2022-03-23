<?php
declare(strict_types=1);

namespace bundles\OtraUser\frontoffice\controllers\index;

use otra\{OtraException, Router, Session};
use phpunit\framework\TestCase;
use ReflectionClass;
use ReflectionException;
use const otra\cache\php\{APP_ENV, BASE_PATH, CORE_PATH, PROD};
use function otra\tools\files\returnLegiblePath2;
use function tests\tools\normalizingHtmlResponse;

/**
 * @runTestsInSeparateProcesses
 */
class NotAjaxLoginFormActionTest extends TestCase
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
    unset($_SESSION['sid'], $_SERVER['HTTP_X_REQUESTED_WITH']);
    Session::init();
    Session::clean();
  }

  /**
   * @throws OtraException
   * @throws ReflectionException
   */
  public function testAjax() : void
  {
    // testing
    $_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';

    // launching
    $response = Router::get(
      'notAjaxLogin',
      []
    );

    // testing
    self::assertEquals(
      file_get_contents(self::LOGIN_FORM_ACTION_EXAMPLE_TEMPLATE),
      normalizingHtmlResponse((new ReflectionClass($response))->getProperty('response')->getValue($response))
    );
  }

  /**
   * @throws OtraException
   * @throws ReflectionException
   */
  public function testNonAjax() : void
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
      'notAjaxLogin',
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
