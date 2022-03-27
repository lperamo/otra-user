<?php
declare(strict_types=1);
namespace bundles\OtraUser\backoffice\services;

use otra\bdd\Sql;
use otra\OtraException;
use otra\user\bundles\OtraUser\backoffice\services\UserService;

/**
 * @param int    $fkIdRole
 * @param string $mail
 * @param string $pwd
 * @param string $pseudo
 * @param string $firstName
 * @param string $lastName
 *
 * @throws OtraException
 * @return void
 */
function addUser(
  int $fkIdRole,
  string $mail,
  string $pwd,
  string $pseudo,
  string $firstName,
  string $lastName
) : void
{
  $db = Sql::getDb();

  $query = 'INSERT INTO ' . UserService::TABLE_USER .
    ' (`id`, `fk_id_role`, `mail`, `pwd`, `pseudo`, `first_name`, `last_name`, `token`) VALUES (NULL, :fkIdRole, :mail, :pwd, :pseudo, :firstName, :lastName, NULL)';

  $statement = $db->prepare($query);

  if (!$statement->execute(
    [
      ':fkIdRole' => $fkIdRole,
      ':mail' => $mail,
      ':pwd' => $pwd,
      ':pseudo' => $pseudo,
      ':firstName' => $firstName,
      ':lastName' => $lastName
    ]
  ))
    throw new OtraException('Cannot execute the query<br>' . $query);
}
