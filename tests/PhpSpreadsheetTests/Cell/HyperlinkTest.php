<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use PHPUnit\Framework\TestCase;

class HyperlinkTest extends TestCase
{
    public function testGetUrl(): void
    {
        $urlValue = 'https://www.example.com';

        $testInstance = new Hyperlink($urlValue);

        $result = $testInstance->getUrl();
        self::assertEquals($urlValue, $result);
    }

    public function testSetUrl(): void
    {
        $initialUrlValue = 'https://www.example.com';
        $newUrlValue = 'http://github.com/PHPOffice/PhpSpreadsheet';

        $testInstance = new Hyperlink($initialUrlValue);
        $testInstance->setUrl($newUrlValue);

        $result = $testInstance->getUrl();
        self::assertEquals($newUrlValue, $result);
    }

    public function testGetTooltip(): void
    {
        $tooltipValue = 'PhpSpreadsheet Web Site';

        $testInstance = new Hyperlink('', $tooltipValue);

        $result = $testInstance->getTooltip();
        self::assertEquals($tooltipValue, $result);
    }

    public function testSetTooltip(): void
    {
        $initialTooltipValue = 'PhpSpreadsheet Web Site';
        $newTooltipValue = 'PhpSpreadsheet Repository on Github';

        $testInstance = new Hyperlink('', $initialTooltipValue);
        $testInstance->setTooltip($newTooltipValue);

        $result = $testInstance->getTooltip();
        self::assertEquals($newTooltipValue, $result);
    }

    public function testIsInternal(): void
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

    public function testGetHashCode(): void
    {
        $url1 = 'https://www.example.com';
        $tooltip1 = 'PhpSpreadsheet Web Site';
        $url2 = 'https://www.example.com';
        $tooltip2 = 'PhpSpreadsheet Web Site';
        $url3 = 'https://www.example.com';
        $tooltip3 = 'PhpSpreadsheet Web Site '; // note extra space

        $hy1 = new Hyperlink($url1, $tooltip1);
        $hy2 = new Hyperlink($url2, $tooltip2);
        $hy3 = new Hyperlink($url3, $tooltip3);
        self::assertNotSame($hy1, $hy2);
        self::assertSame($hy1->getHashCode(), $hy2->getHashCode());
        self::assertNotEquals($hy1->getHashCode(), $hy3->getHashCode());
    }
}
