<?php

namespace PhpOffice\PhpSpreadsheetTests;

class SampleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider providerSample
     *
     * @param mixed $sample
     */
    public function testSample($sample)
    {
        // Suppress output to console
        $this->setOutputCallback(function () {
        });

        require $sample;
    }

    public function providerSample()
    {
        $skipped = [];

        // Unfortunately some tests are too long be ran with code-coverage
        // analysis on Travis, so we need to exclude them
        global $argv;
        if (in_array('--coverage-clover', $argv)) {
            $tooLongToBeCovered = [
                '06 Largescale',
                '06 Largescale with cellcaching',
                '06 Largescale with cellcaching sqlite3',
                '13 CalculationCyclicFormulae',
            ];
            $skipped = array_merge($skipped, $tooLongToBeCovered);
        }

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
