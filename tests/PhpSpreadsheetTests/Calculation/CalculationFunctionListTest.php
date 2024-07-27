<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class CalculationFunctionListTest extends TestCase
{
    private string $compatibilityMode;

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
     * @dataProvider providerGetFunctions
     */
    public function testGetFunctions(array|string $functionCall): void
    {
        self::assertIsCallable($functionCall);
    }

    public static function providerGetFunctions(): array
    {
        $returnFunctions = [];
        $functionList = Calculation::getInstance()->getFunctions();
        foreach ($functionList as $functionName => $functionArray) {
            $returnFunctions[$functionName]['functionCall'] = $functionArray['functionCall'];
        }

        return $returnFunctions;
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
