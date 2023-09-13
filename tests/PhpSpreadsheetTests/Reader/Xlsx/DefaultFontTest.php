<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\TestCase;

class DefaultFontTest extends TestCase
{
    public function testDefaultConditionalFont(): void
    {
        // default fill pattern for a conditional style where the filltype is not defined
        $filename = 'tests/data/Reader/XLSX/pr2050cf-fill.xlsx';
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);

        $style = $spreadsheet->getActiveSheet()->getConditionalStyles('A1')[0]->getStyle();
        self::assertSame('9C0006', $style->getFont()->getColor()->getRGB());
        self::assertNull($style->getFont()->getName());
        self::assertNull($style->getFont()->getSize());
    }
}
