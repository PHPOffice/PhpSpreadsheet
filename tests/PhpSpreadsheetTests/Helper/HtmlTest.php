<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Helper;

use DOMElement;
use PhpOffice\PhpSpreadsheet\Helper\Html;
use PHPUnit\Framework\TestCase;

class HtmlTest extends TestCase
{
    /**
     * @dataProvider providerUtf8EncodingSupport
     */
    public function testUtf8EncodingSupport(string $expected, string $input): void
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
            ["Hello\nItem 1Item 2Goodbye", 'Hello<ul><li>Item 1</li><li>Item 2</li></ul>Goodbye'],
        ];
    }

    public function testLiTag(): void
    {
        $html = new Html();
        /** @var callable */
        $htmlBreakTag = [Html::class, 'breakTag'];
        $html->addStartTagCallback('li', function (DOMElement $tag, Html $object): void {
            $object->stringData .= "\u{00A0}\u{2022} \u{00A0}";
        });
        $html->addEndTagCallback('li', $htmlBreakTag);
        $input = 'Hello<ul><li>Item 1</li><li>Item 2</li></ul>Goodbye';
        $expected = "Hello\n\u{00A0}\u{2022} \u{00A0}Item 1\n\u{00A0}\u{2022} \u{00A0}Item 2\nGoodbye";
        $actual = $html->toRichTextObject($input);

        self::assertSame($expected, $actual->getPlainText());
    }
}
