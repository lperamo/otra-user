<?php
declare(strict_types=1);
namespace otra\user\bundles\OtraUser\backoffice\controllers\index;

use bundles\config\Roles;
use otra\user\bundles\OtraUser\backoffice\services\UserService;
use otra\{config\Routes, Controller, OtraException, Router, Session};
use ReflectionException;

/**
 * OTRA User management page
 *
 * @package bundles\OtraUser\backoffice\controllers\index
 */
class UsersAction extends Controller
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

    // If it is an AJAX request (most common case as we must use an SPA)
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']))
    {
      $userInformation = UserService::getUserInformationIfConnected();

      if (!in_array(
        $userInformation['userRoleMask'],
        [Roles::ROLE_ADMIN->value, Roles::ROLE_MODERATOR->value])
      )
        throw new OtraException('Rights not sufficient.');

      $userManagementData = UserService::getUsersInformation();

      $this->response = $this->renderView(
        'partials/users.phtml',
        [
          'roles' => $userManagementData[UserService::ROLES],
          'users' => $userManagementData[UserService::USER_INFORMATION],
          'viewPath' => $this->viewPath
        ],
        true
      );

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
      echo (Router::get('notAjaxUsers', [], true, true))->response;
  }
}
