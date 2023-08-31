<?php

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Shared\CodePage;
use PHPUnit\Framework\TestCase;

class CodePageTest extends TestCase
{
    /**
     * @dataProvider providerCodePage
     *
     * @param mixed $expectedResult
     * @param mixed $codePageIndex
     */
    public function testCodePageNumberToName($expectedResult, $codePageIndex): void
    {
        if ($expectedResult === 'exception') {
            $this->expectException(Exception::class);
        }
        $result = CodePage::numberToName($codePageIndex);
        if (is_array($expectedResult)) {
            self::assertContains($result, $expectedResult);
        } else {
            self::assertEquals($expectedResult, $result);
        }
    }

    public static function providerCodePage(): array
    {
        return require 'tests/data/Shared/CodePage.php';
    }

    public function testCoverage(): void
    {
        $covered = [];
        $expected = CodePage::getEncodings();
        foreach ($expected as $key => $val) {
            $covered[$key] = 0;
        }
        $tests = $this->providerCodePage();
        foreach ($tests as $test) {
            $covered[$test[1]] = 1;
        }
        foreach ($covered as $key => $val) {
            self::assertEquals(1, $val, "Codepage $key not tested");
        }
    }

    public function testNumberToNameWithInvalidCodePage(): void
    {
        $invalidCodePage = 12345;

        try {
            CodePage::numberToName($invalidCodePage);
        } catch (Exception $e) {
            self::assertEquals($e->getMessage(), 'Unknown codepage: 12345');

            return;
        }
        self::fail('An expected exception has not been raised.');
    }

    public function testNumberToNameWithUnsupportedCodePage(): void
    {
        $unsupportedCodePage = 720;

        try {
            CodePage::numberToName($unsupportedCodePage);
        } catch (Exception $e) {
            self::assertEquals($e->getMessage(), 'Code page 720 not supported.');

            return;
        }
        self::fail('An expected exception has not been raised.');
    }
}
