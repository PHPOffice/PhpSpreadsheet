<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcException;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class AllSetupTeardown extends TestCase
{
    protected $compatibilityMode;

    protected $spreadsheet;

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
        $this->spreadsheet = null;
        $this->sheet = null;
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
}
