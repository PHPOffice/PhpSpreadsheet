<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class CalculationFunctionListTest extends TestCase
{
    /**
     * @var string
     */
    private $compatibilityMode;

    /**
     * @var string
     */
    private $locale;

    protected function setUp(): void
    {
        $this->compatibilityMode = Functions::getCompatibilityMode();
        $calculation = Calculation::getInstance();
        $this->locale = $calculation->getLocale();
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    protected function tearDown(): void
    {
        Functions::setCompatibilityMode($this->compatibilityMode);
        $calculation = Calculation::getInstance();
        $calculation->setLocale($this->locale);
    }

    public function testGetFunctions(): void
    {
        $functionList = Calculation::getInstance()->getFunctions();

        foreach ($functionList as $function) {
            if ($function !== null) {
                self::assertIsCallable($function->functionCall);
            }
        }
    }

    public function testIsImplemented(): void
    {
        $calculation = Calculation::getInstance();
        self::assertFalse($calculation->isImplemented('non-existing-function'));
        self::assertFalse($calculation->isImplemented('AREAS'));
        self::assertTrue($calculation->isImplemented('coUNt'));
        self::assertTrue($calculation->isImplemented('abs'));
    }

    public function testUnknownFunction(): void
    {
        $workbook = new Spreadsheet();
        $sheet = $workbook->getActiveSheet();
        $sheet->setCellValue('A1', '=gzorg()');
        $sheet->setCellValue('A2', '=mode.gzorg(1)');
        $sheet->setCellValue('A3', '=gzorg(1,2)');
        $sheet->setCellValue('A4', '=3+IF(gzorg(),1,2)');
        self::assertEquals('#NAME?', $sheet->getCell('A1')->getCalculatedValue());
        self::assertEquals('#NAME?', $sheet->getCell('A2')->getCalculatedValue());
        self::assertEquals('#NAME?', $sheet->getCell('A3')->getCalculatedValue());
        self::assertEquals('#NAME?', $sheet->getCell('A4')->getCalculatedValue());
    }
}
