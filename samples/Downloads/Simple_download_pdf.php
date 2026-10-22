<?php

use PhpOffice\PhpSpreadsheet\Helper\Downloader;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;

require __DIR__ . '/../templates/Simple_download.php';
/** @var ?Spreadsheet $spreadsheet */
if ($spreadsheet === null) {
    return;
}

// Redirect output to a client’s web browser (PDF)
IOFactory::registerWriter('Pdf', Mpdf::class);
$writer = IOFactory::createWriter($spreadsheet, 'Pdf');
$downloader = new Downloader($writer, 'Simple_download.pdf');
$downloader->download();
