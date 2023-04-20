<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\ColumnDimension;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class PreCalcTest extends AbstractFunctional
{
    /** @var string */
    private $outfile = '';

    protected function tearDown(): void
    {
        if ($this->outfile !== '') {
            unlink($this->outfile);
            $this->outfile = '';
        }
    }

    public static function providerPreCalc(): array
    {
        return [
            [true, 'Xlsx'],
            [false, 'Xlsx'],
            [null, 'Xlsx'],
            [true, 'Xls'],
            [false, 'Xls'],
            [null, 'Xls'],
            [true, 'Ods'],
            [false, 'Ods'],
            [null, 'Ods'],
            [true, 'Html'],
            [false, 'Html'],
            [null, 'Html'],
            [true, 'Csv'],
            [false, 'Csv'],
            [null, 'Csv'],
        ];
    }

    private static function autoSize(?ColumnDimension $columnDimension): void
    {
        if ($columnDimension === null) {
            self::fail('Unable to getColumnDimension');
        } else {
            $columnDimension->setAutoSize(true);
        }
    }

    private static function verifyA2(Calculation $calculation, string $title, ?bool $preCalc): void
    {
        $cellValue = 0;
        // A2 has no cached calculation value if preCalc is false
        if ($preCalc === false) {
            self::assertFalse($calculation->getValueFromCache("$title!A2", $cellValue));
        } else {
            self::assertTrue($calculation->getValueFromCache("$title!A2", $cellValue));
            self::assertSame(3, $cellValue);
        }
    }

    private const AUTOSIZE_TYPES = ['Xlsx', 'Xls', 'Html', 'Ods'];

    private static function verifyA3B2(Calculation $calculation, string $title, ?bool $preCalc, string $type): void
    {
        $cellValue = 0;
        if (in_array($type, self::AUTOSIZE_TYPES) || $preCalc !== false) {
            // These 3 types support auto-sizing.
            // A3 has cached calculation value because it is used in B2 calculation
            self::assertTrue($calculation->getValueFromCache("$title!A3", $cellValue));
            self::assertSame(11, $cellValue);
            // B2 has cached calculation value because its column is auto-sized
            self::assertTrue($calculation->getValueFromCache("$title!B2", $cellValue));
            self::assertSame(14, $cellValue);
        } else {
            self::assertFalse($calculation->getValueFromCache("$title!A3", $cellValue));
            self::assertFalse($calculation->getValueFromCache("$title!B2", $cellValue));
        }
    }

    private static function readFile(string $file): string
    {
        $dataOut = '';
        $data = file_get_contents($file);
        // confirm that file contains B2 pre-calculated or not as appropriate
        if ($data === false) {
            self::fail("Unable to read $file");
        } else {
            $dataOut = $data;
        }

        return $dataOut;
    }

    private function verifyXlsx(?bool $preCalc, string $type): void
    {
        if ($type === 'Xlsx') {
            $file = 'zip://';
            $file .= $this->outfile;
            $file .= '#xl/worksheets/sheet1.xml';
            $data = self::readFile($file);
            // confirm that file contains B2 pre-calculated or not as appropriate
            if ($preCalc === false) {
                self::assertStringContainsString('<c r="B2" t="str"><f>3+A3</f><v>0</v></c>', $data);
            } else {
                self::assertStringContainsString('<c r="B2"><f>3+A3</f><v>14</v></c>', $data);
            }
            $file = 'zip://';
            $file .= $this->outfile;
            $file .= '#xl/workbook.xml';
            $data = self::readFile($file);
            // confirm whether workbook is set to recalculate
            if ($preCalc === false) {
                self::assertStringContainsString('<calcPr calcId="999999" calcMode="auto" calcCompleted="0" fullCalcOnLoad="1" forceFullCalc="1"/>', $data);
            } else {
                self::assertStringContainsString('<calcPr calcId="999999" calcMode="auto" calcCompleted="1" fullCalcOnLoad="0" forceFullCalc="0"/>', $data);
            }
        }
    }

    private function verifyOds(?bool $preCalc, string $type): void
    {
        if ($type === 'Ods') {
            $file = 'zip://';
            $file .= $this->outfile;
            $file .= '#content.xml';
            $data = self::readFile($file);
            // confirm that file contains B2 pre-calculated or not as appropriate
            if ($preCalc === false) {
                self::assertStringContainsString('table:formula="of:=3+[.A3]" office:value-type="string" office:value="=3+A3"', $data);
            } else {
                self::assertStringContainsString(' table:formula="of:=3+[.A3]" office:value-type="float" office:value="14"', $data);
            }
        }
    }

    private function verifyHtml(?bool $preCalc, string $type): void
    {
        if ($type === 'Html') {
            $data = self::readFile($this->outfile);
            // confirm that file contains B2 pre-calculated or not as appropriate
            if ($preCalc === false) {
                self::assertStringContainsString('>=1+2</td>', $data);
                self::assertStringContainsString('>=3+A3</td>', $data);
                self::assertStringContainsString('>=5+6</td>', $data);
            } else {
                self::assertStringContainsString('>3</td>', $data);
                self::assertStringContainsString('>14</td>', $data);
                self::assertStringContainsString('>11</td>', $data);
            }
        }
    }

    private function verifyCsv(?bool $preCalc, string $type): void
    {
        if ($type === 'Csv') {
            $data = self::readFile($this->outfile);
            // confirm that file contains B2 pre-calculated or not as appropriate
            if ($preCalc === false) {
                self::assertStringContainsString('"=1+2"', $data);
                self::assertStringContainsString('"=3+A3"', $data);
                self::assertStringContainsString('"=5+6"', $data);
            } else {
                self::assertStringContainsString('"3"', $data);
                self::assertStringContainsString('"14"', $data);
                self::assertStringContainsString('"11"', $data);
            }
        }
    }

    /**
     * @dataProvider providerPreCalc
     */
    public function testPreCalc(?bool $preCalc, string $type): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue('Column not set to autoSize');
        $sheet->getCell('B1')->setValue('Column set to autoSize');
        $sheet->getCell('A2')->setValue('=1+2');
        $sheet->getCell('A3')->setValue('=5+6');
        $sheet->getCell('B2')->setValue('=3+A3');
        $columnDimension = $sheet->getColumnDimension('B');
        self::autoSize($columnDimension);

        $writer = IOFactory::createWriter($spreadsheet, $type);
        if ($preCalc !== null) {
            $writer->setPreCalculateFormulas($preCalc);
        }
        $this->outfile = File::temporaryFilename();
        $writer->save($this->outfile);
        $title = $sheet->getTitle();
        $calculation = Calculation::getInstance($spreadsheet);
        // verify values in Calculation cache
        self::verifyA2($calculation, $title, $preCalc);
        self::verifyA3B2($calculation, $title, $preCalc, $type);
        // verify values in output file
        $this->verifyXlsx($preCalc, $type);
        $this->verifyOds($preCalc, $type);
        $this->verifyHtml($preCalc, $type);
        $this->verifyCsv($preCalc, $type);
    }
}
