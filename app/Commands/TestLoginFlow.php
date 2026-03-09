<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestLoginFlow extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:login-flow';
    protected $description = 'Test the complete login flow';

    public function run(array $params)
    {
        CLI::write('Testing Login Flow', 'yellow');
        CLI::write(str_repeat("=", 80));
        CLI::newLine();

        // Test 1: Check routes
        CLI::write('1. Checking Routes Configuration...', 'cyan');
        $routes = \Config\Services::routes();
        $routes->loadRoutes();
        
        $loginRoutes = $routes->getRoutes();
        $hasLogin = isset($loginRoutes['login']);
        $hasDashboard = isset($loginRoutes['dashboard']);
        
        if ($hasLogin) {
            CLI::write('   ✓ Login route exists', 'green');
        } else {
            CLI::error('   ✗ Login route missing');
        }
        
        if ($hasDashboard) {
            CLI::write('   ✓ Dashboard route exists', 'green');
        } else {
            CLI::error('   ✗ Dashboard route missing');
        }
        CLI::newLine();

        // Test 2: Check Auth controller
        CLI::write('2. Checking Auth Controller...', 'cyan');
        if (class_exists('App\Controllers\Auth')) {
            CLI::write('   ✓ Auth controller exists', 'green');
            
            $authController = new \App\Controllers\Auth();
            if (method_exists($authController, 'login')) {
                CLI::write('   ✓ login() method exists', 'green');
            }
            if (method_exists($authController, 'dashboard')) {
                CLI::write('   ✓ dashboard() method exists', 'green');
            }
        } else {
            CLI::error('   ✗ Auth controller not found');
        }
        CLI::newLine();

        // Test 3: Check users
        CLI::write('3. Checking Users...', 'cyan');
        $db = \Config\Database::connect();
        
        $superadmin = $db->query("
            SELECT u.*, r.role_name 
            FROM users u 
            JOIN roles r ON r.id = u.role_id 
            WHERE u.username = 'superadmin'
        ")->getRowArray();
        
        if ($superadmin) {
            CLI::write('   ✓ Superadmin user exists', 'green');
            CLI::write('     - Status: ' . $superadmin['status'], 'white');
            CLI::write('     - Role: ' . $superadmin['role_name'], 'white');
            
            if (password_verify('superadmin123', $superadmin['password_hash'])) {
                CLI::write('   ✓ Superadmin password is correct', 'green');
            } else {
                CLI::error('   ✗ Superadmin password verification failed');
            }
        } else {
            CLI::error('   ✗ Superadmin user not found');
        }
        
        $admin = $db->query("
            SELECT u.*, r.role_name 
            FROM users u 
            JOIN roles r ON r.id = u.role_id 
            WHERE u.username = 'admin'
        ")->getRowArray();
        
        if ($admin) {
            CLI::write('   ✓ Admin user exists', 'green');
            CLI::write('     - Status: ' . $admin['status'], 'white');
            CLI::write('     - Role: ' . $admin['role_name'], 'white');
            
            if (password_verify('admin123', $admin['password_hash'])) {
                CLI::write('   ✓ Admin password is correct', 'green');
            } else {
                CLI::error('   ✗ Admin password verification failed');
            }
        } else {
            CLI::error('   ✗ Admin user not found');
        }
        CLI::newLine();

        // Test 4: Check views
        CLI::write('4. Checking Views...', 'cyan');
        if (file_exists(APPPATH . 'Views/auth/login.php')) {
            CLI::write('   ✓ Login view exists', 'green');
        } else {
            CLI::error('   ✗ Login view not found');
        }
        
        if (file_exists(APPPATH . 'Views/dashboard/index.php')) {
            CLI::write('   ✓ Dashboard view exists', 'green');
        } else {
            CLI::error('   ✗ Dashboard view not found');
        }
        CLI::newLine();

        // Test 5: Check session directory
        CLI::write('5. Checking Session Configuration...', 'cyan');
        $sessionPath = WRITEPATH . 'session';
        if (is_dir($sessionPath)) {
            CLI::write('   ✓ Session directory exists', 'green');
            if (is_writable($sessionPath)) {
                CLI::write('   ✓ Session directory is writable', 'green');
            } else {
                CLI::error('   ✗ Session directory is not writable');
            }
        } else {
            CLI::error('   ✗ Session directory not found');
        }
        CLI::newLine();

        CLI::write(str_repeat("=", 80));
        CLI::write('Test Complete!', 'green');
        CLI::newLine();
        CLI::write('Login Credentials:', 'yellow');
        CLI::write('  URL: ' . base_url('login'), 'white');
        CLI::write('  Superadmin: superadmin / superadmin123', 'white');
        CLI::write('  Admin: admin / admin123', 'white');
        CLI::newLine();
        CLI::write('After login, you should be redirected to: ' . base_url('dashboard'), 'yellow');
    }
}
