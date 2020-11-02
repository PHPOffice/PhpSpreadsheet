<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Comment;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    public function testCreateComment()
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

    public function testSetAuthor()
    {
        $comment = new Comment();
        $comment->setAuthor('Mark Baker');
        self::assertEquals('Mark Baker', $comment->getAuthor());
    }

    public function testSetMarginLeft()
    {
        $comment = new Comment();
        $comment->setMarginLeft('20pt');
        self::assertEquals('20pt', $comment->getMarginLeft());
    }

    public function testSetMarginTop()
    {
        $comment = new Comment();
        $comment->setMarginTop('2.5pt');
        self::assertEquals('2.5pt', $comment->getMarginTop());
    }

    public function testSetWidth()
    {
        $comment = new Comment();
        $comment->setWidth('120pt');
        self::assertEquals('120pt', $comment->getWidth());
    }

    public function testSetHeight()
    {
        $comment = new Comment();
        $comment->setHeight('60pt');
        self::assertEquals('60pt', $comment->getHeight());
    }

    public function testSetFillColor()
    {
        $comment = new Comment();
        $comment->setFillColor(new Color('RED'));
        self::assertEquals('RED', $comment->getFillColor()->getARGB());
    }

    public function testSetAlignment()
    {
        $comment = new Comment();
        $comment->setAlignment(Alignment::HORIZONTAL_CENTER);
        self::assertEquals(Alignment::HORIZONTAL_CENTER, $comment->getAlignment());
    }
}
