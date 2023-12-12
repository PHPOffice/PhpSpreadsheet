<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Reader\Ods as ReaderOds;
use PhpOffice\PhpSpreadsheet\Reader\Slk as ReaderSlk;
use PhpOffice\PhpSpreadsheet\Reader\Xls as ReaderXls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as ReaderXlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xml as ReaderXml;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as WriterXlsx;

class TypeAttributePreservationTest extends AbstractFunctional
{
    public static function providerFormulae(): array
    {
        $formats = ['Xlsx'];
        $data = require 'tests/data/Functional/TypeAttributePreservation/Formula.php';

        $result = [];
        foreach ($formats as $f) {
            foreach ($data as $d) {
                $result[] = [$f, $d];
            }
        }

        return $result;
    }

    /**
     * Ensure saved spreadsheets maintain the correct data type.
     *
     * @dataProvider providerFormulae
     */
    public function testFormulae(string $format, array $values): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($values);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format);
        $reloadedSheet = $reloadedSpreadsheet->getActiveSheet();

        $expected = $sheet->getCell('A1')->getCalculatedValue();

        if ($sheet->getCell('A1')->getDataType() === 'f') {
            $actual = $reloadedSheet->getCell('A1')->getOldCalculatedValue();
        } else {
            $actual = $reloadedSheet->getCell('A1')->getValue();
        }

        self::assertSame($expected, $actual);
        $spreadsheet->disconnectWorksheets();
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public static function customizeWriter(WriterXlsx $writer): void
    {
        $writer->setPreCalculateFormulas(false);
    }

    /**
     * Ensure saved spreadsheets maintain the correct data type.
     *
     * @dataProvider providerFormulae
     */
    public function testFormulaeNoPrecalc(string $format, array $values): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($values);

        /** @var callable */
        $writerCustomizer = [self::class, 'customizeWriter'];
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format, null, $writerCustomizer);
        $reloadedSheet = $reloadedSpreadsheet->getActiveSheet();

        $expected = $sheet->getCell('A1')->getCalculatedValue();

        if ($sheet->getCell('A1')->getDataType() === 'f') {
            $actual = $reloadedSheet->getCell('A1')->getOldCalculatedValue();
            self::assertNull($actual);
        } else {
            $actual = $reloadedSheet->getCell('A1')->getValue();
            self::assertSame($expected, $actual);
        }

        $spreadsheet->disconnectWorksheets();
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testUnimplementedFunctionXlsx(): void
    {
        $file = 'tests/data/Reader/XLSX/issue.3658.xlsx';
        $reader = new ReaderXlsx();
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $unimplemented = '=INFO("SYSTEM")';
        self::assertSame(124, $sheet->getCell('A2')->getOldCalculatedValue());
        self::assertSame('00123', $sheet->getCell('A3')->getOldCalculatedValue());
        self::assertSame($unimplemented, $sheet->getCell('A4')->getValue());
        self::assertSame('pcdos', $sheet->getCell('A4')->getCalculatedValue(), 'use oldCalculatedValue from read for unimplemented function');
        $sheet->getCell('D10')->setValue($unimplemented);
        self::assertNull($sheet->getCell('D10')->getCalculatedValue(), 'return null for unimplemented function when old value unassigned');
        $spreadsheet->disconnectWorksheets();
    }

    public function testUnimplementedFunctionXls(): void
    {
        $file = 'tests/data/Reader/XLS/issue.3658.xls';
        $reader = new ReaderXls();
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $unimplemented = '=INFO("SYSTEM")';
        self::assertSame(124, $sheet->getCell('A2')->getOldCalculatedValue());
        self::assertSame('00123', $sheet->getCell('A3')->getOldCalculatedValue());
        self::assertSame($unimplemented, $sheet->getCell('A4')->getValue());
        self::assertSame('pcdos', $sheet->getCell('A4')->getCalculatedValue(), 'use oldCalculatedValue from read for unimplemented function');
        $sheet->getCell('D10')->setValue($unimplemented);
        self::assertNull($sheet->getCell('D10')->getCalculatedValue(), 'return null for unimplemented function when old value unassigned');
        $spreadsheet->disconnectWorksheets();
    }

    public function testUnimplementedFunctionXml(): void
    {
        $file = 'tests/data/Reader/Xml/issue.3658.xml';
        $reader = new ReaderXml();
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $unimplemented = '=INFO("SYSTEM")';
        self::assertSame(124, $sheet->getCell('A2')->getOldCalculatedValue());
        self::assertSame('00123', $sheet->getCell('A3')->getOldCalculatedValue());
        self::assertSame($unimplemented, $sheet->getCell('A4')->getValue());
        self::assertSame('pcdos', $sheet->getCell('A4')->getCalculatedValue(), 'use oldCalculatedValue from read for unimplemented function');
        $sheet->getCell('D10')->setValue($unimplemented);
        self::assertNull($sheet->getCell('D10')->getCalculatedValue(), 'return null for unimplemented function when old value unassigned');
        $spreadsheet->disconnectWorksheets();
    }

    public function testUnimplementedFunctionOds(): void
    {
        $file = 'tests/data/Reader/Ods/issue.3658.ods';
        $reader = new ReaderOds();
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $unimplemented = '=INFO("SYSTEM")';
        self::assertSame(124, $sheet->getCell('A2')->getOldCalculatedValue());
        self::assertSame('00123', $sheet->getCell('A3')->getOldCalculatedValue());
        self::assertSame($unimplemented, $sheet->getCell('A4')->getValue());
        self::assertSame('WNT', $sheet->getCell('A4')->getCalculatedValue(), 'use oldCalculatedValue from read for unimplemented function');
        $sheet->getCell('D10')->setValue($unimplemented);
        self::assertNull($sheet->getCell('D10')->getCalculatedValue(), 'return null for unimplemented function when old value unassigned');
        $spreadsheet->disconnectWorksheets();
    }

    public function testUnimplementedFunctionSlk(): void
    {
        $file = 'tests/data/Reader/Slk/issue.3658.slk';
        $reader = new ReaderSlk();
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $unimplemented = '=INFO("SYSTEM")';
        self::assertSame(124, $sheet->getCell('A2')->getOldCalculatedValue());
        self::assertSame('00123', $sheet->getCell('A3')->getOldCalculatedValue());
        self::assertSame($unimplemented, $sheet->getCell('A4')->getValue());
        self::assertSame('pcdos', $sheet->getCell('A4')->getCalculatedValue(), 'use oldCalculatedValue from read for unimplemented function');
        $sheet->getCell('D10')->setValue($unimplemented);
        self::assertNull($sheet->getCell('D10')->getCalculatedValue(), 'return null for unimplemented function when old value unassigned');
        $spreadsheet->disconnectWorksheets();
    }
}
