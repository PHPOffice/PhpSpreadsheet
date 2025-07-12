<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ReplaceBuiltinNumberFormatTest extends AbstractFunctional
{
    private ?Spreadsheet $spreadsheet = null;

    private ?Spreadsheet $reloadedSpreadsheet = null;

    protected function tearDown(): void
    {
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
        if ($this->reloadedSpreadsheet !== null) {
            $this->reloadedSpreadsheet->disconnectWorksheets();
            $this->reloadedSpreadsheet = null;
        }
    }

    public function testReplaceBuiltinNumberFormat(): void
    {
        $spreadsheet = $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->fromArray([45486, 1023, 45487, 45488, 45489]);
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
        $values = $sheet->toArray();
        $expected = [[
            '7/13/2024', // builtin style 14
            '1,023.00', // #,##0.00
            '7/14/2024', // builtin style 14
            '15-Jul-2024', // dd-MMM-yyyy
            '16-Jul', // builtin style 16
        ]];
        self::assertSame($expected, $values);
        $this->reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $this->reloadedSpreadsheet->replaceBuiltinNumberFormat(
            NumberFormat::SHORT_DATE_INDEX,
            'yyyy-mm-dd'
        );
        $rsheet = $this->reloadedSpreadsheet->getActiveSheet();
        $newValues = $rsheet->toArray();
        $newExpected = [[
            '2024-07-13', // yyyy-mm-dd changed from builtin style 14
            '1,023.00', // unchanged #,##0.00
            '2024-07-14', // yyyy-mm-dd changed from builtin style 14
            '15-Jul-2024', // unchanged dd-MMM-yyyy
            '16-Jul', // unchanged builtin style 16
        ]];
        self::assertSame($newExpected, $newValues);
    }
}
