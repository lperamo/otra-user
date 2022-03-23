<?php
declare(strict_types=1);

namespace bundles\OtraUser\backoffice\controllers\index;

use otra\{OtraException, Router, Session};
use phpunit\framework\TestCase;
use ReflectionException;
use const otra\cache\php\{APP_ENV, BASE_PATH, CORE_PATH, PROD};
use function otra\tools\files\returnLegiblePath2;
use function tests\tools\normalizingHtmlResponse;

/**
 * @runTestsInSeparateProcesses
 */
class UsersActionTest extends TestCase
{
  // It fixes issues like when AllConfig is not loaded while it should be
  protected $preserveGlobalState = FALSE;
  private const USERS_ACTION_EXAMPLE_TEMPLATE = BASE_PATH . 'tests/examples/usersAction.phtml';

  protected function setUp(): void
  {
    parent::setUp();
    $_SERVER[APP_ENV] = PROD;
    $_SERVER['REMOTE_ADDR'] = '::1';
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['HTTP_HOST'] = 'dev.otra-user.tech';
  }

  /**
   * @throws OtraException
   * @throws ReflectionException
   */
  protected function tearDown(): void
  {
    parent::tearDown();
    Session::init();
    Session::clean();
  }

  /**
   * @throws OtraException
   * @throws ReflectionException
   */
  public function testNotAjaxSuccess() : void
  {
    // context
    session_name('__Secure-LPSESSID');
    session_start([
      'cookie_secure' => true,
      'cookie_httponly' => true,
      'cookie_samesite' => 'strict'
    ]);
    require BASE_PATH . 'tests/config/AllConfig.php';
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
      'users',
      []
    );

    // testing
    require BASE_PATH . 'tests/tools/normalizingHtmlResponse.php';
    require CORE_PATH . '/tools/files/returnLegiblePath.php';
    self::assertEquals(
      file_get_contents(self::USERS_ACTION_EXAMPLE_TEMPLATE),
      normalizingHtmlResponse(ob_get_clean()),
      'Comparing template with ' . returnLegiblePath2(self::USERS_ACTION_EXAMPLE_TEMPLATE)
    );
  }

  /**
   * @throws OtraException
   * @throws ReflectionException
   */
  public function testNotAjaxUserWrongRights() : void
  {
    // context
    require BASE_PATH . 'tests/config/AllConfig.php';
    session_name('__Secure-LPSESSID');
    session_start([
      'cookie_secure' => true,
      'cookie_httponly' => true,
      'cookie_samesite' => 'strict'
    ]);
    Session::init();
    Session::sets(
      [
        'userId' => 1,
        'userRoleMask' => 3,
        'timeout' => 300
      ]
    );

    // testing
    $this->expectException(OtraException::class);
    $this->expectExceptionMessage('Rights not sufficient.');

    // launching
    Router::get(
      'users',
      []
    );
  }

  public function testNotAjaxUserNotConnected() : void
  {
    // context
    require BASE_PATH . 'tests/config/AllConfig.php';

    // testing
    $this->expectException(OtraException::class);

    // launching
    Router::get(
      'users',
      []
    );
  }
}
