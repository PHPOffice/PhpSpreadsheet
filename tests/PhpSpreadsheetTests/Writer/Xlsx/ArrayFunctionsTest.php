<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class ArrayFunctionsTest extends TestCase
{
    private string $arrayReturnType;

    private string $outputFile = '';

    protected function setUp(): void
    {
        $this->arrayReturnType = Calculation::getArrayReturnType();
    }

    protected function tearDown(): void
    {
        Calculation::setArrayReturnType($this->arrayReturnType);
        if ($this->outputFile !== '') {
            unlink($this->outputFile);
            $this->outputFile = '';
        }
    }

    public function testArrayOutput(): void
    {
        Calculation::setArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $columnArray = [
            [41],
            [57],
            [51],
            [54],
            [49],
            [43],
            [35],
            [35],
            [44],
            [47],
            [48],
            [26],
            [57],
            [34],
            [61],
            [34],
            [28],
            [29],
            [41],
        ];
        $sheet->fromArray($columnArray, 'A1');
        $sheet->setCellValue('C1', '=UNIQUE(A1:A19)');
        $sheet->setCellValue('D1', '=SORT(A1:A19)');
        $writer = new XlsxWriter($spreadsheet);
        $this->outputFile = File::temporaryFilename();
        $writer->save($this->outputFile);
        $spreadsheet->disconnectWorksheets();

        $reader = new XlsxReader();
        $spreadsheet2 = $reader->load($this->outputFile);
        $sheet2 = $spreadsheet2->getActiveSheet();
        $expectedUnique = [
            ['41'],
            ['57'],
            ['51'],
            ['54'],
            ['49'],
            ['43'],
            ['35'],
            ['44'],
            ['47'],
            ['48'],
            ['26'],
            ['34'],
            ['61'],
            ['28'],
            ['29'],
        ];
        self::assertCount(15, $expectedUnique);
        self::assertSame($expectedUnique, $sheet2->getCell('C1')->getCalculatedValue());
        $expectedSort = [
            [26],
            [28],
            [29],
            [34],
            [34],
            [35],
            [35],
            [41],
            [41],
            [43],
            [44],
            [47],
            [48],
            [49],
            [51],
            [54],
            [57],
            [57],
            [61],
        ];
        self::assertCount(19, $expectedSort);
        self::assertCount(19, $columnArray);
        self::assertSame($expectedSort, $sheet2->getCell('D1')->getCalculatedValue());
        $spreadsheet2->disconnectWorksheets();

        $file = 'zip://';
        $file .= $this->outputFile;
        $file .= '#xl/worksheets/sheet1.xml';
        $data = file_get_contents($file);
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertStringContainsString('<c r="C1"><f t="array" ref="C1:C15" aca="1" ca="1">_xlfn.UNIQUE(A1:A19)</f></c>', $data, '15 results for UNIQUE');
            self::assertStringContainsString('<c r="D1"><f t="array" ref="D1:D19" aca="1" ca="1">_xlfn._xlws.SORT(A1:A19)</f></c>', $data, '19 results for SORT');
        }
    }
}
