<?php
declare(strict_types=1);
namespace otra\user\bundles\OtraUser\backoffice\controllers\index;

use bundles\config\Roles;
use otra\user\bundles\OtraUser\backoffice\services\UserService;
use otra\{Controller, OtraException};
use ReflectionException;
use const otra\cache\php\BUNDLES_PATH;
use function bundles\OtraUser\backoffice\services\removeUser;

/**
 * @package bundles\OtraUser\backoffice\controllers\index
 */
class RemoveUserAction extends Controller
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

    require BUNDLES_PATH . 'OtraUser/backoffice/services/removeUser.php';
    removeUser((int) filter_var($params['userId'], FILTER_SANITIZE_NUMBER_INT));

    echo json_encode(
      [
        'success' => true,
        'html' => '' // TODO Return an HTML list or nothing?
      ]
    );
  }
}
