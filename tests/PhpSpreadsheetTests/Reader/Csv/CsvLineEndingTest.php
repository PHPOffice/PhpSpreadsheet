<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Csv;

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CsvLineEndingTest extends TestCase
{
    private string $tempFile = '';

    private static bool $alwaysFalse = false;

    protected function tearDown(): void
    {
        if ($this->tempFile !== '') {
            unlink($this->tempFile);
            $this->tempFile = '';
        }
    }

    #[DataProvider('providerEndings')]
    #[DataProvider('providerEndings2')]
    public function testEndings(string $ending, int $version = PHP_VERSION_ID): void
    {
        if ($ending === "\r" && $version >= 90000) {
            self::markTestSkipped('Mac line endings not supported for Php9+');
        }
        $this->tempFile = $filename = File::temporaryFilename();
        $data = ['123', '456', '789'];
        file_put_contents($filename, implode($ending, $data));
        $reader = new Csv();
        if (Csv::DEFAULT_TEST_AUTODETECT === self::$alwaysFalse) {
            $reader->setTestAutoDetect(true);
        }
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertEquals($data[0], $sheet->getCell('A1')->getValue());
        self::assertEquals($data[1], $sheet->getCell('A2')->getValue());
        self::assertEquals($data[2], $sheet->getCell('A3')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    #[DataProvider('providerEndings')]
    public function testEndingsNoDetect(string $ending): void
    {
        $this->tempFile = $filename = File::temporaryFilename();
        $data = ['123', '456', '789'];
        file_put_contents($filename, implode($ending, $data));
        $reader = new Csv();
        self::assertSame(self::$alwaysFalse, Csv::DEFAULT_TEST_AUTODETECT);
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        if ($ending === "\r") {
            // Can't handle Mac line endings without autoDetect
            self::assertEquals(implode("\n", $data), $sheet->getCell('A1')->getValue());
            self::assertNull($sheet->getCell('A2')->getValue());
            self::assertNull($sheet->getCell('A3')->getValue());
        } else {
            self::assertEquals($data[0], $sheet->getCell('A1')->getValue());
            self::assertEquals($data[1], $sheet->getCell('A2')->getValue());
            self::assertEquals($data[2], $sheet->getCell('A3')->getValue());
        }
        $spreadsheet->disconnectWorksheets();
    }

    public static function providerEndings(): array
    {
        return [
            'Unix endings' => ["\n"],
            'Mac endings' => ["\r"],
            'Windows endings' => ["\r\n"],
        ];
    }

    public static function providerEndings2(): array
    {
        return [
            'Mac endings Php9+' => ["\r", 90000],
        ];
    }
}
