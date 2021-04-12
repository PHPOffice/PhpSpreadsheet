<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

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
     * @var Spreadsheet
     */
    private $spreadsheet;

    /**
     * @var Worksheet
     */
    protected $sheet;

    protected function setUp(): void
    {
        $this->compatibilityMode = Functions::getCompatibilityMode();
        $this->spreadsheet = new Spreadsheet();
        $this->sheet = $this->spreadsheet->getActiveSheet();
    }

    protected function tearDown(): void
    {
        Functions::setCompatibilityMode($this->compatibilityMode);
        $this->spreadsheet->disconnectWorksheets();
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
                $this->sheet->getCell($cell)->setValueExplicit($value, DataType::TYPE_STRING);
            } else {
                $this->sheet->getCell($cell)->setValue($value);
            }
        }
    }
}
