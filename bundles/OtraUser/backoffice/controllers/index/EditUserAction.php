<?php
declare(strict_types=1);
namespace otra\user\bundles\OtraUser\backoffice\controllers\index;

use bundles\config\Roles;
use otra\user\bundles\OtraUser\backoffice\services\UserService;
use otra\{Controller, OtraException};
use ReflectionException;
use const otra\cache\php\BUNDLES_PATH;
use function bundles\OtraUser\backoffice\services\editUser;

/**
 * @package bundles\OtraUser\backoffice\controllers\index
 */
class EditUserAction extends Controller
{
  /**
   * @param array $otraParams
   * @param array $params
   *
   * @throws OtraException|ReflectionException
   */
  public function __construct(array $otraParams = [], array $params = [])
  {
    parent::__construct($otraParams, $params);

    $userInformation = UserService::getUserInformationIfConnected();

    if ($userInformation['userRoleMask'] != Roles::ROLE_ADMIN->value)
      throw new OtraException('Rights not sufficient.');

    if (!($mail = filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)))
    {
      echo json_encode(
        [
          'success' => false,
          'message' => 'Wrong email syntax'
        ]
      );

      return;
    }

    require BUNDLES_PATH . 'OtraUser/backoffice/services/editUser.php';
    editUser(
      (int) filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT),
      (int) filter_var($_POST['fkIdRole'], FILTER_SANITIZE_NUMBER_INT),
      $mail,
      $_POST['pwd'],
      $_POST['pseudo'],
      $_POST['firstName'],
      $_POST['lastName'],
      $_POST['token']
    );

    echo json_encode(
      [
        'success' => true,
        'html' => '' // TODO Return an HTML list or nothing?
      ]
    );
  }
}
