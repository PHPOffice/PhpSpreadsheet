<?php

declare(strict_types=1);

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
        self::assertFalse($sheet->getCell('A1')->getIgnoredErrors()->getNumberStoredAsText());
        self::assertTrue($sheet->getCell('A2')->getIgnoredErrors()->getNumberStoredAsText());
        self::assertFalse($sheet->getCell('A3')->getIgnoredErrors()->getNumberStoredAsText());
        self::assertTrue($sheet->getCell('A4')->getIgnoredErrors()->getNumberStoredAsText());
        self::assertFalse($sheet->getCell('H2')->getIgnoredErrors()->getNumberStoredAsText());
        self::assertTrue($sheet->getCell('H3')->getIgnoredErrors()->getNumberStoredAsText());
        self::assertFalse($sheet->getCell('I2')->getIgnoredErrors()->getNumberStoredAsText());
        self::assertTrue($sheet->getCell('I3')->getIgnoredErrors()->getNumberStoredAsText());

        self::assertFalse($sheet->getCell('H3')->getIgnoredErrors()->getFormula());
        self::assertFalse($sheet->getCell('D2')->getIgnoredErrors()->getFormula());
        self::assertTrue($sheet->getCell('D3')->getIgnoredErrors()->getFormula());

        self::assertFalse($sheet->getCell('A11')->getIgnoredErrors()->getTwoDigitTextYear());
        self::assertTrue($sheet->getCell('A12')->getIgnoredErrors()->getTwoDigitTextYear());

        self::assertFalse($sheet->getCell('C12')->getIgnoredErrors()->getEvalError());
        self::assertTrue($sheet->getCell('C11')->getIgnoredErrors()->getEvalError());

        $sheetLast = $spreadsheet->getSheetByNameOrThrow('Last');
        self::assertFalse($sheetLast->getCell('D2')->getIgnoredErrors()->getFormula());
        self::assertFalse($sheetLast->getCell('D3')->getIgnoredErrors()->getFormula(), 'prior sheet ignoredErrors shouldn\'t bleed');
        self::assertFalse($sheetLast->getCell('A1')->getIgnoredErrors()->getNumberStoredAsText());
        self::assertFalse($sheetLast->getCell('A2')->getIgnoredErrors()->getNumberStoredAsText());
        self::assertTrue($sheetLast->getCell('A3')->getIgnoredErrors()->getNumberStoredAsText());
        self::assertFalse($sheetLast->getCell('A4')->getIgnoredErrors()->getNumberStoredAsText(), 'prior sheet numberStoredAsText shouldn\'t bleed');

        $spreadsheet->disconnectWorksheets();
    }

    public function testSetIgnoredError(): void
    {
        $originalSpreadsheet = new Spreadsheet();
        $originalSheet = $originalSpreadsheet->getActiveSheet();
        $originalSheet->getCell('A1')->setValueExplicit('0', DataType::TYPE_STRING);
        $originalSheet->getCell('A2')->setValueExplicit('1', DataType::TYPE_STRING);
        $originalSheet->getStyle('A1:A2')->setQuotePrefix(true);
        $originalSheet->getCell('A2')->getIgnoredErrors()->setNumberStoredAsText(true);
        $spreadsheet = $this->writeAndReload($originalSpreadsheet, 'Xlsx');
        $originalSpreadsheet->disconnectWorksheets();
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('0', $sheet->getCell('A1')->getValue());
        self::assertSame('1', $sheet->getCell('A2')->getValue());
        self::assertFalse($sheet->getCell('A1')->getIgnoredErrors()->getNumberStoredAsText());
        self::assertTrue($sheet->getCell('A2')->getIgnoredErrors()->getNumberStoredAsText());
        $spreadsheet->disconnectWorksheets();
    }
}
