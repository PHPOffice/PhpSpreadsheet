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
     */
    public function testCodePageNumberToName($expectedResult, ...$args)
    {
        $result = CodePage::numberToName(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerCodePage()
    {
        return require 'data/Shared/CodePage.php';
    }

    public function testNumberToNameWithInvalidCodePage()
    {
        $invalidCodePage = 12345;

        try {
            CodePage::numberToName($invalidCodePage);
        } catch (Exception $e) {
            self::assertEquals($e->getMessage(), 'Unknown codepage: 12345');

            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function testNumberToNameWithUnsupportedCodePage()
    {
        $unsupportedCodePage = 720;

        try {
            CodePage::numberToName($unsupportedCodePage);
        } catch (Exception $e) {
            self::assertEquals($e->getMessage(), 'Code page 720 not supported.');

            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
}
