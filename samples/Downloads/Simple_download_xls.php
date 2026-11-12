<?php

use PhpOffice\PhpSpreadsheet\Helper\Downloader;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require_once __DIR__ . '/../Bootstrap.php';

require __DIR__ . '/../templates/Simple_download.php';
/** @var ?Spreadsheet $spreadsheet */
if ($spreadsheet === null) {
    return;
}

// Redirect output to a client’s web browser (Xls)
$writer = IOFactory::createWriter($spreadsheet, 'Xls');
$downloader = new Downloader($writer, 'Simple_download.xls');
$downloader->download();
