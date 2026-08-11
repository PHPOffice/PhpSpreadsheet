<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class ArrayFormulaValidationTest extends TestCase
{
    private string $outputFile = '';

    protected function tearDown(): void
    {
        if ($this->outputFile !== '') {
            unlink($this->outputFile);
            $this->outputFile = '';
        }
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('validationProvider')]
    public function testWriteArrayFormulaValidation(string $formula): void
    {
        $spreadsheet = new Spreadsheet();
        Calculation::getInstance($spreadsheet)
            ->setInstanceArrayReturnType(
                Calculation::RETURN_ARRAY_AS_ARRAY
            );
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('C1', 1);
        $sheet->setCellValue('C2', 2);
        $sheet->setCellValue('C3', 3);
        $sheet->setCellValue('C4', 3);
        $sheet->setCellValue('C5', 5);
        $sheet->setCellValue('C6', 6);
        $sheet->setCellValue('B1', '=UNIQUE(C1:C6)');

        $validation = $sheet->getCell('A1')->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_STOP);
        $validation->setAllowBlank(true);
        $validation->setShowDropDown(true);
        $validation->setShowErrorMessage(true);
        $validation->setError('Invalid input');
        $validation->setFormula1($formula);
        $sheet->getCell('A1')->setDataValidation($validation);

        $this->outputFile = File::temporaryFilename();
        $writer = new XlsxWriter($spreadsheet);
        $writer->setUseDiskCaching(true, sys_get_temp_dir());
        $writer->save($this->outputFile);
        $spreadsheet->disconnectWorksheets();

        $reader = new XlsxReader();
        $spreadsheet2 = $reader->load($this->outputFile);
        Calculation::getInstance($spreadsheet2)->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $sheet2 = $spreadsheet2->getActiveSheet();
        $validation2 = $sheet2->getCell('A1')->getDataValidation();
        self::assertSame('ANCHORARRAY($B$1)', $validation2->getFormula1());
        $spreadsheet2->disconnectWorksheets();

        $file = 'zip://';
        $file .= $this->outputFile;
        $file .= '#xl/worksheets/sheet1.xml';
        $data = file_get_contents($file);
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertStringContainsString('<formula1>_xlfn.ANCHORARRAY($B$1)</formula1>', $data);
        }
    }

    public static function validationProvider(): array
    {
        return [
            ['ANCHORARRAY($B$1)'],
            ['$B$1#'],
        ];
    }
}
