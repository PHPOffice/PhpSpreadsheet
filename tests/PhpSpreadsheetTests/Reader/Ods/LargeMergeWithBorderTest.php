<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\Ods as OdsReader;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PHPUnit\Framework\TestCase;

class LargeMergeWithBorderTest extends TestCase
{
    public function testAutoFilterRange(): void
    {
        $filename = 'tests/data/Reader/Ods/border2.ods';
        $reader = new OdsReader();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        //$range1 = 'B2:C3';
        self::assertSame(
            '81aca6',
            $sheet->getStyle('C2')
                ->getBorders()
                ->getTop()
                ->getColor()
                ->getRgb(),
            'c2 is in top row so it has a top color'
        );
        self::assertSame(
            Border::BORDER_NONE,
            $sheet->getStyle('C2')
                ->getBorders()
                ->getBottom()
                ->getBorderStyle(),
            'c2 is not in bottom row so it has no bottom style'
        );
        self::assertSame(
            Border::BORDER_NONE,
            $sheet->getStyle('B3')
                ->getBorders()
                ->getTop()
                ->getBorderStyle(),
            'b3 is not in top row so it has no top style'
        );
        self::assertSame(
            '81aca6',
            $sheet->getStyle('B3')
                ->getBorders()
                ->getBottom()
                ->getColor()
                ->getRgb(),
            'b3 is in bottom row so it has bottom color'
        );
        //$range2 = 'B6:ET218';
        self::assertSame(
            'ff3838',
            $sheet->getStyle('B6')
                ->getBorders()
                ->getTop()
                ->getColor()
                ->getRgb(),
            'style is applied to first cell in range'
        );
        self::assertSame(
            Border::BORDER_NONE,
            $sheet->getStyle('B7')
                ->getBorders()
                ->getTop()
                ->getBorderStyle(),
            'merge range too large - style not applied to range'
        );
        $spreadsheet->disconnectWorksheets();
    }
}
