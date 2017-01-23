<?php

require __DIR__ . '/Header.php';
$spreadsheet = require __DIR__ . '/templates/sampleSpreadsheet.php';

$helper->log('Hide grid lines');
$spreadsheet->getActiveSheet()->setShowGridLines(false);

$helper->log('Set orientation to landscape');
$spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

$rendererName = \PhpOffice\PhpSpreadsheet\Settings::PDF_RENDERER_DOMPDF;
$helper->log("Write to PDF format using {$rendererName}");
\PhpOffice\PhpSpreadsheet\Settings::setPdfRendererName($rendererName);

// Save
$helper->write($spreadsheet, __FILE__, ['Pdf']);
