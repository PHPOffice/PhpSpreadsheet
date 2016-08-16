<?php

namespace PhpSpreadsheet\Tests\Shared;

use PhpSpreadsheet\Exception;
use PhpSpreadsheet\Shared\CodePage;

class CodePageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerCodePage
     */
    public function testCodePageNumberToName()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([CodePage::class, 'numberToName'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerCodePage()
    {
        return require 'data/Shared/CodePage.php';
    }

    public function testNumberToNameWithInvalidCodePage()
    {
        $invalidCodePage = 12345;
        try {
            $result = call_user_func([CodePage::class, 'numberToName'], $invalidCodePage);
        } catch (Exception $e) {
            $this->assertEquals($e->getMessage(), 'Unknown codepage: 12345');

            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function testNumberToNameWithUnsupportedCodePage()
    {
        $unsupportedCodePage = 720;
        try {
            $result = call_user_func([CodePage::class, 'numberToName'], $unsupportedCodePage);
        } catch (Exception $e) {
            $this->assertEquals($e->getMessage(), 'Code page 720 not supported.');

            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
}
