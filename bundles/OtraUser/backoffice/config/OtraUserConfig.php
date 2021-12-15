<?php
declare(strict_types=1);
namespace bundles\OtraUser\backoffice\config;

enum Roles: int {
  case ROLE_ADMIN = 1;
  case ROLE_MODERATOR = 2;
}
