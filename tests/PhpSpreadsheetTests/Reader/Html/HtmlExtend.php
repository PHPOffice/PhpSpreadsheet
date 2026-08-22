<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Html;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;

class HtmlExtend extends \PhpOffice\PhpSpreadsheet\Reader\Html
{
    protected static string $deliberate = 'deliberate';

    protected static function replaceNonAsciiIfNeeded(string $convert): ?string
    {
        if (self::$deliberate !== '') {
            throw new ReaderException(self::$deliberate);
        }

        return parent::replaceNonAsciiIfNeeded($convert);
    }
}
