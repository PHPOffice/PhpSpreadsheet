<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Time;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date as SharedDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class TimeTest extends TestCase
{
    /**
     * @var int
     */
    private $excelCalendar;

    /**
     * @var string
     */
    private $returnDateType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->excelCalendar = SharedDate::getExcelCalendar();
        $this->returnDateType = Functions::getReturnDateType();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        SharedDate::setExcelCalendar($this->excelCalendar);
        Functions::setReturnDateType($this->returnDateType);
    }

    /**
     * @dataProvider providerTIME
     *
     * @param mixed $expectedResult
     */
    public function testDirectCallToTIME($expectedResult, ...$args): void
    {
        $result = Time::fromHMS(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    /**
     * @dataProvider providerTIME
     *
     * @param mixed $expectedResult
     */
    public function testTIMEAsFormula($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=TIME({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    public static function providerTIME(): array
    {
        return require 'tests/data/Calculation/DateTime/TIME.php';
    }

    /**
     * @dataProvider providerUnhappyTIME
     */
    public function testTIMEUnhappyPath(string $expectedException, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=TIME({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyTIME(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for TIME() function', 2023, 03],
        ];
    }

    public function testTIMEtoUnixTimestamp(): void
    {
        Functions::setReturnDateType(Functions::RETURNDATE_UNIX_TIMESTAMP);

        $result = Time::fromHMS(7, 30, 20);
        self::assertEqualsWithDelta(27020, $result, 1E-12);
    }

    public function testTIMEtoDateTimeObject(): void
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_DATETIME_OBJECT);

        $result = Time::fromHMS(7, 30, 20);
        //    Must return an object...
        self::assertIsObject($result);
        //    ... of the correct type
        self::assertTrue(is_a($result, 'DateTimeInterface'));
        //    ... with the correct value
        self::assertEquals($result->format('H:i:s'), '07:30:20');
    }

    public function testTIMEWith1904Calendar(): void
    {
        SharedDate::setExcelCalendar(SharedDate::CALENDAR_MAC_1904);

        $result = Time::fromHMS(0, 0, 0);
        self::assertEquals(0, $result);
    }

    public function testTIME1900(): void
    {
        $result = Time::fromHMS(0, 0, 0);
        self::assertEquals(0, $result);
    }

    /**
     * @dataProvider providerTimeArray
     */
    public function testTimeArray(array $expectedResult, string $hour, string $minute, string $second): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=TIME({$hour}, {$minute}, {$second})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerTimeArray(): array
    {
        return [
            'row vector hour' => [[[0.250706018518519, 0.50070601851852, 0.75070601851852]], '{6,12,18}', '1', '1'],
            'column vector hour' => [[[0.250706018518519], [0.50070601851852], [0.75070601851852]], '{6;12;18}', '1', '1'],
            'matrix hour' => [[[0.250706018518519, 0.50070601851852], [0.75070601851852, 0.95903935185185]], '{6,12;18,23}', '1', '1'],
            'row vector minute' => [[[0.96667824074074, 0.97501157407407, 0.98334490740741, 0.99931712962963]], '23', '{12, 24, 36, 59}', '1'],
            'column vector minute' => [[[0.96734953703704], [0.97568287037037], [0.98401620370370], [0.99998842592593]], '23', '{12; 24; 36; 59}', '59'],
            'matrix minute' => [[[0.50833333333333, 0.51666666666667], [0.52083333333333, 0.5]], '12', '{12, 24; 30, 0}', '0'],
            'row vector second' => [[[0.50069444444444, 0.50137731481481]], '12', '1', '{0,59}'],
            'column vector second' => [[[0.99930555555556], [0.99998842592593]], '23', '59', '{0;59}'],
            'vectors hour and minute' => [
                [
                    [0.87570601851852, 0.88473379629630, 0.89376157407407, 0.90626157407407],
                    [0.91737268518519, 0.92640046296296, 0.93542824074074, 0.94792824074074],
                    [0.95903935185185, 0.96806712962963, 0.97709490740741, 0.98959490740741],
                ],
                '{21;22;23}',
                '{1, 14, 27, 45}',
                '1',
            ],
            'vectors hour and second' => [
                [
                    [0.126041666666667, 0.126215277777778],
                    [0.334375, 0.33454861111111],
                    [0.584375, 0.58454861111111],
                ],
                '{3;8;14}',
                '1',
                '{30,45}',
            ],
            'vectors minute and second' => [
                [
                    [0.75833333333333, 0.75834490740741],
                    [0.76041666666667, 0.76042824074074],
                    [0.77083333333333, 0.77084490740741],
                    [0.75, 0.750011574074074],
                ],
                '18',
                '{12; 15; 30; 0}',
                '{0,1}',
            ],
            'matrices hour and minute' => [
                [
                    [0.25070601851852, 0.50278935185185],
                    [0.75487268518519, 0.96528935185185],
                ],
                '{6, 12; 18, 23}',
                '{1, 4; 7, 10}',
                '1',
            ],
        ];
    }

    /**
     * @dataProvider providerTimeArrayException
     */
    public function testTimeArrayException(string $hour, string $minute, string $second): void
    {
        $calculation = Calculation::getInstance();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Formulae with more than two array arguments are not supported');

        $formula = "=TIME({$hour}, {$minute}, {$second})";
        $calculation->_calculateFormulaValue($formula);
    }

    public static function providerTimeArrayException(): array
    {
        return [
            'matrix arguments with 3 array values' => [
                '{6, 12; 16, 23}',
                '{1, 4; 7, 10}',
                '{0,1}',
            ],
        ];
    }
}
