<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class ShrinkRangeToFitTest extends TestCase
{
    public static function testToArray(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('B2')->setValue(1);
        $sheet->getCell('E10')->setValue(2);
        $testArray = [
            'C3:F12' => 'C3:E10',
            'C3:D12' => 'C3:D10',
            'C3:D9' => 'C3:D9',
            'A1:B2 C3:D4' => 'A1:B2 C3:D4',
            'A1:B11 C3:D4' => 'A1:B10 C3:D4',
            'A1:B2 C3:F12' => 'A1:B2 C3:E10',
            'A1:B11 C3:F12' => 'A1:B10 C3:E10',
            // In both of the following, the range
            //    isn't merely shrunk - it has moved.
            // This doesn't seem right, although I am
            //    hard-pressed to come up with an alternative,
            //    and not willing to make a breaking change,
            //    even to code which probably isn't used much.
            'A11:B12' => 'A10:B10',
            'G1:H4' => 'E1:E4',
        ];
        foreach ($testArray as $input => $expectedOutput) {
            $output = $sheet->shrinkRangeToFit($input);
            self::assertSame($expectedOutput, $output, $input);
        }
        $spreadsheet->disconnectWorksheets();
    }
}
