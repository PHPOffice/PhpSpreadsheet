<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Style;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class MultipleRangeTest extends TestCase
{
    public function testMultipleRange(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $data = [
            [10, 11, 12, 13, 14, 15],
            [20, 21, 22, 23, 24, 25],
            [30, 31, 32, 33, 34, 35],
            [40, 41, 42, 43, 44, 45],
            [50, 51, 52, 53, 54, 55],
            [60, 61, 62, 63, 64, 65],
        ];
        $sheet->fromArray($data);
        $styleArray = ['font' => ['bold' => true]];
        $range1 = 'A2:C5,E2:F5';
        $range1Array = Coordinate::extractAllCellReferencesInRange($range1);
        $sheet
            ->getStyle($range1)
            ->applyFromArray($styleArray);
        self::assertSame($range1, $sheet->getSelectedCells());
        self::assertTrue(
            $sheet->getStyle($range1)->getFont()->getBold()
        );
        $range2 = 'A1,F6,B2:D3';
        $range2Array = Coordinate::extractAllCellReferencesInRange($range2);
        $sheet
            ->getStyle($range2)
            ->getFont()
            ->setItalic(true);
        self::assertSame($range2, $sheet->getSelectedCells());
        self::assertTrue(
            $sheet->getStyle($range2)->getFont()->getItalic()
        );
        // A1 is part of range2 but not range1
        self::assertNotContains('A1', $range1Array);
        self::assertContains('A1', $range2Array);
        self::assertTrue(
            $sheet->getStyle('A1')->getFont()->getItalic()
        );
        self::assertFalse(
            $sheet->getStyle('A1')->getFont()->getBold()
        );
        // B1 is part of neither range2 nor range1
        self::assertNotContains('B1', $range1Array);
        self::assertNotContains('B1', $range2Array);
        self::assertFalse(
            $sheet->getStyle('B1')->getFont()->getItalic()
        );
        self::assertFalse(
            $sheet->getStyle('B1')->getFont()->getBold()
        );
        // B2 is part of both range2 and range1
        self::assertContains('B2', $range1Array);
        self::assertContains('B2', $range2Array);
        self::assertTrue(
            $sheet->getStyle('B2')->getFont()->getItalic()
        );
        self::assertTrue(
            $sheet->getStyle('B2')->getFont()->getBold()
        );
        // C4 is part of range1 but not range2
        self::assertContains('C4', $range1Array);
        self::assertNotContains('C4', $range2Array);
        self::assertTrue(
            $sheet->getStyle('C4')->getFont()->getBold()
        );
        self::assertFalse(
            $sheet->getStyle('C4')->getFont()->getItalic()
        );
        $spreadsheet->disconnectWorksheets();
    }
}
