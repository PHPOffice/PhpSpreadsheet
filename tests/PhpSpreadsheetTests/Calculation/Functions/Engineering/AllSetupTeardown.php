<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcException;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheetTests\Custom\ComplexAssert;
use PHPUnit\Framework\TestCase;

class AllSetupTeardown extends TestCase
{
    /**
     * @var string
     */
    private $compatibilityMode;

    /**
     * @var ?Spreadsheet
     */
    private $spreadsheet;

    /**
     * @var ?Worksheet
     */
    private $sheet;

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

    /**
     * @param mixed $expectedResult
     */
    protected function mightHaveException($expectedResult): void
    {
        if ($expectedResult === 'exception') {
            $this->expectException(CalcException::class);
        }
    }

    /**
     * @param mixed $value
     */
    protected function setCell(string $cell, $value): void
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
     * @param mixed $expectedResult
     * @param array $args
     */
    protected function runTestCase(string $functionName, $expectedResult, ...$args): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $formula = "=$functionName(";
        $comma = '';
        $row = 0;
        foreach ($args as $arg) {
            ++$row;
            $cellId = "A$row";
            $formula .= "$comma$cellId";
            $comma = ',';
            $this->setCell($cellId, $arg);
        }
        $formula .= ')';
        $this->setCell('B1', $formula);
        self::assertEqualsWithDelta($expectedResult, $sheet->getCell('B1')->getCalculatedValue(), 1.0e-8);
    }

    /**
     * @param mixed $expectedResult
     * @param array $args
     */
    protected function runComplexTestCase(string $functionName, $expectedResult, ...$args): void
    {
        $complexAssert = new ComplexAssert();
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $formula = "=$functionName(";
        $comma = '';
        $row = 0;
        foreach ($args as $arg) {
            ++$row;
            $cellId = "A$row";
            $formula .= "$comma$cellId";
            $comma = ',';
            $this->setCell($cellId, $arg);
        }
        $formula .= ')';
        $this->setCell('B1', $formula);
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertTrue(
            $complexAssert->assertComplexEquals($expectedResult, $result, 1.0e-8),
            $complexAssert->getErrorMessage()
        );
    }
}
