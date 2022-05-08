<?php

namespace PhpOffice\PhpSpreadsheetTests\Helper;

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PHPUnit\Framework\TestCase;

class SampleTest extends TestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider providerSample
     */
    public function testSample(string $sample): void
    {
        // Suppress output to console
        $this->setOutputCallback(function (): void {
        });

        require $sample;

        self::assertTrue(true);
    }

    public function providerSample(): array
    {
        $skipped = [
            'Chart/32_Chart_read_write_PDF.php', // Unfortunately JpGraph is not up to date for latest PHP and raise many warnings
            'Chart/32_Chart_read_write_HTML.php', // idem
            'Chart/35_Chart_render.php', // idem
        ];

        // Unfortunately some tests are too long to run with code-coverage
        // analysis on GitHub Actions, so we need to exclude them
        global $argv;
        if (in_array('--coverage-clover', $argv)) {
            $tooLongToBeCovered = [
                'Basic/06_Largescale.php',
                'Basic/13_CalculationCyclicFormulae.php',
            ];
            $skipped = array_merge($skipped, $tooLongToBeCovered);
        }

        $helper = new Sample();
        $result = [];
        foreach ($helper->getSamples() as $samples) {
            foreach ($samples as $sample) {
                if (!in_array($sample, $skipped)) {
                    $file = 'samples/' . $sample;
                    $result[$sample] = [$file];
                }
            }
        }

        return $result;
    }
}
