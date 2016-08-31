<?php

namespace PhpOffice\PhpSpreadsheetTests;

class SampleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider providerSample
     */
    public function testSample($sample)
    {
        require $sample;
    }

    public function providerSample()
    {
        $skipped = [
            '07 Reader PCLZip', // Excel2007 cannot load file, leading to OpenOffice trying to and crashing. This is a bug that should be fixed
            '20 Read OOCalc with PCLZip', // Crash: Call to undefined method \PhpOffice\PhpSpreadsheet\Shared\ZipArchive::statName()
            '21 Pdf', // for now we don't have 3rdparty libs to tests PDF, but it should be added
        ];
        $helper = new \PhpOffice\PhpSpreadsheet\Helper\Sample();
        $samples = [];
        foreach ($helper->getSamples() as $name => $sample) {
            if (!in_array($name, $skipped)) {
                $samples[$name] = [$sample];
            }
        }

        return $samples;
    }
}
