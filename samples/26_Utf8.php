<?php

require __DIR__ . '/Header.php';

//	Change these values to select the PDF Rendering library that you wish to use
//		and its directory location on your server
//$rendererName = \PhpOffice\PhpSpreadsheet\Settings::PDF_RENDERER_TCPDF;
//$rendererName = \PhpOffice\PhpSpreadsheet\Settings::PDF_RENDERER_MPDF;
$rendererName = \PhpOffice\PhpSpreadsheet\Settings::PDF_RENDERER_DOMPDF;

// Read from Xlsx (.xlsx) template
$helper->log('Load Xlsx template file');
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
$spreadsheet = $reader->load(__DIR__ . '/templates/26template.xlsx');

/* at this point, we could do some manipulations with the template, but we skip this step */
$helper->write($spreadsheet, __FILE__, ['Xlsx', 'Xls', 'Html']);

// Export to PDF (.pdf)
$helper->log('Write to PDF format');
\PhpOffice\PhpSpreadsheet\Settings::setPdfRendererName($rendererName);
$helper->write($spreadsheet, __FILE__, ['Pdf']);

// Remove first two rows with field headers before exporting to CSV
$helper->log('Removing first two heading rows for CSV export');
$worksheet = $spreadsheet->getActiveSheet();
$worksheet->removeRow(1, 2);

// Export to CSV (.csv)
$helper->log('Write to CSV format');
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Csv');
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
