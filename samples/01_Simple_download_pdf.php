<?php

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require_once __DIR__ . '/../src/Bootstrap.php';

$helper = new Sample();
if ($helper->isCli()) {
    echo 'This example should only be run from a Web Browser' . PHP_EOL;

    return;
}

//	Change these values to select the Rendering library that you wish to use
//		and its directory location on your server
//$rendererName = \PhpOffice\PhpSpreadsheet\Settings::PDF_RENDERER_TCPDF;
$rendererName = Settings::PDF_RENDERER_MPDF;
//$rendererName = \PhpOffice\PhpSpreadsheet\Settings::PDF_RENDERER_DOMPDF;

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();

// Set document properties
$spreadsheet->getProperties()->setCreator('Maarten Balliauw')
        ->setLastModifiedBy('Maarten Balliauw')
        ->setTitle('PDF Test Document')
        ->setSubject('PDF Test Document')
        ->setDescription('Test document for PDF, generated using PHP classes.')
        ->setKeywords('pdf php')
        ->setCategory('Test result file');

// Add some data
$spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', 'Hello')
        ->setCellValue('B2', 'world!')
        ->setCellValue('C1', 'Hello')
        ->setCellValue('D2', 'world!');

// Miscellaneous glyphs, UTF-8
$spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A4', 'Miscellaneous glyphs')
        ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');

// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Simple');
$spreadsheet->getActiveSheet()->setShowGridLines(false);

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

Settings::setPdfRendererName($rendererName);

// Redirect output to a client’s web browser (PDF)
header('Content-Type: application/pdf');
header('Content-Disposition: attachment;filename="01simple.pdf"');
header('Cache-Control: max-age=0');

$writer = IOFactory::createWriter($spreadsheet, 'Pdf');
$writer->save('php://output');
exit;
