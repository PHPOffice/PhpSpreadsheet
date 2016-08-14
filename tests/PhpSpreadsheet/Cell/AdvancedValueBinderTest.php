<?php

namespace PhpSpreadsheet\Tests\Cell;

use PHPExcel\Worksheet;
use PHPExcel\Style\NumberFormat;
use PHPExcel\Shared\StringHelper;
use PHPExcel\Cell;
use PHPExcel\Cell\AdvancedValueBinder;
use PHPExcel\Cell\DataType;
use PHPExcel\CachedObjectStorage\Memory;

class AdvancedValueBinderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH . '/');
        }
        require_once(PHPEXCEL_ROOT . '/Bootstrap.php');
    }

    public function provider()
    {
        if (!class_exists(NumberFormat::class)) {
            $this->setUp();
        }
        $currencyUSD = NumberFormat::FORMAT_CURRENCY_USD_SIMPLE;
        $currencyEURO = str_replace('$', '€', NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

        return array(
            array('10%', 0.1, NumberFormat::FORMAT_PERCENTAGE_00, ',', '.', '$'),
            array('$10.11', 10.11, $currencyUSD, ',', '.', '$'),
            array('$1,010.12', 1010.12, $currencyUSD, ',', '.', '$'),
            array('$20,20', 20.2, $currencyUSD, '.', ',', '$'),
            array('$2.020,20', 2020.2, $currencyUSD, '.', ',', '$'),
            array('€2.020,20', 2020.2, $currencyEURO, '.', ',', '€'),
            array('€ 2.020,20', 2020.2, $currencyEURO, '.', ',', '€'),
            array('€2,020.22', 2020.22, $currencyEURO, ',', '.', '€'),
        );
    }

    /**
     * @dataProvider provider
     */
    public function testCurrency($value, $valueBinded, $format, $thousandsSeparator, $decimalSeparator, $currencyCode)
    {
        $sheet = $this->getMock(
            Worksheet::class,
            array('getStyle', 'getNumberFormat', 'setFormatCode','getCellCacheController')
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
