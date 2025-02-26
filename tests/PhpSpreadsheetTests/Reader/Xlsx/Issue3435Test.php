<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Issue3435Test extends \PHPUnit\Framework\TestCase
{
    private static string $testbook = 'tests/data/Reader/XLSX/issue.3435.xlsx';

    public function testPreliminaries(): void
    {
        $file = 'zip://';
        $file .= self::$testbook;
        $file .= '#xl/styles.xml';
        $data = file_get_contents($file);
        // confirm that file contains expected namespaced xml tag
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            $expected = <<<EOF
                <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
                <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0" quotePrefix="1"/>
                <xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" quotePrefix="0" applyFont="1" applyFill="1" applyBorder="1"/>
                EOF;
            self::assertStringContainsString($expected, $data);
        }
    }

    public function testQuotePrefix(): void
    {
        // Parsing of quotePrefix="0" was incorrect, now corrected.
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::$testbook);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertTrue($sheet->getStyle('A1')->getQuotePrefix());
        self::assertFalse($sheet->getStyle('A2')->getQuotePrefix());
        self::assertFalse($sheet->getStyle('A3')->getQuotePrefix());
        $spreadsheet->disconnectWorksheets();
    }
}
