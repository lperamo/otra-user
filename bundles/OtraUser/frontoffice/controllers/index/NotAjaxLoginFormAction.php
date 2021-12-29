<?php
declare(strict_types=1);

namespace OtraUser\bundles\OtraUser\frontoffice\controllers\index;

use otra\{Controller, OtraException};

/**
 * @package OtraUser\bundles\OtraUser\frontoffice\controllers\index
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
    echo $this->renderView('login.phtml');
  }
}
