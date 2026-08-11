<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcException;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

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

    protected static function setExcel(): void
    {
        Functions::setCompatibilityMode(
            Functions::COMPATIBILITY_EXCEL
        );
    }

    protected static function setOpenOffice(): void
    {
        Functions::setCompatibilityMode(
            Functions::COMPATIBILITY_OPENOFFICE
        );
    }

    protected static function setGnumeric(): void
    {
        Functions::setCompatibilityMode(
            Functions::COMPATIBILITY_GNUMERIC
        );
    }

    protected function mightHaveException(mixed $expectedResult): void
    {
        if ($expectedResult === 'exception') {
            $this->expectException(CalcException::class);
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
