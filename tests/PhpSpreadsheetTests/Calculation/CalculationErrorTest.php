<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcException;
use PHPUnit\Framework\TestCase;

class CalculationErrorTest extends TestCase
{
    public function testCalculationExceptionSuppressed(): void
    {
        $calculation = Calculation::getInstance();
        self::assertFalse($calculation->getSuppressFormulaErrors());
        $calculation->setSuppressFormulaErrors(true);
        $result = $calculation->_calculateFormulaValue('=SUM(');
        $calculation->setSuppressFormulaErrors(false);
        self::assertFalse($result);
    }

    public function testCalculationException(): void
    {
        $calculation = Calculation::getInstance();
        self::assertFalse($calculation->getSuppressFormulaErrors());
        $this->expectException(CalcException::class);
        $this->expectExceptionMessage("Formula Error: Expecting ')'");
        $result = $calculation->_calculateFormulaValue('=SUM(');
        self::assertFalse($result);
    }
}
