<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class SpreadsheetTest extends TestCase
{
    /** @var ?Spreadsheet */
    private $spreadsheet;

    protected function tearDown(): void
    {
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
    }

    private function getSpreadsheet(): Spreadsheet
    {
        $this->spreadsheet = $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('someSheet1');
        $sheet = new Worksheet();
        $sheet->setTitle('someSheet2');
        $spreadsheet->addSheet($sheet);
        $sheet = new Worksheet();
        $sheet->setTitle('someSheet 3');
        $spreadsheet->addSheet($sheet);

        return $spreadsheet;
    }

    public static function dataProviderForSheetNames(): array
    {
        $array = [
            [0, 'someSheet1'],
            [0, "'someSheet1'"],
            [1, 'someSheet2'],
            [1, "'someSheet2'"],
            [2, 'someSheet 3'],
            [2, "'someSheet 3'"],
            [null, 'someSheet 33'],
        ];

        return $array;
    }

    /**
     * @dataProvider dataProviderForSheetNames
     */
    public function testGetSheetByName(?int $index, string $sheetName): void
    {
        $spreadsheet = $this->getSpreadsheet();
        if ($index === null) {
            self::assertNull($spreadsheet->getSheetByName($sheetName));
        } else {
            self::assertSame($spreadsheet->getSheet($index), $spreadsheet->getSheetByName($sheetName));
        }
    }

    public function testAddSheetDuplicateTitle(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $this->expectException(Exception::class);
        $sheet = new Worksheet();
        $sheet->setTitle('someSheet2');
        $spreadsheet->addSheet($sheet);
    }

    public function testAddSheetNoAdjustActive(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $spreadsheet->setActiveSheetIndex(2);
        self::assertEquals(2, $spreadsheet->getActiveSheetIndex());
        $sheet = new Worksheet();
        $sheet->setTitle('someSheet4');
        $spreadsheet->addSheet($sheet);
        self::assertEquals(2, $spreadsheet->getActiveSheetIndex());
    }

    public function testAddSheetAdjustActive(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $spreadsheet->setActiveSheetIndex(2);
        self::assertEquals(2, $spreadsheet->getActiveSheetIndex());
        $sheet = new Worksheet();
        $sheet->setTitle('someSheet0');
        $spreadsheet->addSheet($sheet, 0);
        self::assertEquals(3, $spreadsheet->getActiveSheetIndex());
    }

    public function testRemoveSheetIndexTooHigh(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $this->expectException(Exception::class);
        $spreadsheet->removeSheetByIndex(4);
    }

    public function testRemoveSheetNoAdjustActive(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $spreadsheet->setActiveSheetIndex(1);
        self::assertEquals(1, $spreadsheet->getActiveSheetIndex());
        $spreadsheet->removeSheetByIndex(2);
        self::assertEquals(1, $spreadsheet->getActiveSheetIndex());
    }

    public function testRemoveSheetAdjustActive(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $spreadsheet->setActiveSheetIndex(2);
        self::assertEquals(2, $spreadsheet->getActiveSheetIndex());
        $spreadsheet->removeSheetByIndex(1);
        self::assertEquals(1, $spreadsheet->getActiveSheetIndex());
    }

    public function testGetSheetIndexTooHigh(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $this->expectException(Exception::class);
        $spreadsheet->getSheet(4);
    }

    public function testGetIndexNonExistent(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $this->expectException(Exception::class);
        $sheet = new Worksheet();
        $sheet->setTitle('someSheet4');
        $spreadsheet->getIndex($sheet);
    }

    public function testSetIndexByName(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $spreadsheet->setIndexByName('someSheet1', 1);
        self::assertEquals('someSheet2', $spreadsheet->getSheet(0)->getTitle());
        self::assertEquals('someSheet1', $spreadsheet->getSheet(1)->getTitle());
        self::assertEquals('someSheet 3', $spreadsheet->getSheet(2)->getTitle());
    }

    public function testRemoveAllSheets(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $spreadsheet->setActiveSheetIndex(2);
        self::assertEquals(2, $spreadsheet->getActiveSheetIndex());
        $spreadsheet->removeSheetByIndex(0);
        self::assertEquals(1, $spreadsheet->getActiveSheetIndex());
        $spreadsheet->removeSheetByIndex(0);
        self::assertEquals(0, $spreadsheet->getActiveSheetIndex());
        $spreadsheet->removeSheetByIndex(0);
        self::assertEquals(-1, $spreadsheet->getActiveSheetIndex());
        $sheet = new Worksheet();
        $sheet->setTitle('someSheet4');
        $spreadsheet->addSheet($sheet);
        self::assertEquals(0, $spreadsheet->getActiveSheetIndex());
    }

    public function testBug1735(): void
    {
        $spreadsheet1 = new Spreadsheet();
        $spreadsheet1->createSheet()->setTitle('addedsheet');
        $spreadsheet1->setActiveSheetIndex(1);
        $spreadsheet1->removeSheetByIndex(0);
        $sheet = $spreadsheet1->getActiveSheet();
        self::assertEquals('addedsheet', $sheet->getTitle());
    }

    public function testSetActiveSheetIndexTooHigh(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $this->expectException(Exception::class);
        $spreadsheet->setActiveSheetIndex(4);
    }

    public function testSetActiveSheetNoSuchName(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $this->expectException(Exception::class);
        $spreadsheet->setActiveSheetIndexByName('unknown');
    }

    public function testAddExternal(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $spreadsheet1 = new Spreadsheet();
        $sheet = $spreadsheet1->createSheet()->setTitle('someSheet19');
        $sheet->getCell('A1')->setValue(1);
        $sheet->getCell('A1')->getStyle()->getFont()->setBold(true);
        $sheet->getCell('B1')->getStyle()->getFont()->setSuperscript(true);
        $sheet->getCell('C1')->getStyle()->getFont()->setSubscript(true);
        self::assertCount(4, $spreadsheet1->getCellXfCollection());
        self::assertEquals(1, $sheet->getCell('A1')->getXfIndex());
        $spreadsheet->getActiveSheet()->getCell('A1')->getStyle()->getFont()->setBold(true);
        self::assertCount(2, $spreadsheet->getCellXfCollection());
        $sheet3 = $spreadsheet->addExternalSheet($sheet);
        self::assertCount(6, $spreadsheet->getCellXfCollection());
        self::assertEquals('someSheet19', $sheet3->getTitle());
        self::assertEquals(1, $sheet3->getCell('A1')->getValue());
        self::assertTrue($sheet3->getCell('A1')->getStyle()->getFont()->getBold());
        // Prove Xf index changed although style is same.
        self::assertEquals(3, $sheet3->getCell('A1')->getXfIndex());
    }

    public function testAddExternalDuplicateName(): void
    {
        $this->expectException(Exception::class);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->createSheet()->setTitle('someSheet1');
        $sheet->getCell('A1')->setValue(1);
        $sheet->getCell('A1')->getStyle()->getFont()->setBold(true);
        $spreadsheet->addExternalSheet($sheet);
    }

    public function testAddExternalColumnDimensionStyles(): void
    {
        $spreadsheet1 = new Spreadsheet();
        $sheet1 = $spreadsheet1->createSheet()->setTitle('sheetWithColumnDimension');
        $sheet1->getCell('A1')->setValue(1);
        $sheet1->getCell('A1')->getStyle()->getFont()->setItalic(true);
        $sheet1->getColumnDimension('B')->setWidth(10)->setXfIndex($sheet1->getCell('A1')->getXfIndex());
        $index = $sheet1->getColumnDimension('B')->getXfIndex();
        self::assertEquals(1, $index);
        self::assertCount(2, $spreadsheet1->getCellXfCollection());

        $spreadsheet2 = new Spreadsheet();
        $sheet2 = $spreadsheet2->createSheet()->setTitle('sheetWithTwoStyles');
        $sheet2->getCell('A1')->setValue(1);
        $sheet2->getCell('A1')->getStyle()->getFont()->setBold(true);
        $sheet2->getCell('B2')->getStyle()->getFont()->setSuperscript(true);
        $countXfs = count($spreadsheet2->getCellXfCollection());
        self::assertEquals(3, $countXfs);

        $sheet3 = $spreadsheet2->addExternalSheet($sheet1);
        self::assertCount(5, $spreadsheet2->getCellXfCollection());
        self::assertTrue($sheet3->getCell('A1')->getStyle()->getFont()->getItalic());
        self::assertTrue($sheet3->getCell('B1')->getStyle()->getFont()->getItalic());
        self::assertFalse($sheet3->getCell('B1')->getStyle()->getFont()->getBold());
        // Prove Xf index changed although style is same.
        self::assertEquals($countXfs + $index, $sheet3->getCell('B1')->getXfIndex());
        self::assertEquals($countXfs + $index, $sheet3->getColumnDimension('B')->getXfIndex());
    }

    public function testAddExternalRowDimensionStyles(): void
    {
        $spreadsheet1 = new Spreadsheet();
        $sheet1 = $spreadsheet1->createSheet()->setTitle('sheetWithColumnDimension');
        $sheet1->getCell('A1')->setValue(1);
        $sheet1->getCell('A1')->getStyle()->getFont()->setItalic(true);
        $sheet1->getRowDimension(2)->setXfIndex($sheet1->getCell('A1')->getXfIndex());
        $index = $sheet1->getRowDimension(2)->getXfIndex();
        self::assertEquals(1, $index);
        self::assertCount(2, $spreadsheet1->getCellXfCollection());

        $spreadsheet2 = new Spreadsheet();
        $sheet2 = $spreadsheet2->createSheet()->setTitle('sheetWithTwoStyles');
        $sheet2->getCell('A1')->setValue(1);
        $sheet2->getCell('A1')->getStyle()->getFont()->setBold(true);
        $sheet2->getCell('B2')->getStyle()->getFont()->setSuperscript(true);
        $countXfs = count($spreadsheet2->getCellXfCollection());
        self::assertEquals(3, $countXfs);

        $sheet3 = $spreadsheet2->addExternalSheet($sheet1);
        self::assertCount(5, $spreadsheet2->getCellXfCollection());
        self::assertTrue($sheet3->getCell('A1')->getStyle()->getFont()->getItalic());
        self::assertTrue($sheet3->getCell('A2')->getStyle()->getFont()->getItalic());
        self::assertFalse($sheet3->getCell('A2')->getStyle()->getFont()->getBold());
        // Prove Xf index changed although style is same.
        self::assertEquals($countXfs + $index, $sheet3->getCell('A2')->getXfIndex());
        self::assertEquals($countXfs + $index, $sheet3->getRowDimension(2)->getXfIndex());
    }

    public function testNotSerializable(): void
    {
        $this->spreadsheet = new Spreadsheet();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Spreadsheet objects cannot be serialized');
        serialize($this->spreadsheet);
    }

    public function testNotJsonEncodable(): void
    {
        $this->spreadsheet = new Spreadsheet();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Spreadsheet objects cannot be json encoded');
        json_encode($this->spreadsheet);
    }
}
