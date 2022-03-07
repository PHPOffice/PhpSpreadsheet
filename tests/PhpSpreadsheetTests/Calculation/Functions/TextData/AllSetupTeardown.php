<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcException;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Settings;
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
     * @var string
     */
    private $locale;

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
        $this->locale = Settings::getLocale();
        $this->compatibilityMode = Functions::getCompatibilityMode();
    }

    protected function tearDown(): void
    {
        Functions::setCompatibilityMode($this->compatibilityMode);
        Settings::setLocale($this->locale);
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
}
