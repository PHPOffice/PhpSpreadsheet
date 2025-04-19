<?php

use PhpOffice\PhpSpreadsheet\Writer\Html;

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Spreadsheet */
$spreadsheet = require __DIR__ . '/../templates/sampleSpreadsheet.php';
$spreadsheet->getProperties()->setTitle('Embedded images');

/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
$helper->write(
    $spreadsheet,
    __FILE__,
    ['Html'],
    false,
    function (Html $writer): void {
        $writer->setEmbedImages(true);
    }
);
