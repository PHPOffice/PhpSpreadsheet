<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcException;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;
use Stringable;

class AllSetupTeardown extends TestCase
{
    private string $compatibilityMode;

    private ?Spreadsheet $spreadsheet = null;

    private ?Worksheet $sheet = null;

    protected function setUp(): void
    {
        $this->compatibilityMode = Functions::getCompatibilityMode();
    }

    protected function tearDown(): void
    {
        Functions::setCompatibilityMode($this->compatibilityMode);
        $this->sheet = null;
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
    }

    protected static function setOpenOffice(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);
    }

    protected static function setGnumeric(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_GNUMERIC);
    }

    protected function mightHaveException(mixed $expectedResult): void
    {
        if ($expectedResult === 'exception') {
            $this->expectException(CalcException::class);
        }
    }

    protected function setCell(string $cell, mixed $value): void
    {
        if ($value !== null) {
            if (is_string($value) && is_numeric($value)) {
                $this->getSheet()->getCell($cell)->setValueExplicit($value, DataType::TYPE_STRING);
            } else {
                $this->getSheet()->getCell($cell)->setValue($value);
            }
        }
    }

    protected function getSpreadsheet(): Spreadsheet
    {
        if ($this->spreadsheet !== null) {
            return $this->spreadsheet;
        }
        $this->spreadsheet = new Spreadsheet();

        return $this->spreadsheet;
    }

    protected function getSheet(): Worksheet
    {
        if ($this->sheet !== null) {
            return $this->sheet;
        }
        $this->sheet = $this->getSpreadsheet()->getActiveSheet();

        return $this->sheet;
    }

    /**
     * Excel handles text/logical/empty cells differently when
     * passed directly as arguments as opposed to cell references or arrays.
     * This function will test both approaches.
     */
    protected function runTestCases(string $functionName, mixed $expectedResult, mixed ...$args): void
    {
        if (is_array($expectedResult)) {
            $this->runTestCaseReference($functionName, $expectedResult[0], ...$args);
            $this->runTestCaseDirect($functionName, $expectedResult[1], ...$args);
        } else {
            $this->runTestCaseReference($functionName, $expectedResult, ...$args);
            $this->runTestCaseDirect($functionName, $expectedResult, ...$args);
        }
    }

    /**
     * Excel handles text/logical/empty cells differently when
     * passed directly as arguments as opposed to cell references or arrays.
     * This functions tests passing as arrays.
     */
    protected function runTestCaseReference(string $functionName, mixed $expectedResult, mixed ...$args): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $formula = "=$functionName(";
        $comma = '';
        $row = 0;
        foreach ($args as $arg) {
            ++$row;
            if (is_array($arg)) {
                $arrayArg = '{';
                $arrayComma = '';
                foreach ($arg as $arrayItem) {
                    $arrayArg .= $arrayComma;
                    $arrayArg .= $this->convertToString($arrayItem);
                    $arrayComma = ';';
                }
                $arrayArg .= '}';
                $formula .= "$comma$arrayArg";
                $comma = ',';
            } else {
                $cellId = "A$row";
                $formula .= "$comma$cellId";
                $comma = ',';
                $this->setCell($cellId, $arg);
            }
        }
        $formula .= ')';
        $this->setCell('B1', $formula);
        self::assertEqualsWithDelta($expectedResult, $sheet->getCell('B1')->getCalculatedValue(), 1.0e-8, 'arguments supplied as references');
    }

    /**
     * Excel handles text/logical/empty cells differently when
     * passed directly as arguments as opposed to cell references or arrays.
     * This functions tests passing as direct arguments.
     */
    protected function runTestCaseDirect(string $functionName, mixed $expectedResult, mixed ...$args): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $formula = "=$functionName(";
        $comma = '';
        foreach ($args as $arg) {
            if (is_array($arg)) {
                foreach ($arg as $arrayItem) {
                    $formula .= $comma;
                    $comma = ',';
                    $formula .= $this->convertToString($arrayItem);
                }
            } else {
                $formula .= $comma;
                $comma = ',';
                /** @var string */
                $argx = $arg;
                $formula .= $this->convertToString($argx);
            }
        }
        $formula .= ')';
        $this->setCell('B2', $formula);
        self::assertEqualsWithDelta($expectedResult, $sheet->getCell('B2')->getCalculatedValue(), 1.0e-8, 'arguments supplied directly');
    }

    /**
     * Excel seems to reject bracket notation for literal arrays
     * for some functions.
     */
    protected function runTestCaseNoBracket(string $functionName, mixed $expectedResult, mixed ...$args): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $formula = "=$functionName(";
        $comma = '';
        $row = 0;
        foreach ($args as $arg) {
            ++$row;
            if (is_array($arg)) {
                $col = 'A';
                $arrayRange = '';
                foreach ($arg as $arrayItem) {
                    $cellId = "$col$row";
                    $arrayRange = "A$row:$cellId";
                    $this->setCell($cellId, $arrayItem);
                    ++$col;
                }
                $formula .= "$comma$arrayRange";
                $comma = ',';
            } else {
                $cellId = "A$row";
                $formula .= "$comma$cellId";
                $comma = ',';
                if (is_string($arg) && str_starts_with($arg, '=')) {
                    $sheet->getCell($cellId)->setValueExplicit($arg, DataType::TYPE_STRING);
                } else {
                    $this->setCell($cellId, $arg);
                }
            }
        }
        $formula .= ')';
        $this->setCell('Z99', $formula);
        self::assertEqualsWithDelta($expectedResult, $sheet->getCell('Z99')->getCalculatedValue(), 1.0e-8, 'arguments supplied as ranges');
    }

    private function convertToString(null|bool|float|int|string|Stringable $arg): string
    {
        if (is_string($arg)) {
            return '"' . $arg . '"';
        }
        if (is_bool($arg)) {
            return $arg ? 'TRUE' : 'FALSE';
        }

        return (string) $arg;
    }
}
