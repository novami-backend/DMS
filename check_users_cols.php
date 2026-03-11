<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
define('ENVIRONMENT', 'development');
require __DIR__ . '/app/Config/Paths.php';
$paths = new Config\Paths();
require_once $paths->systemDirectory . '/Config/DotEnv.php';
(new \CodeIgniter\Config\DotEnv($paths->appDirectory . '/../'))->load();
require_once SYSTEMPATH . 'Common.php';
require_once SYSTEMPATH . 'Config/AutoloadConfig.php';
require_once APPPATH . 'Config/Autoload.php';
require_once SYSTEMPATH . 'Modules/Modules.php';
require_once APPPATH . 'Config/Modules.php';
require_once SYSTEMPATH . 'Autoloader/Autoloader.php';
require_once SYSTEMPATH . 'Config/BaseService.php';
require_once SYSTEMPATH . 'Config/Services.php';
require_once APPPATH . 'Config/Services.php';
\Config\Services::autoloader()->initialize(new \Config\Autoload(), new \Config\Modules())->register();

$db = \Config\Database::connect();
$query = $db->query("SHOW COLUMNS FROM users");
$columns = $query->getResultArray();
foreach ($columns as $column) {
    echo $column['Field'] . "\n";
}
