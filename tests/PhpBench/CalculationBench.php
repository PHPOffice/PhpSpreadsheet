<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetPhpBench;

use PhpBench\Attributes as Bench;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

#[Bench\BeforeMethods('setUp')]
#[Bench\Groups(['calculation'])]
class CalculationBench
{
    private const ROWS = 10000;

    private Spreadsheet $spreadsheet;

    private Worksheet $sheet;

    public function setUp(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $this->sheet = $this->spreadsheet->getActiveSheet();
        mt_srand(42);
        $keys = range(1, self::ROWS);
        shuffle($keys);
        foreach ($keys as $index => $key) {
            $row = $index + 1;
            $this->sheet->getCell("A{$row}")->setValue("Key{$key}");
            $this->sheet->getCell("B{$row}")->setValue($key);
            $this->sheet->getCell("C{$row}")->setValue($key % 7);
        }
    }

    public function benchExpandColumnRange(): void
    {
        Coordinate::extractAllCellReferencesInRange('B1:B40000');
    }

    public function benchExpandBlockRange(): void
    {
        Coordinate::extractAllCellReferencesInRange('A1:T2000');
    }

    public function benchSumLargeRange(): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $this->sheet->getCell("E{$i}")->setValue('=SUM(B1:B' . self::ROWS . ')+' . $i);
            $this->sheet->getCell("E{$i}")->getCalculatedValue();
        }
    }

    public function benchConditionalAggregates(): void
    {
        $this->sheet->getCell('E1')->setValue('=SUMIF(C1:C' . self::ROWS . ',3,B1:B' . self::ROWS . ')');
        $this->sheet->getCell('E2')->setValue('=COUNTIF(C1:C' . self::ROWS . ',">3")');
        $this->sheet->getCell('E3')->setValue('=AVERAGEIFS(B1:B' . self::ROWS . ',C1:C' . self::ROWS . ',"<2")');
        $this->sheet->getCell('E1')->getCalculatedValue();
        $this->sheet->getCell('E2')->getCalculatedValue();
        $this->sheet->getCell('E3')->getCalculatedValue();
    }

    public function benchVlookupExactMatch(): void
    {
        for ($i = 1; $i <= 20; ++$i) {
            $key = $i * 97;
            $this->sheet->getCell("E{$i}")->setValue("=VLOOKUP(\"Key{$key}\",A1:B2000,2,FALSE)");
            $this->sheet->getCell("E{$i}")->getCalculatedValue();
        }
    }

    public function benchLookupFunctions(): void
    {
        for ($i = 1; $i <= 20; ++$i) {
            $key = $i * 97;
            $this->sheet->getCell("E{$i}")->setValue("=INDEX(B1:B2000,MATCH(\"Key{$key}\",A1:A2000,0))");
            $this->sheet->getCell("F{$i}")->setValue("=XLOOKUP(\"Key{$key}\",A1:A2000,B1:B2000)");
            $this->sheet->getCell("E{$i}")->getCalculatedValue();
            $this->sheet->getCell("F{$i}")->getCalculatedValue();
        }
    }

    public function benchDependencyChain(): void
    {
        $this->sheet->getCell('G1')->setValue(1);
        for ($row = 2; $row <= 2000; ++$row) {
            $previous = $row - 1;
            $this->sheet->getCell("G{$row}")->setValue("=G{$previous}+1");
        }
        $this->sheet->getCell('G2000')->getCalculatedValue();
    }

    public function benchParseFormulas(): void
    {
        $calculation = Calculation::getInstance($this->spreadsheet);
        $calculation->disableCalculationCache();
        for ($i = 1; $i <= 200; ++$i) {
            $calculation->parseFormula("=IF(AND(A{$i}>0,B{$i}<100),ROUND(SUM(B1:B{$i})/COUNT(C1:C{$i}),2),IFERROR(VLOOKUP(A{$i},A1:C100,3,FALSE),\"none\"))");
        }
    }
}
