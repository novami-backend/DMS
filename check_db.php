<?php
require 'vendor/autoload.php';
$db = \Config\Database::connect();
$query = $db->query("DESCRIBE notifications");
print_r($query->getResult());
