<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods as OdsWriter;
use PHPUnit\Framework\TestCase;

class Issue4454Test extends TestCase
{
    /**
     * Text inside a string literal must be left alone, while everything
     * outside of it is still converted.
     */
    public function testCellReferencesInStringsAreNotConverted(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Worksheet');
        $sheet->setCellValue('E1', '="THIS IS E1"');
        $sheet->setCellValue('E2', '=G1');
        $sheet->setCellValue('E3', '=G1&"see E1 here"');

        $writer = new OdsWriter($spreadsheet);
        $data = (new OdsWriter\Content($writer))->write();

        self::assertStringContainsString('of:=&quot;THIS IS E1&quot;', $data);
        self::assertStringContainsString('of:=[.G1]', $data);
        self::assertStringContainsString('of:=[.G1]&amp;&quot;see E1 here&quot;', $data);

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * The comma to semicolon replacement for Ods must not reach into strings either.
     */
    public function testCommasInStringsAreNotConverted(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Worksheet');
        $sheet->setCellValue('A1', 1);
        $sheet->setCellValue('B1', '=IF(A1>1,"yes, really","no")');

        $writer = new OdsWriter($spreadsheet);
        $data = (new OdsWriter\Content($writer))->write();

        self::assertStringContainsString(
            'of:=IF([.A1]&gt;1;&quot;yes, really&quot;;&quot;no&quot;)',
            $data
        );

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * A doubled quote escapes a quote inside a string literal, so the reference
     * in the middle of this one is still part of the string.
     */
    public function testEscapedQuotesInStringsAreHandled(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Worksheet');
        $sheet->setCellValue('A1', '=CONCAT("a""b E1",B2)');

        $writer = new OdsWriter($spreadsheet);
        $data = (new OdsWriter\Content($writer))->write();

        self::assertStringContainsString(
            'of:=CONCAT(&quot;a&quot;&quot;b E1&quot;;[.B2])',
            $data
        );

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Ranges are unaffected: a string literal can never interrupt one.
     */
    public function testRangesAreStillConverted(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Worksheet');
        $sheet->setCellValue('A1', '=SUM(B1:B5)');

        $writer = new OdsWriter($spreadsheet);
        $data = (new OdsWriter\Content($writer))->write();

        self::assertStringContainsString('of:=SUM([.B1:.B5])', $data);

        $spreadsheet->disconnectWorksheets();
    }
}
