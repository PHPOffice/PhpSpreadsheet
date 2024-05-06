<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Html;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PHPUnit\Framework\TestCase;

class HtmlCharsetTest extends TestCase
{
    /**
     * @dataProvider providerCharset
     */
    public function testCharset(string $filename, string $expectedResult): void
    {
        if ($expectedResult === 'exception') {
            $this->expectException(ReaderException::class);
            $this->expectExceptionMessage('Failed to load');
        }
        $directory = 'tests/data/Reader/HTML';
        $reader = new Html();
        $spreadsheet = $reader->load("$directory/$filename");
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame($expectedResult, $sheet->getCell('A1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public static function providerCharset(): array
    {
        return [
            ['charset.ISO-8859-1.html', 'À1'],
            ['charset.ISO-8859-1.html4.html', 'À1'],
            ['charset.ISO-8859-2.html', 'Ŕ1'],
            ['charset.nocharset.html', 'À1'],
            ['charset.UTF-8.html', 'À1'],
            ['charset.UTF-8.bom.html', 'À1'],
            ['charset.UTF-16.bebom.html', 'À1'],
            ['charset.UTF-16.lebom.html', 'À1'],
            ['charset.gb18030.html', '电视机'],
            ['charset.unknown.html', 'exception'],
        ];
    }
}
