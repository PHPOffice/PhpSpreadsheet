<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class ConditionalFormattingTest extends TestCase
{
    /**
     * @var Worksheet
     */
    protected $sheet;

    public function setUp(): void
    {
        $filename = 'tests/data/Reader/XLS/CF_Basic_Comparisons.xls';
        $reader = new Xls();
        $spreadsheet = $reader->load($filename);
        $this->sheet = $spreadsheet->getActiveSheet();
    }

    public function testReadConditionalFormatting(): void
    {
        $hasConditionalStyles = $this->sheet->conditionalStylesExists('A2:E5');
        self::assertTrue($hasConditionalStyles);
        $onditionalStyles = $this->sheet->getConditionalStyles('A2:E5');
    }
}
