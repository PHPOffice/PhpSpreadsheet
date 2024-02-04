<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Writer\BaseWriter;

require __DIR__ . '/../Header.php';

// Create new Spreadsheet object
$helper->log('Create new Spreadsheet object');
$spreadsheet = new Spreadsheet();
$sheet1 = $spreadsheet->getActiveSheet();
$sheet1->setTitle('SheetWithData');
$sheet1->getCell('G1')->setValue('X');
$sheet1->getCell('E5')->setValue('Y');
$sheet1->getCell('A8')->setValue('Z');

// Set document properties
$helper->log('Set document properties');
$spreadsheet->getProperties()->setCreator('Maarten Balliauw')
    ->setLastModifiedBy('Maarten Balliauw')
    ->setTitle('Office 2007 XLSX Test Document')
    ->setSubject('Office 2007 XLSX Test Document')
    ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
    ->setKeywords('office 2007 openxml php')
    ->setCategory('Test result file');

// Generate an image
$helper->log('Generate an image');
$gdImage = imagecreatetruecolor(120, 20);
if (!$gdImage) {
    throw new Exception('Cannot Initialize new GD image stream');
}

$textColor = imagecolorallocate($gdImage, 255, 255, 255);
if ($textColor === false) {
    throw new Exception('imagecolorallocate failed');
}
imagestring($gdImage, 1, 5, 5, 'Created with PhpSpreadsheet', $textColor);

// Add a drawing to the worksheet
$helper->log('Add a drawing to the worksheet');
$drawing = new MemoryDrawing();
$drawing->setName('Sample image');
$drawing->setDescription('Sample image');
$drawing->setImageResource($gdImage);
$drawing->setRenderingFunction(MemoryDrawing::RENDERING_JPEG);
$drawing->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT);
$drawing->setHeight(36);
$drawing->setWorksheet($sheet1);
$drawing->setCoordinates('C5');

$helper->log('Create new sheet');
$sheet2 = $spreadsheet->createSheet();
$sheet2->setTitle('SheetWithoutData');

// Add a drawing to the new worksheet
$helper->log('Add a drawing to the new worksheet');
$drawing = new MemoryDrawing();
$drawing->setName('Sample image');
$drawing->setDescription('Sample image');
$drawing->setImageResource($gdImage);
$drawing->setRenderingFunction(MemoryDrawing::RENDERING_JPEG);
$drawing->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT);
$drawing->setHeight(36);
$drawing->setWorksheet($sheet2);
$drawing->setCoordinates('C5');

// Save
$helper->write(
    $spreadsheet,
    __FILE__,
    ['Xlsx', 'Html'],
    false,
    function (BaseWriter $writer): void {
        if (method_exists($writer, 'writeAllSheets')) {
            $writer->writeAllSheets();
        }
    }
);
$spreadsheet->disconnectWorksheets();
