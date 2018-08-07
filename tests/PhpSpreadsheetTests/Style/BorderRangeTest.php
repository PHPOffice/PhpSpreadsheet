<?php

namespace PhpOffice\PhpSpreadsheetTests\Style;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PHPUnit\Framework\TestCase;

class BorderRangeTest extends TestCase
{
    public function testBorderRangeInAction()
    {
        // testcase for the initial bug problem: setting border+color fails
        // set red borders aroundlA1:B3 square. Verify that the borders set are actually correct

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $argb = 'FFFF0000';
        $color = new Color($argb);

        $sheet->getStyle('A1:C1')->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN)->setColor($color);
        $sheet->getStyle('A1:A3')->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN)->setColor($color);
        $sheet->getStyle('C1:C3')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN)->setColor($color);
        $sheet->getStyle('A3:C3')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN)->setColor($color);

        // upper row
        $expectations = [
            // cell => Left/Right/Top/Bottom
            'A1' => 'LT',
            'B1' => 'T',
            'C1' => 'RT',
            'A2' => 'L',
            'B2' => '',
            'C2' => 'R',
            'A3' => 'LB',
            'B3' => 'B',
            'C3' => 'RB',
        ];
        $sides = [
            'L' => 'Left',
            'R' => 'Right',
            'T' => 'Top',
            'B' => 'Bottom',
        ];

        foreach ($expectations as $cell => $borders) {
            $bs = $sheet->getStyle($cell)->getBorders();
            foreach ($sides as $sidekey => $side) {
                $assertion = "setBorderStyle on a range of cells, $cell $side";
                $func = "get$side";
                $b = $bs->$func(); // boo

                if (strpos($borders, $sidekey) === false) {
                    self::assertSame(Border::BORDER_NONE, $b->getBorderStyle(), $assertion);
                } else {
                    self::assertSame(Border::BORDER_THIN, $b->getBorderStyle(), $assertion);
                    self::assertSame($argb, $b->getColor()->getARGB(), $assertion);
                }
            }
        }
    }

    public function testBorderRangeDirectly()
    {
        // testcase for the underlying problem directly
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $style = $sheet->getStyle('A1:C1')->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
        self::assertSame('A1:C1', $style->getSelectedCells(), 'getSelectedCells should not change after a style operation on a border range');
    }
}
