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
     *
     * @param mixed $sample
     */
    public function testSample($sample): void
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
        ];
        // TCPDF and DomPDF libraries don't support PHP8 yet
        if (\PHP_VERSION_ID >= 80000) {
            $skipped = array_merge(
                $skipped,
                [
                    'Pdf/21_Pdf_Domdf.php',
                    'Pdf/21_Pdf_TCPDF.php',
                    'Chart/35_Chart_render.php', // idem
                ]
            );
        }

        // Unfortunately some tests are too long be ran with code-coverage
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
//                if (array_pop(explode('/', $sample)) !== 'DGET.php') {
//                    continue;
//                }
                if (!in_array($sample, $skipped)) {
                    $file = 'samples/' . $sample;
                    $result[$sample] = [$file];
                }
            }
        }

        return $result;
    }
}
