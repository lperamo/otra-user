<?php
declare(strict_types=1);
namespace bundles\OtraUser\backoffice\controllers\index;

use bundles\OtraUser\backoffice\{config\Roles, services\UserService};
use otra\{Controller, OtraException, Router, Session};
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
      $userInformation = Session::getArrayIfExists(['userId', 'userRoleMask']);

      // Not logged-in users must be redirected to the login page
      if ($userInformation === false)
      {
        header('Location: ' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . Router::getRouteUrl('login'));
        throw new OtraException(code: 0, exit: true);
      }

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
          'users' => $userManagementData[UserService::USER_INFORMATION]
        ],
        true
      );
    }
    else
      Router::get('notAjaxUsers', [], true, true);
  }
}
