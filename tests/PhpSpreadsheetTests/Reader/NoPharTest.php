<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PHPUnit\Framework\TestCase;

class NoPharTest extends TestCase
{
    /**
     * @dataProvider providerReaders
     *
     * @param class-string<IReader> $reader
     */
    public function testNoPhar(string $reader): void
    {
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('Stream wrappers are not permitted');
        $reader = new $reader();
        $reader->load('phar://anyoldname');
    }

    /**
     * @return array<array<class-string<IReader>>>
     */
    public static function providerReaders(): array
    {
        $readers = IOFactory::getReaders();
        $array = [];
        foreach ($readers as $key => $reader) {
            $array[$key] = [$reader];
        }

        return $array;
    }
}
