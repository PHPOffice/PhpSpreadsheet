<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Ods;

class Mimetype extends WriterPart
{
    /**
     * Write mimetype to plain text format.
     *
     * @return string XML Output
     */
    public function write()
    {
        return 'application/vnd.oasis.opendocument.spreadsheet';
    }
}
