<?php

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Shared\OLE;
use PHPUnit\Framework\TestCase;

/**
 * There were problems running these tests in OLETest with PhpUnit 10.
 * These replacements seem to work. I want them run in separate
 * processes because I am nervous about set_error_handler.
 *
 * @runTestsInSeparateProcesses
 */
class OLEPhpunit10Test extends TestCase
{
    /** @var string */
    private static $errorString;

    protected function setUp(): void
    {
        self::$errorString = '';
        set_error_handler([self::class, 'errorHandler']);
    }

    protected function tearDown(): void
    {
        restore_error_handler();
    }

    public static function errorHandler(int $errno, string $errstr): bool
    {
        if ($errno === E_USER_WARNING) {
            self::$errorString = $errstr;

            return true; // stop error handling
        }

        return false; // continue error handling
    }

    public function testChainedWriteMode(): void
    {
        self::assertSame('', self::$errorString);
        $ole = new OLE\ChainedBlockStream();
        $openedPath = '';
        self::assertFalse($ole->stream_open('whatever', 'w', 0, $openedPath));

        $ole->stream_open('whatever', 'w', STREAM_REPORT_ERRORS, $openedPath);
        self::assertSame('Only reading is supported', self::$errorString);
    }

    public function testChainedBadPath(): void
    {
        self::assertSame('', self::$errorString);
        $ole = new OLE\ChainedBlockStream();
        $openedPath = '';
        self::assertFalse($ole->stream_open('whatever', 'r', 0, $openedPath));

        $ole->stream_open('whatever', 'r', STREAM_REPORT_ERRORS, $openedPath);
        self::assertSame('OLE stream not found', self::$errorString);
    }
}
