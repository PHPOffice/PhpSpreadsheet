<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;

require __DIR__ . '/../Header.php';

// Read from Xls (.xls) template
$helper->log('Load Xlsx template file');
$reader = IOFactory::createReader('Xls');
$initialSpreadsheet = $reader->load(__DIR__ . '/../templates/27template.xls');

$xlsxFile = File::temporaryFilename();
$writer = new XlsxWriter($initialSpreadsheet);
$helper->log('Save as Xlsx');
$writer->save($xlsxFile);
$initialSpreadsheet->disconnectWorksheets();
$reader2 = new XlsxReader();
$helper->log('Load Xlsx');
$spreadsheet = $reader2->load($xlsxFile);

$helper->log('Hide grid lines');
$spreadsheet->getActiveSheet()->setShowGridLines(false);

$helper->log('Set orientation to landscape');
$spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

$className = Mpdf::class;
$helper->log("Write to PDF format using {$className}, and to Html");
IOFactory::registerWriter('Pdf', $className);

// Save
$helper->write($spreadsheet, __FILE__, ['Pdf', 'Html']);
unlink($xlsxFile);
$spreadsheet->disconnectWorksheets();
