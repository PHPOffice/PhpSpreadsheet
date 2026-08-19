<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx\Streaming;

use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Streaming\StreamingWriter;
use PHPUnit\Framework\TestCase;

class StreamingMemoryTest extends TestCase
{
    public function testMemoryIsIndependentOfRowCount(): void
    {
        if (!function_exists('memory_reset_peak_usage')) {
            self::markTestSkipped('memory_reset_peak_usage requires PHP 8.2');
        }
        // ZipStream v3 reads sheet XML in fixed 16MB blocks, so the close()-time
        // peak saturates once the sheet exceeds one block (~100k rows here).
        // Doubling the rows past saturation must not raise the peak further.
        $atSaturation = $this->measurePeak(100000);
        $doubled = $this->measurePeak(200000);
        self::assertLessThan($atSaturation + 4 * 1024 * 1024, $doubled);
        self::assertLessThan(64 * 1024 * 1024, $doubled);
    }

    private function measurePeak(int $rows): int
    {
        $file = File::temporaryFilename();

        try {
            $writer = new StreamingWriter($file);
            $sheet = $writer->startSheet('Big');
            if (function_exists('memory_reset_peak_usage')) { // PHP 8.2+, the caller skips otherwise
                memory_reset_peak_usage();
            }
            $before = memory_get_peak_usage(true);
            for ($row = 1; $row <= $rows; ++$row) {
                $sheet->appendRow(['row ' . $row, $row, $row * 1.5, $row % 2 === 0]);
            }
            $writer->close();
            self::assertGreaterThan(0, filesize($file));

            return memory_get_peak_usage(true) - $before;
        } finally {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
}
