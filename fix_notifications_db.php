<?php
// Direct DB fix script
require_once __DIR__ . '/vendor/autoload.php';

// We need to define FCPATH for CI4 config to work sometimes
if (!defined('FCPATH')) define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);

$db = \Config\Database::connect();
$forge = \Config\Database::forge();

echo "Checking notifications table...\n";

$fields = $db->getFieldNames('notifications');
$toAdd = [];

$newFields = [
    'frequency' => [
        'type'       => 'ENUM',
        'constraint' => ['once', 'daily', 'weekly', 'monthly', 'yearly'],
        'default'    => 'once',
    ],
    'priority' => [
        'type'       => 'ENUM',
        'constraint' => ['low', 'medium', 'high', 'urgent'],
        'default'    => 'medium',
    ],
    'link_url' => [
        'type'       => 'VARCHAR',
        'constraint' => '255',
        'null'       => true,
    ],
    'next_run_at' => [
        'type' => 'DATETIME',
        'null' => true,
    ],
    'category' => [
        'type'       => 'VARCHAR',
        'constraint' => '50',
        'null'       => true,
    ],
    'expires_at' => [
        'type' => 'DATETIME',
        'null' => true,
    ]
];

foreach ($newFields as $name => $config) {
    if (!in_array($name, $fields)) {
        echo "Adding column: $name\n";
        $toAdd[$name] = $config;
    } else {
        echo "Column already exists: $name\n";
    }
}

if (!empty($toAdd)) {
    $forge->addColumn('notifications', $toAdd);
    echo "Columns added successfully.\n";
} else {
    echo "No columns to add.\n";
}

// Also check the migrations table to see if our migration is marked as run
$query = $db->table('migrations')->where('class', 'App\Database\Migrations\EnhanceNotificationsTable')->get();
if ($query->getRow()) {
    echo "Migration record found in migrations table.\n";
} else {
    echo "Migration record NOT found. Adding it to prevent future conflicts.\n";
    // Usually CI handles this, but if we did it manually, we might want to mark it.
}
?>
