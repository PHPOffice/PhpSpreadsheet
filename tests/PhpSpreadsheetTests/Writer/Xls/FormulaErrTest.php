<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xls;

use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use PhpOffice\PhpSpreadsheet\Writer\Xls\Worksheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class FormulaErrTest extends AbstractFunctional
{
    private ?Spreadsheet $spreadsheet = null;

    private ?Spreadsheet $reloadedSpreadsheet = null;

    private bool $allowThrow;

    protected function setUp(): void
    {
        $this->allowThrow = Worksheet::getAllowThrow();
    }

    protected function tearDown(): void
    {
        Worksheet::setAllowThrow($this->allowThrow);
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
        if ($this->reloadedSpreadsheet !== null) {
            $this->reloadedSpreadsheet->disconnectWorksheets();
            $this->reloadedSpreadsheet = null;
        }
    }

    private function xtestFormulaError(bool $allowThrow): void
    {
        Worksheet::setAllowThrow($allowThrow);
        $this->spreadsheet = $obj = new Spreadsheet();
        $sheet0 = $obj->setActiveSheetIndex(0);
        $sheet0->setCellValue('A1', 2);
        $obj->addNamedRange(new NamedRange('DEFNAM', $sheet0, '$A$1'));
        $sheet0->setCellValue('B1', '=2*DEFNAM');
        $sheet0->setCellValue('C1', '=DEFNAM=2');
        $sheet0->setCellValue('D1', '=CONCATENATE("X",DEFNAM)');
        $this->reloadedSpreadsheet = $robj = $this->writeAndReload($obj, 'Xls');
        $sheet0 = $robj->setActiveSheetIndex(0);
        $a1 = $sheet0->getCell('A1')->getCalculatedValue();
        self::assertEquals(2, $a1);
        $b1 = $sheet0->getCell('B1')->getCalculatedValue();
        self::assertEquals(4, $b1);
        $c1 = $sheet0->getCell('C1')->getCalculatedValue();
        $tru = true;
        self::assertEquals($tru, $c1);
        $d1 = $sheet0->getCell('D1')->getCalculatedValue();
        self::assertEquals('X2', $d1);
    }

    public function testFormulaErrorWithThrow(): void
    {
        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('Cannot yet write formulae with defined names to Xls');
        $this->xtestFormulaError(true);
    }

    public function testFormulaError(): void
    {
        $this->xtestFormulaError(false);
    }
}
