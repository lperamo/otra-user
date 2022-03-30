<?php
declare(strict_types=1);
namespace otra\user\bundles\OtraUser\backoffice\controllers\index;

use bundles\config\Roles;
use otra\user\bundles\OtraUser\backoffice\services\UserService;
use otra\{config\Routes, Controller, OtraException, Router, Session};
use ReflectionException;
use const bundles\config\OTRA_USER_URL_PATH;

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
   * @throws \Exception
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

      $response = ['success' => true];

      // Do we come from the login page? (the yes is handled by the else block)
      if ($_SERVER['REQUEST_URI'] !== Router::getRouteUrl('loginCheck'))
      {
        self::css([
          self::CSS_MEDIA_SCREEN =>
            OTRA_USER_URL_PATH . 'bundles/OtraUser/backoffice/resources/css/pages/users/ajaxUsers'
        ]);
        $response = [
          ...$response,
          ...[
            'html' => $this->renderView(
              'partials/users.phtml',
              [
                'roles' => $userManagementData[UserService::ROLES],
                'users' => $userManagementData[UserService::USER_INFORMATION],
                'viewPath' => $this->viewPath
              ],
              true
            )
          ]
        ];
      } else
      {
        // Adding dynamic CSS and JavaScript files to the list of assets to add in the html output
        self::css([
          self::CSS_MEDIA_SCREEN =>
            [OTRA_USER_URL_PATH . 'bundles/OtraUser/backoffice/resources/css/pages/users/users']
        ]);
        self::js([
          OTRA_USER_URL_PATH . 'bundles/OtraUser/backoffice/resources/js/userLogout'
        ]);

        $response = [
          ...$response,
          ...[
//            'css' => self::getAjaxCSS(),
            'js' => self::getAjaxJS(),
            'html' => $this->renderView(
              'partials/usersBody.phtml',
              [
                'roles' => $userManagementData[UserService::ROLES],
                'users' => $userManagementData[UserService::USER_INFORMATION],
                'viewPath' => $this->viewPath
              ],
              true
            )
          ]
        ];
      }

      // If the actual url is the same that the used route, then we are using the JavaScript API History
//      if ($_SERVER['REQUEST_URI'] === Routes::$allRoutes[$this->route]['chunks'][Routes::ROUTES_CHUNKS_URL])
        $this->response = json_encode($response);
    }
    else
      echo (Router::get('notAjaxUsers', [], true, true))->response;
  }
}
