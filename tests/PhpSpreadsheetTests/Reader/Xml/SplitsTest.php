<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Reader\Xml;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class SplitsTest extends AbstractFunctional
{
    private static string $testbook = 'tests/data/Reader/Xml/splits.xml';

    public function testSplits(): void
    {
        $reader = new Xml();
        $spreadsheet = $reader->load(self::$testbook);

        $sheet = $spreadsheet->getSheetByNameOrThrow('Freeze');
        self::assertSame('frozen', $sheet->getPaneState());
        self::assertSame('E7', $sheet->getFreezePane());
        self::assertSame('L7', $sheet->getPaneTopLeftCell());
        self::assertSame('L7', $sheet->getTopLeftCell());
        //self::assertSame('L7', $sheet->getSelectedCells());

        $sheet = $spreadsheet->getSheetByNameOrThrow('SplitVertical');
        self::assertNull($sheet->getFreezePane());
        self::assertSame('G1', $sheet->getTopLeftCell());
        self::assertSame('E1', $sheet->getPaneTopLeftCell());
        self::assertNotEquals(0, $sheet->getXSplit());
        self::assertEquals(0, $sheet->getYSplit());
        //self::assertSame('E1', $sheet->getSelectedCells());
        //self::assertNotNull($sheet->getPane('topRight'));

        $sheet = $spreadsheet->getSheetByNameOrThrow('SplitHorizontal');
        self::assertNull($sheet->getFreezePane());
        self::assertSame('A3', $sheet->getTopLeftCell());
        self::assertSame('A6', $sheet->getPaneTopLeftCell());
        self::assertEquals(0, $sheet->getXSplit());
        self::assertNotEquals(0, $sheet->getYSplit());
        //self::assertSame('A7', $sheet->getSelectedCells());
        //self::assertNotNull($sheet->getPane('bottomLeft'));

        $sheet = $spreadsheet->getSheetByNameOrThrow('SplitBoth');
        self::assertNull($sheet->getFreezePane());
        self::assertSame('H3', $sheet->getTopLeftCell());
        self::assertSame('E19', $sheet->getPaneTopLeftCell());
        self::assertNotEquals(0, $sheet->getXSplit());
        self::assertNotEquals(0, $sheet->getYSplit());
        //self::assertSame('E20', $sheet->getSelectedCells());
        //self::assertNotNull($sheet->getPane('bottomLeft'));
        //self::assertNotNull($sheet->getPane('bottomRight'));
        //self::assertNotNull($sheet->getPane('topRight'));

        $sheet = $spreadsheet->getSheetByNameOrThrow('NoFreezeNorSplit');
        self::assertNull($sheet->getFreezePane());
        self::assertSame('D3', $sheet->getTopLeftCell());
        self::assertSame('', $sheet->getPaneTopLeftCell());
        self::assertSame('D5', $sheet->getSelectedCells());
        self::assertNull($sheet->getPane('bottomLeft'));
        self::assertNull($sheet->getPane('bottomRight'));
        self::assertNull($sheet->getPane('topRight'));

        $sheet = $spreadsheet->getSheetByNameOrThrow('FrozenSplit');
        self::assertSame('B4', $sheet->getFreezePane());
        self::assertSame('frozenSplit', $sheet->getPaneState());
        self::assertSame('B4', $sheet->getPaneTopLeftCell());
        self::assertSame('B4', $sheet->getTopLeftCell());
        //self::assertSame('B4', $sheet->getSelectedCells());

        $spreadsheet->disconnectWorksheets();
    }
}
