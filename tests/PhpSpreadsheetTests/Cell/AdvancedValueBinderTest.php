<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\CachedObjectStorage\Memory;
use PhpOffice\PhpSpreadsheet\Cell;
use PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet;

class AdvancedValueBinderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!defined('PHPSPREADSHEET_ROOT')) {
            define('PHPSPREADSHEET_ROOT', APPLICATION_PATH . '/');
        }
        require_once PHPSPREADSHEET_ROOT . '/Bootstrap.php';
    }

    public function provider()
    {
        if (!class_exists(NumberFormat::class)) {
            $this->setUp();
        }
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
    public function testCurrency($value, $valueBinded, $format, $thousandsSeparator, $decimalSeparator, $currencyCode)
    {
        $sheet = $this->getMock(
            Worksheet::class,
            ['getStyle', 'getNumberFormat', 'setFormatCode', 'getCellCacheController']
        );
        $cache = $this->getMockBuilder(Memory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cache->expects($this->any())
                 ->method('getParent')
                 ->will($this->returnValue($sheet));

        $sheet->expects($this->once())
                 ->method('getStyle')
                 ->will($this->returnSelf());
        $sheet->expects($this->once())
                 ->method('getNumberFormat')
                 ->will($this->returnSelf());
        $sheet->expects($this->once())
                 ->method('setFormatCode')
                 ->with($format)
                 ->will($this->returnSelf());
        $sheet->expects($this->any())
                 ->method('getCellCacheController')
                 ->will($this->returnValue($cache));

        StringHelper::setCurrencyCode($currencyCode);
        StringHelper::setDecimalSeparator($decimalSeparator);
        StringHelper::setThousandsSeparator($thousandsSeparator);

        $cell = new Cell(null, DataType::TYPE_STRING, $sheet);

        $binder = new AdvancedValueBinder();
        $binder->bindValue($cell, $value);
        $this->assertEquals($valueBinded, $cell->getValue());
    }
}
