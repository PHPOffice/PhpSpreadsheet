<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;

require __DIR__ . '/../Header.php';

// Read from Xlsx (.xlsx) template
$helper->log('Load Xlsx template file');
$reader = IOFactory::createReader('Xlsx');
$spreadsheet = $reader->load(__DIR__ . '/../templates/26template.xlsx');
$spreadsheet->getActiveSheet()->setPrintGridlines(true);

// at this point, we could do some manipulations with the template, but we skip this step
$helper->write($spreadsheet, __FILE__, ['Xlsx', 'Xls', 'Html']);

// Export to PDF (mpdf)
function mpdfCjkWriter(Mpdf $writer): void
{
    /** @var callable */
    $callback = 'mpdfCjk';
    $writer->setEditHtmlCallback($callback);
}

function mpdfCjk(string $html): string
{
    $html = str_replace("'Calibri'", "'Calibri',Sun-ExtA", $html);

    return str_replace("'Times New Roman'", "'Times New Roman',Sun-ExtA", $html);
}

$helper->log('Write to Mpdf');
IOFactory::registerWriter('Pdf', Mpdf::class);
/** @var callable */
$callback = 'mpdfCjkWriter';
$filename = __FILE__;
//$filename = str_replace('.php', '.mdpf.php', __FILE__);
$helper->write($spreadsheet, $filename, ['Pdf'], false, $callback);

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
