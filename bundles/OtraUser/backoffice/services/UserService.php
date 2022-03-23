<?php
declare(strict_types=1);
namespace otra\user\bundles\OtraUser\backoffice\services;

use otra\bdd\Sql;
use otra\{OtraException, Router, Session};
use ReflectionException;

class UserService
{
  public const
    USER_INFORMATION = 0,
    ROLES = 1,
    TABLE_USER = 'user',
    TABLE_ROLE = 'role';

  /**
   * Gets user fields
   *
   * @param int $userId
   *
   * @throws OtraException
   * @return array
   */
  public static function getUserInformation(int $userId): array
  {
    $db = Sql::getDb();
    $query = 'SELECT u.id, u.pseudo, u.first_name, u.last_name, u.mail, u.token, r.mask
      FROM `' . static::TABLE_USER . '` u
      JOIN `' . static::TABLE_ROLE . '` r on r.id = u.fk_id_role
      WHERE u.id = :userId';

    $statement = $db->prepare($query);

    if (!$statement->execute(
      [':userId' => $userId]
    ))
      throw new OtraException('Cannot execute the query<br>' . $query);

    $result = $db->fetchAssoc($statement);
    $db->freeResult($statement);

    return $result;
  }

  /**
   * Gets users fields and all the types of role
   *
   * @throws OtraException
   * @return array
   */
  public static function getUsersInformation(): array
  {
    $db = Sql::getDb();
    $query = 'SELECT u.id, u.pseudo, u.first_name, u.last_name, u.mail, u.token, r.mask
      FROM `' . static::TABLE_USER . '` u
      JOIN `' . static::TABLE_ROLE . '` r on r.id = u.fk_id_role';

    $statement = $db->prepare($query);

    if (!$statement->execute())
      throw new OtraException('Cannot execute the query<br>' . $query);

    $result = [$db->fetchAllAssoc($statement)];
    $db->freeResult($statement);

    $query = 'SELECT r.id, r.name FROM `' . static::TABLE_ROLE . '` r';
    $statement = $db->prepare($query);

    if (!$statement->execute())
      throw new OtraException('Cannot execute the query<br>' . $query);

    $result[] = $db->fetchAllByPair($statement);
    $db->freeResult($statement);

    return $result;
  }

  /**
   * @param bool $sessionInit
   *
   * @throws OtraException
   * @throws ReflectionException
   * @return bool|array
   */
  public static function getUserInformationIfConnected(bool $sessionInit = true): bool|array
  {
    if ($sessionInit)
      Session::init(7);

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

    $_SESSION['sid'] = true; // Informs OTRA that a user is connected

    return $userInformation;
  }
}
