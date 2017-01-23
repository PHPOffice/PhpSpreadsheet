<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;

class HyperlinkTest extends \PHPUnit_Framework_TestCase
{
    public function testGetUrl()
    {
        $urlValue = 'http://www.phpexcel.net';

        $testInstance = new Hyperlink($urlValue);

        $result = $testInstance->getUrl();
        $this->assertEquals($urlValue, $result);
    }

    public function testSetUrl()
    {
        $initialUrlValue = 'http://www.phpexcel.net';
        $newUrlValue = 'http://github.com/PHPOffice/PhpSpreadsheet';

        $testInstance = new Hyperlink($initialUrlValue);
        $result = $testInstance->setUrl($newUrlValue);
        $this->assertTrue($result instanceof Hyperlink);

        $result = $testInstance->getUrl();
        $this->assertEquals($newUrlValue, $result);
    }

    public function testGetTooltip()
    {
        $tooltipValue = 'PhpSpreadsheet Web Site';

        $testInstance = new Hyperlink(null, $tooltipValue);

        $result = $testInstance->getTooltip();
        $this->assertEquals($tooltipValue, $result);
    }

    public function testSetTooltip()
    {
        $initialTooltipValue = 'PhpSpreadsheet Web Site';
        $newTooltipValue = 'PhpSpreadsheet Repository on Github';

        $testInstance = new Hyperlink(null, $initialTooltipValue);
        $result = $testInstance->setTooltip($newTooltipValue);
        $this->assertTrue($result instanceof Hyperlink);

        $result = $testInstance->getTooltip();
        $this->assertEquals($newTooltipValue, $result);
    }

    public function testIsInternal()
    {
        $initialUrlValue = 'http://www.phpexcel.net';
        $newUrlValue = 'sheet://Worksheet1!A1';

        $testInstance = new Hyperlink($initialUrlValue);
        $result = $testInstance->isInternal();
        $this->assertFalse($result);

        $testInstance->setUrl($newUrlValue);
        $result = $testInstance->isInternal();
        $this->assertTrue($result);
    }

    public function testGetHashCode()
    {
        $urlValue = 'http://www.phpexcel.net';
        $tooltipValue = 'PhpSpreadsheet Web Site';
        $initialExpectedHash = '6f1d4cbf40034b9ddc3fbf6019506e91';

        $testInstance = new Hyperlink($urlValue, $tooltipValue);

        $result = $testInstance->getHashCode();
        $this->assertEquals($initialExpectedHash, $result);
    }
}
