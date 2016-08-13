<?php

namespace PhpSpreadsheet\Tests\Cell;

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
        if (!class_exists('\\PHPExcel\\Style\\NumberFormat')) {
            $this->setUp();
        }
        $currencyUSD = \PHPExcel\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE;
        $currencyEURO = str_replace('$', '€', \PHPExcel\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

        return array(
            array('10%', 0.1, \PHPExcel\Style\NumberFormat::FORMAT_PERCENTAGE_00, ',', '.', '$'),
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
            '\\PHPExcel\\Worksheet',
            array('getStyle', 'getNumberFormat', 'setFormatCode','getCellCacheController')
        );
        $cache = $this->getMockBuilder('\\PHPExcel\\CachedObjectStorage\\Memory')
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

        \PHPExcel\Shared\StringHelper::setCurrencyCode($currencyCode);
        \PHPExcel\Shared\StringHelper::setDecimalSeparator($decimalSeparator);
        \PHPExcel\Shared\StringHelper::setThousandsSeparator($thousandsSeparator);

        $cell = new \PHPExcel\Cell(null, \PHPExcel\Cell\DataType::TYPE_STRING, $sheet);

        $binder = new \PHPExcel\Cell\AdvancedValueBinder();
        $binder->bindValue($cell, $value);
        $this->assertEquals($valueBinded, $cell->getValue());
    }
}
