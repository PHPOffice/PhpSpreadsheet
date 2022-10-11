<?php

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Exception as SSException;
use PhpOffice\PhpSpreadsheet\Shared\Font;
use PhpOffice\PhpSpreadsheet\Style\Font as StyleFont;
use PHPUnit\Framework\TestCase;

class Font3Test extends TestCase
{
    /** @var string */
    private $holdDirectory;

    protected function setUp(): void
    {
        $this->holdDirectory = Font::getTrueTypeFontPath();
    }

    protected function tearDown(): void
    {
        Font::setTrueTypeFontPath($this->holdDirectory);
    }

    public function testGetTrueTypeException1(): void
    {
        $this->expectException(SSException::class);
        $this->expectExceptionMessage('Valid directory to TrueType Font files not specified');
        $font = new StyleFont();
        $font->setName('unknown');
        Font::getTrueTypeFontFileFromFont($font);
    }

    public function testGetTrueTypeException2(): void
    {
        Font::setTrueTypeFontPath(__DIR__);
        $this->expectException(SSException::class);
        $this->expectExceptionMessage('Unknown font name');
        $font = new StyleFont();
        $font->setName('unknown');
        Font::getTrueTypeFontFileFromFont($font);
    }

    public function testGetTrueTypeException3(): void
    {
        Font::setTrueTypeFontPath(__DIR__);
        $this->expectException(SSException::class);
        $this->expectExceptionMessage('TrueType Font file not found');
        $font = new StyleFont();
        $font->setName('Calibri');
        Font::getTrueTypeFontFileFromFont($font);
    }
}
