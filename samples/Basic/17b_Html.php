<?php

use PhpOffice\PhpSpreadsheet\Writer\Html;

require __DIR__ . '/../Header.php';
$spreadsheet = require __DIR__ . '/../templates/sampleSpreadsheet.php';

$filename = $helper->getFilename(__FILE__, 'html');
$writer = new Html($spreadsheet);

function changeGridlines(string $html): string
{
    return str_replace('{border: 1px solid black;}', '{border: 2px dashed red;}', $html);
}

$callStartTime = microtime(true);
$writer->setEmbedImages(true);
$writer->setEditHtmlCallback('changeGridlines');
$writer->save($filename);
$helper->logWrite($writer, $filename, $callStartTime);
