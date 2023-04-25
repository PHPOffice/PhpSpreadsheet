<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
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
            for ($column = 'A'; $column !== 'G'; ++$column, ++$column) {
                self::assertEquals(
                    $expectedColours[$row][$column],
                    $worksheet->getStyle($column . $row)->getFill()->getStartColor()->getRGB()
                );
            }
        }
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
    }

    public function testLoadXlsxAutofilter(): void
    {
        $filename = 'tests/data/Reader/XLSX/autofilterTest.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $worksheet = $spreadsheet->getActiveSheet();

        $autofilter = $worksheet->getAutoFilter();
        self::assertInstanceOf(AutoFilter::class, $autofilter);
        self::assertEquals('A1:D57', $autofilter->getRange());
        self::assertEquals(
            AutoFilter\Column::AUTOFILTER_FILTERTYPE_FILTER,
            $autofilter->getColumn('A')->getFilterType()
        );
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
        self::assertInstanceOf(Style::class, $conditionalRule->getStyle());
    }

    /**
     * Test load Xlsx file without cell reference.
     *
     * @doesNotPerformAssertions
     */
    public function testLoadXlsxWithoutCellReference(): void
    {
        $filename = 'tests/data/Reader/XLSX/without_cell_reference.xlsx';
        $reader = new Xlsx();
        $reader->load($filename);
    }

    /**
     * Test load Xlsx file and use a read filter.
     */
    public function testLoadWithReadFilter(): void
    {
        $filename = 'tests/data/Reader/XLSX/without_cell_reference.xlsx';
        $reader = new Xlsx();
        $reader->setReadFilter(new OddColumnReadFilter());
        $data = $reader->load($filename)->getActiveSheet()->toArray();
        $ref = [1.0, null, 3.0, null, 5.0, null, 7.0, null, 9.0, null];

        for ($i = 0; $i < 10; ++$i) {
            self::assertEquals($ref, \array_slice($data[$i], 0, 10, true));
        }
    }

    /**
     * Test load Xlsx file with drawing having double attributes.
     *
     * @doesNotPerformAssertions
     */
    public function testLoadXlsxWithDoubleAttrDrawing(): void
    {
        $filename = 'tests/data/Reader/XLSX/double_attr_drawing.xlsx';
        $reader = new Xlsx();
        $reader->load($filename);
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
        // Fake assert. The only thing we need is to ensure the file is loaded without exception
        self::assertNotNull($excel);
    }

    /**
     * Test if all whitespace is removed from a style definition string.
     * This is needed to parse it into properties with the correct keys.
     *
     * @dataProvider providerStripsWhiteSpaceFromStyleString
     */
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
}
