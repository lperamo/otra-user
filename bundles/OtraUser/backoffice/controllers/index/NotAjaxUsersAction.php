<?php
declare(strict_types=1);
namespace otra\user\bundles\OtraUser\backoffice\controllers\index;

use bundles\config\Roles;
use otra\user\bundles\OtraUser\backoffice\services\UserService;
use otra\{Controller, OtraException};
use ReflectionException;

/**
 * OTRA User management page
 *
 * @package bundles\OtraUser\backoffice\controllers\index
 */
class NotAjaxUsersAction extends Controller
{
  public string $response;

  /**
   * @param array $baseParams
   * @param array $getParams
   *
   * @throws OtraException|ReflectionException
   */
  public function __construct(array $baseParams = [], array $getParams = [])
  {
    parent::__construct($baseParams, $getParams);
    $userInformation = UserService::getUserInformationIfConnected();

    if (!in_array(
      $userInformation['userRoleMask'],
      [Roles::ROLE_ADMIN->value, Roles::ROLE_MODERATOR->value]
    ))
      throw new OtraException('Rights not sufficient.');

    $userManagementData = UserService::getUsersInformation();

    $this->response = $this->renderView(
      'users.phtml',
      [
        'roles' => $userManagementData[UserService::ROLES],
        'users' => $userManagementData[UserService::USER_INFORMATION],
        'viewPath' => $this->viewPath,
        'route' => $this->route
      ]
    );
  }
}
