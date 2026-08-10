<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Writer\BaseWriter;

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */

// Create new Spreadsheet object
$helper->log('Create new Spreadsheet object');
$spreadsheet = new Spreadsheet();
$sheet1 = $spreadsheet->getActiveSheet();

// Set document properties
$helper->log('Set document properties');
$spreadsheet->getProperties()->setCreator('Maarten Balliauw')
    ->setLastModifiedBy('Maarten Balliauw')
    ->setTitle('Office 2007 XLSX Test Document')
    ->setSubject('Office 2007 XLSX Test Document')
    ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
    ->setKeywords('office 2007 openxml php')
    ->setCategory('Test result file');

$sheet1->setTitle('JpegWithData');
$sheet1->getCell('G1')->setValue('X');
$sheet1->getCell('E5')->setValue('Y');
$sheet1->getCell('A8')->setValue('Z');
$helper->log('Generate an image as jpg');
$gdImage = imagecreatetruecolor(150, 20);
if (!$gdImage) {
    throw new Exception('Cannot Initialize new GD image stream');
}
$textColor = imagecolorallocate($gdImage, 255, 255, 255);
if ($textColor === false) {
    throw new Exception('imagecolorallocate failed');
}
imagestring($gdImage, 1, 5, 5, 'Jpeg made with PhpSpreadsheet', $textColor);
$helper->log('Add image to the worksheet');
$drawing = new MemoryDrawing();
$drawing->setName('Sample JPEG image');
$drawing->setDescription('Sample JPEG image');
$drawing->setImageResource($gdImage);
$drawing->setRenderingFunction(MemoryDrawing::RENDERING_JPEG);
$drawing->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT);
$drawing->setHeight(36);
$drawing->setWorksheet($sheet1);
$drawing->setCoordinates('C5');

$helper->log('Create new sheet');
$sheet2 = $spreadsheet->createSheet();
$sheet2->setTitle('Gif');
$helper->log('Generate a second image as gif');
$gdImage2 = imagecreatetruecolor(150, 20);
if (!$gdImage2) {
    throw new Exception('Cannot Initialize new GD image stream');
}
$textColor = imagecolorallocate($gdImage, 255, 255, 255);
if ($textColor === false) {
    throw new Exception('imagecolorallocate failed');
}
imagestring($gdImage2, 1, 5, 5, 'Gif made with PhpSpreadsheet', $textColor);
// Add a drawing to the new worksheet
$helper->log('Add image to the new worksheet');
$drawing = new MemoryDrawing();
$drawing->setName('Sample GIF image');
$drawing->setDescription('Sample GIF image');
$drawing->setImageResource($gdImage2);
$drawing->setRenderingFunction(MemoryDrawing::RENDERING_GIF);
$drawing->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT);
$drawing->setHeight(36);
$drawing->setWorksheet($sheet2);
$drawing->setCoordinates('C5');

$helper->log('Create a third sheet');
$sheet3 = $spreadsheet->createSheet();
$sheet3->setTitle('Png');
$helper->log('Generate a third image as png');
$gdImage3 = imagecreatetruecolor(150, 20);
if (!$gdImage3) {
    throw new Exception('Cannot Initialize new GD image stream');
}
$textColor = imagecolorallocate($gdImage3, 255, 255, 255);
if ($textColor === false) {
    throw new Exception('imagecolorallocate failed');
}
imagestring($gdImage3, 1, 5, 5, 'Png made with PhpSpreadsheet', $textColor);
// Add a drawing to the new worksheet
$helper->log('Add image to the new worksheet');
$drawing = new MemoryDrawing();
$drawing->setName('Sample PNG image');
$drawing->setDescription('Sample PNG image');
$drawing->setImageResource($gdImage3);
$drawing->setRenderingFunction(MemoryDrawing::RENDERING_PNG);
$drawing->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT);
$drawing->setHeight(36);
$drawing->setWorksheet($sheet3);
$drawing->setCoordinates('C5');

// Save
$helper->write(
    $spreadsheet,
    __FILE__,
    ['Xlsx', 'Html', 'Ods'],
    false,
    function (BaseWriter $writer): void {
        if (method_exists($writer, 'writeAllSheets')) {
            $writer->writeAllSheets();
        }
    }
);
$spreadsheet->disconnectWorksheets();
