<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PHPUnit_Framework_TestCase;

class SampleTest extends PHPUnit_Framework_TestCase
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
                '13 CalculationCyclicFormulae',
            ];
            $skipped = array_merge($skipped, $tooLongToBeCovered);
        }

        $helper = new Sample();
        $result = [];
        foreach ($helper->getSamples() as $samples) {
            foreach ($samples as $name => $sample) {
                if (!in_array($name, $skipped)) {
                    $file = '../samples/' . $sample;
                    $result[] = [$file];
                }
            }
        }

        return $result;
    }
}
