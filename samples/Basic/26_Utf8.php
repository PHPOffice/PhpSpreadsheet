<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

require __DIR__ . '/../Header.php';

// Read from Xlsx (.xlsx) template
$helper->log('Load Xlsx template file');
$reader = IOFactory::createReader('Xlsx');
$spreadsheet = $reader->load(__DIR__ . '/../templates/26template.xlsx');

// at this point, we could do some manipulations with the template, but we skip this step
$helper->write($spreadsheet, __FILE__, ['Xlsx', 'Xls', 'Html']);

// Export to PDF (.pdf)
$helper->log('Write to PDF format');
IOFactory::registerWriter('Pdf', \PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf::class);
$helper->write($spreadsheet, __FILE__, ['Pdf']);

// Remove first two rows with field headers before exporting to CSV
$helper->log('Removing first two heading rows for CSV export');
$worksheet = $spreadsheet->getActiveSheet();
$worksheet->removeRow(1, 2);

// Export to CSV (.csv)
$helper->log('Write to CSV format');
/** @var \PhpOffice\PhpSpreadsheet\Writer\Csv $writer */
$writer = IOFactory::createWriter($spreadsheet, 'Csv');
$filename = $helper->getFilename(__FILE__, 'csv');
$callStartTime = microtime(true);
$writer->save($filename);
$helper->logWrite($writer, $filename, $callStartTime);

// Export to CSV with BOM (.csv)
$filename = str_replace('.csv', '-bom.csv', $filename);
$helper->log('Write to CSV format (with BOM)');
$writer->setUseBOM(true);
$callStartTime = microtime(true);
$writer->save($filename);
$helper->logWrite($writer, $filename, $callStartTime);
