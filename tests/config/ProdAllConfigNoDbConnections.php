<?php
declare(strict_types=1);

/** THE framework production config
 *
 * @author Lionel Péramo
 */

namespace otra\config;

/**
 * @package config
 */
abstract class AllConfig
{
  public static string $defaultConn = ''; // mandatory in order to modify it later if needed
}
