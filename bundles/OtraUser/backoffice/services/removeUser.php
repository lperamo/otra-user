<?php
declare(strict_types=1);
namespace bundles\OtraUser\backoffice\services;

use otra\bdd\Sql;
use otra\OtraException;
use otra\user\bundles\OtraUser\backoffice\services\UserService;

/**
 * @throws OtraException
 */
function removeUser(int $userId) : void
{
  $db = Sql::getDb();

  $query = 'DELETE FROM ' . UserService::TABLE_USER . ' WHERE id = :userId';

  $statement = $db->prepare($query);

  if (!$statement->execute(
    [':userId' => $userId]
  ))
    throw new OtraException('Cannot execute the query<br>' . $query);
}
