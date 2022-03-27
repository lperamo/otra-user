<?php
declare(strict_types=1);
namespace bundles\OtraUser\backoffice\services;

use otra\bdd\Sql;
use otra\OtraException;
use otra\user\bundles\OtraUser\backoffice\services\UserService;

/**
 * @param int    $id
 * @param int    $fkIdRole
 * @param string $mail
 * @param string $pwd
 * @param string $pseudo
 * @param string $firstName
 * @param string $lastName
 * @param string $token
 *
 * @throws OtraException
 * @return void
 */
function editUser(
  int $id,
  int $fkIdRole,
  string $mail,
  string $pwd,
  string $pseudo,
  string $firstName,
  string $lastName,
  string $token
) : void
{
  $db = Sql::getDb();

  $query = 'UPDATE ' . UserService::TABLE_USER .
    ' SET fk_id_role=:fkIdRole, mail=:mail, pwd=:pwd, pseudo=:pseudo, first_name=:firstName, last_name=:lastName, token=:token
    WHERE id=:id';

  $statement = $db->prepare($query);

  if (!$statement->execute(
    [
      ':id' => $id,
      ':fkIdRole' => $fkIdRole,
      ':mail' => $mail,
      ':pwd' => $pwd,
      ':pseudo' => $pseudo,
      ':firstName' => $firstName,
      ':lastName' => $lastName,
      ':token' => $token
    ]
  ))
    throw new OtraException('Cannot execute the query<br>' . $query);
}
