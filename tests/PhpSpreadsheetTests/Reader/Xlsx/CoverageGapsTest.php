<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class CoverageGapsTest extends AbstractFunctional
{
    public function testCoverageGaps(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet
            ->getStyle('A1')
            ->getBorders()
            ->setDiagonalDirection(Borders::DIAGONAL_BOTH)
            ->getDiagonal()
            ->setBorderStyle(Border::BORDER_DASHDOTDOT);
        $sheet
            ->getStyle('A2')
            ->getProtection()
            ->setLocked(Protection::PROTECTION_PROTECTED);
        $sheet
            ->getStyle('A3')
            ->getAlignment()
            ->setTextRotation(Alignment::TEXTROTATION_STACK_EXCEL);
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();

        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        self::assertSame(Borders::DIAGONAL_BOTH, $rsheet->getStyle('A1')->getBorders()->getDiagonalDirection());
        self::assertSame(Border::BORDER_DASHDOTDOT, $rsheet->getStyle('A1')->getBorders()->getDiagonal()->getBorderStyle());
        self::assertSame(Protection::PROTECTION_PROTECTED, $rsheet->getStyle('A2')->getProtection()->getLocked());
        self::assertSame(Alignment::TEXTROTATION_STACK_PHPSPREADSHEET, $rsheet->getStyle('A3')->getAlignment()->getTextRotation());

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
