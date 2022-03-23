<?php
declare(strict_types=1);
namespace bundles\OtraUser\frontoffice\services;

use otra\user\bundles\OtraUser\frontoffice\services\UserService as UserServiceFromOtraUser;

class UserService extends UserServiceFromOtraUser
{
  public const
    TABLE_USER = 'ou_user',
    TABLE_ROLE = 'ou_role';
}
