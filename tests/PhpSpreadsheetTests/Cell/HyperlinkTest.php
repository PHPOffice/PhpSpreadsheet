<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use PHPUnit\Framework\TestCase;

class HyperlinkTest extends TestCase
{
    public function testGetUrl()
    {
        $urlValue = 'https://www.example.com';

        $testInstance = new Hyperlink($urlValue);

        $result = $testInstance->getUrl();
        self::assertEquals($urlValue, $result);
    }

    public function testSetUrl()
    {
        $initialUrlValue = 'https://www.example.com';
        $newUrlValue = 'http://github.com/PHPOffice/PhpSpreadsheet';

        $testInstance = new Hyperlink($initialUrlValue);
        $result = $testInstance->setUrl($newUrlValue);
        self::assertInstanceOf(Hyperlink::class, $result);

        $result = $testInstance->getUrl();
        self::assertEquals($newUrlValue, $result);
    }

    public function testGetTooltip()
    {
        $tooltipValue = 'PhpSpreadsheet Web Site';

        $testInstance = new Hyperlink(null, $tooltipValue);

        $result = $testInstance->getTooltip();
        self::assertEquals($tooltipValue, $result);
    }

    public function testSetTooltip()
    {
        $initialTooltipValue = 'PhpSpreadsheet Web Site';
        $newTooltipValue = 'PhpSpreadsheet Repository on Github';

        $testInstance = new Hyperlink(null, $initialTooltipValue);
        $result = $testInstance->setTooltip($newTooltipValue);
        self::assertInstanceOf(Hyperlink::class, $result);

        $result = $testInstance->getTooltip();
        self::assertEquals($newTooltipValue, $result);
    }

    public function testIsInternal()
    {
        $initialUrlValue = 'https://www.example.com';
        $newUrlValue = 'sheet://Worksheet1!A1';

        $testInstance = new Hyperlink($initialUrlValue);
        $result = $testInstance->isInternal();
        self::assertFalse($result);

        $testInstance->setUrl($newUrlValue);
        $result = $testInstance->isInternal();
        self::assertTrue($result);
    }

    public function testGetHashCode()
    {
        $urlValue = 'https://www.example.com';
        $tooltipValue = 'PhpSpreadsheet Web Site';
        $initialExpectedHash = '3a8d5a682dba27276dce538c39402437';

        $testInstance = new Hyperlink($urlValue, $tooltipValue);

        $result = $testInstance->getHashCode();
        self::assertEquals($initialExpectedHash, $result);
    }
}
