<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class AlignmentTest extends AbstractFunctional
{
    private ?Spreadsheet $spreadsheet = null;

    private ?Spreadsheet $reloadedSpreadsheet = null;

    protected function tearDown(): void
    {
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
        if ($this->reloadedSpreadsheet !== null) {
            $this->reloadedSpreadsheet->disconnectWorksheets();
            $this->reloadedSpreadsheet = null;
        }
    }

    public function testJustifyLastLine(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'ABC');
        $sheet->setCellValue('A2', 'DEF');
        $sheet->setCellValue('A3', 'GHI');
        $sheet->getStyle('A1')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_DISTRIBUTED)
            ->setJustifyLastLine(true);
        $sheet->getStyle('A2')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_DISTRIBUTED)
            ->setJustifyLastLine(false);
        $sheet->getStyle('A3')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_DISTRIBUTED);
        $this->reloadedSpreadsheet = $this->writeAndReload($this->spreadsheet, 'Xlsx');
        $rsheet = $this->reloadedSpreadsheet->getActiveSheet();
        self::assertTrue(
            $rsheet->getStyle('A1')
                ->getAlignment()
                ->getJustifyLastLine()
        );
        self::assertFalse(
            $rsheet->getStyle('A2')
                ->getAlignment()
                ->getJustifyLastLine()
        );
        self::assertNull(
            $rsheet->getStyle('A3')
                ->getAlignment()
                ->getJustifyLastLine()
        );
    }
}
