<?php

namespace PhpOffice\PhpSpreadsheet\Shared;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;

class CodePage
{
    public const DEFAULT_CODE_PAGE = 'CP1252';

    /** @var array */
    private static $pageArray = [
        0 => 'CP1252', //    CodePage is not always correctly set when the xls file was saved by Apple's Numbers program
        367 => 'ASCII', //    ASCII
        437 => 'CP437', //    OEM US
        //720 => 'notsupported', //    OEM Arabic
        737 => 'CP737', //    OEM Greek
        775 => 'CP775', //    OEM Baltic
        850 => 'CP850', //    OEM Latin I
        852 => 'CP852', //    OEM Latin II (Central European)
        855 => 'CP855', //    OEM Cyrillic
        857 => 'CP857', //    OEM Turkish
        858 => 'CP858', //    OEM Multilingual Latin I with Euro
        860 => 'CP860', //    OEM Portugese
        861 => 'CP861', //    OEM Icelandic
        862 => 'CP862', //    OEM Hebrew
        863 => 'CP863', //    OEM Canadian (French)
        864 => 'CP864', //    OEM Arabic
        865 => 'CP865', //    OEM Nordic
        866 => 'CP866', //    OEM Cyrillic (Russian)
        869 => 'CP869', //    OEM Greek (Modern)
        874 => 'CP874', //    ANSI Thai
        932 => 'CP932', //    ANSI Japanese Shift-JIS
        936 => 'CP936', //    ANSI Chinese Simplified GBK
        949 => 'CP949', //    ANSI Korean (Wansung)
        950 => 'CP950', //    ANSI Chinese Traditional BIG5
        1200 => 'UTF-16LE', //    UTF-16 (BIFF8)
        1250 => 'CP1250', //    ANSI Latin II (Central European)
        1251 => 'CP1251', //    ANSI Cyrillic
        1252 => 'CP1252', //    ANSI Latin I (BIFF4-BIFF7)
        1253 => 'CP1253', //    ANSI Greek
        1254 => 'CP1254', //    ANSI Turkish
        1255 => 'CP1255', //    ANSI Hebrew
        1256 => 'CP1256', //    ANSI Arabic
        1257 => 'CP1257', //    ANSI Baltic
        1258 => 'CP1258', //    ANSI Vietnamese
        1361 => 'CP1361', //    ANSI Korean (Johab)
        10000 => 'MAC', //    Apple Roman
        10001 => 'CP932', //    Macintosh Japanese
        10002 => 'CP950', //    Macintosh Chinese Traditional
        10003 => 'CP1361', //    Macintosh Korean
        10004 => 'MACARABIC', //    Apple Arabic
        10005 => 'MACHEBREW', //    Apple Hebrew
        10006 => 'MACGREEK', //    Macintosh Greek
        10007 => 'MACCYRILLIC', //    Macintosh Cyrillic
        10008 => 'CP936', //    Macintosh - Simplified Chinese (GB 2312)
        10010 => 'MACROMANIA', //    Macintosh Romania
        10017 => 'MACUKRAINE', //    Macintosh Ukraine
        10021 => 'MACTHAI', //    Macintosh Thai
        10029 => ['MACCENTRALEUROPE', 'MAC-CENTRALEUROPE'], //    Macintosh Central Europe
        10079 => 'MACICELAND', //    Macintosh Icelandic
        10081 => 'MACTURKISH', //    Macintosh Turkish
        10082 => 'MACCROATIAN', //    Macintosh Croatian
        21010 => 'UTF-16LE', //    UTF-16 (BIFF8) This isn't correct, but some Excel writer libraries erroneously use Codepage 21010 for UTF-16LE
        32768 => 'MAC', //    Apple Roman
        //32769 => 'unsupported', //    ANSI Latin I (BIFF2-BIFF3)
        65000 => 'UTF-7', //    Unicode (UTF-7)
        65001 => 'UTF-8', //    Unicode (UTF-8)
        99999 => ['unsupported'], //    Unicode (UTF-8)
    ];

    public static function validate(string $codePage): bool
    {
        return in_array($codePage, self::$pageArray, true);
    }

    /**
     * Convert Microsoft Code Page Identifier to Code Page Name which iconv
     * and mbstring understands.
     *
     * @param int $codePage Microsoft Code Page Indentifier
     *
     * @return string Code Page Name
     */
    public static function numberToName(int $codePage): string
    {
        if (array_key_exists($codePage, self::$pageArray)) {
            $value = self::$pageArray[$codePage];
            if (is_array($value)) {
                foreach ($value as $encoding) {
                    if (@iconv('UTF-8', $encoding, ' ') !== false) {
                        self::$pageArray[$codePage] = $encoding;

                        return $encoding;
                    }
                }

                throw new PhpSpreadsheetException("Code page $codePage not implemented on this system.");
            } else {
                return $value;
            }
        }
        if ($codePage == 720 || $codePage == 32769) {
            throw new PhpSpreadsheetException("Code page $codePage not supported."); //    OEM Arabic
        }

        throw new PhpSpreadsheetException('Unknown codepage: ' . $codePage);
    }

    public static function getEncodings(): array
    {
        return self::$pageArray;
    }
}
