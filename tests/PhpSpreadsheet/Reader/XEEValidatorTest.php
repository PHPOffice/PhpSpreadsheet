<?php

namespace PhpSpreadsheet\Tests\Reader;

class XEEValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerInvalidXML
     * @expectedException \PHPExcel\Reader\Exception
     */
    public function testInvalidXML($filename)
    {
        $reader = $this->getMockForAbstractClass('\PHPExcel\Reader\BaseReader');
        $expectedResult = 'FAILURE: Should throw an Exception rather than return a value';
        $result = $reader->securityScanFile($filename);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerInvalidXML()
    {
        $tests = [];
        foreach (glob('rawTestData/Reader/XEETestInvalid*.xml') as $file) {
            $tests[] = [realpath($file), true];
        }
        return $tests;
    }

    /**
     * @dataProvider providerValidXML
     */
    public function testValidXML($filename, $expectedResult)
    {
        $reader = $this->getMockForAbstractClass('\PHPExcel\Reader\BaseReader');
        $result = $reader->securityScanFile($filename);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerValidXML()
    {
        $tests = array();
        foreach (glob('rawTestData/Reader/XEETestValid*.xml') as $file) {
            $tests[] = [realpath($file), file_get_contents($file)];
        }
        return $tests;
    }
}
