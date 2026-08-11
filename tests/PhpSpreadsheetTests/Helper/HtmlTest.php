<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Helper;

use DOMElement;
use PhpOffice\PhpSpreadsheet\Helper\Html;
use PHPUnit\Framework\TestCase;

class HtmlTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerUtf8EncodingSupport')]
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
        $htmlBreakTag = $html->breakTag(...);
        $html->addStartTagCallback('li', function (DOMElement $tag, Html $object): void {
            $object->stringData .= "\u{00A0}\u{2022} \u{00A0}";
        });
        $html->addEndTagCallback('li', $htmlBreakTag);
        $input = 'Hello<ul><li>Item 1</li><li>Item 2</li></ul>Goodbye';
        $expected = "Hello\n\u{00A0}\u{2022} \u{00A0}Item 1\n\u{00A0}\u{2022} \u{00A0}Item 2\nGoodbye";
        $actual = $html->toRichTextObject($input);

        self::assertSame($expected, $actual->getPlainText());
    }

    public function testSTag(): void
    {
        $html = new Html();
        $input = 'Hello <s>test</s>world';
        $richText = $html->toRichTextObject($input);
        $elements = $richText->getRichTextElements();

        self::assertSame(count($elements), 3);

        self::assertSame($elements[0]->getText(), 'Hello ');
        self::assertNotNull($elements[0]->getFont());
        self::assertFalse($elements[0]->getFont()->getStrikethrough());

        self::assertSame($elements[1]->getText(), 'test');
        self::assertNotNull($elements[1]->getFont());
        self::assertTrue($elements[1]->getFont()->getStrikethrough());

        self::assertSame($elements[2]->getText(), 'world');
        self::assertNotNull($elements[2]->getFont());
        self::assertFalse($elements[2]->getFont()->getStrikethrough());
    }
}
