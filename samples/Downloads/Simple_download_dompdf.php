<?php

use PhpOffice\PhpSpreadsheet\Helper\Downloader;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../templates/Simple_download.php';
/** @var ?Spreadsheet $spreadsheet */
if ($spreadsheet === null) {
    return;
}

// Redirect output to a client’s web browser (PDF)
$writer = IOFactory::createWriter($spreadsheet, 'Dompdf');
$downloader = new Downloader($writer, 'Simple_download.dompdf.pdf');
$downloader->download();
