<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class CloneTest extends TestCase
{
    public function testUnattachedIndex(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->getCell('A1')->setValue(10);
        $sheet2 = clone $sheet1;
        $sheet2->getCell('A1')->setValue(20);
        self::assertSame(0, $spreadsheet->getIndex($sheet1));
        $idx = $spreadsheet->getIndex($sheet2, true);
        self::assertSame(-1, $idx);
        $sheet2->setTitle('clone');
        $spreadsheet->addSheet($sheet2);
        $idx = $spreadsheet->getIndex($sheet2, true);
        self::assertSame(1, $idx);
        self::assertSame(10, $spreadsheet->getSheet(0)->getCell('A1')->getValue());
        self::assertSame(20, $spreadsheet->getSheet(1)->getCell('A1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testGetCloneIndex(): void
    {
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('Sheet does not exist');
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->getCell('A1')->setValue(10);
        $sheet2 = clone $sheet1;
        $spreadsheet->getSheet($spreadsheet->getIndex($sheet2))
            ->setCellValue('A1', 100);
    }

    public function testSerialize1(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->getCell('A1')->setValue(10);
        $serialized = serialize($sheet1);
        $newSheet = unserialize($serialized);
        self::assertInstanceOf(Worksheet::class, $newSheet);
        self::assertSame(10, $newSheet->getCell('A1')->getValue());
        self::assertNotSame($newSheet, $sheet1);
        self::assertNotNull($newSheet->getParent());
        self::assertNotSame($newSheet->getParent(), $sheet1->getParent());
        $newSheet->getParent()->disconnectWorksheets();
        $spreadsheet->disconnectWorksheets();
    }

    public function testSerialize2(): void
    {
        $sheet1 = new Worksheet();
        $sheet1->getCell('A1')->setValue(10);
        $serialized = serialize($sheet1);
        $newSheet = unserialize($serialized);
        self::assertInstanceOf(Worksheet::class, $newSheet);
        self::assertSame(10, $newSheet->getCell('A1')->getValue());
        self::assertNotSame($newSheet, $sheet1);
    }
}
