<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx\Streaming;

use DOMDocument;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Streaming\StreamingWriter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ZipArchive;

class StreamingFormulaTest extends TestCase
{
    private string $file = '';

    protected function setUp(): void
    {
        $this->file = File::temporaryFilename();
    }

    protected function tearDown(): void
    {
        if (file_exists($this->file)) {
            unlink($this->file);
        }
    }

    #[DataProvider('invalidCharacterProvider')]
    public function testXmlForbiddenCharacterThrows(int $character): void
    {
        $writer = new StreamingWriter($this->file);
        $sheet = $writer->startSheet('Formulas');
        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('control character');
        $sheet->appendRow(['="a' . chr($character) . 'b"']);
    }

    public static function invalidCharacterProvider(): array
    {
        $cases = [];
        foreach (array_merge(range(0, 8), [11, 12], range(14, 31)) as $character) {
            $cases['character ' . $character] = [$character];
        }

        return $cases;
    }

    public function testXmlWhitespaceAndUnicodeRemainValid(): void
    {
        $writer = new StreamingWriter($this->file);
        $formula = "=\"é<&\t\n\r😀\"";
        $writer->startSheet('Formulas')->appendRow([$formula]);
        $writer->close();
        $zip = new ZipArchive();
        self::assertTrue($zip->open($this->file));
        $data = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        self::assertIsString($data);
        $xml = new DOMDocument();
        self::assertTrue($xml->loadXML($data));
        self::assertSame(substr($formula, 1), $xml->getElementsByTagName('f')->item(0)?->textContent);
    }
}
