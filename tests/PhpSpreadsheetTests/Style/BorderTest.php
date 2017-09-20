<?php

namespace PhpOffice\PhpSpreadsheetTests\Style;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PHPUnit_Framework_TestCase;

class BorderTest extends PHPUnit_Framework_TestCase
{
    public function testCase()
    {
        $spreadsheet = new Spreadsheet();
        $borders = $spreadsheet->getActiveSheet()->getStyle('A1')->getBorders();
        $allBorders = $borders->getAllBorders();
        $bottom = $borders->getBottom();

        $actual = $bottom->getBorderStyle();
        self::assertSame(Border::BORDER_NONE, $actual, 'should default to none');

        $allBorders->setBorderStyle(Border::BORDER_THIN);

        $actual = $bottom->getBorderStyle();
        self::assertSame(Border::BORDER_THIN, $actual, 'should have been set via allBorders');
    }
}
