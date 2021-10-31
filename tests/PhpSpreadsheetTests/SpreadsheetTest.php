<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class SpreadsheetTest extends TestCase
{
    /** @var Spreadsheet */
    private $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new Spreadsheet();
        $sheet = $this->object->getActiveSheet();

        $sheet->setTitle('someSheet1');
        $sheet = new Worksheet();
        $sheet->setTitle('someSheet2');
        $this->object->addSheet($sheet);
        $sheet = new Worksheet();
        $sheet->setTitle('someSheet 3');
        $this->object->addSheet($sheet);
    }

    public function dataProviderForSheetNames(): array
    {
        $array = [
            [0, 'someSheet1'],
            [0, "'someSheet1'"],
            [1, 'someSheet2'],
            [1, "'someSheet2'"],
            [2, 'someSheet 3'],
            [2, "'someSheet 3'"],
        ];

        return $array;
    }

    /**
     * @dataProvider dataProviderForSheetNames
     */
    public function testGetSheetByName($index, $sheetName): void
    {
        self::assertSame($this->object->getSheet($index), $this->object->getSheetByName($sheetName));
    }

    public function testAddSheetDuplicateTitle(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);
        $sheet = new Worksheet();
        $sheet->setTitle('someSheet2');
        $this->object->addSheet($sheet);
    }

    public function testAddSheetNoAdjustActive(): void
    {
        $this->object->setActiveSheetIndex(2);
        self::assertEquals(2, $this->object->getActiveSheetIndex());
        $sheet = new Worksheet();
        $sheet->setTitle('someSheet4');
        $this->object->addSheet($sheet);
        self::assertEquals(2, $this->object->getActiveSheetIndex());
    }

    public function testAddSheetAdjustActive(): void
    {
        $this->object->setActiveSheetIndex(2);
        self::assertEquals(2, $this->object->getActiveSheetIndex());
        $sheet = new Worksheet();
        $sheet->setTitle('someSheet0');
        $this->object->addSheet($sheet, 0);
        self::assertEquals(3, $this->object->getActiveSheetIndex());
    }

    public function testRemoveSheetIndexTooHigh(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);
        $this->object->removeSheetByIndex(4);
    }

    public function testRemoveSheetNoAdjustActive(): void
    {
        $this->object->setActiveSheetIndex(1);
        self::assertEquals(1, $this->object->getActiveSheetIndex());
        $this->object->removeSheetByIndex(2);
        self::assertEquals(1, $this->object->getActiveSheetIndex());
    }

    public function testRemoveSheetAdjustActive(): void
    {
        $this->object->setActiveSheetIndex(2);
        self::assertEquals(2, $this->object->getActiveSheetIndex());
        $this->object->removeSheetByIndex(1);
        self::assertEquals(1, $this->object->getActiveSheetIndex());
    }

    public function testGetSheetIndexTooHigh(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);
        $this->object->getSheet(4);
    }

    public function testGetIndexNonExistent(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);
        $sheet = new Worksheet();
        $sheet->setTitle('someSheet4');
        $this->object->getIndex($sheet);
    }

    public function testSetIndexByName(): void
    {
        $this->object->setIndexByName('someSheet1', 1);
        self::assertEquals('someSheet2', $this->object->getSheet(0)->getTitle());
        self::assertEquals('someSheet1', $this->object->getSheet(1)->getTitle());
        self::assertEquals('someSheet 3', $this->object->getSheet(2)->getTitle());
    }

    public function testRemoveAllSheets(): void
    {
        $this->object->setActiveSheetIndex(2);
        self::assertEquals(2, $this->object->getActiveSheetIndex());
        $this->object->removeSheetByIndex(0);
        self::assertEquals(1, $this->object->getActiveSheetIndex());
        $this->object->removeSheetByIndex(0);
        self::assertEquals(0, $this->object->getActiveSheetIndex());
        $this->object->removeSheetByIndex(0);
        self::assertEquals(-1, $this->object->getActiveSheetIndex());
        $sheet = new Worksheet();
        $sheet->setTitle('someSheet4');
        $this->object->addSheet($sheet);
        self::assertEquals(0, $this->object->getActiveSheetIndex());
    }

    public function testBug1735(): void
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->createSheet()->setTitle('addedsheet');
        $spreadsheet->setActiveSheetIndex(1);
        $spreadsheet->removeSheetByIndex(0);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertEquals('addedsheet', $sheet->getTitle());
    }

    public function testSetActiveSheetIndexTooHigh(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);
        $this->object->setActiveSheetIndex(4);
    }

    public function testSetActiveSheetNoSuchName(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);
        $this->object->setActiveSheetIndexByName('unknown');
    }

    public function testAddExternal(): void
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->createSheet()->setTitle('someSheet19');
        $sheet->getCell('A1')->setValue(1);
        $sheet->getCell('A1')->getStyle()->getFont()->setBold(true);
        $sheet->getCell('B1')->getStyle()->getFont()->setSuperscript(true);
        $sheet->getCell('C1')->getStyle()->getFont()->setSubscript(true);
        self::assertCount(4, $spreadsheet->getCellXfCollection());
        self::assertEquals(1, $sheet->getCell('A1')->getXfIndex());
        $this->object->getActiveSheet()->getCell('A1')->getStyle()->getFont()->setBold(true);
        self::assertCount(2, $this->object->getCellXfCollection());
        $sheet3 = $this->object->addExternalSheet($sheet);
        self::assertCount(6, $this->object->getCellXfCollection());
        self::assertEquals('someSheet19', $sheet3->getTitle());
        self::assertEquals(1, $sheet3->getCell('A1')->getValue());
        self::assertTrue($sheet3->getCell('A1')->getStyle()->getFont()->getBold());
        // Prove Xf index changed although style is same.
        self::assertEquals(3, $sheet3->getCell('A1')->getXfIndex());
    }

    public function testAddExternalDuplicateName(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->createSheet()->setTitle('someSheet1');
        $sheet->getCell('A1')->setValue(1);
        $sheet->getCell('A1')->getStyle()->getFont()->setBold(true);
        $this->object->addExternalSheet($sheet);
    }
}
