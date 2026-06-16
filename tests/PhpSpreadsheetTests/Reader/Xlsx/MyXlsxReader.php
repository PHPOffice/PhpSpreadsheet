<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class MyXlsxReader extends XlsxReader
{
    protected function newSpreadsheet(): Spreadsheet
    {
        return new MySpreadsheet();
    }
}
