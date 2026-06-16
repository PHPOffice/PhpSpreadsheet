<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

class HtmlExtend2 extends \PhpOffice\PhpSpreadsheet\Writer\Html
{
    protected static bool $alwaysFalse = false;

    protected function tryClose(): bool
    {
        return fclose($this->fileHandle) && self::$alwaysFalse;
    }
}
