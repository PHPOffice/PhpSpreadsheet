<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls\ErrorCode;
use PHPUnit\Framework\TestCase;

class ErrorCodeMapTest extends TestCase
{
    /**
     * @dataProvider errorCodeMapProvider
     */
    public function testErrorCode(bool|string $expected, int $index): void
    {
        self::assertSame($expected, ErrorCode::lookup($index));
    }

    public static function errorCodeMapProvider(): array
    {
        return [
            [false, 0x01],
            ['#NULL!', 0x00],
            ['#DIV/0!', 0x07],
            ['#VALUE!', 0x0F],
            ['#REF!', 0x17],
            ['#NAME?', 0x1D],
            ['#NUM!', 0x24],
            ['#N/A', 0x2A],
        ];
    }
}
