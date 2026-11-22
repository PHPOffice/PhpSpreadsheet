<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NoPharTest extends TestCase
{
    /**
     * @param class-string<IReader> $readerx
     */
    #[DataProvider('providerReaders')]
    public function testPhar3Slashes(string $readerx): void
    {
        $invalidProtocol = [
            'normal phar' => 'phar://anyoldname',
            '3 slashes' => 'phar:///anyoldname',
            'mixed case' => 'Phar:///anyoldname',
            'embedded space' => 'ph ar://anyoldname',
            'leading space' => ' phar://anyoldname',
            'embedded control character' => "ph\x04ar://anyoldname",
            'filter with phar' => 'php://filter/read=convert.base64-encode/resource=phar:///tmp/x.Phar',
            'filter with phar and newline' => "php://filter/read=convert.base64-encode/\nresource=phar:///tmp/x.Phar",
            'protocol with period followed by phar' => 'compress.bzip2://phar:///x.phar',
            'protocol with period and embedded space' => 'comp ress.zlib://anything',
        ];
        $reader = new $readerx();
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
     * @param class-string<IReader> $readerx
     */
    #[DataProvider('providerReaders')]
    public function testFtp(string $readerx): void
    {
        $invalidProtocol = [
            'ftp' => 'ftp://whatever',
            'ftps' => 'ftps://whatever',
            'ftp with space in filename' => 'ftp://127.0.0.1:2121/a b.png',
            'http' => 'http://whatever.com',
            'https with spece in filename' => 'http://whatever.com/a b.html',
            'rar' => 'rar://whatever',
            'ssh2.sftp' => 'ssh2.sftp://whatever',
        ];
        // Most readers just go with 'is not readable',
        // but Csv/Html/Slk catch that and issue their own exception.
        $expectedMessage = '/is not readable|is an Invalid/';
        $reader = new $readerx();
        foreach ($invalidProtocol as $key => $value) {
            try {
                $reader->load($value);
                self::fail("Should have thrown exception - $key");
            } catch (SpreadsheetException $e) {
                self::assertMatchesRegularExpression(
                    $expectedMessage,
                    $e->getMessage()
                );
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

    // Show that Windows drive letters are accepted when test runs on Windows
    public static function testLocalFiles(): void
    {
        $file = str_replace('\\', '/', __FILE__);
        File::assertFile($file);
        self::assertTrue(File::testFileNoThrow($file));
    }
}
