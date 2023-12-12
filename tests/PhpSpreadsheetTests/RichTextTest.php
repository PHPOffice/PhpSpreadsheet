<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\RichText\TextElement;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class RichTextTest extends TestCase
{
    public function testConstructorSpecifyingCell(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell = $sheet->getCell('A1');
        $cell->setValue(2);
        self::assertSame(2, $cell->getCalculatedValue());
        $cell->getStyle()->getFont()->setName('whatever');
        $richText = new RichText($cell);
        self::assertSame('whatever', $sheet->getCell('A1')->getStyle()->getFont()->getName());
        self::assertEquals($richText, $cell->getValue());
        self::assertSame('2', $cell->getCalculatedValue());

        $spreadsheet->disconnectWorksheets();
    }

    public function testTextElements(): void
    {
        $element1 = new TextElement('A');
        $element2 = new TextElement('B');
        $element3 = new TextElement('C');
        $richText = new RichText();
        $richText->setRichTextElements([$element1, $element2, $element3]);
        self::assertSame('ABC', $richText->getPlainText());
        $cloneText = clone $richText;
        self::assertEquals($richText, $cloneText);
        self::assertNotSame($richText, $cloneText);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue($richText);
        self::assertInstanceOf(RichText::class, $sheet->getCell('A1')->getValue());
        self::assertSame('ABC', $sheet->getCell('A1')->getFormattedValue());
        $sheet->getCell('B1')->setValue(-3.5);
        self::assertSame([['ABC', '-3.5']], $sheet->toArray());
        $spreadsheet->disconnectWorksheets();
    }

    public function testNullFont(): void
    {
        $richText = new RichText();
        $textRun = $richText->createTextRun('hello');
        self::assertNotNull($textRun->getFontOrThrow());
        $textRun->setFont(null);

        try {
            $textRun->getFontOrThrow();
            $foundFont = true;
        } catch (SpreadsheetException $e) {
            $foundFont = false;
        }
        self::assertFalse($foundFont, 'expected exception not received');
    }
}
