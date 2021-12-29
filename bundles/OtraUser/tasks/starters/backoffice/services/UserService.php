<?php
declare(strict_types=1);
namespace bundles\OtraUser\backoffice\services;

use OtraUser\bundles\OtraUser\backoffice\services\UserService as UserServiceFromOtraUser;

class UserService extends UserServiceFromOtraUser
{
  public const
    TABLE_USER = 'user',
    TABLE_ROLE = 'role';
}
