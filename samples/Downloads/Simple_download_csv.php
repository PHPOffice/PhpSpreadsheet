<?php

use PhpOffice\PhpSpreadsheet\Helper\Downloader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

require __DIR__ . '/../templates/Simple_download.php';
/** @var ?Spreadsheet $spreadsheet */
if ($spreadsheet === null) {
    return;
}

// Redirect output to a client’s web browser (Csv)
$writer = new Csv($spreadsheet);
$writer->setUseBOM(true); // there are UTF-8 chars on spreadsheet
$downloader = new Downloader($writer, 'Simple_download.csv');
$downloader->download();
