<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcException;
use PHPUnit\Framework\TestCase;
use Throwable;

class CalculationErrorTest extends TestCase
{
    public function testCalculationException(): void
    {
        $this->expectException(CalcException::class);
        $this->expectExceptionMessage('Formula Error:');
        $calculation = Calculation::getInstance();
        $result = $calculation->_calculateFormulaValue('=SUM(');
        self::assertFalse($result);
    }

    public function testCalculationError(): void
    {
        $calculation = Calculation::getInstance();
        $calculation->suppressFormulaErrors = true;
        $error = false;

        try {
            $calculation->_calculateFormulaValue('=SUM(');
        } catch (Throwable $e) {
            self::assertSame("Formula Error: Expecting ')'", $e->getMessage());
            self::assertSame('PHPUnit\\Framework\\Error\\Error', get_class($e));
            $error = true;
        }
        self::assertTrue($error);
    }

    /**
     * @param mixed $args
     */
    public static function errhandler2(...$args): bool
    {
        return $args[0] === E_USER_ERROR;
    }

    public function testCalculationErrorTrulySuppressed(): void
    {
        $calculation = Calculation::getInstance();
        $calculation->suppressFormulaErrors = true;
        set_error_handler([self::class, 'errhandler2']);
        $result = $calculation->_calculateFormulaValue('=SUM(');
        restore_error_handler();
        self::assertFalse($result);
    }
}
