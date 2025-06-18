<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Reader\Html;
use PHPUnit\Framework\TestCase;

class HtmlTest extends TestCase
{
    public function testCsvWithAngleBracket()
    {
        $filename = __DIR__ . '/../../data/Reader/HTML/csv_with_angle_bracket.csv';
        $reader = new Html();
        self::assertFalse($reader->canRead($filename));
    }

    public function providerCanReadVerySmallFile()
    {
        $padding = str_repeat('a', 2048);

        return [
            [true, ' <html> ' . $padding . ' </html> '],
            [true, ' <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"> <html>' . $padding . '</html>'],
            [true, '<html></html>'],
            [false, ''],
        ];
    }

    /**
     * @dataProvider providerCanReadVerySmallFile
     *
     * @param bool $expected
     * @param string $content
     */
    public function testCanReadVerySmallFile($expected, $content)
    {
        $filename = tempnam(sys_get_temp_dir(), 'html');
        file_put_contents($filename, $content);

        $reader = new Html();
        $actual = $reader->canRead($filename);
        unlink($filename);

        self::assertSame($expected, $actual);
    }
}
