<?php
declare(strict_types=1);

namespace bundles\OtraUser\frontoffice\controllers\index;

use otra\{Controller, OtraException, Router};

/**
 * @package bundles\OtraUser\frontoffice\controllers\index
 */
class LoginFormAction extends Controller
{
  /**
   * @param array $baseParams
   * @param array $getParams
   *
   * @throws OtraException
   */
  public function __construct(array $baseParams = [], array $getParams = [])
  {
    parent::__construct($baseParams, $getParams);

    // If it is an AJAX request (most common case as we must use an SPA)
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']))
      $this->response = $this->renderView('login.phtml', [], true);
    else
      Router::get('notAjaxLogin', [], true, true);
  }
}
