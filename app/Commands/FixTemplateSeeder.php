<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class FixTemplateSeeder extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'fix:template-seeder';
    protected $description = 'Fix field_order to display_order in TemplateSeeder';

    public function run(array $params)
    {
        CLI::write('Fixing TemplateSeeder...', 'yellow');
        
        $file = APPPATH . 'Database/Seeds/TemplateSeeder.php';
        
        if (!file_exists($file)) {
            CLI::error('TemplateSeeder.php not found!');
            return;
        }
        
        $content = file_get_contents($file);
        $originalContent = $content;
        
        // Replace 'field_order' with 'display_order'
        $content = str_replace("'field_order'", "'display_order'", $content);
        
        if ($content === $originalContent) {
            CLI::write('No changes needed', 'yellow');
            return;
        }
        
        file_put_contents($file, $content);
        
        CLI::write('✓ Fixed TemplateSeeder.php', 'green');
        CLI::write('  Replaced field_order with display_order', 'white');
    }
}
