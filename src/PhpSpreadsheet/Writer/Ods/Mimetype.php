<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Mimetype extends WriterPart
{
    /**
     * Write mimetype to plain text format.
     *
     * @param Spreadsheet $spreadsheet
     *
     * @return string XML Output
     */
    public function write(?Spreadsheet $spreadsheet = null)
    {
        return 'application/vnd.oasis.opendocument.spreadsheet';
    }
}
