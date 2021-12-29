<?php
declare(strict_types=1);
namespace OtraUser\bundles\OtraUser\frontoffice\services;

use otra\bdd\Sql;
use otra\OtraException;

class UserService
{
  const
    TABLE_USER = 'user',
    TABLE_ROLE = 'role';

  /**
   * @throws OtraException
   */
  public static function getUserIdAndRoleMask(string $login, string $password): array|bool|null
  {
    $db = Sql::getDb();
    $query = 'SELECT u.id, u.pwd, r.mask
      FROM `' . static::TABLE_USER . '` u
      JOIN `' . static::TABLE_ROLE . '` r on r.id = u.fk_id_role
      WHERE u.pseudo = :login
        OR u.mail = :login';

    $statement = $db->prepare($query);
    if (!$statement->execute([
      ':login' => $login
    ]))
      throw new OtraException('Cannot bind parameters to the query<br>' . $query);

    if ($statement->rowCount() === 0)
      return false;

    $result = $db->fetchAssoc($statement);
    $db->freeResult($statement);

    // Following best practices of the article
    // http://web.archive.org/web/20211214135752/https://paragonie.com/blog/2015/04/secure-authentication-php-with-long-term-persistence
    return (password_verify(
      base64_encode(
        hash('sha256', $password)
      ),
      $result['pwd']
    ))
      ? $result
      : false;
  }
}
