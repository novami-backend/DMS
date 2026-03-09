<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class GenerateCambriaFont extends BaseCommand
{
    protected $group       = 'Font';
    protected $name        = 'font:generate-cambria';
    protected $description = 'Generate TCPDF font definition files for Cambria TTF font';

    public function run(array $params)
    {
        $cambriaTTFPath = FCPATH . 'assets/fonts/cambria.ttf';
        $tcpdfFontsDir = FCPATH . '../vendor/tecnickcom/tcpdf/fonts/';

        CLI::write('Font path: ' . $cambriaTTFPath, 'yellow');
        CLI::write('Output dir: ' . $tcpdfFontsDir, 'yellow');
        CLI::write('File exists: ' . (file_exists($cambriaTTFPath) ? 'YES' : 'NO'), 'yellow');
        CLI::write('Dir writable: ' . (is_writable($tcpdfFontsDir) ? 'YES' : 'NO'), 'yellow');

        if (!file_exists($cambriaTTFPath)) {
            CLI::error('Cambria TTF font file not found at: ' . $cambriaTTFPath);
            return;
        }

        CLI::write('Generating Cambria font definition files from TTF...', 'green');

        try {
            // Use TCPDF_FONTS::addTTFfont to add the TTF font
            $fontName = \TCPDF_FONTS::addTTFfont(
                $cambriaTTFPath,           // Font file path (TTF)
                'TrueTypeUnicode',         // Font type
                '',                        // Encoding (empty for Unicode)
                32,                        // Flags (32 = non-symbolic)
                $tcpdfFontsDir,            // Output path for generated files
                3,                         // Platform ID (3 = Windows)
                1,                         // Encoding ID (1 = Unicode)
                false,                     // Add character bounding box
                false                      // Link to system font
            );

            CLI::write('Font name result: ' . var_export($fontName, true), 'yellow');

            if ($fontName && $fontName !== false) {
                CLI::write('✓ Cambria font successfully generated as: ' . $fontName, 'green');
                
                // List generated files
                $files = glob($tcpdfFontsDir . $fontName . '*');
                if (!empty($files)) {
                    CLI::write('Generated files:', 'cyan');
                    foreach ($files as $file) {
                        CLI::write('  - ' . basename($file), 'white');
                    }
                }
            } else {
                CLI::error('Failed to generate Cambria font - returned: ' . var_export($fontName, true));
            }
        } catch (\Throwable $e) {
            CLI::error('Error: ' . $e->getMessage());
            CLI::error('Trace: ' . $e->getTraceAsString());
        }
    }
}
