<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
require_once __DIR__ . '/vendor/autoload.php';
$config = new \Config\Database();
$db = \Config\Database::connect();
$query = $db->query("DESCRIBE notifications");
$result = $query->getResult();
file_put_contents('db_dump.json', json_encode($result, JSON_PRETTY_PRINT));
print_r($result);
