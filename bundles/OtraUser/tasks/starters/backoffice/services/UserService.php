<?php
declare(strict_types=1);
namespace bundles\OtraUser\backoffice\services;

use otra\user\bundles\OtraUser\backoffice\services\UserService as UserServiceFromOtraUser;

class UserService extends UserServiceFromOtraUser
{
  public const
    TABLE_USER = 'ou_user',
    TABLE_ROLE = 'ou_role';
}
