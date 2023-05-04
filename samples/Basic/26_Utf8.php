<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

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
$helper->write($spreadsheet, __FILE__, ['Csv']);

// Export to CSV with BOM (.csv)
$filename = str_replace('.php', '-bom.php', __FILE__);
$helper->log('Write to CSV format (with BOM)');
$helper->write(
    $spreadsheet,
    $filename,
    ['Csv'],
    false,
    function (Csv $writer): void {
        $writer->setUseBOM(true);
    }
);
