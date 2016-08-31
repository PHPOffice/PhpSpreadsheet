<?php

require __DIR__ . '/Header.php';
$spreadsheet = require __DIR__ . '/templates/sampleSpreadsheet.php';

// Change these values to select the Rendering library that you wish to use
// and its directory location on your server
//$rendererName = \PhpOffice\PhpSpreadsheet\Settings::PDF_RENDERER_TCPDF;
//$rendererName = \PhpOffice\PhpSpreadsheet\Settings::PDF_RENDERER_MPDF;
$rendererName = \PhpOffice\PhpSpreadsheet\Settings::PDF_RENDERER_DOMPDF;
//$rendererLibrary = 'tcPDF5.9';
//$rendererLibrary = 'mPDF5.4';
$rendererLibrary = 'domPDF0.6.0beta3';
$rendererLibraryPath = '/php/libraries/PDF/' . $rendererLibrary;

$helper->log('Hide grid lines');
$spreadsheet->getActiveSheet()->setShowGridLines(false);

$helper->log('Set orientation to landscape');
$spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

$helper->log("Write to PDF format using {$rendererName}");

if (!\PhpOffice\PhpSpreadsheet\Settings::setPdfRenderer($rendererName, $rendererLibraryPath)) {
    $helper->log('NOTICE: Please set the $rendererName and $rendererLibraryPath values at the top of this script as appropriate for your directory structure');
}

// Save
$helper->write($spreadsheet, __FILE__, ['PDF' => 'pdf']);
