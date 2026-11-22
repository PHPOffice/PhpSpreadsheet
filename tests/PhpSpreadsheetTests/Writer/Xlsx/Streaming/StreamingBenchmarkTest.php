<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx\Streaming;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheetBenchmarks\StreamingWriterBenchmark;
use PHPUnit\Framework\TestCase;

class StreamingBenchmarkTest extends TestCase
{
    /** @var string[] */
    private array $files = [];

    protected function tearDown(): void
    {
        foreach ($this->files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    public function testBothEnginesProduceEquivalentValuesAndDateFormats(): void
    {
        $standardFile = File::temporaryFilename();
        $streamingFile = File::temporaryFilename();
        $this->files = [$standardFile, $streamingFile];
        StreamingWriterBenchmark::writeStandard(6, $standardFile);
        StreamingWriterBenchmark::writeStreaming(6, $streamingFile);
        $standard = (new Xlsx())->load($standardFile);
        $streaming = (new Xlsx())->load($streamingFile);
        self::assertSame($standard->getSheetNames(), $streaming->getSheetNames());
        self::assertSame(
            $standard->getActiveSheet()->toArray(null, false, false),
            $streaming->getActiveSheet()->toArray(null, false, false)
        );
        foreach ([$standard, $streaming] as $spreadsheet) {
            $sheet = $spreadsheet->getActiveSheet();
            self::assertFalse($sheet->getCell('D1')->getValue());
            self::assertTrue($sheet->getCell('D2')->getValue());
            self::assertFalse($sheet->getCell('H1')->getValue());
            self::assertTrue($sheet->getCell('H3')->getValue());
            for ($row = 1; $row <= 6; ++$row) {
                self::assertSame(46023.0, $sheet->getCell('E' . $row)->getValue());
                self::assertSame(NumberFormat::FORMAT_DATE_DATETIME_BETTER, $sheet->getStyle('E' . $row)->getNumberFormat()->getFormatCode());
            }
            $spreadsheet->disconnectWorksheets();
        }
    }
}
