<?php
declare(strict_types=1);

namespace otra\user\bundles\OtraUser\frontoffice\controllers\index;

use otra\{config\Routes, Controller, OtraException, Router};

/**
 * @package OtraUser\bundles\OtraUser\frontoffice\controllers\index
 */
class LoginFormAction extends Controller
{
  public string $response;

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
    {
      $this->response = $this->renderView('login.phtml', [], true);

      // If the actual url is the same that the used route, then we are using the JavaScript API History
      if ($_SERVER['REQUEST_URI'] === Routes::$allRoutes[$this->route]['chunks'][Routes::ROUTES_CHUNKS_URL])
        echo json_encode(
          [
            'success' => true,
            'html' => $this->response
          ]
        );
    }
    else
    {
      $this->response = Router::get('notAjaxLogin', [], true, true)->response;
    }
  }
}
