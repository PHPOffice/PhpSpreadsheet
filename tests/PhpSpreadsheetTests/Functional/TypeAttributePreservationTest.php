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
use PHPUnit\Framework\Attributes\DataProvider;

class TypeAttributePreservationTest extends AbstractFunctional
{
    public static function providerFormulae(): array
    {
        $formats = ['Xlsx'];
        /** @var mixed[] */
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
     * @param mixed[] $values
     */
    #[DataProvider('providerFormulae')]
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
     * @param mixed[] $values
     */
    #[DataProvider('providerFormulae')]
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
}
