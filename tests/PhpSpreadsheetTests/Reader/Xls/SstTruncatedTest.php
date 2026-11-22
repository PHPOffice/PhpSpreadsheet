<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Xls as XlsReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls as XlsWriter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SstTruncatedTest extends TestCase
{
    public function testSplitSstFixedHeaderIsRejected(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('SST fixed header is split across records.');
        (new XlsSstProbe())->readSstPayload(str_repeat("\0", 4));
    }

    public function testSstExtRstExtentIsValidated(): void
    {
        $payload = pack('VV', 1, 1)
            . pack('vC', 0, 0x04)
            . pack('V', 1000);

        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Invalid SST string metadata length.');
        (new XlsSstProbe())->readSstPayload($payload);
    }

    public function testSplitSstStringHeaderIsRejected(): void
    {
        // The fixed SST header is complete, but the string's two-byte cch
        // crosses into the CONTINUE record.
        $first = pack('VV', 1, 1) . "\x00";

        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('SST string header is split across records.');
        (new XlsSstProbe())->readSstRecords($first, '');
    }

    public function testSplitSstOptionalStringHeaderIsRejected(): void
    {
        // cch and flags fit, but the optional cRun field crosses the splice.
        $first = pack('VV', 1, 1) . pack('vC', 1, 0x08);

        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('SST string header is split across records.');
        (new XlsSstProbe())->readSstRecords($first, '');
    }

    public function testSplitUncompressedCharacterIsRejected(): void
    {
        // First record ends after the first byte of a UTF-16 character.
        $first = pack('VV', 1, 1)
            . pack('v', 4)
            . "\x01"
            . "\x41";

        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('SST string is split in the middle of a character.');
        (new XlsSstProbe())->readSstRecords($first, '');
    }

    /** @return array<string, array{int, int}> */
    public static function continuationEncodings(): array
    {
        return [
            'compressed to compressed' => [0, 0],
            'compressed to UTF-16' => [0, 1],
            'UTF-16 to compressed' => [1, 0],
            'UTF-16 to UTF-16' => [1, 1],
        ];
    }

    #[DataProvider('continuationEncodings')]
    public function testCharactersStartingInContinuation(int $initialEncoding, int $continuedEncoding): void
    {
        $first = pack('VV', 1, 1) . pack('vC', 1, $initialEncoding);
        $second = $continuedEncoding === 1 ? "\x01A\0" : "\0A";
        $probe = new XlsSstProbe();

        self::assertSame(1, $probe->readSstRecords($first, $second));
        self::assertSame('A', $probe->getSst()[0]['value']);
    }

    public function testManyShortContinuationRecordsAreParsed(): void
    {
        $strings = [];
        for ($i = 0; $i < 2000; ++$i) {
            $strings[] = sprintf('%06d', $i);
        }
        $records = [pack('VV', count($strings), count($strings)) . self::sstString($strings[0])];
        for ($i = 1; $i < count($strings); ++$i) {
            $records[] = self::sstString($strings[$i]);
        }

        $probe = new XlsSstProbe();
        self::assertSame(count($strings), $probe->readSstRecordList($records));
        $sst = $probe->getSst();
        self::assertSame($strings[0], $sst[0]['value']);
        self::assertSame($strings[1999], $sst[1999]['value']);
    }

    private static function sstString(string $value): string
    {
        return pack('vC', strlen($value), 0x00) . $value;
    }

    public function testNegativeSstMetadataLengthIsRejected(): void
    {
        $payload = pack('VV', 0, 5)
            . pack('vC', 0, 0x04)
            . pack('V', -7 & 0xFFFFFFFF);

        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Invalid SST string metadata length.');
        (new XlsSstProbe())->readSstPayload($payload);
    }

    public function testTruncatedSstStringIsRejected(): void
    {
        $marker = 'SST-BOUNDS-MARKER';
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->setCellValue('A1', $marker);
        $filename = File::temporaryFilename();

        try {
            (new XlsWriter($spreadsheet))->save($filename);
        } finally {
            $spreadsheet->disconnectWorksheets();
        }

        $data = (string) file_get_contents($filename);
        $utf16Marker = '';
        foreach (str_split($marker) as $character) {
            $utf16Marker .= $character . "\x00";
        }
        $offset = strpos($data, $utf16Marker);
        if ($offset === false) {
            $offset = strpos($data, $marker);
        }
        self::assertNotFalse($offset);

        // The character count is stored two bytes before the option-flags byte.
        $declaredCharacters = strlen($marker) + 10;
        $data[$offset - 3] = chr($declaredCharacters & 0xFF);
        $data[$offset - 2] = chr(($declaredCharacters >> 8) & 0xFF);
        file_put_contents($filename, $data);

        try {
            $this->expectException(ReaderException::class);
            $this->expectExceptionMessage('SST string data is truncated.');
            (new XlsReader())->load($filename);
        } finally {
            unlink($filename);
        }
    }
}
