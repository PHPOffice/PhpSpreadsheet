<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Document\Properties;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PHPUnit\Framework\TestCase;

class XlsxTest extends TestCase
{
    public function testLoadXlsxWorkbookProperties(): void
    {
        $customPropertySet = [
            'Publisher' => ['type' => Properties::PROPERTY_TYPE_STRING, 'value' => 'PHPOffice Suite'],
            'Tested' => ['type' => Properties::PROPERTY_TYPE_BOOLEAN, 'value' => true],
            'Counter' => ['type' => Properties::PROPERTY_TYPE_INTEGER, 'value' => 15],
            'Rate' => ['type' => Properties::PROPERTY_TYPE_FLOAT, 'value' => 1.15],
            'Refactor Date' => ['type' => Properties::PROPERTY_TYPE_DATE, 'value' => '2019-06-10'],
        ];

        $filename = 'tests/data/Reader/XLSX/propertyTest.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $properties = $spreadsheet->getProperties();
        // Core Properties
        self::assertSame('Mark Baker', $properties->getCreator());
        self::assertSame('Unit Testing', $properties->getTitle());
        self::assertSame('Property Test', $properties->getSubject());

        // Extended Properties
        self::assertSame('PHPOffice', $properties->getCompany());
        self::assertSame('The Big Boss', $properties->getManager());

        // Custom Properties
        $customProperties = $properties->getCustomProperties();
        self::assertIsArray($customProperties);
        $customProperties = array_flip($customProperties);
        self::assertArrayHasKey('Publisher', $customProperties);

        foreach ($customPropertySet as $propertyName => $testData) {
            self::assertTrue($properties->isCustomPropertySet($propertyName));
            self::assertSame($testData['type'], $properties->getCustomPropertyType($propertyName));
            if ($properties->getCustomPropertyType($propertyName) == Properties::PROPERTY_TYPE_DATE) {
                self::assertSame($testData['value'], date('Y-m-d', $properties->getCustomPropertyValue($propertyName)));
            } else {
                self::assertSame($testData['value'], $properties->getCustomPropertyValue($propertyName));
            }
        }
    }

    public function testListWorksheetInfo(): void
    {
        $filename = 'tests/data/Reader/XLSX/rowColumnAttributeTest.xlsx';
        $reader = new Xlsx();
        $actual = $reader->listWorksheetInfo($filename);

        $expected = [
            [
                'worksheetName' => 'Sheet1',
                'lastColumnLetter' => 'F',
                'lastColumnIndex' => 5,
                'totalRows' => '6',
                'totalColumns' => 6,
            ],
        ];

        self::assertEquals($expected, $actual);
    }

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
        self::assertEquals(2.5, $pageMargins->getTop() * 2.54);
        self::assertEquals(3.3, $pageMargins->getLeft() * 2.54);
        self::assertEquals(3.3, $pageMargins->getRight() * 2.54);
        self::assertEquals(1.3, $pageMargins->getHeader() * 2.54);

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

    public function testLoadXlsxDataValidation(): void
    {
        $filename = 'tests/data/Reader/XLSX/dataValidationTest.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $worksheet = $spreadsheet->getActiveSheet();

        self::assertTrue($worksheet->getCell('B3')->hasDataValidation());
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
    public function testStripsWhiteSpaceFromStyleString($string): void
    {
        $string = Xlsx::stripWhiteSpaceFromStyleString($string);
        self::assertEquals(preg_match('/\s/', $string), 0);
    }

    public function providerStripsWhiteSpaceFromStyleString(): array
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
