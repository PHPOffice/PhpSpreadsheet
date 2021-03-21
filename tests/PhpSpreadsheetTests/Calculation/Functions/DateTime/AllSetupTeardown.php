<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcException;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class AllSetupTeardown extends TestCase
{
    protected $compatibilityMode;

    protected $excelCalendar;

    protected $returnDateType;

    protected $spreadsheet;

    protected $sheet;

    protected function setUp(): void
    {
        $this->compatibilityMode = Functions::getCompatibilityMode();
        $this->excelCalendar = Date::getExcelCalendar();
        $this->returnDateType = Functions::getReturnDateType();
        $this->spreadsheet = new Spreadsheet();
        $this->sheet = $this->spreadsheet->getActiveSheet();
    }

    protected function tearDown(): void
    {
        Date::setExcelCalendar($this->excelCalendar);
        Functions::setCompatibilityMode($this->compatibilityMode);
        Functions::setReturnDateType($this->returnDateType);
        $this->spreadsheet->disconnectWorksheets();
        $this->spreadsheet = null;
        $this->sheet = null;
    }

    protected static function setMac1904(): void
    {
        Date::setExcelCalendar(Date::CALENDAR_MAC_1904);
    }

    protected static function setUnixReturn(): void
    {
        Functions::setReturnDateType(Functions::RETURNDATE_UNIX_TIMESTAMP);
    }

    protected static function setObjectReturn(): void
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_DATETIME_OBJECT);
    }

    protected static function setOpenOffice(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);
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
