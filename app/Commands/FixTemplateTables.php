<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class FixTemplateTables extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:fix-template-tables';
    protected $description = 'Drop and recreate template tables';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        CLI::write('Fixing template tables...', 'yellow');
        CLI::newLine();

        try {
            // Drop tables if they exist
            CLI::write('Dropping existing tables...', 'cyan');
            $db->query("DROP TABLE IF EXISTS template_fields");
            CLI::write('✓ Dropped template_fields (if existed)', 'green');
            
            $db->query("DROP TABLE IF EXISTS document_templates");
            CLI::write('✓ Dropped document_templates (if existed)', 'green');
            CLI::newLine();

            // Create document_templates
            CLI::write('Creating document_templates table...', 'cyan');
            $db->query("
                CREATE TABLE `document_templates` (
                    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `document_type_id` INT(11) UNSIGNED NOT NULL,
                    `name` VARCHAR(255) NOT NULL,
                    `code` VARCHAR(50) NOT NULL UNIQUE,
                    `version` VARCHAR(20) DEFAULT '1.0',
                    `description` TEXT NULL,
                    `layout_template` VARCHAR(255) NULL COMMENT 'PDF template file name',
                    `is_active` TINYINT(1) DEFAULT 1,
                    `created_at` DATETIME NULL,
                    `updated_at` DATETIME NULL,
                    PRIMARY KEY (`id`),
                    CONSTRAINT `document_templates_document_type_id_fk` 
                        FOREIGN KEY (`document_type_id`) 
                        REFERENCES `document_types`(`id`) 
                        ON DELETE CASCADE 
                        ON UPDATE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            ");
            CLI::write('✓ Created document_templates table', 'green');
            CLI::newLine();

            // Create template_fields
            CLI::write('Creating template_fields table...', 'cyan');
            $db->query("
                CREATE TABLE `template_fields` (
                    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `template_id` INT(11) UNSIGNED NOT NULL,
                    `field_name` VARCHAR(100) NOT NULL,
                    `field_label` VARCHAR(255) NOT NULL,
                    `field_type` ENUM('text', 'textarea', 'number', 'date', 'select', 'checkbox', 'radio', 'file') NOT NULL,
                    `field_options` TEXT NULL COMMENT 'JSON array for select/radio/checkbox options',
                    `is_required` TINYINT(1) DEFAULT 0,
                    `default_value` TEXT NULL,
                    `placeholder` VARCHAR(255) NULL,
                    `validation_rules` VARCHAR(255) NULL,
                    `display_order` INT(11) DEFAULT 0,
                    `help_text` TEXT NULL,
                    `created_at` DATETIME NULL,
                    `updated_at` DATETIME NULL,
                    PRIMARY KEY (`id`),
                    CONSTRAINT `template_fields_template_id_fk` 
                        FOREIGN KEY (`template_id`) 
                        REFERENCES `document_templates`(`id`) 
                        ON DELETE CASCADE 
                        ON UPDATE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            ");
            CLI::write('✓ Created template_fields table', 'green');
            CLI::newLine();

            CLI::write('Template tables fixed successfully!', 'green');

        } catch (\Exception $e) {
            CLI::error('Error: ' . $e->getMessage());
        }
    }
}
