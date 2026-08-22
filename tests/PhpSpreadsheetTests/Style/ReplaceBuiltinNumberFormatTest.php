<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Style;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PHPUnit\Framework\TestCase;

class ReplaceBuiltinNumberFormatTest extends TestCase
{
    private ?Spreadsheet $spreadsheet = null;

    protected function tearDown(): void
    {
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
    }

    public function testReplaceBuiltinNumberFormat(): void
    {
        $spreadsheet = $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->fromArray([45486, 1023, 45487, 45488, 45489, 45480, 45479, 45478, 45477, 45476, 45475]);
        $sheet->getStyle('A1')->getNumberFormat()
            ->setBuiltInFormatCode(NumberFormat::SHORT_DATE_INDEX);
        $sheet->getStyle('B1')->getNumberFormat()
            ->setFormatCode('#,##0.00');
        $sheet->getStyle('C1')->getNumberFormat()
            ->setBuiltInFormatCode(NumberFormat::SHORT_DATE_INDEX);
        $sheet->getStyle('D1')->getNumberFormat()
            ->setFormatCode('dd-MMM-yyyy');
        $sheet->getStyle('E1')->getNumberFormat()
            ->setBuiltInFormatCode(16);
        $sheet->getStyle('F1')->getNumberFormat()
            ->setFormatCode('d-MMM-yy');
        $sheet->getStyle('G1')->getNumberFormat()
            ->setFormatCode('dd/m/yy');
        $sheet->getStyle('H1')->getNumberFormat()
            ->setFormatCode('mm-dd-yy');
        $sheet->getStyle('I1')->getNumberFormat()
            ->setFormatCode('yy-mm-dd');
        $sheet->getStyle('J1')->getNumberFormat()
            ->setFormatCode('m/d/yyyy h:mm');
        $sheet->getStyle('K1')->getNumberFormat()
            ->setFormatCode('d"/"m"/"yy');
        $values = $sheet->toArray();
        $expected = [[
            '7/13/2024', // builtin style 14
            '1,023.00', // #,##0.00
            '7/14/2024', // builtin style 14
            '15-Jul-2024', // dd-MMM-yyyy
            '16-Jul', // builtin style 16
            '7-Jul-24', // d-MMM-yy
            '06/7/24', // dd/m/yy
            '07-05-24', // mm-dd-yy
            '24-07-04', // yy-mm-dd
            '7/3/2024 0:00', // m/d/yyyy h:mm
            '2/7/24', // d"/"m"/"yy
        ]];
        self::assertSame($expected, $values);
        $spreadsheet->replaceBuiltinNumberFormat(
            NumberFormat::SHORT_DATE_INDEX,
            'yyyy-mm-dd'
        );
        $newValues = $sheet->toArray();
        $newExpected = [[
            '2024-07-13', // yyyy-mm-dd changed from builtin style 14
            '1,023.00', // unchanged #,##0.00
            '2024-07-14', // yyyy-mm-dd changed from builtin style 14
            '15-Jul-2024', // unchanged dd-MMM-yyyy
            '16-Jul', // unchanged builtin style 16
            '7-Jul-24', // unchanged d-MMM-yy
            '06/7/24', // unchanged dd/m/yy
            '07-05-24', // unchanged mm-dd-yy
            '24-07-04', // unchanged yy-mm-dd
            '7/3/2024 0:00', // unchanged m/d/yyyy h:mm
            '2/7/24', // unchanged d"/"m"/"yy
        ]];
        self::assertSame($newExpected, $newValues);
        $spreadsheet->disambiguateDateStyles();
        $newValues2 = $sheet->toArray();
        $newExpected2 = [[
            '2024-07-13', // unchanged yyyy-mm-dd
            '1,023.00', // unchanged #,##0.00
            '2024-07-14', // unchanged yyyy-mm-dd
            '15-Jul-2024', // unchanged dd-MMM-yyyy
            '16-Jul', // unchanged builtin style 16
            '7-Jul-2024', // changed from d-MMM-yy to d-MMM-yyyy
            '2024-07-06', // changed from dd/m/yy to yyyy-mm-dd
            '2024-07-05', // changed from mm-dd-yy tp yyyy-mm-dd
            '2024-07-04', // changed from yy-mm-dd to yyyy-mm-dd
            '2024-07-03 0:00', // changed m/d/yyyy h:mm to yyyy-mm-dd h:mm
            '2024-07-02', // changed d"/"m"/"yy to yyyy-mm-dd
        ]];
        self::assertSame($newExpected2, $newValues2);
    }
}
