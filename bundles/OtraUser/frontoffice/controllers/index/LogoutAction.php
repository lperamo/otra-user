<?php
declare(strict_types=1);

namespace otra\user\bundles\OtraUser\frontoffice\controllers\index;

use otra\{Controller, OtraException, Router, Session};
use Exception;
use ReflectionException;

/**
 * @package OtraUser\bundles\OtraUser\frontoffice\controllers\index
 */
class LogoutAction extends Controller
{
  /**
   * @param array $baseParams
   * @param array $getParams
   *
   * @throws OtraException|ReflectionException
   * @throws Exception
   */
  public function __construct(array $baseParams = [], array $getParams = [])
  {
    parent::__construct($baseParams, $getParams);
    Session::init();
    Session::clean();
    unset($_SESSION['sid']); // Informs OTRA that no user is connected

    // Redirects to the `login` route
    Router::get('login', [], true, true);
  }
}
