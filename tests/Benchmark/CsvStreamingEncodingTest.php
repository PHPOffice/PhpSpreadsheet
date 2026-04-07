<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetBenchmarks;

use Exception;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Shared\File;
//use PhpOffice\PhpSpreadsheetBenchmarks\CsvChunk;
use PhpOffice\PhpSpreadsheetTests\Reader\Csv\CsvIconv2;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the streaming/chunked encoding conversion in the CSV reader.
 *
 * Verifies that encoding conversion produces correct results when
 * processing files in chunks rather than loading them entirely into memory.
 */
class CsvStreamingEncodingTest extends TestCase
{
    private string $whichCsv = 'CsvChunk';
    //private string $whichCsv = 'CsvIconv2';
    //private string $whichCsv = 'Csv';

    private string $tempFile = '';

    protected function tearDown(): void
    {
        if ($this->tempFile !== '') {
            unlink($this->tempFile);
            $this->tempFile = '';
        }
    }

    private function newCsv(): Csv
    {
        if ($this->whichCsv === 'CsvChunk') {
            return new CsvChunk();
        }

        if ($this->whichCsv === 'CsvIconv2') {
            return new CsvIconv2();
        }

        return new Csv();
    }

    /**
     * Test that existing non-UTF-8 CSV files are still read correctly
     * with the streaming approach.
     */
    #[DataProvider('providerExistingEncodings')]
    public function testExistingNonUtf8FilesReadCorrectly(string $filename, string $encoding): void
    {
        $reader = $this->newCsv();
        $reader->setInputEncoding($encoding);
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertEquals("\u{00C5}", $sheet->getCell('A1')->getValue(), 'Å character should be preserved after streaming encoding conversion');
        $spreadsheet->disconnectWorksheets();
    }

    public static function providerExistingEncodings(): array
    {
        return [
            'ISO-8859-1' => ['tests/data/Reader/CSV/encoding.iso88591.csv', 'ISO-8859-1'],
            'UTF-16BE' => ['tests/data/Reader/CSV/encoding.utf16be.csv', 'UTF-16BE'],
            'UTF-16LE' => ['tests/data/Reader/CSV/encoding.utf16le.csv', 'UTF-16LE'],
            'UTF-32BE' => ['tests/data/Reader/CSV/encoding.utf32be.csv', 'UTF-32BE'],
            'UTF-32LE' => ['tests/data/Reader/CSV/encoding.utf32le.csv', 'UTF-32LE'],
        ];
    }

    /**
     * Test that UTF-8 files (no conversion needed) are unaffected by the changes.
     */
    public function testUtf8FileUnaffected(): void
    {
        $reader = $this->newCsv();
        $reader->setInputEncoding('UTF-8');
        $spreadsheet = $reader->load('tests/data/Reader/CSV/encoding.utf8.csv');
        $sheet = $spreadsheet->getActiveSheet();
        self::assertEquals("\u{00C5}", $sheet->getCell('A1')->getValue());
        $val = $sheet->getCell('B1')->getValue();
        self::assertIsScalar($val);
        self::assertEquals(1, (int) $val);
        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test that UTF-8 BOM files work correctly without conversion.
     */
    public function testUtf8BomFileUnaffected(): void
    {
        $reader = $this->newCsv();
        $reader->setInputEncoding('UTF-8');
        $spreadsheet = $reader->load('tests/data/Reader/CSV/encoding.utf8bom.csv');
        $sheet = $spreadsheet->getActiveSheet();
        self::assertEquals("\u{00C5}", $sheet->getCell('A1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test Windows-1252 encoded file with various special characters
     * is correctly converted via streaming.
     */
    public function testWindows1252SpecialCharacters(): void
    {
        // Build a CSV in Windows-1252 with various special characters
        $utf8Csv = "Name,City,Note\n"
            . "Müller,Zürich,Straße\n"
            . "Café,Père,£100\n"
            . "Smörgås,Göteborg,©2024\n";

        $win1252Csv = mb_convert_encoding($utf8Csv, 'Windows-1252', 'UTF-8');
        $filename = $this->tempFile = File::temporaryFileName();
        file_put_contents($filename, $win1252Csv);

        $reader = $this->newCsv();
        $reader->setInputEncoding('CP1252');
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();

        // Row 1: headers
        self::assertSame('Name', $sheet->getCell('A1')->getValue());
        self::assertSame('City', $sheet->getCell('B1')->getValue());
        self::assertSame('Note', $sheet->getCell('C1')->getValue());

        // Row 2: German characters
        self::assertSame('Müller', $sheet->getCell('A2')->getValue());
        self::assertSame('Zürich', $sheet->getCell('B2')->getValue());
        self::assertSame('Straße', $sheet->getCell('C2')->getValue());

        // Row 3: French + symbol
        self::assertSame('Café', $sheet->getCell('A3')->getValue());
        self::assertSame('Père', $sheet->getCell('B3')->getValue());
        self::assertSame('£100', $sheet->getCell('C3')->getValue());

        // Row 4: Swedish + symbol
        self::assertSame('Smörgås', $sheet->getCell('A4')->getValue());
        self::assertSame('Göteborg', $sheet->getCell('B4')->getValue());
        self::assertSame('©2024', $sheet->getCell('C4')->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test ISO-8859-1 encoded file with accented characters.
     */
    public function testIso88591AccentedCharacters(): void
    {
        $utf8Csv = "première,deuxième,troisième\n"
            . "quatrième,cinquième,sixième\n";

        $iso88591Csv = mb_convert_encoding($utf8Csv, 'ISO-8859-1', 'UTF-8');
        $filename = $this->tempFile = File::temporaryFileName();
        file_put_contents($filename, $iso88591Csv);

        $reader = $this->newCsv();
        $reader->setInputEncoding('ISO-8859-1');
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();

        self::assertSame('première', $sheet->getCell('A1')->getValue());
        self::assertSame('sixième', $sheet->getCell('C2')->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test that a large non-UTF-8 file produces identical results to
     * what the old (non-streaming) approach would produce.
     * This verifies that chunk boundaries don't corrupt data.
     */
    public function testLargeFileProducesCorrectResults(): void
    {
        // Generate a file large enough to span multiple chunks (>64KB).
        // Each row has accented characters to verify encoding conversion.
        $utf8Rows = [];
        $utf8Rows[] = 'id,name,description';
        for ($i = 1; $i <= 2000; ++$i) {
            $utf8Rows[] = sprintf(
                '%d,"Ñoño %d","Descripción número %d con carácteres especiales: äöüß"',
                $i,
                $i,
                $i
            );
        }
        $utf8Csv = implode("\n", $utf8Rows) . "\n";

        // Convert to ISO-8859-1
        $isoCsv = mb_convert_encoding($utf8Csv, 'ISO-8859-1', 'UTF-8');
        $filename = $this->tempFile = File::temporaryFileName();
        file_put_contents($filename, $isoCsv);

        // Verify file is large enough to trigger multiple chunks
        self::assertGreaterThan(65536, strlen($isoCsv), 'Test file should be larger than one chunk');

        $reader = $this->newCsv();
        $reader->setInputEncoding('ISO-8859-1');
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();

        // Check first row (header)
        self::assertSame('id', $sheet->getCell('A1')->getValue());
        self::assertSame('name', $sheet->getCell('B1')->getValue());
        self::assertSame('description', $sheet->getCell('C1')->getValue());

        // Check several data rows including ones near chunk boundaries
        self::assertSame('Ñoño 1', $sheet->getCell('B2')->getValue());
        self::assertSame('Descripción número 1 con carácteres especiales: äöüß', $sheet->getCell('C2')->getValue());

        // Check a row in the middle
        self::assertSame('Ñoño 1000', $sheet->getCell('B1001')->getValue());
        self::assertSame('Descripción número 1000 con carácteres especiales: äöüß', $sheet->getCell('C1001')->getValue());

        // Check the last row
        self::assertSame('Ñoño 2000', $sheet->getCell('B2001')->getValue());
        self::assertSame('Descripción número 2000 con carácteres especiales: äöüß', $sheet->getCell('C2001')->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test a large UTF-16LE file to verify multi-byte character boundary
     * handling across chunks.
     */
    public function testLargeUtf16leFile(): void
    {
        $utf8Rows = [];
        $utf8Rows[] = 'id,value';
        for ($i = 1; $i <= 1500; ++$i) {
            // Include characters that use multi-byte UTF-16 sequences
            $utf8Rows[] = sprintf('%d,"Ströëm café résumé #%d"', $i, $i);
        }
        $utf8Csv = implode("\n", $utf8Rows) . "\n";

        // Convert to UTF-16LE (2 bytes per character)
        $utf16leCsv = mb_convert_encoding($utf8Csv, 'UTF-16LE', 'UTF-8');
        $filename = $this->tempFile = File::temporaryFileName() . '.csv';
        file_put_contents($filename, $utf16leCsv);

        self::assertGreaterThan(65536, strlen($utf16leCsv), 'UTF-16LE file should be larger than one chunk');

        $reader = $this->newCsv();
        $reader->setInputEncoding('UTF-16LE');
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();

        self::assertSame('id', $sheet->getCell('A1')->getValue());
        self::assertSame('value', $sheet->getCell('B1')->getValue());
        self::assertSame('Ströëm café résumé #1', $sheet->getCell('B2')->getValue());
        self::assertSame('Ströëm café résumé #750', $sheet->getCell('B751')->getValue());
        self::assertSame('Ströëm café résumé #1500', $sheet->getCell('B1501')->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test a large UTF-32BE file to verify 4-byte character boundary
     * handling across chunks.
     */
    public function testLargeUtf32beFile(): void
    {
        $utf8Rows = [];
        $utf8Rows[] = 'col1,col2';
        for ($i = 1; $i <= 1000; ++$i) {
            $utf8Rows[] = sprintf('%d,"Ünïcödé têst %d"', $i, $i);
        }
        $utf8Csv = implode("\n", $utf8Rows) . "\n";

        $utf32beCsv = mb_convert_encoding($utf8Csv, 'UTF-32BE', 'UTF-8');
        $filename = $this->tempFile = File::temporaryFileName() . '.csv';
        file_put_contents($filename, $utf32beCsv);

        self::assertGreaterThan(65536, strlen($utf32beCsv), 'UTF-32BE file should be larger than one chunk');

        $reader = $this->newCsv();
        $reader->setInputEncoding('UTF-32BE');
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();

        self::assertSame('col1', $sheet->getCell('A1')->getValue());
        self::assertSame('col2', $sheet->getCell('B1')->getValue());
        self::assertSame('Ünïcödé têst 1', $sheet->getCell('B2')->getValue());
        self::assertSame('Ünïcödé têst 1000', $sheet->getCell('B1001')->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test that listWorksheetInfo also works with the streaming approach
     * for non-UTF-8 files.
     */
    public function testListWorksheetInfoWithNonUtf8(): void
    {
        $utf8Csv = "a,b,c\n1,2,3\n4,5,6\n";
        $isoCsv = mb_convert_encoding($utf8Csv, 'ISO-8859-1', 'UTF-8');
        $filename = $this->tempFile = File::temporaryFileName();
        file_put_contents($filename, $isoCsv);

        $reader = $this->newCsv();
        $reader->setInputEncoding('ISO-8859-1');
        $info = $reader->listWorksheetInfo($filename);

        self::assertCount(1, $info);
        self::assertSame(3, $info[0]['totalRows']);
        self::assertSame(3, $info[0]['totalColumns']);
        self::assertSame('C', $info[0]['lastColumnLetter']);
    }

    /**
     * Test that listWorksheetNames works with non-UTF-8 streaming path.
     */
    public function testListWorksheetNamesWithNonUtf8(): void
    {
        $utf8Csv = "a,b,c\n1,2,3\n";
        $isoCsv = mb_convert_encoding($utf8Csv, 'ISO-8859-1', 'UTF-8');
        $filename = $this->tempFile = File::temporaryFileName();
        file_put_contents($filename, $isoCsv);

        $reader = $this->newCsv();
        $reader->setInputEncoding('ISO-8859-1');
        $names = $reader->listWorksheetNames($filename);

        self::assertCount(1, $names);
        self::assertSame('Worksheet', $names[0]);
    }

    /**
     * Test that an empty non-UTF-8 file is handled gracefully.
     */
    public function testEmptyNonUtf8File(): void
    {
        $filename = $this->tempFile = File::temporaryFileName();
        file_put_contents($filename, '');

        $reader = $this->newCsv();
        $reader->setInputEncoding('ISO-8859-1');
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();

        self::assertNull($sheet->getCell('A1')->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test the leftover flush path: a UTF-16LE file whose total byte
     * count is odd triggers the leftover flush at lines 373-377 in Csv.php.
     *
     * The leftover contains an incomplete multi-byte character, so iconv
     * raises a warning (converted to an exception by the test error handler).
     *
     * Note that, if iconv is not available for some reason,
     * we will try mb_convert_encoding, which converts the leftover
     * to a question mark with no message of any kind.
     */
    public function testLeftoverFlushWithUnalignedUtf16le(): void
    {
        $utf8Csv = "a,b\n1,2\n";
        $utf16leCsv = mb_convert_encoding($utf8Csv, 'UTF-16LE', 'UTF-8');
        // Append a single extra byte to make total length odd (not aligned to charWidth=2)
        $utf16leCsv .= "\x00";

        $filename = $this->tempFile = File::temporaryFileName() . '.csv';
        file_put_contents($filename, $utf16leCsv);

        self::assertSame(1, strlen($utf16leCsv) % 2, 'File byte count should be odd to trigger leftover path');

        $this->expectException(Exception::class);
        if ($this->whichCsv === 'CsvIconv2') {
            $this->expectExceptionMessage('invalid multibyte sequence');
        } else {
            $this->expectExceptionMessage('Detected an incomplete multibyte character');
        }

        $reader = $this->newCsv();
        $reader->setInputEncoding('UTF-16LE');
        $reader->load($filename);
    }

    /**
     * Test the leftover flush path with UTF-32BE: file byte count not
     * divisible by 4 triggers leftover handling with incomplete characters.
     *
     * Note that, if iconv is not available for some reason,
     * we will try mb_convert_encoding, which converts the leftover
     * to a question mark with no message of any kind.
     */
    public function testLeftoverFlushWithUnalignedUtf32be(): void
    {
        $utf8Csv = "x,y\n3,4\n";
        $utf32beCsv = mb_convert_encoding($utf8Csv, 'UTF-32BE', 'UTF-8');
        // Append 2 extra bytes so length % 4 != 0
        $utf32beCsv .= "\x00\x00";

        $filename = $this->tempFile = File::temporaryFileName() . '.csv';
        file_put_contents($filename, $utf32beCsv);

        self::assertNotSame(0, strlen($utf32beCsv) % 4, 'File byte count should not be aligned to 4 to trigger leftover path');

        $this->expectException(Exception::class);
        if ($this->whichCsv === 'CsvIconv2') {
            $this->expectExceptionMessage('invalid multibyte sequence');
        } else {
            $this->expectExceptionMessage('Detected an incomplete multibyte character');
        }

        $reader = $this->newCsv();
        $reader->setInputEncoding('UTF-32BE');
        $reader->load($filename);
    }

    /**
     * Test encodingCharWidth returns 2 for UCS-2 variants by loading
     * a file with UCS-2BE input encoding.
     */
    #[DataProvider('providerUcsEncodings')]
    public function testUcsEncodingVariants(string $inputEncoding, string $mbEncoding, int $charWidth): void
    {
        $utf8Csv = "a,b\n1,2\n";
        $encoded = mb_convert_encoding($utf8Csv, $mbEncoding, 'UTF-8');

        $filename = $this->tempFile = File::temporaryFileName() . '.csv';
        file_put_contents($filename, $encoded);

        // Verify encoding alignment: byte count should be divisible by char width
        self::assertSame(0, strlen($encoded) % $charWidth);

        $reader = $this->newCsv();
        $reader->setInputEncoding($inputEncoding);
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();

        self::assertSame('a', $sheet->getCell('A1')->getValue());
        self::assertSame('b', $sheet->getCell('B1')->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUcsEncodings(): array
    {
        return [
            'UCS-2BE' => ['UCS-2BE', 'UCS-2BE', 2],
            'UCS-2LE' => ['UCS-2LE', 'UCS-2LE', 2],
            'UCS-4BE' => ['UCS-4BE', 'UCS-4BE', 4],
            'UCS-4LE' => ['UCS-4LE', 'UCS-4LE', 4],
        ];
    }

    /**
     * Test loadSpreadsheetFromString with non-UTF-8 content pre-converted
     * to UTF-8. This verifies the string loading path works for content
     * that was originally in a different encoding.
     */
    public function testLoadFromStringWithConvertedEncoding(): void
    {
        $utf8Csv = "Name,City\nMüller,Zürich\nCafé,Père\n";

        $reader = $this->newCsv();
        $spreadsheet = $reader->loadSpreadsheetFromString($utf8Csv);
        $sheet = $spreadsheet->getActiveSheet();

        self::assertSame('Müller', $sheet->getCell('A2')->getValue());
        self::assertSame('Zürich', $sheet->getCell('B2')->getValue());
        self::assertSame('Café', $sheet->getCell('A3')->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test guess encoding still works with the streaming path.
     */
    #[DataProvider('providerGuessEncodingStreaming')]
    public function testGuessEncodingWithStreaming(string $filename): void
    {
        $reader = $this->newCsv();
        $reader->setInputEncoding(Csv::GUESS_ENCODING);
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertEquals('première', $sheet->getCell('A1')->getValue());
        self::assertEquals('sixième', $sheet->getCell('C2')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public static function providerGuessEncodingStreaming(): array
    {
        return [
            'UTF-16BE' => ['tests/data/Reader/CSV/premiere.utf16be.csv'],
            'UTF-16LE' => ['tests/data/Reader/CSV/premiere.utf16le.csv'],
            'UTF-32BE' => ['tests/data/Reader/CSV/premiere.utf32be.csv'],
            'UTF-32LE' => ['tests/data/Reader/CSV/premiere.utf32le.csv'],
            'Win-1252' => ['tests/data/Reader/CSV/premiere.win1252.csv'],
        ];
    }
}
