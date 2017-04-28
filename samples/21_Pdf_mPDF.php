<?php

use PhpOffice\PhpSpreadsheet\Settings;

require __DIR__ . '/Header.php';
$spreadsheet = require __DIR__ . '/templates/sampleSpreadsheet.php';

$helper->log('Hide grid lines');
$spreadsheet->getActiveSheet()->setShowGridLines(false);

$helper->log('Set orientation to landscape');
$spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

$rendererName = Settings::PDF_RENDERER_MPDF;
$helper->log("Write to PDF format using {$rendererName}");
Settings::setPdfRendererName($rendererName);

// Save
$helper->write($spreadsheet, __FILE__, ['Pdf']);
