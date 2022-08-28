<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xls;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use PhpOffice\PhpSpreadsheet\Writer\Xls\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    /** @var ?Spreadsheet */
    private $spreadsheet;

    protected function tearDown(): void
    {
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
    }

    public function testNonArray(): void
    {
        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('Unexpected non-array');
        $this->spreadsheet = new Spreadsheet();
        $parser = new Parser($this->spreadsheet);
        $parser->toReversePolish();
    }

    public function testMissingIndex(): void
    {
        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('Unexpected non-array');
        $this->spreadsheet = new Spreadsheet();
        $parser = new Parser($this->spreadsheet);
        $parser->toReversePolish(['left' => 0]);
    }

    public function testParseError(): void
    {
        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('Unknown token +');
        $this->spreadsheet = new Spreadsheet();
        $parser = new Parser($this->spreadsheet);
        $parser->toReversePolish(['left' => 1, 'right' => 2, 'value' => '+']);
    }

    public function testGoodParse(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $parser = new Parser($this->spreadsheet);
        self::assertSame('1e01001e02001e0300', bin2hex($parser->toReversePolish(['left' => 1, 'right' => 2, 'value' => 3])));
    }
}
