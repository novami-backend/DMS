<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ResetPassword extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'users:reset-password';
    protected $description = 'Reset password for admin and superadmin users';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        // Reset superadmin password
        $superadminHash = password_hash('superadmin123', PASSWORD_DEFAULT);
        $db->query("UPDATE users SET password_hash = ? WHERE username = 'superadmin'", [$superadminHash]);
        CLI::write("Superadmin password reset to: superadmin123", 'green');

        // Reset admin password
        $adminHash = password_hash('admin123', PASSWORD_DEFAULT);
        $db->query("UPDATE users SET password_hash = ? WHERE username = 'admin'", [$adminHash]);
        CLI::write("Admin password reset to: admin123", 'green');

        CLI::newLine();
        CLI::write("Login credentials:", 'yellow');
        CLI::write("Superadmin - Username: superadmin, Password: superadmin123");
        CLI::write("Admin - Username: admin, Password: admin123");
    }
}
