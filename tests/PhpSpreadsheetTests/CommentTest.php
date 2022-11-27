<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Comment;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\RichText\TextElement;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    public function testCreateComment(): void
    {
        $comment = new Comment();
        self::assertEquals('Author', $comment->getAuthor());
        self::assertEquals('96pt', $comment->getWidth());
        self::assertEquals('59.25pt', $comment->getMarginLeft());
        self::assertEquals('1.5pt', $comment->getMarginTop());
        self::assertEquals('55.5pt', $comment->getHeight());
        self::assertInstanceOf(Color::class, $comment->getFillColor());
        self::assertEquals('FFFFFFE1', $comment->getFillColor()->getARGB());
        self::assertInstanceOf(RichText::class, $comment->getText());
        self::assertEquals(Alignment::HORIZONTAL_GENERAL, $comment->getAlignment());
        self::assertFalse($comment->getVisible());
    }

    public function testSetAuthor(): void
    {
        $comment = new Comment();
        $comment->setAuthor('Mark Baker');
        self::assertEquals('Mark Baker', $comment->getAuthor());
    }

    public function testSetMarginLeft(): void
    {
        $comment = new Comment();
        $comment->setMarginLeft('20pt');
        self::assertEquals('20pt', $comment->getMarginLeft());
    }

    public function testSetMarginTop(): void
    {
        $comment = new Comment();
        $comment->setMarginTop('2.5pt');
        self::assertEquals('2.5pt', $comment->getMarginTop());
    }

    public function testSetWidth(): void
    {
        $comment = new Comment();
        $comment->setWidth('120pt');
        self::assertEquals('120pt', $comment->getWidth());
    }

    public function testSetHeight(): void
    {
        $comment = new Comment();
        $comment->setHeight('60px');
        self::assertEquals('60px', $comment->getHeight());
    }

    public function testSetFillColor(): void
    {
        $comment = new Comment();
        $comment->setFillColor(new Color('RED'));
        self::assertEquals(Color::COLOR_RED, $comment->getFillColor()->getARGB());
    }

    public function testSetAlignment(): void
    {
        $comment = new Comment();
        $comment->setAlignment(Alignment::HORIZONTAL_CENTER);
        self::assertEquals(Alignment::HORIZONTAL_CENTER, $comment->getAlignment());
    }

    public function testSetText(): void
    {
        $comment = new Comment();
        $test = new RichText();
        $test->addText(new TextElement('This is a test comment'));
        $comment->setText($test);
        self::assertEquals('This is a test comment', (string) $comment);
    }

    public function testRemoveComment(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getComment('A2')->getText()->createText('Comment to delete');
        self::assertArrayHasKey('A2', $sheet->getComments());
        $sheet->removeComment('A2');
        self::assertEmpty($sheet->getComments()); // @phpstan-ignore-line
    }
}
