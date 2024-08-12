<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Helper;

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PHPUnit\Framework\TestCase;

class SampleTest extends TestCase
{
    /**
     * @runInSeparateProcess
     *
     * @preserveGlobalState disabled
     *
     * @dataProvider providerSample
     */
    public function testSample(string $sample): void
    {
        ob_start();
        require $sample;
        ob_end_clean();

        self::assertTrue(true);
    }

    public static function providerSample(): array
    {
        $skipped = [
        ];

        // Unfortunately some tests are too long to run with code-coverage
        // analysis on GitHub Actions, so we need to exclude them
        global $argv;
        if (in_array('--coverage-clover', $argv)) {
            $tooLongToBeCovered = [
                'Basic/06_Largescale.php',
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
