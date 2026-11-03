<?php

use PhpOffice\PhpSpreadsheet\Helper\Downloader;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../templates/Simple_download.php';
/** @var ?Spreadsheet $spreadsheet */
if ($spreadsheet === null) {
    return;
}

// Redirect output to a client’s web browser (Xlsx)
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$downloader = new Downloader($writer, 'Simple_download.xlsx');
$downloader->download();
