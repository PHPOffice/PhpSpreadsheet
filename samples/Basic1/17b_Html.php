<?php

use PhpOffice\PhpSpreadsheet\Writer\Html;

require __DIR__ . '/../Header.php';
$spreadsheet = require __DIR__ . '/../templates/sampleSpreadsheet.php';

function changeGridlines(string $html): string
{
    return str_replace('{border: 1px solid black;}', '{border: 2px dashed red;}', $html);
}

$helper->write(
    $spreadsheet,
    __FILE__,
    ['Html'],
    false,
    function (Html $writer): void {
        $writer->setEmbedImages(true);
        $writer->setEditHtmlCallback('changeGridlines');
    }
);
