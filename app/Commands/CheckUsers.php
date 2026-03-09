<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckUsers extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'users:check';
    protected $description = 'Check and create admin/superadmin users';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        // Check users
        $query = $db->query("
            SELECT u.id, u.username, u.email, u.status, r.role_name 
            FROM users u 
            LEFT JOIN roles r ON r.id = u.role_id 
            WHERE u.username IN ('admin', 'superadmin')
        ");

        $users = $query->getResultArray();

        CLI::write('Current Users:', 'green');
        CLI::write(str_repeat("-", 80));
        
        foreach ($users as $user) {
            CLI::write("ID: {$user['id']}");
            CLI::write("Username: {$user['username']}");
            CLI::write("Email: {$user['email']}");
            CLI::write("Status: {$user['status']}");
            CLI::write("Role: " . ($user['role_name'] ?? 'No role assigned'));
            CLI::write(str_repeat("-", 80));
        }

        // Check if superadmin user exists
        $superadminExists = false;
        foreach ($users as $user) {
            if ($user['username'] === 'superadmin') {
                $superadminExists = true;
                break;
            }
        }

        if (!$superadminExists) {
            CLI::write("\nSuperadmin user does not exist. Creating...", 'yellow');
            
            // Get superadmin role ID
            $roleQuery = $db->query("SELECT id FROM roles WHERE role_name = 'superadmin'");
            $role = $roleQuery->getRow();
            
            if ($role) {
                // Create superadmin user
                $db->query("
                    INSERT INTO users (username, password_hash, email, status, created_at) 
                    VALUES ('superadmin', ?, 'superadmin@example.com', 'active', NOW())
                ", [password_hash('superadmin123', PASSWORD_DEFAULT)]);
                
                $userId = $db->insertID();
                
                // Assign role
                $db->query("
                    UPDATE users 
                    SET role_id = ? 
                    WHERE id = ?
                ", [$role->id, $userId]);
                
                CLI::write("Superadmin user created successfully!", 'green');
                CLI::write("Username: superadmin");
                CLI::write("Password: superadmin123");
            } else {
                CLI::error("Error: Superadmin role not found!");
            }
        }

        CLI::newLine();
        CLI::write("You can now login with:", 'green');
        CLI::write("Username: superadmin");
        CLI::write("Password: superadmin123");
        CLI::write("\nOR\n");
        CLI::write("Username: admin");
        CLI::write("Password: admin123");
    }
}
