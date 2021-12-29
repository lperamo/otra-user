<?php
declare(strict_types=1);

namespace OtraUser\bundles\OtraUser\frontoffice\controllers\index;

use otra\{Controller, OtraException, Router, Session};
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
   */
  public function __construct(array $baseParams = [], array $getParams = [])
  {
    parent::__construct($baseParams, $getParams);
    Session::init();
    Session::clean();
    echo json_encode(
      [
        'success' => true,
        'html' => (Router::get('login', [], true, true))->response
      ]
    );
  }
}
