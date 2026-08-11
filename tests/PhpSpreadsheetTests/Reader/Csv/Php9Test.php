<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Csv;

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class Php9Test extends TestCase
{
    #[DataProvider('providerVersion')]
    public function testAffectedByPhp9(int $version): void
    {
        if ($version >= 90000) {
            $this->expectException(ReaderException::class);
            $this->expectExceptionMessage('Php7.4 or Php8');
        }
        $dir = 'tests/data/Reader/CSV';
        $files = glob("$dir/*");
        self::assertNotFalse($files);
        $affected = [];
        foreach ($files as $file) {
            $base = basename($file);
            $encoding = 'UTF-8';
            if (str_contains($base, 'utf') && !str_contains($base, 'bom')) {
                $encoding = 'guess';
            }
            $result = Csv::affectedByPhp9($file, $encoding, version: $version);
            if ($result) {
                $affected[] = $base;
            }
        }
        $expected = ['backslash.csv', 'escape.csv', 'linend.mac.csv'];
        self::assertSame($expected, $affected);
    }

    public static function providerVersion(): array
    {
        return [
            [PHP_VERSION_ID],
            [90000],
        ];
    }
}
