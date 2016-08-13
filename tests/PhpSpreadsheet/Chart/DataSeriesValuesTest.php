<?php

namespace PhpSpreadsheet\Tests\Chart;

class DataSeriesValuesTest extends \PHPUnit_Framework_TestCase
{
    public function testSetDataType()
    {
        $dataTypeValues = array(
            'Number',
            'String'
        );

        $testInstance = new \PHPExcel\Chart\DataSeriesValues;

        foreach ($dataTypeValues as $dataTypeValue) {
            $result = $testInstance->setDataType($dataTypeValue);
            $this->assertTrue($result instanceof \PHPExcel\Chart\DataSeriesValues);
        }
    }

    public function testSetInvalidDataTypeThrowsException()
    {
        $testInstance = new \PHPExcel\Chart\DataSeriesValues;

        try {
            $result = $testInstance->setDataType('BOOLEAN');
        } catch (\PHPExcel\Exception $e) {
            $this->assertEquals($e->getMessage(), 'Invalid datatype for chart data series values');
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function testGetDataType()
    {
        $dataTypeValue = 'String';

        $testInstance = new \PHPExcel\Chart\DataSeriesValues;
        $setValue = $testInstance->setDataType($dataTypeValue);

        $result = $testInstance->getDataType();
        $this->assertEquals($dataTypeValue, $result);
    }
}
