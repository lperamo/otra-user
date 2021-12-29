<?php
declare(strict_types=1);

namespace OtraUser\bundles\OtraUser\frontoffice\controllers\index;

use bundles\OtraUser\frontoffice\services\UserService;
use otra\Controller;
use otra\OtraException;
use otra\Router;
use otra\Session;
use ReflectionException;

/**
 * OTRA login action
 *
 * @package OtraUser\bundles\OtraUser\frontoffice\controllers\index
 */
class LoginCheckAction extends Controller
{
  /**
   * HomeAction constructor.
   *
   * @param array $baseParams
   * @param array $getParams
   *
   * @throws OtraException|ReflectionException
   */
  public function __construct(array $baseParams = [], array $getParams = [])
  {
    parent::__construct($baseParams, $getParams);

    // Get `POST` parameters
    $email = $_POST['otra-email'];
    $password = $_POST['otra-password'];
    $remember = $_POST['otra-remember'];

    // Validating the parameters
    if (filter_var($remember, FILTER_VALIDATE_INT) === false)
      throw new OtraException('Hack!');

    $userInformation = UserService::getUserIdAndRoleMask($email, $password);

    if ($userInformation !== false)
    {
      Session::init();
      Session::sets(
        [
          'userId' => $userInformation['id'],
          'userRoleMask' => $userInformation['mask'],
          'timeout' => 300 // 5 minutes. If we do something, we have to reset this to 300
        ]
      );
      Session::toFile();
      echo json_encode([
        'success' => true,
        'html' => (Router::get('users', $userInformation, true, true))->response
      ]);
    } else
    {
      echo json_encode([
        'success' => false,
        'message' => 'Wrong credentials'
      ]);
    }
  }
}
