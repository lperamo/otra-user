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
class LogoutActionTest extends TestCase
{
  private const
    AJAX_LOGIN_FORM_ACTION_EXAMPLE_TEMPLATE = BASE_PATH . 'tests/examples/ajaxLoginFormAction.phtml',
    LOGIN_FORM_ACTION_EXAMPLE_TEMPLATE = BASE_PATH . 'tests/examples/loginFormAction.phtml',
    LABEL_JS = 'js';

  // It fixes issues like when AllConfig is not loaded while it should be
  protected $preserveGlobalState = FALSE;

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
    require CORE_PATH . '/tools/files/returnLegiblePath.php';
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

  public function testAjaxNoSpa() : void
  {
    // testing
    $_SERVER['REQUEST_URI'] = '';
    $_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';
    Session::sets(
      [
        'userId' => 1,
        'userRoleMask' => 1,
        'timeout' => 300
      ]
    );
    ob_start();

    // launching
    Router::get(
      'logout',
      []
    );

    // testing
   self::assertEquals('', ob_get_clean(), 'Checking that the output is empty.');
  }

  public function testNotAjax() : void
  {
    // testing
    Session::sets(
      [
        'userId' => 1,
        'userRoleMask' => 1,
        'timeout' => 300
      ]
    );
    ob_start();

    // launching
    Router::get(
      'logout',
      []
    );

    // testing
    require BASE_PATH . 'tests/tools/normalizingHtmlResponse.php';
    self::assertEquals(
      file_get_contents(self::LOGIN_FORM_ACTION_EXAMPLE_TEMPLATE),
      normalizingHtmlResponse(ob_get_clean()),
      'Comparing template with ' . returnLegiblePath2(self::LOGIN_FORM_ACTION_EXAMPLE_TEMPLATE)
    );
    self::assertEquals(false, Session::getIfExists('userId'));
    self::assertEquals(false, Session::getIfExists('userRoleMask'));
    self::assertEquals(false, Session::getIfExists('timeout'));
  }
}
