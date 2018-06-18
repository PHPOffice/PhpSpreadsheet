<?php

namespace PhpOffice\PhpSpreadsheetTests\Helper;

use PhpOffice\PhpSpreadsheet\Helper\Html;
use PHPUnit\Framework\TestCase;

class HtmlTest extends TestCase
{
    /**
     * @dataProvider providerUtf8EncodingSupport
     *
     * @param mixed $expected
     * @param mixed $input
     */
    public function testUtf8EncodingSupport($expected, $input)
    {
        $html = new Html();
        $actual = $html->toRichTextObject($input);

        self::assertSame($expected, $actual->getPlainText());
    }

    public function providerUtf8EncodingSupport()
    {
        return [
            ['foo', 'foo'],
            ['können', 'können'],
            ['русский', 'русский'],
            ["foo\nbar", '<p>foo</p><p>bar</p>'],
        ];
    }
}
