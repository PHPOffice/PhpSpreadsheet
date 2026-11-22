<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PHPUnit\Framework\TestCase;

class Biff8ConstantArrayTest extends TestCase
{
    private static function numberConstant(float $value): string
    {
        return "\x01" . pack('e', $value);
    }

    private static function stringConstant(string $value): string
    {
        return "\x02" . pack('v', strlen($value)) . "\x00" . $value;
    }

    public function testValidConstantArray(): void
    {
        $arrayData = pack('Cv', 1, 1)
            . self::numberConstant(1.5)
            . self::numberConstant(1.5)
            . self::numberConstant(1.5)
            . self::numberConstant(1.5);

        $result = Biff8Probe::readConstantArray($arrayData);

        self::assertSame('{1.5,1.5;1.5,1.5}', $result['value']);
        self::assertSame(strlen($arrayData), $result['size']);
    }

    public function testTruncatedHeaderIsRejected(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Truncated BIFF8 constant array.');
        Biff8Probe::readConstantArray("\x01\x01");
    }

    public function testConstantCountBeyondRecordSizeIsRejected(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('BIFF8 constant array exceeds record size.');
        Biff8Probe::readConstantArray(pack('Cv', 255, 65535));
    }

    public function testMissingConstantsAreRejected(): void
    {
        $arrayData = pack('Cv', 1, 1) . self::numberConstant(1.5);

        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('BIFF8 constant array exceeds record size.');
        Biff8Probe::readConstantArray($arrayData);
    }

    public function testTruncatedConstantIsRejected(): void
    {
        $arrayData = pack('Cv', 0, 0) . "\x04" . str_repeat("\x00", 4);

        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Truncated BIFF8 constant array.');
        Biff8Probe::readConstantArray($arrayData);
    }

    public function testStringConstantAt255IsAccepted(): void
    {
        $value = str_repeat('A', 255);
        $arrayData = pack('Cv', 0, 0) . self::stringConstant($value);

        $result = Biff8Probe::readConstantArray($arrayData);

        self::assertSame('{"' . $value . '"}', $result['value']);
        self::assertSame(strlen($arrayData), $result['size']);
    }

    public function testSequentialStringConstantsEscapeQuotes(): void
    {
        $arrayData = pack('Cv', 1, 0)
            . self::stringConstant('a"b')
            . self::stringConstant('c');

        $result = Biff8Probe::readConstantArray($arrayData);

        self::assertSame('{"a""b","c"}', $result['value']);
        self::assertSame(strlen($arrayData), $result['size']);
    }

    public function testStringConstantOver255IsRejected(): void
    {
        $arrayData = pack('Cv', 0, 0) . self::stringConstant(str_repeat('A', 256));

        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('BIFF8 string constant exceeds 255 characters.');
        Biff8Probe::readConstantArray($arrayData);
    }

    public function testTruncatedStringConstantIsRejected(): void
    {
        $constant = "\x02" . pack('v', 10) . "\x00" . 'A';
        $arrayData = pack('Cv', 0, 0) . $constant;

        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Truncated BIFF8 constant array.');
        Biff8Probe::readConstantArray($arrayData);
    }

    public function testUnsupportedConstantTypeIsRejected(): void
    {
        $arrayData = pack('Cv', 0, 0) . "\xFF" . str_repeat("\x00", 8);

        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Unsupported BIFF8 constant');
        Biff8Probe::readConstantArray($arrayData);
    }
}
