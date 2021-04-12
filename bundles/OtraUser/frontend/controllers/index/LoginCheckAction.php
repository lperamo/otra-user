<?php
declare(strict_types=1);

namespace bundles\OtraUser\frontend\controllers\index;

use otra\Controller;

/**
 * OTRA login action
 *
 * @package bundles\OtraUser\frontend\controllers\index
 */
class LoginCheckAction extends Controller
{
  /**
   * HomeAction constructor.
   *
   * @param array $baseParams
   * @param array $getParams
   *
   * @throws \otra\OtraException
   */
  public function __construct(array $baseParams = [], array $getParams = [])
  {
    parent::__construct($baseParams, $getParams);
    echo '{success:true}';
  }
}

