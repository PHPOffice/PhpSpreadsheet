<?php

namespace PhpOffice\PhpSpreadsheetTests\Helper;

use PhpOffice\PhpSpreadsheet\Helper\Html;
use PHPUnit\Framework\TestCase;

class HtmlTest extends TestCase
{
    /**
     * @dataProvider providerUtf8EncodingSupport
     */
    public function testUtf8EncodingSupport(mixed $expected, mixed $input): void
    {
        $html = new Html();
        $actual = $html->toRichTextObject($input);

        self::assertSame($expected, $actual->getPlainText());
    }

    public static function providerUtf8EncodingSupport(): array
    {
        return [
            ['foo', 'foo'],
            ['können', 'können'],
            ['русский', 'русский'],
            ["foo\nbar", '<p>foo</p><p>bar</p>'],
            'issue2810' => ['0', '0'],
        ];
    }
}
