<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class SetupTeardownDatabases extends TestCase
{
    protected const RESULT_CELL = 'Z1';

    private ?Spreadsheet $spreadsheet = null;

    private ?Worksheet $sheet = null;

    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    protected function tearDown(): void
    {
        $this->sheet = null;
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    protected static function database1(): array
    {
        return [
            ['Tree', 'Height', 'Age', 'Yield', 'Profit'],
            ['Apple', 18, 20, 14, 105],
            ['Pear', 12, 12, 10, 96],
            ['Cherry', 13, 14, 9, 105],
            ['Apple', 14, 15, 10, 75],
            ['Pear', 9, 8, 8, 76.8],
            ['Apple', 8, 9, 6, 45],
        ];
    }

    protected static function database2(): array
    {
        return [
            ['Quarter', 'Area', 'Sales Rep.', 'Sales'],
            [1, 'North', 'Jeff', 223000],
            [1, 'North', 'Chris', 125000],
            [1, 'South', 'Carol', 456000],
            [1, 'South', 'Tina', 289000],
            [2, 'North', 'Jeff', 322000],
            [2, 'North', 'Chris', 340000],
            [2, 'South', 'Carol', 198000],
            [2, 'South', 'Tina', 222000],
            [3, 'North', 'Jeff', 310000],
            [3, 'North', 'Chris', 250000],
            [3, 'South', 'Carol', 460000],
            [3, 'South', 'Tina', 395000],
            [4, 'North', 'Jeff', 261000],
            [4, 'North', 'Chris', 389000],
            [4, 'South', 'Carol', 305000],
            [4, 'South', 'Tina', 188000],
        ];
    }

    protected static function database3(): array
    {
        return [
            ['Name', 'Gender', 'Age', 'Subject', 'Score'],
            ['Amy', 'Female', 8, 'Math', 0.63],
            ['Amy', 'Female', 8, 'English', 0.78],
            ['Amy', 'Female', 8, 'Science', 0.39],
            ['Bill', 'Male', 8, 'Math', 0.55],
            ['Bill', 'Male', 8, 'English', 0.71],
            ['Bill', 'Male', 8, 'Science', 'awaiting'],
            ['Sue', 'Female', 9, 'Math', null],
            ['Sue', 'Female', 9, 'English', 0.52],
            ['Sue', 'Female', 9, 'Science', 0.48],
            ['Tom', 'Male', 9, 'Math', 0.78],
            ['Tom', 'Male', 9, 'English', 0.69],
            ['Tom', 'Male', 9, 'Science', 0.65],
        ];
    }

    protected static function database3FilledIn(): array
    {
        // same as database3 except two omitted scores are filled in
        return [
            ['Name', 'Gender', 'Age', 'Subject', 'Score'],
            ['Amy', 'Female', 10, 'Math', 0.63],
            ['Amy', 'Female', 10, 'English', 0.78],
            ['Amy', 'Female', 10, 'Science', 0.39],
            ['Bill', 'Male', 8, 'Math', 0.55],
            ['Bill', 'Male', 8, 'English', 0.71],
            ['Bill', 'Male', 8, 'Science', 0.51],
            ['Sam', 'Male', 9, 'Math', 0.39],
            ['Sam', 'Male', 9, 'English', 0.52],
            ['Sam', 'Male', 9, 'Science', 0.48],
            ['Tom', 'Male', 9, 'Math', 0.78],
            ['Tom', 'Male', 9, 'English', 0.69],
            ['Tom', 'Male', 9, 'Science', 0.65],
        ];
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

    public function prepareWorksheetWithFormula(string $functionName, array $database, null|int|string $field, array $criteria): void
    {
        $sheet = $this->getSheet();
        $maxCol = '';
        $startCol = 'A';
        $maxRow = 0;
        $startRow = 1;
        $row = $startRow;
        foreach ($database as $dataRow) {
            $col = $startCol;
            foreach ($dataRow as $dataCell) {
                $sheet->getCell("$col$row")->setValue($dataCell);
                $maxCol = max($col, $maxCol);
                ++$col;
            }
            $maxRow = $row;
            ++$row;
        }
        $databaseCells = "$startCol$startRow:$maxCol$maxRow";
        $maxCol = '';
        $startCol = 'P';
        $maxRow = 0;
        $startRow = 1;
        $row = $startRow;
        foreach ($criteria as $dataRow) {
            $col = $startCol;
            foreach ($dataRow as $dataCell) {
                if ($dataCell !== null) {
                    $sheet->getCell("$col$row")->setValueExplicit($dataCell, DataType::TYPE_STRING);
                }
                $maxCol = max($col, $maxCol);
                ++$col;
            }
            $maxRow = $row;
            ++$row;
        }
        $criteriaCells = "$startCol$startRow:$maxCol$maxRow";
        $sheet->getCell('N1')->setValue($field);
        $sheet->getCell(self::RESULT_CELL)->setValue("=$functionName($databaseCells, N1, $criteriaCells)");
    }
}
