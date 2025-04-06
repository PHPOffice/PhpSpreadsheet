<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
$inputFileName = 'TableFormat.xlsx';
$inputFilePath = __DIR__ . '/../templates/' . $inputFileName;

$codePath = $helper->isCli() ? ('samples/templates/' . $inputFileName) : ('<code>' . 'samples/templates/' . $inputFileName . '</code>');
$helper->log('Read ' . $codePath);
$reader = IOFactory::createReader('Xlsx');
$reader->setReadDataOnly(false);
$spreadsheet = $reader->load($inputFilePath);
$helper->log('Enable table formatting output');
$helper->log('Enable conditional formatting output');

function writerCallback(HtmlWriter $writer): void
{
    $writer->setPreCalculateFormulas(true);
    $writer->setTableFormats(true);
    $writer->setConditionalFormatting(true);
}

// Save
$helper->write($spreadsheet, __FILE__, ['Html'], false, writerCallback: writerCallback(...));
