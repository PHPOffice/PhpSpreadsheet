<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class Issue641Test extends TestCase
{
    /**
     * Problem cloning sheet referred to in formulas.
     */
    public function testIssue641(): void
    {
        $xlsx = new Spreadsheet();
        $xlsx->removeSheetByIndex(0);
        $availableWs = [];

        $worksheet = $xlsx->createSheet();
        $worksheet->setTitle('Condensed A');
        $worksheet->getCell('A1')->setValue("=SUM('Detailed A'!A1:A10)");
        $worksheet->getCell('A2')->setValue(mt_rand(1, 30));
        $availableWs[] = 'Condensed A';

        $worksheet = $xlsx->createSheet();
        $worksheet->setTitle('Condensed B');
        $worksheet->getCell('A1')->setValue("=SUM('Detailed B'!A1:A10)");
        $worksheet->getCell('A2')->setValue(mt_rand(1, 30));
        $availableWs[] = 'Condensed B';

        // at this point the value in worksheet 'Condensed B' cell A1 is
        // =SUM('Detailed B'!A1:A10)

        // worksheet in question is cloned and totals are attached
        $totalWs1 = clone $xlsx->getSheet($xlsx->getSheetCount() - 1);
        $totalWs1->setTitle('Condensed Total');
        $xlsx->addSheet($totalWs1);
        $formula = '=';
        foreach ($availableWs as $ws) {
            $formula .= sprintf("+'%s'!A2", $ws);
        }
        $totalWs1->getCell('A1')->setValue("=SUM('Detailed Total'!A1:A10)");
        $totalWs1->getCell('A2')->setValue($formula);

        $availableWs = [];

        $worksheet = $xlsx->createSheet();
        $worksheet->setTitle('Detailed A');
        for ($step = 1; $step <= 10; ++$step) {
            $worksheet->getCell("A{$step}")->setValue(mt_rand(1, 30));
        }
        $availableWs[] = 'Detailed A';

        $worksheet = $xlsx->createSheet();
        $worksheet->setTitle('Detailed B');
        for ($step = 1; $step <= 10; ++$step) {
            $worksheet->getCell("A{$step}")->setValue(mt_rand(1, 30));
        }
        $availableWs[] = 'Detailed B';

        $totalWs2 = clone $xlsx->getSheet($xlsx->getSheetCount() - 1);
        $totalWs2->setTitle('Detailed Total');
        $xlsx->addSheet($totalWs2);

        for ($step = 1; $step <= 10; ++$step) {
            $formula = '=';
            foreach ($availableWs as $ws) {
                $formula .= sprintf("+'%s'!A%s", $ws, $step);
            }
            $totalWs2->getCell("A{$step}")->setValue($formula);
        }

        $xsheet = $xlsx->getSheetByNameOrThrow('Condensed A');
        $cell = $xsheet->getCell('A1');
        self::assertNotNull($cell);
        self::assertSame("=SUM('Detailed A'!A1:A10)", $cell->getValue());

        $xsheet = $xlsx->getSheetByNameOrThrow('Condensed B');
        $cell = $xsheet->getCell('A1');
        self::assertNotNull($cell);
        self::assertSame("=SUM('Detailed B'!A1:A10)", $cell->getValue());

        $xsheet = $xlsx->getSheetByNameOrThrow('Condensed Total');
        $cell = $xsheet->getCell('A1');
        self::assertNotNull($cell);
        self::assertSame("=SUM('Detailed Total'!A1:A10)", $cell->getValue());

        $xsheet = $xlsx->getSheetByNameOrThrow('Detailed Total');
        $cell = $xsheet->getCell('A1');
        self::assertNotNull($cell);
        self::assertSame("=+'Detailed A'!A1+'Detailed B'!A1", $cell->getValue());

        $xlsx->disconnectWorksheets();
    }
}
