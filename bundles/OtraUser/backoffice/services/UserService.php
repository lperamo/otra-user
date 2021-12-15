<?php
declare(strict_types=1);
namespace bundles\OtraUser\backoffice\services;

use otra\bdd\Sql;
use otra\OtraException;

class UserService
{
  public const
    USER_INFORMATION = 0,
    ROLES = 1;

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
      FROM user u
      JOIN role r on r.id = u.fk_id_role
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
      FROM user u
      JOIN role r on r.id = u.fk_id_role';

    $statement = $db->prepare($query);

    if (!$statement->execute())
      throw new OtraException('Cannot execute the query<br>' . $query);

    $result = [$db->fetchAllAssoc($statement)];
    $db->freeResult($statement);

    $query = 'SELECT r.id, r.name FROM role r';
    $statement = $db->prepare($query);

    if (!$statement->execute())
      throw new OtraException('Cannot execute the query<br>' . $query);

    $result[] = $db->fetchAllByPair($statement);
    $db->freeResult($statement);

    return $result;
  }
}
