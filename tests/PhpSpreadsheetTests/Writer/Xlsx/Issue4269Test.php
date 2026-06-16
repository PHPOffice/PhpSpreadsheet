<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class Issue4269Test extends TestCase
{
    private string $outputFile = '';

    private static bool $alwaysFalse = false;

    protected function tearDown(): void
    {
        if ($this->outputFile !== '') {
            unlink($this->outputFile);
            $this->outputFile = '';
        }
    }

    #[DataProvider('validationProvider')]
    public function testWriteArrayFormulaTextJoin(
        bool $preCalculateFormulas,
        ?bool $forceFullCalc,
        string $expected
    ): void {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', '=A2*2');
        $sheet->setCellValue('A2', 0);

        $writer = new XlsxWriter($spreadsheet);
        $writer->setForceFullCalc($forceFullCalc);
        $this->outputFile = File::temporaryFilename();
        if ($preCalculateFormulas) {
            $writer->save($this->outputFile);
        } else {
            $writer->save($this->outputFile, XlsxWriter::DISABLE_PRECALCULATE_FORMULAE);
        }
        $spreadsheet->disconnectWorksheets();

        $file = 'zip://';
        $file .= $this->outputFile;
        $file .= '#xl/workbook.xml';
        $data = file_get_contents($file);
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertStringContainsString($expected, $data);
        }
    }

    /** @return array<string, array<int, mixed>> */
    public static function validationProvider(): array
    {
        return [
            'normal case' => [true, null, 'calcMode="auto" calcCompleted="1" fullCalcOnLoad="0" forceFullCalc="0"'],
            'issue 456' => [false, null, 'calcMode="auto" calcCompleted="0" fullCalcOnLoad="1" forceFullCalc="1"'],
            'better choice for no precalc' => [false, false, 'calcMode="auto" calcCompleted="0" fullCalcOnLoad="1" forceFullCalc="0"'],
            'unlikely use case' => [true, true, 'calcMode="auto" calcCompleted="1" fullCalcOnLoad="0" forceFullCalc="1"'],
        ];
    }

    public function testDefault(): void
    {
        self::assertSame(self::$alwaysFalse, XlsxWriter::DEFAULT_FORCE_FULL_CALC);
    }
}
