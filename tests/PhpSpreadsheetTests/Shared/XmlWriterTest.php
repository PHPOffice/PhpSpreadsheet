<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PHPUnit\Framework\TestCase;

class XmlWriterTest extends TestCase
{
    private bool $debugEnabled;

    protected function setUp(): void
    {
        $this->debugEnabled = XMLWriter::$debugEnabled;
    }

    protected function tearDown(): void
    {
        XMLWriter::$debugEnabled = $this->debugEnabled;
    }

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

    public function testDebugEnabled(): void
    {
        XMLWriter::$debugEnabled = true;
        $indent = ' ';
        $indentnl = "\n";
        $objWriter = new XMLWriter();
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');
        $expected = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $objWriter->startElement('root');
        $expected .= '<root>' . $indentnl;
        $objWriter->startElement('node');
        $expected .= $indent . '<node>';
        $objWriter->writeRawData('xyz');
        $expected .= 'xyz';
        $objWriter->writeRawData(null);
        $objWriter->writeRawData(['12', '34', '5']);
        $expected .= "12\n34\n5";
        $objWriter->endElement(); // node
        $expected .= '</node>' . $indentnl;
        $objWriter->endElement(); // root
        $expected .= '</root>' . $indentnl;
        self::assertSame($expected, $objWriter->getData());
    }

    public function testDiskCache(): void
    {
        XMLWriter::$debugEnabled = false;
        $indent = '';
        $indentnl = '';
        $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK);
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');
        $expected = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $objWriter->startElement('root');
        $expected .= '<root>' . $indentnl;
        $objWriter->startElement('node');
        $expected .= $indent . '<node>';
        $objWriter->writeRawData('xyz');
        $expected .= 'xyz';
        $objWriter->writeRawData(null);
        $objWriter->writeRawData(['12', '34', '5']);
        $expected .= "12\n34\n5";
        $objWriter->endElement(); // node
        $expected .= '</node>' . $indentnl;
        $objWriter->endElement(); // root
        $expected .= '</root>' . $indentnl;
        self::assertSame($expected, $objWriter->getData());
    }
}
