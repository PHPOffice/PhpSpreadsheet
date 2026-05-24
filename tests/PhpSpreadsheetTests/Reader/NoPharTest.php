<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NoPharTest extends TestCase
{
    /**
     * @param class-string<IReader> $reader
     */
    #[DataProvider('providerReaders')]
    public function testNoPhar(string $reader): void
    {
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('Disallowed stream wrapper');
        $reader = new $reader();
        $reader->load('phar://anyoldname');
    }

    /**
     * @param class-string<IReader> $reader
     */
    #[DataProvider('providerReaders')]
    public function testPhar3Slashes(string $reader): void
    {
        $invalidProtocol = [
            '3 slashes' => 'phar:///anyoldname',
            'mixed case' => 'Phar:///anyoldname',
            'embedded space' => 'ph ar://anyoldname',
            'embedded control character' => "ph\x04ar://anyoldname",
            'filter with phar' => 'php://filter/read=convert.base64-encode/resource=phar:///tmp/x.Phar',
        ];
        $reader = new $reader();
        foreach ($invalidProtocol as $key => $value) {
            try {
                $reader->load($value);
                self::fail("Should have thrown exception - $key");
            } catch (SpreadsheetException $e) {
                self::assertStringContainsString('Disallowed stream wrapper', $e->getMessage(), $key);
            }
        }
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
