<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Xml;
use PHPUnit\Framework\TestCase;

class ArrayFormulaTest extends TestCase
{
    public function testArrayFormulaReader(): void
    {
        $filename = 'tests/data/Reader/Xml/ArrayFormula.xml';
        $reader = new Xml();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();

        self::assertSame(DataType::TYPE_FORMULA, $sheet->getCell('B1')->getDataType());
        self::assertSame(['t' => 'array', 'ref' => 'B1:B3'], $sheet->getCell('B1')->getFormulaAttributes());
        self::assertSame('=CONCATENATE(A1:A3,"-",C1:C3)', strtoupper($sheet->getCell('B1')->getValue()));
        self::assertSame('a-1', $sheet->getCell('B1')->getOldCalculatedValue());
        self::assertSame('b-2', $sheet->getCell('B2')->getValue());
        self::assertSame('c-3', $sheet->getCell('B3')->getValue());
        $spreadsheet->disconnectWorksheets();
    }
}
