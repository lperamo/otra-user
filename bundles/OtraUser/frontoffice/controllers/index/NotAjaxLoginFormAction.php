<?php
declare(strict_types=1);

namespace otra\user\bundles\OtraUser\frontoffice\controllers\index;

use otra\{Controller, OtraException};

/**
 * @package otra\user\bundles\OtraUser\frontoffice\controllers\index
 */
class NotAjaxLoginFormAction extends Controller
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
    $this->response = $this->renderView('login.phtml');

    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']))
      return $this->response;
    else
      echo $this->response;
  }
}
