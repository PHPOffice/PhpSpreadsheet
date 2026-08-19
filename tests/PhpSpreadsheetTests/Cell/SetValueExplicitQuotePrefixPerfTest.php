<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class SetValueExplicitQuotePrefixPerfTest extends TestCase
{
    public function testQuotePrefixAppliedForLeadingEqualsString(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setSelectedCells('Z99');
        $spreadsheet->createSheet()->setTitle('Other');
        $spreadsheet->setActiveSheetIndex(1);

        $cell = $sheet->getCell('A1');
        $cell->setValueExplicit('=not-a-formula', DataType::TYPE_STRING);

        // Read quotePrefix from the cell xf without going through Worksheet::getStyle()
        // (which would change the selection).
        $xf = $spreadsheet->getCellXfByIndex($sheet->getCell('A1')->getXfIndex());
        self::assertTrue($xf->getQuotePrefix());
        self::assertSame('Z99', $sheet->getSelectedCells());
        self::assertSame(1, $spreadsheet->getActiveSheetIndex());

        $spreadsheet->disconnectWorksheets();
    }

    public function testQuotePrefixNotAppliedForNormalString(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValueExplicit('hello', DataType::TYPE_STRING);

        $xf = $spreadsheet->getCellXfByIndex($sheet->getCell('A1')->getXfIndex());
        self::assertFalse($xf->getQuotePrefix());

        $spreadsheet->disconnectWorksheets();
    }

    public function testQuotePrefixClearedWhenReplacingPrefixedString(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValueExplicit('=prefixed', DataType::TYPE_STRING);
        self::assertTrue($spreadsheet->getCellXfByIndex($sheet->getCell('A1')->getXfIndex())->getQuotePrefix());

        $sheet->getCell('A1')->setValueExplicit('plain', DataType::TYPE_STRING);
        self::assertFalse($spreadsheet->getCellXfByIndex($sheet->getCell('A1')->getXfIndex())->getQuotePrefix());

        $spreadsheet->disconnectWorksheets();
    }

    public function testQuotePrefixClearedWhenReplacingWithNumericValue(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValueExplicit('=prefixed', DataType::TYPE_STRING);
        self::assertTrue($spreadsheet->getCellXfByIndex($sheet->getCell('A1')->getXfIndex())->getQuotePrefix());

        $sheet->getCell('A1')->setValueExplicit(42, DataType::TYPE_NUMERIC);

        self::assertFalse($spreadsheet->getCellXfByIndex($sheet->getCell('A1')->getXfIndex())->getQuotePrefix());
        self::assertSame(42, $sheet->getCell('A1')->getValue());

        $spreadsheet->disconnectWorksheets();
    }
}
