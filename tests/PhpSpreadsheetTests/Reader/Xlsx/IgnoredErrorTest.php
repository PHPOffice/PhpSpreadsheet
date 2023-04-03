<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class IgnoredErrorTest extends AbstractFunctional
{
    private const FILENAME = 'tests/data/Reader/XLSX/ignoreerror.xlsx';

    public function testIgnoredError(): void
    {
        $reader = new Xlsx();
        $originalSpreadsheet = $reader->load(self::FILENAME);
        $spreadsheet = $this->writeAndReload($originalSpreadsheet, 'Xlsx');
        $originalSpreadsheet->disconnectWorksheets();
        $sheet = $spreadsheet->getActiveSheet();
        self::assertFalse($sheet->getCell('A1')->getIgnoredErrorNumberStoredAsText());
        self::assertTrue($sheet->getCell('A2')->getIgnoredErrorNumberStoredAsText());
        self::assertFalse($sheet->getCell('H2')->getIgnoredErrorNumberStoredAsText());
        self::assertTrue($sheet->getCell('H3')->getIgnoredErrorNumberStoredAsText());
        self::assertFalse($sheet->getCell('I2')->getIgnoredErrorNumberStoredAsText());
        self::assertTrue($sheet->getCell('I3')->getIgnoredErrorNumberStoredAsText());

        self::assertFalse($sheet->getCell('H3')->getIgnoredErrorFormula());
        self::assertFalse($sheet->getCell('D2')->getIgnoredErrorFormula());
        self::assertTrue($sheet->getCell('D3')->getIgnoredErrorFormula());

        self::assertFalse($sheet->getCell('A11')->getIgnoredErrorTwoDigitTextYear());
        self::assertTrue($sheet->getCell('A12')->getIgnoredErrorTwoDigitTextYear());

        self::assertFalse($sheet->getCell('C12')->getIgnoredErrorEvalError());
        self::assertTrue($sheet->getCell('C11')->getIgnoredErrorEvalError());

        $spreadsheet->disconnectWorksheets();
    }

    public function testSetIgnoredError(): void
    {
        $originalSpreadsheet = new Spreadsheet();
        $originalSheet = $originalSpreadsheet->getActiveSheet();
        $originalSheet->getCell('A1')->setValueExplicit('0', DataType::TYPE_STRING);
        $originalSheet->getCell('A2')->setValueExplicit('1', DataType::TYPE_STRING);
        $originalSheet->getStyle('A1:A2')->setQuotePrefix(true);
        $originalSheet->getCell('A2')->setIgnoredErrorNumberStoredAsText(true);
        $spreadsheet = $this->writeAndReload($originalSpreadsheet, 'Xlsx');
        $originalSpreadsheet->disconnectWorksheets();
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('0', $sheet->getCell('A1')->getValue());
        self::assertSame('1', $sheet->getCell('A2')->getValue());
        self::assertFalse($sheet->getCell('A1')->getIgnoredErrorNumberStoredAsText());
        self::assertTrue($sheet->getCell('A2')->getIgnoredErrorNumberStoredAsText());
        $spreadsheet->disconnectWorksheets();
    }
}
