<?php

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

/**
 * Not clear that Dompdf will be Php8.4 compatible in time.
 * Run in separate process and add version test till it is ready.
 *
 * @runTestsInSeparateProcesses
 */
class StreamTest extends TestCase
{
    public static function providerFormats(): array
    {
        $providerFormats = [
            ['Xls'],
            ['Xlsx'],
            ['Ods'],
            ['Csv'],
            ['Html'],
            ['Mpdf'],
            ['Dompdf'],
            ['Tcpdf'],
        ];

        return $providerFormats;
    }

    /**
     * @dataProvider providerFormats
     */
    public function testAllWritersCanWriteToStream(string $format): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->setCellValue('A1', 'foo');
        $writer = IOFactory::createWriter($spreadsheet, $format);

        $stream = fopen('php://memory', 'wb+');
        $stat = ($stream === false) ? false : fstat($stream);
        if ($stream === false || $stat === false) {
            self::fail('fopen or fstat failed');
        } else {
            self::assertSame(0, $stat['size']);

            if ($format === 'Dompdf' && PHP_VERSION_ID >= 80400) {
                @$writer->save($stream);
            } else {
                $writer->save($stream);
            }

            self::assertIsResource($stream, 'should not close the stream for further usage out of PhpSpreadsheet');
            $stat = fstat($stream);
            if ($stat === false) {
                self::fail('fstat failed');
            } else {
                self::assertGreaterThan(0, $stat['size'], 'something should have been written to the stream');
            }
            self::assertGreaterThan(0, ftell($stream), 'should not be rewinded, because not all streams support it');
        }
    }
}
