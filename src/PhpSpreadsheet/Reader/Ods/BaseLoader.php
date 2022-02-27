<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Ods;

use DOMElement;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

abstract class BaseLoader
{
    /**
     * @var Spreadsheet
     */
    protected $spreadsheet;

    /**
     * @var string
     */
    protected $tableNs;

    public function __construct(Spreadsheet $spreadsheet, string $tableNs)
    {
        $this->spreadsheet = $spreadsheet;
        $this->tableNs = $tableNs;
    }

    abstract public function read(DOMElement $workbookData): void;
}
