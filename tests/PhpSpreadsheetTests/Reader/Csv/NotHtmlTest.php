<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Csv;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv as CsvReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PHPUnit\Framework\TestCase;

class NotHtmlTest extends TestCase
{
    private string $tempFile = '';

    protected function tearDown(): void
    {
        if ($this->tempFile !== '') {
            unlink($this->tempFile);
            $this->tempFile = '';
        }
    }

    public function testHtmlCantRead(): void
    {
        // This test has a file which IOFactory will identify as Csv.
        // So file can be read using either Csv Reader or IOFactory.
        $this->tempFile = $filename = File::temporaryFilename();
        $cells = [
            ['1', '<a href="http://example.com">example</a>', '3'],
            ['4', '5', '6'],
        ];
        $handle = fopen($filename, 'wb');
        self::assertNotFalse($handle);
        foreach ($cells as $row) {
            fwrite($handle, "{$row[0]},{$row[1]},{$row[2]}\n");
        }
        fclose($handle);
        // Php8.3- identify file as text/html.
        // Php8.4+ identify file as text/csv, and this type of change
        //    has been known to be retrofitted to prior versions.
        $mime = mime_content_type($filename);
        if ($mime !== 'text/csv') {
            self::assertSame('text/html', $mime);
        }
        self::assertSame('Csv', IOFactory::identify($filename));
        $reader = new CsvReader();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame($cells, $sheet->toArray());
        $spreadsheet->disconnectWorksheets();
    }

    public function testHtmlCanRead(): void
    {
        // This test has a file which IOFactory will identify as Html.
        // So file has to be read using Csv Reader, not IOFactory.
        $this->tempFile = $filename = File::temporaryFilename();
        $cells = [
            ['<a href="http://example.com">example</a>', '<div>hello', '3'],
            ['4', '5', '</div>'],
        ];
        $handle = fopen($filename, 'wb');
        self::assertNotFalse($handle);
        foreach ($cells as $row) {
            fwrite($handle, "{$row[0]},{$row[1]},{$row[2]}\n");
        }
        fclose($handle);
        // Php8.3- identify file as text/html.
        // Php8.4+ identify file as text/csv, and this type of change
        //    has been known to be retrofitted to prior versions.
        $mime = mime_content_type($filename);
        if ($mime !== 'text/csv') {
            self::assertSame('text/html', $mime);
        }
        self::assertSame('Html', IOFactory::identify($filename));
        $reader = new CsvReader();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame($cells, $sheet->toArray());
        $spreadsheet->disconnectWorksheets();
    }
}
