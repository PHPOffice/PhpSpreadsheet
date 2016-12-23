<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\IOFactory;

class IOFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerIdentify
     *
     * @param mixed $file
     * @param mixed $expected
     */
    public function testIdentify($file, $expected)
    {
        $actual = IOFactory::identify($file);
        $this->assertSame($expected, $actual);
    }

    public function providerIdentify()
    {
        return [
            ['../samples/templates/26template.xlsx', 'Xlsx'],
            ['../samples/templates/GnumericTest.gnumeric', 'Gnumeric'],
            ['../samples/templates/30template.xls', 'Xls'],
            ['../samples/templates/OOCalcTest.ods', 'Ods'],
            ['../samples/templates/SylkTest.slk', 'SYLK'],
            ['../samples/templates/Excel2003XMLTest.xml', 'Excel2003XML'],
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIdentifyNonExistingFileThrowException()
    {
        IOFactory::identify('/non/existing/file');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIdentifyExistingDirectoryThrowExceptions()
    {
        IOFactory::identify('.');
    }
}
