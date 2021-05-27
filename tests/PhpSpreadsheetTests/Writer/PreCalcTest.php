<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
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

    public function providerPreCalc(): array
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
        if ($columnDimension === null) {
            self::fail('Unable to getColumnDimension');
        } else {
            $columnDimension->setAutoSize(true);
        }

        $writer = IOFactory::createWriter($spreadsheet, $type);
        if ($preCalc !== null) {
            $writer->setPreCalculateFormulas($preCalc);
        }
        $this->outfile = File::temporaryFilename();
        $writer->save($this->outfile);
        $title = $sheet->getTitle();
        $calculation = Calculation::getInstance($spreadsheet);
        $cellValue = 0;
        // A2 has no cached calculation value if preCalc is false
        if ($preCalc === false) {
            self::assertFalse($calculation->getValueFromCache("$title!A2", $cellValue));
        } else {
            self::assertTrue($calculation->getValueFromCache("$title!A2", $cellValue));
            self::assertSame(3, $cellValue);
        }
        if ($type === 'Xlsx' || $type === 'Xls' || $type === 'Html' || $preCalc !== false) {
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
        if ($type === 'Xlsx') {
            $file = 'zip://';
            $file .= $this->outfile;
            $file .= '#xl/worksheets/sheet1.xml';
            $data = file_get_contents($file);
            // confirm that file contains B2 pre-calculated or not as appropriate
            if ($data === false) {
                self::fail('Unable to read worksheet file');
            } else {
                if ($preCalc === false) {
                    self::assertStringContainsString('<c r="B2" t="str"><f>3+A3</f><v>0</v></c>', $data);
                } else {
                    self::assertStringContainsString('<c r="B2"><f>3+A3</f><v>14</v></c>', $data);
                }
            }
            $file = 'zip://';
            $file .= $this->outfile;
            $file .= '#xl/workbook.xml';
            $data = file_get_contents($file);
            // confirm whether workbook is set to recalculate
            if ($data === false) {
                self::fail('Unable to read workbook file');
            } elseif ($preCalc === false) {
                self::assertStringContainsString('<calcPr calcId="999999" calcMode="auto" calcCompleted="0" fullCalcOnLoad="1" forceFullCalc="1"/>', $data);
            } else {
                self::assertStringContainsString('<calcPr calcId="999999" calcMode="auto" calcCompleted="1" fullCalcOnLoad="0" forceFullCalc="0"/>', $data);
            }
        } elseif ($type === 'Ods') {
            $file = 'zip://';
            $file .= $this->outfile;
            $file .= '#content.xml';
            $data = file_get_contents($file);
            // confirm that file contains B2 pre-calculated or not as appropriate
            if ($data === false) {
                self::fail('Unable to read Ods file');
            } else {
                if ($preCalc === false) {
                    self::assertStringContainsString('table:formula="of:=3+[.A3]" office:value-type="string" office:value="=3+A3"', $data);
                } else {
                    self::assertStringContainsString(' table:formula="of:=3+[.A3]" office:value-type="float" office:value="14"', $data);
                }
            }
        } elseif ($type === 'Html') {
            $data = file_get_contents($this->outfile);
            // confirm that file contains B2 pre-calculated or not as appropriate
            if ($data === false) {
                self::fail('Unable to read Html file');
            } else {
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
        } elseif ($type === 'Csv') {
            $data = file_get_contents($this->outfile);
            // confirm that file contains B2 pre-calculated or not as appropriate
            if ($data === false) {
                self::fail('Unable to read Csv file');
            } else {
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
    }
}
