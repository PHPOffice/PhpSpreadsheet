<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Named ranges whose value has no leading '=' (which is how the Xlsx reader loads them)
 * must be adjusted like those that have one: absolute parts move, relative parts do not.
 */
class ReferenceHelperNamedRangeTest extends AbstractFunctional
{
    public static function providerInsertRows(): array
    {
        return [
            'unquoted sheet' => ['Data', 'Data!$A$2:$A$4', 'Data!$A$4:$A$6'],
            'quoted sheet' => ['Data', "'Data'!\$A\$2:\$A\$4", "'Data'!\$A\$4:\$A\$6"],
            'sheet name with space' => ['My Data', "'My Data'!\$A\$2:\$A\$4", "'My Data'!\$A\$4:\$A\$6"],
            'relative part is not moved' => ['Data', 'Data!$A$2:$A4', 'Data!$A$4:$A4'],
            'single cell' => ['Data', 'Data!$A$3', 'Data!$A$5'],
            'with leading equals (unchanged behaviour)' => ['Data', '=Data!$A$2:$A$4', '=Data!$A$4:$A$6'],
        ];
    }

    #[DataProvider('providerInsertRows')]
    public function testInsertRows(string $title, string $value, string $expected): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($title);
        $spreadsheet->addNamedRange(new NamedRange('GLOBAL_NAME', $sheet, $value));
        $spreadsheet->addNamedRange(new NamedRange('LOCAL_NAME', $sheet, $value, true));

        $sheet->insertNewRowBefore(2, 2);

        self::assertSame($expected, $spreadsheet->getDefinedName('GLOBAL_NAME')?->getValue());
        self::assertSame($expected, $spreadsheet->getDefinedName('LOCAL_NAME', $sheet)?->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testDeleteRows(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data');
        $spreadsheet->addNamedRange(new NamedRange('NAME', $sheet, 'Data!$A$4:$A$6'));

        $sheet->removeRow(2, 2);

        self::assertSame('Data!$A$2:$A$4', $spreadsheet->getDefinedName('NAME')?->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testInsertColumns(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data');
        $spreadsheet->addNamedRange(new NamedRange('NAME', $sheet, 'Data!$B$1:$D$1'));

        $sheet->insertNewColumnBefore('A', 2);

        self::assertSame('Data!$D$1:$F$1', $spreadsheet->getDefinedName('NAME')?->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testOtherSheetIsNotMoved(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data');
        $other = $spreadsheet->createSheet();
        $other->setTitle('Other');
        $spreadsheet->addNamedRange(new NamedRange('NAME', $other, 'Other!$A$2:$A$4'));

        $sheet->insertNewRowBefore(2, 2);

        self::assertSame('Other!$A$2:$A$4', $spreadsheet->getDefinedName('NAME')?->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testInsertRowsAfterXlsxRoundTrip(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data');
        $sheet->fromArray([[1], [2], [3], [4], [5]]);
        $spreadsheet->addNamedRange(new NamedRange('VALUES', $sheet, '=Data!$A$2:$A$4'));
        $sheet->setCellValue('C1', '=SUM(VALUES)');

        $reloaded = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $reloadedSheet = $reloaded->getSheetByNameOrThrow('Data');

        $reloadedSheet->insertNewRowBefore(2, 2);
        Calculation::getInstance($reloaded)->clearCalculationCache();

        self::assertSame("'Data'!\$A\$4:\$A\$6", $reloaded->getDefinedName('VALUES')?->getValue());
        self::assertSame(9, $reloadedSheet->getCell('C1')->getCalculatedValue());
        $reloaded->disconnectWorksheets();
    }
}
