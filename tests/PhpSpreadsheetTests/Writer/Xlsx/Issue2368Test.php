<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as Writer;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Issue2368Test extends AbstractFunctional
{
    public function testBoolWrite(): void
    {
        // DataValidations were incorrectly written twice.
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $validation = $sheet->getDataValidation('A1:A10');
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setShowDropDown(true);
        $validation->setFormula1('"Option 1, Option 2"');

        $outputFilename = File::temporaryFilename();
        $writer = new Writer($spreadsheet);
        $writer->save($outputFilename);
        $zipfile = "zip://$outputFilename#xl/worksheets/sheet1.xml";
        $contents = file_get_contents($zipfile);
        unlink($outputFilename);
        $spreadsheet->disconnectWorksheets();
        if ($contents === false) {
            self::fail('Unable to open file');
        } else {
            self::assertSame(0, substr_count($contents, '<extLst>'));
            self::assertSame(2, substr_count($contents, 'dataValidations')); // start and end tags
        }
    }

    public function testMultipleRange(): void
    {
        // DataValidations which were identical except for sqref were incorrectly merged.
        $filename = 'tests/data/Writer/XLSX/issue.2368new.xlsx';
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        $validations = $sheet->getDataValidationCollection();
        /** @var string[] */
        $ranges = [];
        foreach ($validations as $validation) {
            $ranges[] = $validation->getSqref();
        }
        self::assertContains('A1:A5', $ranges);
        self::assertContains('A10:A14', $ranges);
        self::assertContains('A20:A24', $ranges);
        self::assertSame('"yes,no"', $sheet->getCell('A3')->getDataValidation()->getFormula1());
        self::assertSame('"yes,no"', $sheet->getCell('A10')->getDataValidation()->getFormula1());
        self::assertSame('"yes,no"', $sheet->getCell('A24')->getDataValidation()->getFormula1());

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();

        $sheet2 = $reloadedSpreadsheet->getActiveSheet();
        $validation2 = $sheet2->getDataValidationCollection();
        /** @var string[] */
        $range2 = [];
        foreach ($validation2 as $validation) {
            $range2[] = $validation->getSqref();
        }
        self::assertContains('A1:A5', $range2);
        self::assertContains('A10:A14', $range2);
        self::assertContains('A20:A24', $range2);
        self::assertSame('"yes,no"', $sheet2->getCell('A3')->getDataValidation()->getFormula1());
        self::assertSame('"yes,no"', $sheet2->getCell('A10')->getDataValidation()->getFormula1());
        self::assertSame('"yes,no"', $sheet2->getCell('A24')->getDataValidation()->getFormula1());

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
