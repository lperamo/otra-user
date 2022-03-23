<?php
declare(strict_types=1);

namespace bundles\OtraUser\frontoffice\controllers\index;

use otra\config\Routes;
use otra\Router;
use phpunit\framework\TestCase;
use const otra\cache\php\{APP_ENV, BASE_PATH, CORE_PATH, PROD};
use function otra\tools\files\returnLegiblePath2;
use function tests\tools\normalizingHtmlResponse;

/**
 * @runTestsInSeparateProcesses
 */
class LoginCheckActionTest extends TestCase
{
  // It fixes issues like when AllConfig is not loaded while it should be
  protected $preserveGlobalState = FALSE;
  private const AJAX_USERS_ACTION_EXAMPLE_TEMPLATE = BASE_PATH . 'tests/examples/ajaxUsersAction.phtml';

  protected function setUp(): void
  {
    parent::setUp();
    $_SERVER[APP_ENV] = PROD;
    $_SERVER['REMOTE_ADDR'] = '::1';
  }

  public function testSuccess() : void
  {
    // context
    $_POST = [
      'otra-email' => 'contact@website.com',
      'otra-password' => 'test',
      'otra-remember' => true
    ];
    // We only access this route via AJAX
    $_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';
    $_SERVER['REQUEST_URI'] = Routes::$allRoutes['loginCheck']['chunks'][Routes::ROUTES_CHUNKS_URL];

    // testing
    ob_start();

    // launching
    Router::get(
      'loginCheck',
      []
    );

    // testing
    require BASE_PATH . 'tests/tools/normalizingHtmlResponse.php';
    $jsonResponse = json_decode(ob_get_clean(), true);
    ob_start();
    require CORE_PATH . '/tools/files/returnLegiblePath.php';

    require self::AJAX_USERS_ACTION_EXAMPLE_TEMPLATE;
    self::assertEquals(
      ob_get_clean(),
      normalizingHtmlResponse($jsonResponse['html']),
      'Comparing template with ' . returnLegiblePath2(self::AJAX_USERS_ACTION_EXAMPLE_TEMPLATE)
    );

    foreach ($jsonResponse['js'] as &$javaScriptInformation)
    {
      $javaScriptInformation = preg_replace('@[0-9a-f]{64}@', 'nonce', $javaScriptInformation);
    }

    self::assertEquals(
      [
        [
          'nonce' => 'nonce',
          'src' => '/bundles/resources/js/spaCall.js'
        ],
        [
          'nonce' => 'nonce',
          'src' => '/bundles/resources/js/menu.js'
        ],
        [
          'nonce' => 'nonce',
          'src' => '/bundles/OtraUser/backoffice/resources/js/userLogout.js'
        ]
      ],
      $jsonResponse['js'],
      'Checking JavaScript...'
    );

    self::assertEquals(
      true,
      $jsonResponse['success'],
      'Checking status...'
    );
  }
}
