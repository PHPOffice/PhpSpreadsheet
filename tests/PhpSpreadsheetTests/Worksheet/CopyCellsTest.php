<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class CopyCellsTest extends TestCase
{
    public function testCopyCells(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(
            [
                ['hello1', 'goodbye1', 'neither1', 'constant'],
                ['hello2', 'goodbye2', 'neither2'],
                ['hello3', 'goodbye3', 'neither3'],
                ['hello4', 'goodbye4', 'neither4'],
                ['hello5', 'goodbye5', 'neither5'],
            ],
        );
        $sheet->getCell('E3')->setValue('=A1&B1');
        $sheet->getStyle('E3')->getFont()->setBold(true);
        $sheet->copyCells('E3', 'E3:F7');
        $result1 = $sheet->rangeToArray('E3:F7', null, false, false);
        $expected1 = [
            ['=A1&B1', '=B1&C1'],
            ['=A2&B2', '=B2&C2'],
            ['=A3&B3', '=B3&C3'],
            ['=A4&B4', '=B4&C4'],
            ['=A5&B5', '=B5&C5'],
        ];
        self::assertSame($expected1, $result1);
        self::assertSame('goodbye3neither3', $sheet->getCell('F5')->getCalculatedValue());
        self::assertTrue($sheet->getCell('F5')->getStyle()->getFont()->getBold());

        $sheet->getCell('E14')->setValue('=A5&$D$1');
        $sheet->copyCells('E14', 'E10:E14');
        $result2 = $sheet->rangeToArray('E10:E14', null, false, false);
        $expected2 = [
            ['=A1&$D$1'],
            ['=A2&$D$1'],
            ['=A3&$D$1'],
            ['=A4&$D$1'],
            ['=A5&$D$1'],
        ];
        self::assertSame($expected2, $result2);
        self::assertSame('hello4constant', $sheet->getCell('E13')->getCalculatedValue());

        $sheet->getCell('I3')->setValue('=A1&$B1');
        $sheet->getStyle('I3')->getFont()->setItalic(true);
        $sheet->copyCells('I3', 'I3:J7', false);
        $result3 = $sheet->rangeToArray('I3:J7', null, false, false);
        $expected3 = [
            ['=A1&$B1', '=B1&$B1'],
            ['=A2&$B2', '=B2&$B2'],
            ['=A3&$B3', '=B3&$B3'],
            ['=A4&$B4', '=B4&$B4'],
            ['=A5&$B5', '=B5&$B5'],
        ];
        self::assertSame($expected3, $result3);
        self::assertSame('hello2goodbye2', $sheet->getCell('I4')->getCalculatedValue());
        self::assertFalse($sheet->getCell('I5')->getStyle()->getFont()->getItalic());

        try {
            $sheet->copyCells('invalid', 'Z1:Z10');
            self::fail('Did not receive expected exception');
        } catch (SpreadsheetException $e) {
            self::assertStringContainsString('Invalid cell coordinate', $e->getMessage());
        }

        try {
            $sheet->copyCells('A1', 'invalid');
            self::fail('Did not receive expected exception');
        } catch (SpreadsheetException $e) {
            self::assertStringContainsString('Column string index', $e->getMessage());
        }

        $spreadsheet->disconnectWorksheets();
    }
}
