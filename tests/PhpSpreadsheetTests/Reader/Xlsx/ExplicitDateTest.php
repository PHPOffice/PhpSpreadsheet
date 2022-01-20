<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;

class ExplicitDateTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    private static $testbook = 'tests/data/Reader/XLSX/explicitdate.xlsx';

    public function testPreliminaries(): void
    {
        $file = 'zip://';
        $file .= self::$testbook;
        $file .= '#xl/worksheets/sheet1.xml';
        $data = file_get_contents($file);
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            // confirm that file contains type "d" cells
            self::assertStringContainsString('<c r="A3" s="1" t="d"><v>2021-12-31T23:44:51.894</v></c>', $data);
            self::assertStringContainsString('<c r="B3" s="2" t="d"><v>2021-12-31</v></c>', $data);
            self::assertStringContainsString('<c r="C3" s="3" t="d"><v>23:44:51.894</v></c>', $data);
        }
    }

    public static function testExplicitDate(): void
    {
        $spreadsheet = IOFactory::load(self::$testbook);
        $sheet = $spreadsheet->getActiveSheet();
        // DateTime
        $value = $sheet->getCell('A3')->getValue();
        $formatted = $sheet->getCell('A3')->getFormattedValue();
        self::assertEqualsWithDelta(44561.98948, $value, 0.00001);
        self::assertSame('2021-12-31 23:44:51', $formatted);
        // Date only
        $value = $sheet->getCell('B3')->getValue();
        $formatted = $sheet->getCell('B3')->getFormattedValue();
        self::assertEquals(44561, $value);
        self::assertSame('2021-12-31', $formatted);
        // Time only
        $value = $sheet->getCell('C3')->getValue();
        $formatted = $sheet->getCell('C3')->getFormattedValue();
        self::assertEqualsWithDelta(0.98948, $value, 0.00001);
        self::assertSame('23:44:51', $formatted);

        $spreadsheet->disconnectWorksheets();
    }
}
