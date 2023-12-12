<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Html;

use PhpOffice\PhpSpreadsheet\Reader\Html;
use PHPUnit\Framework\TestCase;

/**
 * There were problems running this test in HtmlTest with PhpUnit 10.
 * This replacement seem to work. I want it run in a separate
 * process because I am nervous about set_error_handler.
 *
 * @runTestsInSeparateProcesses
 */
class HtmlPhpunit10Test extends TestCase
{
    private static string $errorString;

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
        if ($errno === E_WARNING) {
            self::$errorString = $errstr;

            return true; // stop error handling
        }

        return false; // continue error handling
    }

    public function testBadHtml(): void
    {
        $filename = 'tests/data/Reader/HTML/badhtml.html';
        $reader = new Html();
        self::assertTrue($reader->canRead($filename));
        $reader->load($filename);
        self::assertStringContainsString('DOMDocument::loadHTML', self::$errorString);
    }

    public function testLoadInvalidString(): void
    {
        $html = '<table<>';
        (new Html())->loadFromString($html);
        self::assertStringContainsString('DOMDocument::loadHTML', self::$errorString);
    }
}
