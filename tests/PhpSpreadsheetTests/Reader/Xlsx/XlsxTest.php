<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class XlsxTest extends TestCase
{
    const XLSX_PRECISION = 1.0E-8;

    public function testLoadXlsxRowColumnAttributes(): void
    {
        $filename = 'tests/data/Reader/XLSX/rowColumnAttributeTest.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $worksheet = $spreadsheet->getActiveSheet();
        for ($row = 1; $row <= 4; ++$row) {
            self::assertEquals($row * 5 + 10, floor($worksheet->getRowDimension($row)->getRowHeight()));
        }

        self::assertFalse($worksheet->getRowDimension(5)->getVisible());

        for ($column = 1; $column <= 4; ++$column) {
            $columnAddress = Coordinate::stringFromColumnIndex($column);
            self::assertEquals(
                $column * 2 + 2,
                floor($worksheet->getColumnDimension($columnAddress)->getWidth())
            );
        }

        self::assertFalse($worksheet->getColumnDimension('E')->getVisible());
        $spreadsheet->disconnectWorksheets();
    }

    public function testLoadXlsxWithStyles(): void
    {
        $expectedColours = [
            1 => ['A' => 'C00000', 'C' => 'FF0000', 'E' => 'FFC000'],
            3 => ['A' => '7030A0', 'C' => 'FFFFFF', 'E' => 'FFFF00'],
            5 => ['A' => '002060', 'C' => 'FFFFFF', 'E' => '92D050'],
            7 => ['A' => '0070C0', 'C' => '00B0F0', 'E' => '00B050'],
        ];

        $filename = 'tests/data/Reader/XLSX/stylesTest.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $worksheet = $spreadsheet->getActiveSheet();
        for ($row = 1; $row <= 8; $row += 2) {
            for ($column = 'A'; $column !== 'G'; StringHelper::stringIncrement($column), StringHelper::stringIncrement($column)) {
                self::assertEquals(
                    $expectedColours[$row][$column],
                    $worksheet->getStyle($column . $row)->getFill()->getStartColor()->getRGB()
                );
            }
        }
        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test load Xlsx file without styles.xml.
     */
    public function testLoadXlsxWithoutStyles(): void
    {
        $filename = 'tests/data/Reader/XLSX/issue.2246a.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $tempFilename = File::temporaryFilename();
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($tempFilename);

        $reader = new Xlsx();
        $reloadedSpreadsheet = $reader->load($tempFilename);
        unlink($tempFilename);

        $reloadedWorksheet = $reloadedSpreadsheet->getActiveSheet();

        self::assertEquals('TipoDato', $reloadedWorksheet->getCell('A1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test load Xlsx file with empty styles.xml.
     */
    public function testLoadXlsxWithEmptyStyles(): void
    {
        $filename = 'tests/data/Reader/XLSX/issue.2246b.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $tempFilename = File::temporaryFilename();
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($tempFilename);

        $reader = new Xlsx();
        $reloadedSpreadsheet = $reader->load($tempFilename);
        unlink($tempFilename);

        $reloadedWorksheet = $reloadedSpreadsheet->getActiveSheet();
        self::assertEquals('TipoDato', $reloadedWorksheet->getCell('A1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testLoadXlsxAutofilter(): void
    {
        $filename = 'tests/data/Reader/XLSX/autofilterTest.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $worksheet = $spreadsheet->getActiveSheet();

        $autofilter = $worksheet->getAutoFilter();
        self::assertEquals('A1:D57', $autofilter->getRange());
        self::assertEquals(
            AutoFilter\Column::AUTOFILTER_FILTERTYPE_FILTER,
            $autofilter->getColumn('A')->getFilterType()
        );
        $spreadsheet->disconnectWorksheets();
    }

    public function testLoadXlsxPageSetup(): void
    {
        $filename = 'tests/data/Reader/XLSX/pageSetupTest.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $worksheet = $spreadsheet->getActiveSheet();

        $pageMargins = $worksheet->getPageMargins();
        // Convert from inches to cm for testing
        self::assertEqualsWithDelta(2.5, $pageMargins->getTop() * 2.54, self::XLSX_PRECISION);
        self::assertEqualsWithDelta(3.3, $pageMargins->getLeft() * 2.54, self::XLSX_PRECISION);
        self::assertEqualsWithDelta(3.3, $pageMargins->getRight() * 2.54, self::XLSX_PRECISION);
        self::assertEqualsWithDelta(1.3, $pageMargins->getHeader() * 2.54, self::XLSX_PRECISION);

        self::assertEquals(PageSetup::PAPERSIZE_A4, $worksheet->getPageSetup()->getPaperSize());
        self::assertEquals(['A10', 'A20', 'A30', 'A40', 'A50'], array_keys($worksheet->getBreaks()));
    }

    public function testLoadXlsxConditionalFormatting(): void
    {
        $filename = 'tests/data/Reader/XLSX/conditionalFormattingTest.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $worksheet = $spreadsheet->getActiveSheet();

        $conditionalStyle = $worksheet->getCell('B2')->getStyle()->getConditionalStyles();

        self::assertNotEmpty($conditionalStyle);
        $conditionalRule = $conditionalStyle[0];
        self::assertNotEmpty($conditionalRule->getConditions());
        self::assertEquals(Conditional::CONDITION_CELLIS, $conditionalRule->getConditionType());
        self::assertEquals(Conditional::OPERATOR_BETWEEN, $conditionalRule->getOperatorType());
        self::assertEquals(['200', '400'], $conditionalRule->getConditions());
        /** @var mixed[][] */
        $temp = $conditionalRule->getStyle()->exportArray();
        self::assertSame('#,##0.00_-"€"', $temp['numberFormat']['formatCode']);
        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test load Xlsx file without cell reference.
     */
    public function testLoadXlsxWithoutCellReference(): void
    {
        $filename = 'tests/data/Reader/XLSX/without_cell_reference.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);
        self::assertSame(1, $spreadsheet->getActiveSheet()->getCell('A1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test load Xlsx file and use a read filter.
     */
    public function testLoadWithReadFilter(): void
    {
        $filename = 'tests/data/Reader/XLSX/without_cell_reference.xlsx';
        $reader = new Xlsx();
        $reader->setReadFilter(new OddColumnReadFilter());
        $spreadsheet = $reader->load($filename);
        $data = $spreadsheet->getActiveSheet()->toArray(formatData: false);
        $ref = [1, null, 3, null, 5, null, 7, null, 9, null];

        for ($i = 0; $i < 10; ++$i) {
            self::assertSame($ref, \array_slice($data[$i], 0, 10, true));
        }
        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test load Xlsx file with drawing having double attributes.
     */
    public function testLoadXlsxWithDoubleAttrDrawing(): void
    {
        $filename = 'tests/data/Reader/XLSX/double_attr_drawing.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);
        self::assertSame('TOSHIBA_HITACHI_SKYWORTH', $spreadsheet->getActiveSheet()->getTitle());
        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test correct save and load xlsx files with empty drawings.
     * Such files can be generated by Google Sheets.
     */
    public function testLoadSaveWithEmptyDrawings(): void
    {
        $filename = 'tests/data/Reader/XLSX/empty_drawing.xlsx';
        $reader = new Xlsx();
        $excel = $reader->load($filename);
        $resultFilename = File::temporaryFilename();
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($excel);
        $writer->save($resultFilename);
        $excel = $reader->load($resultFilename);
        unlink($resultFilename);
        self::assertSame(1.0, $excel->getActiveSheet()->getCell('A1')->getValue());
        $excel->disconnectWorksheets();
    }

    /**
     * Test if all whitespace is removed from a style definition string.
     * This is needed to parse it into properties with the correct keys.
     */
    #[DataProvider('providerStripsWhiteSpaceFromStyleString')]
    public function testStripsWhiteSpaceFromStyleString(string $string): void
    {
        $string = Xlsx::stripWhiteSpaceFromStyleString($string);
        self::assertEquals(preg_match('/\s/', $string), 0);
    }

    public static function providerStripsWhiteSpaceFromStyleString(): array
    {
        return [
            ['position:absolute;margin-left:424.5pt;margin-top:169.5pt;width:67.5pt;
        height:13.5pt;z-index:5;mso-wrap-style:tight'],
            ['position:absolute;margin-left:424.5pt;margin-top:169.5pt;width:67.5pt;
height:13.5pt;z-index:5;mso-wrap-style:tight'],
            ['position:absolute; margin-left:424.5pt; margin-top:169.5pt; width:67.5pt;
            height:13.5pt;z-index:5;mso-wrap-style:tight'],
        ];
    }

    public function testLoadDataOnlyLoadsAlsoTables(): void
    {
        $filename = 'tests/data/Reader/XLSX/data_with_tables.xlsx';
        $reader = new Xlsx();
        $excel = $reader->load($filename, IReader::READ_DATA_ONLY);

        self::assertEquals(['First', 'Second'], $excel->getSheetNames());

        $table = $excel->getTableByName('Tableau1');
        $firstSheet = $excel->getSheetByName('First');
        $secondSheet = $excel->getSheetByName('Second');
        if (!$table || !$firstSheet || !$secondSheet) {
            self::fail('Table or Sheet not found.');
        }

        self::assertEquals('A1:B5', $table->getRange());
        self::assertEquals([['1', '2', '3']], $firstSheet->toArray());
        self::assertEquals([
            ['Colonne1', 'Colonne2'],
            ['a', 'b'],
            ['c', 'd'],
            ['e', 'f'],
            ['g', 'h'],
        ], $secondSheet->toArray());
        $excel->disconnectWorksheets();
    }
}
