<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Shared\CodePage;

class XlsSpecialCodePage extends \PhpOffice\PhpSpreadsheet\Reader\Xls
{
    protected function readCodepage(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; code page identifier
        $codepage = self::getUInt2d($recordData, 0);

        if ($this->codepage === '') {
            $this->codepage = CodePage::numberToName($codepage);
        }
    }
}
