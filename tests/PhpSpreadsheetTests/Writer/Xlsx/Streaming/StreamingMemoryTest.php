<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx\Streaming;

use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Streaming\StreamingWriter;
use PHPUnit\Framework\TestCase;

class StreamingMemoryTest extends TestCase
{
    public function testMemoryStaysFlat(): void
    {
        if (!function_exists('memory_reset_peak_usage')) {
            self::markTestSkipped('memory_reset_peak_usage requires PHP 8.2');
        }
        $file = File::temporaryFilename();

        try {
            $writer = new StreamingWriter($file);
            $sheet = $writer->startSheet('Big');
            memory_reset_peak_usage();
            $before = memory_get_peak_usage(true);
            for ($row = 1; $row <= 100000; ++$row) {
                $sheet->appendRow(['row ' . $row, $row, $row * 1.5, $row % 2 === 0]);
            }
            $writer->close();
            $peakDelta = memory_get_peak_usage(true) - $before;
            // 100k rows x 4 cells at ~1KB/cell would need ~400MB in the
            // standard model; the streaming writer must stay under 24MB
            // (temp stream spill threshold + zip deflate buffers).
            self::assertLessThan(24 * 1024 * 1024, $peakDelta);
            self::assertGreaterThan(0, filesize($file));
        } finally {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
}
