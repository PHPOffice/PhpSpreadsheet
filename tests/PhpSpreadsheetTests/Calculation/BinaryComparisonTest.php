<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\BinaryComparison;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class BinaryComparisonTest extends TestCase
{
    /**
     * @var string
     */
    private $compatibilityMode;

    protected function setUp(): void
    {
        $this->compatibilityMode = Functions::getCompatibilityMode();
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    protected function tearDown(): void
    {
        Functions::setCompatibilityMode($this->compatibilityMode);
    }

    /**
     * @dataProvider providerBinaryComparison
     *
     * @param mixed $operand1
     * @param mixed $operand2
     */
    public function testBinaryComparisonOperation(
        $operand1,
        $operand2,
        string $operator,
        bool $expectedResultExcel,
        bool $expectedResultOpenOffice
    ): void {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        $resultExcel = BinaryComparison::compare($operand1, $operand2, $operator);
        self::assertEquals($expectedResultExcel, $resultExcel, 'should be Excel compatible');

        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);
        $resultOpenOffice = BinaryComparison::compare($operand1, $operand2, $operator);
        self::assertEquals($expectedResultOpenOffice, $resultOpenOffice, 'should be OpenOffice compatible');
    }

    public static function providerBinaryComparison(): array
    {
        return require 'tests/data/Calculation/BinaryComparisonOperations.php';
    }

    public function testInvalidOperator(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unsupported binary comparison operator');
        BinaryComparison::compare(1, 2, '!=');
    }
}
