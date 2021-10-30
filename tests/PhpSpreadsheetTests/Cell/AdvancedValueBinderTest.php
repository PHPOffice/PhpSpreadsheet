<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Collection\Cells;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AdvancedValueBinderTest extends TestCase
{
    /**
     * @var string
     */
    private $currencyCode;

    /**
     * @var string
     */
    private $decimalSeparator;

    /**
     * @var string
     */
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

    public function testNullValue(): void
    {
        /** @var Cell&MockObject $cellStub */
        $cellStub = $this->getMockBuilder(Cell::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Configure the stub.
        $cellStub->expects(self::once())
            ->method('setValueExplicit')
            ->with(null, DataType::TYPE_NULL)
            ->willReturn(true);

        $binder = new AdvancedValueBinder();
        $binder->bindValue($cellStub, null);
    }

    /**
     * @dataProvider currencyProvider
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
            ->onlyMethods(['getStyle', 'getCellCollection'])
            ->addMethods(['getNumberFormat', 'setFormatCode'])
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
        // @phpstan-ignore-next-line
        $sheet->expects(self::once())
            ->method('getNumberFormat')
            ->willReturnSelf();
        // @phpstan-ignore-next-line
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

    public function currencyProvider(): array
    {
        $currencyUSD = NumberFormat::FORMAT_CURRENCY_USD_SIMPLE;
        $currencyEURO = str_replace('$', '€', NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

        return [
            ['$10.11', 10.11, $currencyUSD, ',', '.', '$'],
            ['$1,010.12', 1010.12, $currencyUSD, ',', '.', '$'],
            ['$20,20', 20.2, $currencyUSD, '.', ',', '$'],
            ['$2.020,20', 2020.2, $currencyUSD, '.', ',', '$'],
            ['€2.020,20', 2020.2, $currencyEURO, '.', ',', '€'],
            ['€ 2.020,20', 2020.2, $currencyEURO, '.', ',', '€'],
            ['€2,020.22', 2020.22, $currencyEURO, ',', '.', '€'],
            ['$10.11', 10.11, $currencyUSD, ',', '.', '€'],
        ];
    }

    /**
     * @dataProvider fractionProvider
     *
     * @param mixed $value
     * @param mixed $valueBinded
     * @param mixed $format
     */
    public function testFractions($value, $valueBinded, $format): void
    {
        $sheet = $this->getMockBuilder(Worksheet::class)
            ->onlyMethods(['getStyle', 'getCellCollection'])
            ->addMethods(['getNumberFormat', 'setFormatCode'])
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
        // @phpstan-ignore-next-line
        $sheet->expects(self::once())
            ->method('getNumberFormat')
            ->willReturnSelf();
        // @phpstan-ignore-next-line
        $sheet->expects(self::once())
            ->method('setFormatCode')
            ->with($format)
            ->willReturnSelf();
        $sheet->expects(self::any())
            ->method('getCellCollection')
            ->willReturn($cellCollection);

        $cell = new Cell(null, DataType::TYPE_STRING, $sheet);

        $binder = new AdvancedValueBinder();
        $binder->bindValue($cell, $value);
        self::assertEquals($valueBinded, $cell->getValue());
    }

    public function fractionProvider(): array
    {
        return [
            ['1/5', 0.2, '?/?'],
            ['-1/5', -0.2, '?/?'],
            ['12/5', 2.4, '??/?'],
            ['2/100', 0.02, '?/???'],
            ['15/12', 1.25, '??/??'],
            ['20/100', 0.2, '??/???'],
            ['1 3/5', 1.6, '# ?/?'],
            ['-1 3/5', -1.6, '# ?/?'],
            ['1 4/20', 1.2, '# ?/??'],
            ['1 16/20', 1.8, '# ??/??'],
            ['12 20/100', 12.2, '# ??/???'],
        ];
    }

    /**
     * @dataProvider percentageProvider
     *
     * @param mixed $value
     * @param mixed $valueBinded
     * @param mixed $format
     */
    public function testPercentages($value, $valueBinded, $format): void
    {
        $sheet = $this->getMockBuilder(Worksheet::class)
            ->onlyMethods(['getStyle', 'getCellCollection'])
            ->addMethods(['getNumberFormat', 'setFormatCode'])
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
        // @phpstan-ignore-next-line
        $sheet->expects(self::once())
            ->method('getNumberFormat')
            ->willReturnSelf();
        // @phpstan-ignore-next-line
        $sheet->expects(self::once())
            ->method('setFormatCode')
            ->with($format)
            ->willReturnSelf();
        $sheet->expects(self::any())
            ->method('getCellCollection')
            ->willReturn($cellCollection);

        $cell = new Cell(null, DataType::TYPE_STRING, $sheet);

        $binder = new AdvancedValueBinder();
        $binder->bindValue($cell, $value);
        self::assertEquals($valueBinded, $cell->getValue());
    }

    public function percentageProvider(): array
    {
        return [
            ['10%', 0.1, NumberFormat::FORMAT_PERCENTAGE_00],
            ['-12%', -0.12, NumberFormat::FORMAT_PERCENTAGE_00],
            ['120%', 1.2, NumberFormat::FORMAT_PERCENTAGE_00],
            ['12.5%', 0.125, NumberFormat::FORMAT_PERCENTAGE_00],
        ];
    }

    /**
     * @dataProvider timeProvider
     *
     * @param mixed $value
     * @param mixed $valueBinded
     * @param mixed $format
     */
    public function testTimes($value, $valueBinded, $format): void
    {
        $sheet = $this->getMockBuilder(Worksheet::class)
            ->onlyMethods(['getStyle', 'getCellCollection'])
            ->addMethods(['getNumberFormat', 'setFormatCode'])
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
        // @phpstan-ignore-next-line
        $sheet->expects(self::once())
            ->method('getNumberFormat')
            ->willReturnSelf();
        // @phpstan-ignore-next-line
        $sheet->expects(self::once())
            ->method('setFormatCode')
            ->with($format)
            ->willReturnSelf();
        $sheet->expects(self::any())
            ->method('getCellCollection')
            ->willReturn($cellCollection);

        $cell = new Cell(null, DataType::TYPE_STRING, $sheet);

        $binder = new AdvancedValueBinder();
        $binder->bindValue($cell, $value);
        self::assertEquals($valueBinded, $cell->getValue());
    }

    public function timeProvider(): array
    {
        return [
            ['1:20', 0.05555555556, NumberFormat::FORMAT_DATE_TIME3],
            ['09:17', 0.386805555556, NumberFormat::FORMAT_DATE_TIME3],
            ['15:00', 0.625, NumberFormat::FORMAT_DATE_TIME3],
            ['17:12:35', 0.71707175926, NumberFormat::FORMAT_DATE_TIME4],
            ['23:58:20', 0.99884259259, NumberFormat::FORMAT_DATE_TIME4],
        ];
    }

    /**
     * @dataProvider stringProvider
     */
    public function testStringWrapping(string $value, bool $wrapped): void
    {
        $sheet = $this->getMockBuilder(Worksheet::class)
            ->onlyMethods(['getStyle', 'getCellCollection'])
            ->addMethods(['getAlignment', 'setWrapText'])
            ->getMock();
        $cellCollection = $this->getMockBuilder(Cells::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cellCollection->expects(self::any())
            ->method('getParent')
            ->willReturn($sheet);

        $sheet->expects($wrapped ? self::once() : self::never())
            ->method('getStyle')
            ->willReturnSelf();
        // @phpstan-ignore-next-line
        $sheet->expects($wrapped ? self::once() : self::never())
            ->method('getAlignment')
            ->willReturnSelf();
        // @phpstan-ignore-next-line
        $sheet->expects($wrapped ? self::once() : self::never())
            ->method('setWrapText')
            ->with($wrapped)
            ->willReturnSelf();
        $sheet->expects(self::any())
            ->method('getCellCollection')
            ->willReturn($cellCollection);

        $cell = new Cell(null, DataType::TYPE_STRING, $sheet);

        $binder = new AdvancedValueBinder();
        $binder->bindValue($cell, $value);
    }

    public function stringProvider(): array
    {
        return [
            ['Hello World', false],
            ["Hello\nWorld", true],
        ];
    }
}
