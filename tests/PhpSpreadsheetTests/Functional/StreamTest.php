<?php

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class StreamTest extends TestCase
{
    public function providerFormats(): array
    {
        $providerFormats = [
            ['Xls'],
            ['Xlsx'],
            ['Ods'],
            ['Csv'],
            ['Html'],
            ['Mpdf'],
        ];

        if (\PHP_VERSION_ID < 80000) {
            $providerFormats = array_merge(
                $providerFormats,
                [['Tcpdf'], ['Dompdf']]
            );
        }

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
        self::assertSame(0, fstat($stream)['size']);

        $writer->save($stream);

        self::assertIsResource($stream, 'should not close the stream for further usage out of PhpSpreadsheet');
        self::assertGreaterThan(0, fstat($stream)['size'], 'something should have been written to the stream');
        self::assertGreaterThan(0, ftell($stream), 'should not be rewinded, because not all streams support it');
    }
}
