<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PHPUnit\Framework\TestCase;

class DataTypeTest extends TestCase
{
    public function testGetErrorCodes(): void
    {
        $result = DataType::getErrorCodes();
        self::assertIsArray($result);
        self::assertGreaterThan(0, count($result));
        self::assertArrayHasKey('#NULL!', $result);
    }

    public function testCheckString(): void
    {
        $richText = new RichText();
        $result1 = DataType::checkString($richText);
        self::assertSame($richText, $result1);

        $stringLimit = 32767;
        $randString = $this->randr($stringLimit + 10);
        $result2 = DataType::checkString($randString);
        self::assertIsString($result2);
        self::assertSame($stringLimit, strlen($result2));

        $dirtyString = "bla bla\r\n bla\r test\n";
        $expected = "bla bla\n bla\n test\n";
        $result3 = DataType::checkString($dirtyString);
        self::assertSame($expected, $result3);
    }

    private function randr(int $length = 8): string
    {
        $string = '';
        for ($i = 0; $i < $length; ++$i) {
            $x = mt_rand(0, 2);
            switch ($x) {
                case 0:
                    $string .= chr(mt_rand(97, 122));

                    break;
                case 1:
                    $string .= chr(mt_rand(65, 90));

                    break;
                case 2:
                    $string .= chr(mt_rand(48, 57));

                    break;
            }
        }

        return $string;
    }
}
