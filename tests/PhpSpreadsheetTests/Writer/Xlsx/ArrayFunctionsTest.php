<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class ArrayFunctionsTest extends TestCase
{
    private string $outputFile = '';

    public function testArrayOutput(): void
    {
        $spreadsheet = new Spreadsheet();
        Calculation::getInstance($spreadsheet)->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $sheet = $spreadsheet->getActiveSheet();
        $columnArray = [
            [41],
            [57],
            [51],
            [54],
            [49],
            [43],
            [35],
            [35],
            [44],
            [47],
            [48],
            [26],
            [57],
            [34],
            [61],
            [34],
            [28],
            [29],
            [41],
        ];
        $sheet->fromArray($columnArray, 'A1');
        $sheet->setCellValue('C1', '=UNIQUE(A1:A19)');
        $sheet->setCellValue('D1', '=SORT(A1:A19)');
        $writer = new XlsxWriter($spreadsheet);
        $this->outputFile = File::temporaryFilename();
        $writer->save($this->outputFile);
        $spreadsheet->disconnectWorksheets();

        $reader = new XlsxReader();
        $spreadsheet2 = $reader->load($this->outputFile);
        Calculation::getInstance($spreadsheet2)->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $sheet2 = $spreadsheet2->getActiveSheet();
        $expectedUnique = [
            [41],
            [57],
            [51],
            [54],
            [49],
            [43],
            [35],
            [44],
            [47],
            [48],
            [26],
            [34],
            [61],
            [28],
            [29],
        ];
        self::assertCount(15, $expectedUnique);
        self::assertSame($expectedUnique, $sheet2->getCell('C1')->getCalculatedValue());
        for ($row = 2; $row <= 15; ++$row) {
            self::assertSame($expectedUnique[$row - 1][0], $sheet2->getCell("C$row")->getCalculatedValue(), "cell C$row");
        }
        $expectedSort = [
            [26],
            [28],
            [29],
            [34],
            [34],
            [35],
            [35],
            [41],
            [41],
            [43],
            [44],
            [47],
            [48],
            [49],
            [51],
            [54],
            [57],
            [57],
            [61],
        ];
        self::assertCount(19, $expectedSort);
        self::assertCount(19, $columnArray);
        self::assertSame($expectedSort, $sheet2->getCell('D1')->getCalculatedValue());
        $spreadsheet2->disconnectWorksheets();

        $file = 'zip://';
        $file .= $this->outputFile;
        $file .= '#xl/worksheets/sheet1.xml';
        $data = file_get_contents($file);
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertStringContainsString('<c r="C1" cm="1"><f t="array" ref="C1:C15" aca="1" ca="1">_xlfn.UNIQUE(A1:A19)</f><v>41</v></c>', $data, '15 results for UNIQUE');
            self::assertStringContainsString('<c r="D1" cm="1"><f t="array" ref="D1:D19" aca="1" ca="1">_xlfn._xlws.SORT(A1:A19)</f><v>26</v></c>', $data, '19 results for SORT');
        }

        $file = 'zip://';
        $file .= $this->outputFile;
        $file .= '#xl/metadata.xml';
        $data = @file_get_contents($file);
        self::assertNotFalse($data, 'metadata.xml should exist');

        $file = 'zip://';
        $file .= $this->outputFile;
        $file .= '#[Content_Types].xml';
        $data = file_get_contents($file);
        self::assertStringContainsString('metadata', $data);

        $file = 'zip://';
        $file .= $this->outputFile;
        $file .= '#xl/_rels/workbook.xml.rels';
        $data = file_get_contents($file);
        self::assertStringContainsString('metadata', $data);
    }

    public function testArrayOutputCSE(): void
    {
        $spreadsheet = new Spreadsheet();
        Calculation::getInstance($spreadsheet)->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $sheet = $spreadsheet->getActiveSheet();
        $columnArray = [
            [41],
            [57],
            [51],
            [54],
            [49],
            [43],
            [35],
            [35],
            [44],
            [47],
            [48],
            [26],
            [57],
            [34],
            [61],
            [34],
            [28],
            [29],
            [41],
        ];
        $sheet->fromArray($columnArray, 'A1');
        $sheet->setCellValue('C1', '=UNIQUE(A1:A19)');
        $sheet->setCellValue('D1', '=SORT(A1:A19)');
        $writer = new XlsxWriter($spreadsheet);
        $this->outputFile = File::temporaryFilename();
        $writer->setUseCSEArrays(true);
        $writer->save($this->outputFile);
        $spreadsheet->disconnectWorksheets();

        $reader = new XlsxReader();
        $spreadsheet2 = $reader->load($this->outputFile);
        Calculation::getInstance($spreadsheet2)->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $sheet2 = $spreadsheet2->getActiveSheet();
        $expectedUnique = [
            [41],
            [57],
            [51],
            [54],
            [49],
            [43],
            [35],
            [44],
            [47],
            [48],
            [26],
            [34],
            [61],
            [28],
            [29],
        ];
        self::assertCount(15, $expectedUnique);
        self::assertSame($expectedUnique, $sheet2->getCell('C1')->getCalculatedValue());
        for ($row = 2; $row <= 15; ++$row) {
            self::assertSame($expectedUnique[$row - 1][0], $sheet2->getCell("C$row")->getCalculatedValue(), "cell C$row");
        }
        $expectedSort = [
            [26],
            [28],
            [29],
            [34],
            [34],
            [35],
            [35],
            [41],
            [41],
            [43],
            [44],
            [47],
            [48],
            [49],
            [51],
            [54],
            [57],
            [57],
            [61],
        ];
        self::assertCount(19, $expectedSort);
        self::assertCount(19, $columnArray);
        self::assertSame($expectedSort, $sheet2->getCell('D1')->getCalculatedValue());
        $spreadsheet2->disconnectWorksheets();

        $file = 'zip://';
        $file .= $this->outputFile;
        $file .= '#xl/worksheets/sheet1.xml';
        $data = file_get_contents($file);
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertStringContainsString('<c r="C1"><f t="array" ref="C1:C15" aca="1" ca="1">_xlfn.UNIQUE(A1:A19)</f><v>41</v></c>', $data, '15 results for UNIQUE');
            self::assertStringContainsString('<c r="D1"><f t="array" ref="D1:D19" aca="1" ca="1">_xlfn._xlws.SORT(A1:A19)</f><v>26</v></c>', $data, '19 results for SORT');
        }

        $file = 'zip://';
        $file .= $this->outputFile;
        $file .= '#xl/metadata.xml';
        $data = @file_get_contents($file);
        self::assertFalse($data, 'metadata.xml should not exist');

        $file = 'zip://';
        $file .= $this->outputFile;
        $file .= '#[Content_Types].xml';
        $data = file_get_contents($file);
        self::assertStringNotContainsString('metadata', $data);

        $file = 'zip://';
        $file .= $this->outputFile;
        $file .= '#xl/_rels/workbook.xml.rels';
        $data = file_get_contents($file);
        self::assertStringNotContainsString('metadata', $data);
    }

    public function testUnimplementedArrayOutput(): void
    {
        //Calculation::setArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY); // not required for this test
        $reader = new XlsxReader();
        $spreadsheet = $reader->load('tests/data/Reader/XLSX/atsign.choosecols.xlsx');
        $writer = new XlsxWriter($spreadsheet);
        $this->outputFile = File::temporaryFilename();
        $writer->save($this->outputFile);
        $spreadsheet->disconnectWorksheets();

        $reader = new XlsxReader();
        $spreadsheet2 = $reader->load($this->outputFile);
        $sheet2 = $spreadsheet2->getActiveSheet();
        self::assertSame('=CHOOSECOLS(A1:C5,3,1)', $sheet2->getCell('F1')->getValue());
        $expectedFG = [
            ['11', '1'],
            ['12', '2'],
            ['13', '3'],
            ['14', '4'],
            ['15', '5'],
        ];
        $actualFG = $sheet2->rangeToArray('F1:G5');
        self::assertSame($expectedFG, $actualFG);
        self::assertSame('=CELL("width")', $sheet2->getCell('I1')->getValue());
        self::assertSame(8, $sheet2->getCell('I1')->getCalculatedValue());
        self::assertTrue($sheet2->getCell('J1')->getValue());
        $spreadsheet2->disconnectWorksheets();

        $file = 'zip://';
        $file .= $this->outputFile;
        $file .= '#xl/worksheets/sheet1.xml';
        $data = file_get_contents($file);
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertStringContainsString('<f t="array" ref="F1:G5" aca="1" ca="1">_xlfn.CHOOSECOLS(A1:C5,3,1)</f><v>11</v>', $data);
            self::assertStringContainsString('<f t="array" ref="I1:J1" aca="1" ca="1">CELL(&quot;width&quot;)</f><v>8</v></c><c r="J1" t="b"><v>1</v></c>', $data);
        }
    }

    public function testArrayMultipleColumns(): void
    {
        $spreadsheet = new Spreadsheet();
        Calculation::getInstance($spreadsheet)->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $sheet = $spreadsheet->getActiveSheet();
        $columnArray = [
            [100, 91],
            [85, 1],
            [100, 92],
            [734, 12],
            [100, 91],
            [5, 2],
        ];
        $sheet->fromArray($columnArray);
        $sheet->setCellValue('H1', '=UNIQUE(A1:B6)');
        $writer = new XlsxWriter($spreadsheet);
        $this->outputFile = File::temporaryFilename();
        $writer->save($this->outputFile);
        $spreadsheet->disconnectWorksheets();

        $reader = new XlsxReader();
        $spreadsheet2 = $reader->load($this->outputFile);
        Calculation::getInstance($spreadsheet2)->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $sheet2 = $spreadsheet2->getActiveSheet();
        $expectedUnique = [
            [100, 91],
            [85, 1],
            [100, 92],
            [734, 12],
            //[100, 91], // not unique
            [5, 2],
        ];
        self::assertCount(5, $expectedUnique);
        self::assertSame($expectedUnique, $sheet2->getCell('H1')->getCalculatedValue());
        for ($row = 1; $row <= 5; ++$row) {
            if ($row > 1) {
                self::assertSame($expectedUnique[$row - 1][0], $sheet2->getCell("H$row")->getValue(), "cell H$row");
            } else {
                self::assertTrue($sheet2->getCell("H$row")->isFormula());
                self::assertSame($expectedUnique[$row - 1][0], $sheet2->getCell("H$row")->getOldCalculatedValue(), "cell H$row");
            }
            self::assertSame($expectedUnique[$row - 1][1], $sheet2->getCell("I$row")->getValue(), "cell I$row");
        }
        $cellFormulaAttributes = $sheet2->getCell('H1')->getFormulaAttributes();
        self::assertArrayHasKey('t', $cellFormulaAttributes);
        self::assertSame('array', $cellFormulaAttributes['t']);
        self::assertArrayHasKey('ref', $cellFormulaAttributes);
        self::assertSame('H1:I5', $cellFormulaAttributes['ref']);
        $spreadsheet2->disconnectWorksheets();
    }

    public function testMetadataWritten(): void
    {
        $spreadsheet = new Spreadsheet();
        Calculation::getInstance($spreadsheet)->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $writer = new XlsxWriter($spreadsheet);
        $writerMetadata = new XlsxWriter\Metadata($writer);
        self::assertNotEquals('', $writerMetadata->writeMetaData());
        $writer->setUseCSEArrays(true);
        $writerMetadata2 = new XlsxWriter\Metadata($writer);
        self::assertSame('', $writerMetadata2->writeMetaData());
        $spreadsheet->disconnectWorksheets();
    }

    public function testSpill(): void
    {
        $spreadsheet = new Spreadsheet();
        Calculation::getInstance($spreadsheet)->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A3')->setValue('x');
        $sheet->getCell('A1')->setValue('=UNIQUE({1;2;3})');
        $writer = new XlsxWriter($spreadsheet);
        $this->outputFile = File::temporaryFilename();
        $writer->save($this->outputFile);
        $spreadsheet->disconnectWorksheets();

        $reader = new XlsxReader();
        $spreadsheet2 = $reader->load($this->outputFile);
        Calculation::getInstance($spreadsheet2)->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $sheet2 = $spreadsheet2->getActiveSheet();
        self::assertSame('#SPILL!', $sheet2->getCell('A1')->getOldCalculatedValue());
        self::assertSame('=UNIQUE({1;2;3})', $sheet2->getCell('A1')->getValue());
        self::assertNull($sheet2->getCell('A2')->getValue());
        self::assertSame('x', $sheet2->getCell('A3')->getValue());
        $spreadsheet2->disconnectWorksheets();
    }

    public function testArrayStringOutput(): void
    {
        $spreadsheet = new Spreadsheet();
        Calculation::getInstance($spreadsheet)->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $sheet = $spreadsheet->getActiveSheet();
        $columnArray = [
            ['item1'],
            ['item2'],
            ['item3'],
            ['item1'],
            ['item1'],
            ['item6'],
            ['item7'],
            ['item1'],
            ['item9'],
            ['item1'],
        ];
        $sheet->fromArray($columnArray, 'A1');
        $sheet->setCellValue('C1', '=UNIQUE(A1:A10)');
        $writer = new XlsxWriter($spreadsheet);
        $this->outputFile = File::temporaryFilename();
        $writer->save($this->outputFile);
        $spreadsheet->disconnectWorksheets();

        $reader = new XlsxReader();
        $spreadsheet2 = $reader->load($this->outputFile);
        Calculation::getInstance($spreadsheet2)->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $sheet2 = $spreadsheet2->getActiveSheet();
        $expectedUnique = [
            ['item1'],
            ['item2'],
            ['item3'],
            ['item6'],
            ['item7'],
            ['item9'],
        ];
        self::assertCount(6, $expectedUnique);
        self::assertSame($expectedUnique, $sheet2->getCell('C1')->getCalculatedValue());
        self::assertSame($expectedUnique[0][0], $sheet2->getCell('C1')->getCalculatedValueString());
        for ($row = 2; $row <= 6; ++$row) {
            self::assertSame($expectedUnique[$row - 1][0], $sheet2->getCell("C$row")->getCalculatedValue(), "cell C$row");
        }
        $spreadsheet2->disconnectWorksheets();

        $file = 'zip://';
        $file .= $this->outputFile;
        $file .= '#xl/worksheets/sheet1.xml';
        $data = file_get_contents($file);
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertStringContainsString('<c r="C1" cm="1" t="str"><f t="array" ref="C1:C6" aca="1" ca="1">_xlfn.UNIQUE(A1:A10)</f><v>item1</v></c>', $data, '6 results for UNIQUE');
        }
    }
}
