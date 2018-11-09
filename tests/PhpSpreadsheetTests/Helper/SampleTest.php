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
            'Chart/32_Chart_read_write_PDF.php', // Unfortunately JpGraph is not up to date for latest PHP and raise many warnings
            'Chart/32_Chart_read_write_HTML.php', // idem
        ];

        // TCPDF does not support PHP 7.2
        if (version_compare(PHP_VERSION, '7.2.0') >= 0) {
            $skipped[] = 'Pdf/21_Pdf_TCPDF.php';
        }

        // DomPDF does not support PHP 7.3
        if (version_compare(PHP_VERSION, '7.2.99') >= 0) {
            $skipped[] = 'Basic/26_Utf8.php';
            $skipped[] = 'Pdf/21_Pdf_Domdf.php';
        }

        // Unfortunately some tests are too long be ran with code-coverage
        // analysis on Travis, so we need to exclude them
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
                    $file = '../samples/' . $sample;
                    $result[] = [$file];
                }
            }
        }

        return $result;
    }
}
