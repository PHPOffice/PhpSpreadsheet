<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class CloneTest extends TestCase
{
    private Spreadsheet $spreadsheet;

    protected function setUp(): void
    {
        $this->spreadsheet = new Spreadsheet();
    }

    protected function tearDown(): void
    {
        $this->spreadsheet->disconnectWorksheets();
        unset($this->spreadsheet);
    }

    public function testUnattachedIndex(): void
    {
        $sheet1 = $this->spreadsheet->getActiveSheet();
        $sheet1->getCell('A1')->setValue(10);
        $sheet2 = clone $sheet1;
        $sheet2->getCell('A1')->setValue(20);
        self::assertSame(0, $this->spreadsheet->getIndex($sheet1));
        $idx = $this->spreadsheet->getIndex($sheet2, true);
        self::assertSame(-1, $idx);
        $sheet2->setTitle('clone');
        $this->spreadsheet->addSheet($sheet2);
        $idx = $this->spreadsheet->getIndex($sheet2, true);
        self::assertSame(1, $idx);
        self::assertSame(10, $this->spreadsheet->getSheet(0)->getCell('A1')->getValue());
        self::assertSame(20, $this->spreadsheet->getSheet(1)->getCell('A1')->getValue());
    }

    public function testGetCloneIndex(): void
    {
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('Sheet does not exist');
        $sheet1 = $this->spreadsheet->getActiveSheet();
        $sheet1->getCell('A1')->setValue(10);
        $sheet2 = clone $sheet1;
        $this->spreadsheet
            ->getSheet($this->spreadsheet->getIndex($sheet2))
            ->setCellValue('A1', 100);
    }

    public function testSerialize1(): void
    {
        $sheet1 = $this->spreadsheet->getActiveSheet();
        $sheet1->getCell('A1')->setValue(10);
        $serialized = serialize($sheet1);
        $newSheet = unserialize($serialized);
        self::assertInstanceOf(Worksheet::class, $newSheet);
        self::assertSame(10, $newSheet->getCell('A1')->getValue());
        self::assertNotEquals($newSheet->getHashInt(), $sheet1->getHashInt());
        self::assertNotNull($newSheet->getParent());
        self::assertNotSame($newSheet->getParent(), $sheet1->getParent());
        $newSheet->getParent()->disconnectWorksheets();
    }

    public function testSerialize2(): void
    {
        $sheet1 = new Worksheet();
        $sheet1->getCell('A1')->setValue(10);
        $serialized = serialize($sheet1);
        /** @var Worksheet */
        $newSheet = unserialize($serialized);
        self::assertSame(10, $newSheet->getCell('A1')->getValue());
        self::assertNotEquals($newSheet->getHashInt(), $sheet1->getHashInt());
    }
}
