<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcException;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
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
     * Adjust result if it is close enough to expected by ratio
     *     rather than offset.
     *
     * @param mixed $result
     * @param mixed $expectedResult
     */
    protected function adjustResult(&$result, $expectedResult): void
    {
        if (is_numeric($result) && is_numeric($expectedResult)) {
            if ($expectedResult != 0) {
                $frac = $result / $expectedResult;
                if ($frac > 0.999999 && $frac < 1.000001) {
                    $result = $expectedResult;
                }
            }
        }
    }

    /**
     * @param mixed $expectedResult
     */
    public function runTestCase(string $functionName, $expectedResult, array $args): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $formula = "=$functionName(";
        $row = 0;
        $col = 'A';
        $comma = '';
        foreach ($args as $arg) {
            ++$row;
            $cell = "$col$row";
            $this->setCell($cell, $arg);
            $formula .= "$comma$cell";
            $comma = ',';
        }
        $formula .= ')';
        $sheet->getCell('B1')->setValue($formula);
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-7);
    }
}
