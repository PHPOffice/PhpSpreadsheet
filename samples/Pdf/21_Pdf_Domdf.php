<?php

use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

require __DIR__ . '/../Header.php';
$spreadsheet = require __DIR__ . '/../templates/sampleSpreadsheet.php';

$helper->log('Hide grid lines');
$spreadsheet->getActiveSheet()->setShowGridLines(false);

$helper->log('Set orientation to landscape');
$spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

$rendererName = \PhpOffice\PhpSpreadsheet\Writer\Pdf\DomPDF::class;
$helper->log("Write to PDF format using {$rendererName}");
Settings::setDefaultPdfWriter($rendererName);

// Save
$helper->write($spreadsheet, __FILE__, ['Pdf']);
