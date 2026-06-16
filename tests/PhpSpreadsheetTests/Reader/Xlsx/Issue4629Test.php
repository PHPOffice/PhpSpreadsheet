<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PHPUnit\Framework\TestCase;

class Issue4629Test extends TestCase
{
    public function testExternalAndInternalCondionalStyles(): void
    {
        $infile = 'tests/data/Reader/XLSX/issue.4629.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($infile);
        $sheet = $spreadsheet->getSheetByNameOrThrow('top');
        $conditionals = $sheet->getStyle('A1:A20')->getConditionalStyles();
        self::assertCount(3, $conditionals);

        $conditional = $conditionals[0];
        self::assertSame('expression', $conditional->getConditionType());
        self::assertFalse($conditional->getStopIfTrue());
        self::assertSame(['$A1<>$B1'], $conditional->getConditions());
        self::assertSame(2, $conditional->getPriority());

        $conditional = $conditionals[1];
        self::assertSame('expression', $conditional->getConditionType());
        self::assertFalse($conditional->getStopIfTrue());
        self::assertSame(['AND($A1="cheese", $C1="yogurt")'], $conditional->getConditions());
        self::assertSame(3, $conditional->getPriority());

        $conditional = $conditionals[2]; // defined within <ext>
        self::assertSame('expression', $conditional->getConditionType());
        self::assertTrue($conditional->getStopIfTrue());
        self::assertSame(['A1<>bottom!A1'], $conditional->getConditions());
        self::assertSame(1, $conditional->getPriority());
        self::assertSame('FF9C5700', $conditional->getStyle()->getFont()->getColor()->getArgb());

        $spreadsheet->disconnectWorksheets();
    }
}
