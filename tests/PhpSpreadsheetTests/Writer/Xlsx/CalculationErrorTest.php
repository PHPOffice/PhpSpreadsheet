<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class CalculationErrorTest extends AbstractFunctional
{
    /** @var ?Spreadsheet */
    private $spreadsheet;

    /** @var ?Spreadsheet */
    private $reloadedSpreadsheet;

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

    public function testCalculationExceptionSuppressed(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $calculation = Calculation::getInstance($this->spreadsheet);
        self::assertFalse($calculation->getSuppressFormulaErrors());
        $calculation->setSuppressFormulaErrors(true);
        $sheet->getCell('A1')->setValue('=SUM(');
        $sheet->getCell('A2')->setValue('=2+3');
        $this->reloadedSpreadsheet = $this->writeAndReload($this->spreadsheet, 'Xlsx');
        $rcalculation = Calculation::getInstance($this->reloadedSpreadsheet);
        self::assertFalse($rcalculation->getSuppressFormulaErrors());
        $rcalculation->setSuppressFormulaErrors(true);
        $rsheet = $this->reloadedSpreadsheet->getActiveSheet();
        self::assertSame('=SUM(', $rsheet->getCell('A1')->getValue());
        self::assertFalse($rsheet->getCell('A1')->getCalculatedValue());
        self::assertSame('=2+3', $rsheet->getCell('A2')->getValue());
        self::assertSame(5, $rsheet->getCell('A2')->getCalculatedValue());
        $calculation->setSuppressFormulaErrors(false);
        $rcalculation->setSuppressFormulaErrors(false);
    }

    public function testCalculationException(): void
    {
        $this->expectException(CalcException::class);
        $this->expectExceptionMessage("Formula Error: Expecting ')'");
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $calculation = Calculation::getInstance($this->spreadsheet);
        self::assertFalse($calculation->getSuppressFormulaErrors());
        $sheet->getCell('A1')->setValue('=SUM(');
        $sheet->getCell('A2')->setValue('=2+3');
        $this->reloadedSpreadsheet = $this->writeAndReload($this->spreadsheet, 'Xlsx');
    }
}
