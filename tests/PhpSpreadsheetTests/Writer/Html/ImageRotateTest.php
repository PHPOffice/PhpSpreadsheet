<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ImageRotateTest extends AbstractFunctional
{
    public function testImageCopyXls(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $drawing = new Drawing();
        $drawing->setName('Blue Square');
        $drawing->setDescription('Blue_Square');
        $drawing->setPath('samples/images/blue_square.png');
        $drawing->setCoordinates('C5');
        $drawing->setRotation(45);
        $drawing->setWorksheet($sheet);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Html');
        $spreadsheet->disconnectWorksheets();

        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        $drawings = $rsheet->getDrawingCollection();
        self::assertCount(1, $drawings);
        $drawing = $drawings[0];
        self::assertNotNull($drawing);
        self::assertSame(45, $drawing->getRotation());

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
