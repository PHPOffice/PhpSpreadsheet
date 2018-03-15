<?php

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Exception;
use PHPUnit\Framework\TestCase;

class DataSeriesValuesTest extends TestCase
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
            self::assertInstanceOf(DataSeriesValues::class, $result);
        }
    }

    public function testSetInvalidDataTypeThrowsException()
    {
        $testInstance = new DataSeriesValues();

        try {
            $testInstance->setDataType('BOOLEAN');
        } catch (Exception $e) {
            self::assertEquals($e->getMessage(), 'Invalid datatype for chart data series values');

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
        self::assertEquals($dataTypeValue, $result);
    }

    public function testGetLineWidth()
    {
        $testInstance = new DataSeriesValues();
        self::assertEquals(12700, $testInstance->getLineWidth(), 'should have default');

        $testInstance->setLineWidth(40000);
        self::assertEquals(40000, $testInstance->getLineWidth());

        $testInstance->setLineWidth(1);
        self::assertEquals(12700, $testInstance->getLineWidth(), 'should enforce minimum width');
    }
}
