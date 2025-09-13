<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Issue1324Test extends AbstractFunctional
{
    public function testPrecision(): void
    {
        $string1 = '753.149999999999';
        $s1 = (float) $string1;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue($s1);
        self::assertNotSame(753.15, $sheet->getCell('A1')->getValue());
        $formats = ['Csv', 'Xlsx', 'Xls', 'Ods', 'Html'];
        foreach ($formats as $format) {
            $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format);
            $rsheet = $reloadedSpreadsheet->getActiveSheet();
            $s2 = $rsheet->getCell('A1')->getValue();
            self::assertSame($s1, $s2, "difference for $format");
            $reloadedSpreadsheet->disconnectWorksheets();
        }
        $spreadsheet->disconnectWorksheets();
    }

    public function testCsv(): void
    {
        $string1 = '753.149999999999';
        $s1 = (float) $string1;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue($s1);
        $writer = new CsvWriter($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $output = (string) ob_get_clean();
        self::assertStringContainsString($string1, $output);
        $spreadsheet->disconnectWorksheets();
    }

    public static function testLessPrecision(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $inArray = [
            [123.4, 753.149999999999, 12.04, 224.245],
            [12345.6789, 44125.62188657407387],
            [true, 16, 'hello'],
            [753.149999999999, 44125.62188657407387],
        ];
        $sheet->fromArray($inArray, null, 'A1', true);
        $sheet->getStyle('A4:B4')->getNumberFormat()
            ->setFormatCode('#.##');
        $precise = $sheet->toArray();
        $expectedPrecise = [
            ['123.40000000000001', '753.14999999999895', '12.039999999999999', '224.245'],
            ['12345.67890000000079', '44125.62188657407387', null, null],
            ['TRUE', '16', 'hello', null],
            ['753.15', '44125.62', null, null],
        ];
        self::assertSame($expectedPrecise, $precise);
        $lessPrecise = $sheet->toArray(lessFloatPrecision: true);
        $expectedLessPrecise = [
            ['123.4', '753.15', '12.04', '224.245'],
            ['12345.6789', '44125.621886574', null, null],
            ['TRUE', '16', 'hello', null],
            ['753.15', '44125.62', null, null],
        ];
        self::assertSame($expectedLessPrecise, $lessPrecise);
        $spreadsheet->disconnectWorksheets();
        $preg = '/^[-+]?\d+[.]\d+$/';
        $diffs = [];
        for ($i = 0; $i < 2; ++$i) {
            $preciseRow = $precise[$i];
            $lessPreciseRow = $lessPrecise[$i];
            $entries = count($preciseRow);
            for ($j = 0; $j < $entries; ++$j) {
                $comparand1 = $preciseRow[$j];
                $comparand2 = $lessPreciseRow[$j];
                if (preg_match($preg, $comparand1 ?? '') && preg_match($preg, $comparand2 ?? '')) {
                    $comparand1 = (float) $comparand1;
                    $comparand2 = (float) $comparand2;
                }
                if ($comparand1 !== $comparand2) {
                    $diffs[] = [$i, $j];
                }
            }
        }
        self::assertSame([[0, 1], [1, 1]], $diffs, 'cells which do not evaluate to same float value');
    }
}
