<?php

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Exception;

class DataSeriesValuesTest extends \PHPUnit_Framework_TestCase
{
    public function testSetDataType()
    {
        $dataTypeValues = [
            'Number',
            'String',
        ];

        $testInstance = new DataSeriesValues();

        foreach ($dataTypeValues as $dataTypeValue) {
            $result = $testInstance->setDataType($dataTypeValue);
            $this->assertTrue($result instanceof DataSeriesValues);
        }
    }

    public function testSetInvalidDataTypeThrowsException()
    {
        $testInstance = new DataSeriesValues();

        try {
            $testInstance->setDataType('BOOLEAN');
        } catch (Exception $e) {
            $this->assertEquals($e->getMessage(), 'Invalid datatype for chart data series values');

            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function testGetDataType()
    {
        $dataTypeValue = 'String';

        $testInstance = new DataSeriesValues();
        $testInstance->setDataType($dataTypeValue);

        $result = $testInstance->getDataType();
        $this->assertEquals($dataTypeValue, $result);
    }
}
