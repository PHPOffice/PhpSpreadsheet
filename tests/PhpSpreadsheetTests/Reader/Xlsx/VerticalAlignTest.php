<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\TestCase;

class VerticalAlignTest extends TestCase
{
    public function testVerticalAlignStyle(): void
    {
        $filename = 'tests/data/Reader/XLSX/verticalAlignTest.xlsx';
        $reader = IOFactory::createReader('Xlsx');
        $sheet = $reader->load($filename)->getActiveSheet();

        self::assertTrue($sheet->getCell('A1')->getStyle()->getFont()->getSuperscript());
        self::assertFalse($sheet->getCell('A1')->getStyle()->getFont()->getSubscript());

        self::assertTrue($sheet->getCell('B1')->getStyle()->getFont()->getSubscript());
        self::assertFalse($sheet->getCell('B1')->getStyle()->getFont()->getSuperscript());

        self::assertFalse($sheet->getCell('C1')->getStyle()->getFont()->getSubscript());
        self::assertFalse($sheet->getCell('C1')->getStyle()->getFont()->getSuperscript());
    }
}
