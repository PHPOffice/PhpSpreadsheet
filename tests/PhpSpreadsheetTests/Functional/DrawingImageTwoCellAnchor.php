<?php


namespace PhpOffice\PhpSpreadsheetTests\Functional;


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;

class DrawingImageTwoCellAnchor extends AbstractFunctional
{

  public function testDrawingImageTwoCellAnchor()
  {
    $spreadsheet = new Spreadsheet();

    $aSheet = $spreadsheet->getActiveSheet();

    $gdImage = @imagecreatetruecolor(120, 20);
    $textColor = imagecolorallocate($gdImage, 255, 255, 255);
    imagestring($gdImage, 1, 5, 5, 'Created with PhpSpreadsheet', $textColor);


    $listOfModes = ['twoCell', 'absolute', 'oneCell'];

    foreach ($listOfModes as $i => $mode) {
      $drawing = new MemoryDrawing();
      $drawing->setName('In-Memory image '. $i);
      $drawing->setDescription('In-Memory image '. $i);

      $drawing->setCoordinates('A1');
      $drawing->setBottomRightCell('D4');
      $drawing->editAs($mode);

      $drawing->setImageResource($gdImage);
      $drawing->setRenderingFunction(
        MemoryDrawing::RENDERING_JPEG
      );

      $drawing->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT);

      $drawing->setWorksheet($aSheet);
    }

    $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');

    foreach ($reloadedSpreadsheet->getActiveSheet()->getDrawingCollection() as $index => $pDrawing) {
      self::assertEquals($listOfModes[$index], $pDrawing->getEditAs(), 'functional test drawing twoCellAnchor');
    }
  }
}
