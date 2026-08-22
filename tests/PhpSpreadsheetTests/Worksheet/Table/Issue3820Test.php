<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet\Table;

use PhpOffice\PhpSpreadsheet\Worksheet\Table;

class Issue3820Test extends SetupTeardown
{
    public function testTableOnOtherSheet(): void
    {
        // Clone worksheet failed when table was on sheet
        $spreadsheet = $this->getSpreadsheet();
        $sheet = $this->getSheet();
        $sheet->setTitle('Original');
        $sheet->fromArray(
            [
                ['MyCol', 'Colonne2', 'Colonne3'],
                [10, 20],
                [2],
                [3],
                [4],
            ],
            null,
            'B1',
            true
        );
        $table = new Table('B1:D5', 'Tableau1');
        $sheet->addTable($table);
        $clonedSheet = clone $sheet;
        $clonedSheet->setTitle('Cloned');
        $spreadsheet->addsheet($clonedSheet);
        $originalTable = $spreadsheet->getTableByName('Tableau1');
        $newTable = $spreadsheet->getTableByName('Tableau1clone');
        self::assertNotNull($newTable);
        self::assertSame($table, $originalTable);
        self::assertSame('Cloned', $newTable->getWorksheet()?->getTitle());
        self::assertSame('B1:D5', $newTable->getRange());
    }
}
