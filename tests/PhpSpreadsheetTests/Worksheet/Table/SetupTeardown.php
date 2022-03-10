<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet\Table;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class SetupTeardown extends TestCase
{
    /**
     * @var ?Spreadsheet
     */
    private $spreadsheet;

    /**
     * @var ?Worksheet
     */
    private $sheet;

    /**
     * @var int
     */
    protected $maxRow = 4;

    protected function tearDown(): void
    {
        $this->sheet = null;
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
    }

    protected function getSpreadsheet(): Spreadsheet
    {
        if ($this->spreadsheet !== null) {
            return $this->spreadsheet;
        }
        $this->spreadsheet = new Spreadsheet();

        return $this->spreadsheet;
    }

    protected function getSheet(): Worksheet
    {
        if ($this->sheet !== null) {
            return $this->sheet;
        }
        $this->sheet = $this->getSpreadsheet()->getActiveSheet();

        return $this->sheet;
    }
}
