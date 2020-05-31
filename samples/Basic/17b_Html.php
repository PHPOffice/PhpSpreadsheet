<?php

use PhpOffice\PhpSpreadsheet\Writer\Html;

require __DIR__ . '/../Header.php';
$spreadsheet = require __DIR__ . '/../templates/sampleSpreadsheet.php';

$filename = $helper->getFilename(__FILE__, 'html');
$writer = new Html($spreadsheet);

function webfont(string $html): string
{
    $linktag = <<<EOF
<link href="https://fonts.googleapis.com/css2?family=Poiret+One&display=swap" rel="stylesheet" />

EOF;
    $html = preg_replace('@<style@', "$linktag<style", $html, 1);
    $html = str_replace("font-family:'Calibri';", "font-family:'Poiret One','Calibri',sans-serif;", $html);

    return $html;
}

$callStartTime = microtime(true);
$writer->setEmbedImages(true);
$writer->setEditHtmlCallback('webfont');
$writer->save($filename);
$helper->logWrite($writer, $filename, $callStartTime);
