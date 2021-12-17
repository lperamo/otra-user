<?php
declare(strict_types=1);

namespace otra\config;

use const otra\cache\php\{BASE_PATH, CORE_PATH};

return [
  'config\\AllConfig' => BASE_PATH . 'config/AllConfig.php',
  'otra\\Controller' => CORE_PATH . 'Controller.php'
];
