<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Reader\Ods as OdsReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Writer\Ods as OdsWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;
use ZipArchive;

class TableTest extends AbstractFunctional
{
    public function testReadTables(): void
    {
        // Written by LibreOffice from an XLSX: tables Sales (with a header) and NoHead (without)
        // on sheet Data, and the anonymous range of the filter of sheet 'Other sheet'
        $spreadsheet = (new OdsReader())->load('tests/data/Reader/Ods/Tables.ods');
        self::assertSame([
            ['NoHead', 'D1:E2', false, false],
            ['Sales', 'A1:B3', true, true],
        ], self::describe($spreadsheet->getSheetByNameOrThrow('Data')->getTableCollection()->getArrayCopy()));
        self::assertSame('', $spreadsheet->getSheetByNameOrThrow('Data')->getAutoFilter()->getRange());
        self::assertSame([], $spreadsheet->getSheetByNameOrThrow('Other sheet')->getTableNames());
        self::assertSame('A1:A3', $spreadsheet->getSheetByNameOrThrow('Other sheet')->getAutoFilter()->getRange());
        $spreadsheet->disconnectWorksheets();

        // A sheet that was not loaded takes neither its tables nor its filter
        $spreadsheet = (new OdsReader())->setLoadSheetsOnly(['Other sheet'])->load('tests/data/Reader/Ods/Tables.ods');
        self::assertSame([], $spreadsheet->getActiveSheet()->getTableNames());
        self::assertSame('A1:A3', $spreadsheet->getActiveSheet()->getAutoFilter()->getRange());
        $spreadsheet->disconnectWorksheets();
    }

    public function testReadDatabaseRangesAsLibreOfficeDoes(): void
    {
        $ranges = '<table:database-range table:name="__Anonymous_DB__1" table:target-range-address="Data.G1:Data.G2"/>'
            . '<table:database-range table:name="Tx" table:target-range-address="$Data.$A$1:$Data.$B$3"/>'
            . '<table:database-range table:name="tx" table:target-range-address="Data.D1:Data.E2"/>'
            . '<table:database-range table:name="A1" table:target-range-address="Data.D1:Data.E2"/>'
            . '<table:database-range table:name="Elsewhere" table:target-range-address="Nope.A1:Nope.B3"/>';
        $file = (string) tempnam(sys_get_temp_dir(), 'ods');
        copy('tests/data/Reader/Ods/Tables.ods', $file);
        $zip = new ZipArchive();
        $zip->open($file);
        $content = (string) $zip->getFromName('content.xml');
        $zip->addFromString('content.xml', (string) preg_replace('~<table:database-ranges>.*</table:database-ranges>~s', '<table:database-ranges>' . str_replace('$', '\$', $ranges) . '</table:database-ranges>', $content));
        $zip->close();

        $spreadsheet = (new OdsReader())->load($file);
        unlink($file);
        // A name taken already or one that cannot be a table is dropped; an anonymous range is a filter
        $data = $spreadsheet->getSheetByNameOrThrow('Data');
        self::assertSame([['Tx', 'A1:B3', true, false]], self::describe($data->getTableCollection()->getArrayCopy()));
        self::assertSame('G1:G2', $data->getAutoFilter()->getRange());
        self::assertSame([], $spreadsheet->getSheetByNameOrThrow('Other sheet')->getTableNames());
        self::assertSame('', $spreadsheet->getSheetByNameOrThrow('Other sheet')->getAutoFilter()->getRange());
        $spreadsheet->disconnectWorksheets();
    }

    public function testWriteTables(): void
    {
        $spreadsheetOld = new Spreadsheet();
        $sheet = $spreadsheetOld->getActiveSheet()->setTitle("Bob's data");
        $sheet->fromArray([['Name', 'Qty'], ['A', 1], ['B', 2]]);
        $noHeader = new Table('D1:E2');
        $noHeader->setShowHeaderRow(false);
        $sheet->addTable($noHeader);
        $sheet->addTable(new Table('A1:B3', 'Sales'));
        // Takes the name the unnamed table would get first
        $noFilter = new Table('G1:G2', 'table1');
        $noFilter->setAllowFilter(false);
        $sheet->addTable($noFilter);
        $spreadsheetOld->addNamedRange(new NamedRange('Named', $sheet, '$A$1'));
        $spreadsheetOld->createSheet()->setTitle('Filtered')->setAutoFilter('A1:A3');
        $spreadsheetOld->createSheet()->setTitle('Last');

        $content = (new OdsWriter($spreadsheetOld))->getWriterPartContent()->write();
        self::assertStringContainsString(
            '<table:database-ranges>'
            . '<table:database-range table:name="Table2" table:target-range-address="\'Bob\'\'s data\'.D1:\'Bob\'\'s data\'.E2" table:contains-header="false"/>'
            . '<table:database-range table:name="Sales" table:target-range-address="\'Bob\'\'s data\'.A1:\'Bob\'\'s data\'.B3" table:display-filter-buttons="true"/>'
            . '<table:database-range table:name="table1" table:target-range-address="\'Bob\'\'s data\'.G1:\'Bob\'\'s data\'.G2"/>'
            . '<table:database-range table:orientation="column" table:display-filter-buttons="true" table:target-range-address="\'Filtered\'.A1:A3"/>'
            . '</table:database-ranges>',
            $content
        );
        // The schema puts the database ranges after the named expressions
        self::assertLessThan(strpos($content, '<table:database-ranges>'), strpos($content, '<table:named-expressions>'));

        $spreadsheet = $this->writeAndReload($spreadsheetOld, 'Ods');
        $spreadsheetOld->disconnectWorksheets();
        self::assertSame([
            ['Table2', 'D1:E2', false, false],
            ['Sales', 'A1:B3', true, true],
            ['table1', 'G1:G2', true, false],
        ], self::describe($spreadsheet->getSheet(0)->getTableCollection()->getArrayCopy()));
        // The filter stays on its sheet, not on the one active while reading
        self::assertSame('A1:A3', $spreadsheet->getSheetByNameOrThrow('Filtered')->getAutoFilter()->getRange());
        self::assertSame('', $spreadsheet->getSheetByNameOrThrow('Last')->getAutoFilter()->getRange());
        $spreadsheet->disconnectWorksheets();
    }

    /**
     * @param Table[] $tables
     *
     * @return array{string, string, bool, bool}[]
     */
    private static function describe(array $tables): array
    {
        return array_map(
            fn (Table $table): array => [$table->getName(), $table->getRange(), $table->getShowHeaderRow(), $table->getAllowFilter()],
            $tables
        );
    }
}
