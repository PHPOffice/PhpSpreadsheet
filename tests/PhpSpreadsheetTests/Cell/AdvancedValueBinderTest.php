<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Collection\Cells;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class AdvancedValueBinderTest extends TestCase
{
    private $currencyCode;

    private $decimalSeparator;

    private $thousandsSeparator;

    protected function setUp(): void
    {
        $this->currencyCode = StringHelper::getCurrencyCode();
        $this->decimalSeparator = StringHelper::getDecimalSeparator();
        $this->thousandsSeparator = StringHelper::getThousandsSeparator();
    }

    protected function tearDown(): void
    {
        StringHelper::setCurrencyCode($this->currencyCode);
        StringHelper::setDecimalSeparator($this->decimalSeparator);
        StringHelper::setThousandsSeparator($this->thousandsSeparator);
    }

    public function provider()
    {
        $currencyUSD = NumberFormat::FORMAT_CURRENCY_USD_SIMPLE;
        $currencyEURO = str_replace('$', '€', NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

        return [
            ['10%', 0.1, NumberFormat::FORMAT_PERCENTAGE_00, ',', '.', '$'],
            ['$10.11', 10.11, $currencyUSD, ',', '.', '$'],
            ['$1,010.12', 1010.12, $currencyUSD, ',', '.', '$'],
            ['$20,20', 20.2, $currencyUSD, '.', ',', '$'],
            ['$2.020,20', 2020.2, $currencyUSD, '.', ',', '$'],
            ['€2.020,20', 2020.2, $currencyEURO, '.', ',', '€'],
            ['€ 2.020,20', 2020.2, $currencyEURO, '.', ',', '€'],
            ['€2,020.22', 2020.22, $currencyEURO, ',', '.', '€'],
        ];
    }

    /**
     * @dataProvider provider
     *
     * @param mixed $value
     * @param mixed $valueBinded
     * @param mixed $format
     * @param mixed $thousandsSeparator
     * @param mixed $decimalSeparator
     * @param mixed $currencyCode
     */
    public function testCurrency($value, $valueBinded, $format, $thousandsSeparator, $decimalSeparator, $currencyCode): void
    {
        $sheet = $this->getMockBuilder(Worksheet::class)
            ->setMethods(['getStyle', 'getNumberFormat', 'setFormatCode', 'getCellCollection'])
            ->getMock();
        $cellCollection = $this->getMockBuilder(Cells::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cellCollection->expects(self::any())
            ->method('getParent')
            ->willReturn($sheet);

        $sheet->expects(self::once())
            ->method('getStyle')
            ->willReturnSelf();
        $sheet->expects(self::once())
            ->method('getNumberFormat')
            ->willReturnSelf();
        $sheet->expects(self::once())
            ->method('setFormatCode')
            ->with($format)
            ->willReturnSelf();
        $sheet->expects(self::any())
            ->method('getCellCollection')
            ->willReturn($cellCollection);

        StringHelper::setCurrencyCode($currencyCode);
        StringHelper::setDecimalSeparator($decimalSeparator);
        StringHelper::setThousandsSeparator($thousandsSeparator);

        $cell = new Cell(null, DataType::TYPE_STRING, $sheet);

        $binder = new AdvancedValueBinder();
        $binder->bindValue($cell, $value);
        self::assertEquals($valueBinded, $cell->getValue());
    }
}
