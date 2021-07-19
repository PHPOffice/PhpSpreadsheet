<?php

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PHPUnit\Framework\TestCase;

class TitleTest extends TestCase
{
    public function testString(): void
    {
        $title = new Title('hello');
        self::assertSame('hello', $title->getCaption());
        self::assertSame('hello', $title->getCaptionText());
    }

    public function testStringArray(): void
    {
        $title = new Title();
        $title->setCaption(['Hello', ', ', 'world.']);
        self::assertSame('Hello, world.', $title->getCaptionText());
    }

    public function testRichText(): void
    {
        $title = new Title();
        $richText = new RichText();
        $part = $richText->createTextRun('Hello');
        $font = $part->getFont();
        if ($font === null) {
            self::fail('Unable to retrieve font');
        } else {
            $font->setBold(true);
            $title->setCaption($richText);
            self::assertSame('Hello', $title->getCaptionText());
        }
    }

    public function testMixedArray(): void
    {
        $title = new Title();
        $richText1 = new RichText();
        $part1 = $richText1->createTextRun('Hello');
        $font1 = $part1->getFont();
        $richText2 = new RichText();
        $part2 = $richText2->createTextRun('world');
        $font2 = $part2->getFont();
        if ($font1 === null || $font2 === null) {
            self::fail('Unable to retrieve font');
        } else {
            $font1->setBold(true);
            $font2->setItalic(true);
            $title->setCaption([$richText1, ', ', $richText2, '.']);
            self::assertSame('Hello, world.', $title->getCaptionText());
        }
    }
}
