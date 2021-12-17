<?php
declare(strict_types=1);
namespace bundles\OtraUser\backoffice\controllers\index;

use bundles\config\Roles;
use bundles\OtraUser\backoffice\services\UserService;
use otra\
{config\Routes, Controller, OtraException, Router, Session};
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

    // If the actual url is the same that the used route, then we are using the JavaScript API History
//    if ($_SERVER['REQUEST_URI'] === Routes::$allRoutes[$this->route]['chunks'][Routes::ROUTES_CHUNKS_URL])
      Session::init();

    $userInformation = Session::getArrayIfExists(['userId', 'userRoleMask']);

    // Not logged-in users must be redirected to the login page
    if ($userInformation === false)
    {
      header(
        'Location: ' .
        (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']
          ? 'https'
          : 'http'
        ) . '://' . $_SERVER['HTTP_HOST'] . Router::getRouteUrl('login')
      );
      throw new OtraException(code: 0, exit: true);
    }

    if (!in_array($userInformation['userRoleMask'], [Roles::ROLE_ADMIN->value, Roles::ROLE_MODERATOR->value]))
      throw new OtraException('Rights not sufficient.');

    $userManagementData = UserService::getUsersInformation();

    echo $this->renderView(
      'users.phtml',
      [
        'roles' => $userManagementData[UserService::ROLES],
        'users' => $userManagementData[UserService::USER_INFORMATION],
        'viewPath' => $this->viewPath
      ]
    );
  }
}
