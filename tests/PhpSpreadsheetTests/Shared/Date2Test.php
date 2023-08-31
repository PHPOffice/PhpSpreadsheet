<?php

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PHPUnit\Framework\TestCase;

class Date2Test extends TestCase
{
    /** @var ?Spreadsheet */
    private $spreadsheet;

    /** @var int */
    private $calculateDateTimeType;

    protected function setUp(): void
    {
        $this->calculateDateTimeType = Cell::getCalculateDateTimeType();
    }

    protected function tearDown(): void
    {
        Cell::setCalculateDateTimeType($this->calculateDateTimeType);
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
    }

    public function testInvalidType(): void
    {
        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage('for calculated date time type');
        Cell::setCalculateDateTimeType(-1);
    }

    /**
     * @dataProvider providerTimeOnly
     *
     * @param float|int $expectedResult
     * @param float|int $value
     * @param string $format
     */
    public function testTimeOnly($expectedResult, $value, ?string $format = null): void
    {
        Cell::setCalculateDateTimeType(Cell::CALCULATE_TIME_FLOAT);
        $this->spreadsheet = new Spreadsheet();
        self::assertSame(0, $this->spreadsheet->getActiveSheetIndex());
        $sheet = $this->spreadsheet->getActiveSheet();
        $newSheet = $this->spreadsheet->createSheet();
        $newSheet->getCell('B7')->setValue('Here');
        $sheet->getCell('A1')->setValue($value);
        if ($format !== null) {
            $sheet->getStyle('A1')->getNumberFormat()->setFormatCode($format);
        }
        $sheet->setSelectedCells('B7');
        $this->spreadsheet->setActiveSheetIndex(1);
        self::assertSame($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
        self::assertSame('B7', $sheet->getSelectedCells());
        self::assertSame(1, $this->spreadsheet->getActiveSheetIndex());
    }

    public static function providerTimeOnly(): array
    {
        $integerValue = 44046;
        $integerValueAsFloat = (float) $integerValue;
        $integerValueAsDateFormula = '=DATEVALUE("2020-08-03")';
        $floatValue = 44015.25;
        $floatValueAsDateFormula = '=DATEVALUE("2020-07-03")+TIMEVALUE("06:00")';

        return [
            'default format integer' => [$integerValue, $integerValue],
            'default format float' => [$floatValue, $floatValue],
            'date format integer' => [$integerValue, $integerValue, NumberFormat::FORMAT_DATE_YYYYMMDD],
            'date format float' => [$floatValue, $floatValue, NumberFormat::FORMAT_DATE_YYYYMMDD],
            'datetime format integer' => [$integerValueAsFloat, $integerValue, 'yyyy-mm-dd h:mm'],
            'datetime format float' => [$floatValue, $floatValue, 'yyyy-mm-dd h:mm'],
            'time format integer' => [$integerValueAsFloat, $integerValue, NumberFormat::FORMAT_DATE_TIME1],
            'time format float' => [$floatValue, $floatValue, NumberFormat::FORMAT_DATE_TIME1],
            'date formula integer fltfmt' => [$integerValueAsFloat, $integerValueAsDateFormula, NumberFormat::FORMAT_DATE_TIME1],
            'date formula float' => [$floatValue, $floatValueAsDateFormula, NumberFormat::FORMAT_DATE_TIME1],
            'date formula integer intfmt but formula returns float' => [$integerValueAsFloat, $integerValueAsDateFormula, NumberFormat::FORMAT_DATE_YYYYMMDD],
        ];
    }

    /**
     * @dataProvider providerDateAndTime
     *
     * @param float|int $expectedResult
     * @param float|int $value
     * @param string $format
     */
    public function testDateAndTime($expectedResult, $value, ?string $format = null): void
    {
        Cell::setCalculateDateTimeType(Cell::CALCULATE_DATE_TIME_FLOAT);
        $this->spreadsheet = new Spreadsheet();
        self::assertSame(0, $this->spreadsheet->getActiveSheetIndex());
        $sheet = $this->spreadsheet->getActiveSheet();
        $newSheet = $this->spreadsheet->createSheet();
        $newSheet->getCell('B7')->setValue('Here');
        $sheet->getCell('A1')->setValue($value);
        if ($format !== null) {
            $sheet->getStyle('A1')->getNumberFormat()->setFormatCode($format);
        }
        $sheet->setSelectedCells('B7');
        $this->spreadsheet->setActiveSheetIndex(1);
        self::assertSame($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
        self::assertSame('B7', $sheet->getSelectedCells());
        self::assertSame(1, $this->spreadsheet->getActiveSheetIndex());
    }

    public static function providerDateAndTime(): array
    {
        $integerValue = 44046;
        $integerValueAsFloat = (float) $integerValue;
        $integerValueAsDateFormula = '=DATEVALUE("2020-08-03")';
        $floatValue = 44015.25;
        $floatValueAsDateFormula = '=DATEVALUE("2020-07-03")+TIMEVALUE("06:00")';

        return [
            'default format integer' => [$integerValue, $integerValue],
            'default format float' => [$floatValue, $floatValue],
            'date format integer' => [$integerValueAsFloat, $integerValue, NumberFormat::FORMAT_DATE_YYYYMMDD],
            'date format float' => [$floatValue, $floatValue, NumberFormat::FORMAT_DATE_YYYYMMDD],
            'datetime format integer' => [$integerValueAsFloat, $integerValue, 'yyyy-mm-dd h:mm'],
            'datetime format float' => [$floatValue, $floatValue, 'yyyy-mm-dd h:mm'],
            'time format integer' => [$integerValueAsFloat, $integerValue, NumberFormat::FORMAT_DATE_TIME1],
            'time format float' => [$floatValue, $floatValue, NumberFormat::FORMAT_DATE_TIME1],
            'date formula integer fltfmt' => [$integerValueAsFloat, $integerValueAsDateFormula, NumberFormat::FORMAT_DATE_TIME1],
            'date formula float' => [$floatValue, $floatValueAsDateFormula, NumberFormat::FORMAT_DATE_TIME1],
            'date formula integer intfmt but formula returns float' => [$integerValueAsFloat, $integerValueAsDateFormula, NumberFormat::FORMAT_DATE_YYYYMMDD],
        ];
    }

    /**
     * @dataProvider providerAsis
     *
     * @param float|int $expectedResult
     * @param float|int $value
     * @param string $format
     */
    public function testDefault($expectedResult, $value, ?string $format = null): void
    {
        //Cell::setCalculateDateTimeType(Cell::CALCULATE_DATE_TIME_ASIS);
        $this->spreadsheet = new Spreadsheet();
        self::assertSame(0, $this->spreadsheet->getActiveSheetIndex());
        $sheet = $this->spreadsheet->getActiveSheet();
        $newSheet = $this->spreadsheet->createSheet();
        $newSheet->getCell('B7')->setValue('Here');
        $sheet->getCell('A1')->setValue($value);
        if ($format !== null) {
            $sheet->getStyle('A1')->getNumberFormat()->setFormatCode($format);
        }
        $sheet->setSelectedCells('B7');
        $this->spreadsheet->setActiveSheetIndex(1);
        self::assertSame($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
        self::assertSame('B7', $sheet->getSelectedCells());
        self::assertSame(1, $this->spreadsheet->getActiveSheetIndex());
    }

    /**
     * @dataProvider providerAsis
     *
     * @param float|int $expectedResult
     * @param float|int $value
     * @param string $format
     */
    public function testAsis($expectedResult, $value, ?string $format = null): void
    {
        Cell::setCalculateDateTimeType(Cell::CALCULATE_DATE_TIME_ASIS);
        $this->spreadsheet = new Spreadsheet();
        self::assertSame(0, $this->spreadsheet->getActiveSheetIndex());
        $sheet = $this->spreadsheet->getActiveSheet();
        $newSheet = $this->spreadsheet->createSheet();
        $newSheet->getCell('B7')->setValue('Here');
        $sheet->getCell('A1')->setValue($value);
        if ($format !== null) {
            $sheet->getStyle('A1')->getNumberFormat()->setFormatCode($format);
        }
        $sheet->setSelectedCells('B7');
        $this->spreadsheet->setActiveSheetIndex(1);
        self::assertSame($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
        self::assertSame('B7', $sheet->getSelectedCells());
        self::assertSame(1, $this->spreadsheet->getActiveSheetIndex());
    }

    public static function providerAsis(): array
    {
        $integerValue = 44046;
        $integerValueAsFloat = (float) $integerValue;
        $integerValueAsDateFormula = '=DATEVALUE("2020-08-03")';
        $floatValue = 44015.25;
        $floatValueAsDateFormula = '=DATEVALUE("2020-07-03")+TIMEVALUE("06:00")';

        return [
            'default format integer' => [$integerValue, $integerValue],
            'default format float' => [$floatValue, $floatValue],
            'date format integer' => [$integerValue, $integerValue, NumberFormat::FORMAT_DATE_YYYYMMDD],
            'date format float' => [$floatValue, $floatValue, NumberFormat::FORMAT_DATE_YYYYMMDD],
            'datetime format integer' => [$integerValue, $integerValue, 'yyyy-mm-dd h:mm'],
            'datetime format float' => [$floatValue, $floatValue, 'yyyy-mm-dd h:mm'],
            'time format integer' => [$integerValue, $integerValue, NumberFormat::FORMAT_DATE_TIME1],
            'time format float' => [$floatValue, $floatValue, NumberFormat::FORMAT_DATE_TIME1],
            'date formula integer fltfmt' => [$integerValueAsFloat, $integerValueAsDateFormula, NumberFormat::FORMAT_DATE_TIME1],
            'date formula float' => [$floatValue, $floatValueAsDateFormula, NumberFormat::FORMAT_DATE_TIME1],
            'date formula integer intfmt but formula returns float' => [$integerValueAsFloat, $integerValueAsDateFormula, NumberFormat::FORMAT_DATE_YYYYMMDD],
        ];
    }
}
