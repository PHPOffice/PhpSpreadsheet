<?php

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PHPUnit\Framework\TestCase;

class XmlWriterTest extends TestCase
{
    public function testUnserialize(): void
    {
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('Unserialize not permitted');
        $className = XMLWriter::class;
        $classLen = strlen($className);
        $text = "O:$classLen:\"$className\":1:{";
        $text2 = "\x00$className\x00tempFileName";
        $text2Len = strlen($text2);
        $text .= "s:$text2Len:\"$text2\"";
        $text .= ';s:0:"";}';
        unserialize($text);
    }
}
