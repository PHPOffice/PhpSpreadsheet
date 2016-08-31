<?php

require __DIR__ . '/Header.php';

//	Change these values to select the PDF Rendering library that you wish to use
//		and its directory location on your server
//$rendererName = \PhpOffice\PhpSpreadsheet\Settings::PDF_RENDERER_TCPDF;
//$rendererName = \PhpOffice\PhpSpreadsheet\Settings::PDF_RENDERER_MPDF;
$rendererName = \PhpOffice\PhpSpreadsheet\Settings::PDF_RENDERER_DOMPDF;
//$rendererLibrary = 'tcPDF5.9';
//$rendererLibrary = 'mPDF5.4';
$rendererLibrary = 'domPDF0.6.0beta3';
$rendererLibraryPath = '/php/libraries/PDF/' . $rendererLibrary;

// Read from Excel2007 (.xlsx) template
$helper->log('Load Excel2007 template file');
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Excel2007');
$spreadsheet = $reader->load(__DIR__ . '/templates/26template.xlsx');

/* at this point, we could do some manipulations with the template, but we skip this step */
$helper->write($spreadsheet, __FILE__, ['Excel2007' => 'xlsx', 'Excel5' => 'xls', 'HTML' => 'html']);

// Export to PDF (.pdf)
$helper->log('Write to PDF format');
try {
    if (!\PhpOffice\PhpSpreadsheet\Settings::setPdfRenderer($rendererName, $rendererLibraryPath)) {
        $helper->log('NOTICE: Please set the $rendererName and $rendererLibraryPath values at the top of this script as appropriate for your directory structure');
    } else {
        $helper->write($spreadsheet, __FILE__, ['PDF' => 'pdf']);
    }
} catch (Exception $e) {
    $helper->log('EXCEPTION: ' . $e->getMessage());
}

// Remove first two rows with field headers before exporting to CSV
$helper->log('Removing first two heading rows for CSV export');
$worksheet = $spreadsheet->getActiveSheet();
$worksheet->removeRow(1, 2);

// Export to CSV (.csv)
$helper->log('Write to CSV format');
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'CSV');
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
