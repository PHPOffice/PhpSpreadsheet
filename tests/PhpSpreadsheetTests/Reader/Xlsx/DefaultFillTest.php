<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\TestCase;

class DefaultFillTest extends TestCase
{
    public function testDefaultFill(): void
    {
        // default fill pattern doesn't specify filltype
        $filename = 'tests/data/Reader/XLSX/pr1769g.py.xlsx';
        $file = 'zip://';
        $file .= $filename;
        $file .= '#xl/styles.xml';
        $data = file_get_contents($file);
        // confirm that file contains expected empty xml tag
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertStringContainsString('<patternFill/>', $data);
        }
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('none', $sheet->getCell('A1')->getStyle()->getFill()->getFillType());
        self::assertSame('none', $sheet->getCell('D4')->getStyle()->getFill()->getFillType());
        self::assertSame('none', $sheet->getCell('J16')->getStyle()->getFill()->getFillType());
        self::assertSame('solid', $sheet->getCell('C2')->getStyle()->getFill()->getFillType());
    }

    public function testDefaultConditionalFill(): void
    {
        // default fill pattern for a conditional style where the filltype is not defined
        $filename = 'tests/data/Reader/XLSX/pr2050cf-fill.xlsx';
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);

        $style = $spreadsheet->getActiveSheet()->getConditionalStyles('A1')[0]->getStyle();
        self::assertSame('solid', $style->getFill()->getFillType());
    }
}
