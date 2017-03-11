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
        $skipped = [
            '21 Pdf', // for now we don't have 3rdparty libs to tests PDF, but it should be added
            '06 Largescale with cellcaching sqlite3', // Travis started crashing after they upgraded from PHP 7.0.13 to 7.0.14, so we disable it for now
        ];

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
