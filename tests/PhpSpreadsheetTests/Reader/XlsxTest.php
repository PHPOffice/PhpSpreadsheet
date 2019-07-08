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
    public function testLoadXlsxWorkbookProperties()
    {
        $customPropertySet = [
            'Publisher' => ['type' => Properties::PROPERTY_TYPE_STRING, 'value' => 'PHPOffice Suite'],
            'Tested' => ['type' => Properties::PROPERTY_TYPE_BOOLEAN, 'value' => true],
            'Counter' => ['type' => Properties::PROPERTY_TYPE_INTEGER, 'value' => 15],
            'Rate' => ['type' => Properties::PROPERTY_TYPE_FLOAT, 'value' => 1.15],
            'Refactor Date' => ['type' => Properties::PROPERTY_TYPE_DATE, 'value' => '2019-06-10'],
        ];

        $filename = './data/Reader/XLSX/propertyTest.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $properties = $spreadsheet->getProperties();
        // Core Properties
        $this->assertSame('Mark Baker', $properties->getCreator());
        $this->assertSame('Unit Testing', $properties->getTitle());
        $this->assertSame('Property Test', $properties->getSubject());

        // Extended Properties
        $this->assertSame('PHPOffice', $properties->getCompany());
        $this->assertSame('The Big Boss', $properties->getManager());

        // Custom Properties
        $customProperties = $properties->getCustomProperties();
        $this->assertInternalType('array', $customProperties);
        $customProperties = array_flip($customProperties);
        $this->assertArrayHasKey('Publisher', $customProperties);

        foreach ($customPropertySet as $propertyName => $testData) {
            $this->assertTrue($properties->isCustomPropertySet($propertyName));
            $this->assertSame($testData['type'], $properties->getCustomPropertyType($propertyName));
            if ($properties->getCustomPropertyType($propertyName) == Properties::PROPERTY_TYPE_DATE) {
                $this->assertSame($testData['value'], date('Y-m-d', $properties->getCustomPropertyValue($propertyName)));
            } else {
                $this->assertSame($testData['value'], $properties->getCustomPropertyValue($propertyName));
            }
        }
    }

    public function testLoadXlsxRowColumnAttributes()
    {
        $filename = './data/Reader/XLSX/rowColumnAttributeTest.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $worksheet = $spreadsheet->getActiveSheet();
        for ($row = 1; $row <= 4; ++$row) {
            $this->assertEquals($row * 5 + 10, floor($worksheet->getRowDimension($row)->getRowHeight()));
        }

        $this->assertFalse($worksheet->getRowDimension(5)->getVisible());

        for ($column = 1; $column <= 4; ++$column) {
            $columnAddress = Coordinate::stringFromColumnIndex($column);
            $this->assertEquals(
                $column * 2 + 2,
                floor($worksheet->getColumnDimension($columnAddress)->getWidth())
            );
        }

        $this->assertFalse($worksheet->getColumnDimension('E')->getVisible());
    }

    public function testLoadXlsxWithStyles()
    {
        $expectedColours = [
            1 => ['A' => 'C00000', 'C' => 'FF0000', 'E' => 'FFC000'],
            3 => ['A' => '7030A0', 'C' => '000000', 'E' => 'FFFF00'],
            5 => ['A' => '002060', 'C' => '000000', 'E' => '92D050'],
            7 => ['A' => '0070C0', 'C' => '00B0F0', 'E' => '00B050'],
        ];

        $filename = './data/Reader/XLSX/stylesTest.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $worksheet = $spreadsheet->getActiveSheet();
        for ($row = 1; $row <= 8; $row += 2) {
            for ($column = 'A'; $column !== 'G'; ++$column, ++$column) {
                $this->assertEquals(
                    $expectedColours[$row][$column],
                    $worksheet->getStyle($column . $row)->getFill()->getStartColor()->getRGB()
                );
            }
        }
    }

    public function testLoadXlsxAutofilter()
    {
        $filename = './data/Reader/XLSX/autofilterTest.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $worksheet = $spreadsheet->getActiveSheet();

        $autofilter = $worksheet->getAutoFilter();
        $this->assertInstanceOf(AutoFilter::class, $autofilter);
        $this->assertEquals('A1:D57', $autofilter->getRange());
        $this->assertEquals(
            AutoFilter\Column::AUTOFILTER_FILTERTYPE_FILTER,
            $autofilter->getColumn('A')->getFilterType()
        );
    }

    public function testLoadXlsxPageSetup()
    {
        $filename = './data/Reader/XLSX/pageSetupTest.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $worksheet = $spreadsheet->getActiveSheet();

        $pageMargins = $worksheet->getPageMargins();
        // Convert from inches to cm for testing
        $this->assertEquals(2.5, $pageMargins->getTop() * 2.54);
        $this->assertEquals(3.3, $pageMargins->getLeft() * 2.54);
        $this->assertEquals(3.3, $pageMargins->getRight() * 2.54);
        $this->assertEquals(1.3, $pageMargins->getHeader() * 2.54);

        $this->assertEquals(PageSetup::PAPERSIZE_A4, $worksheet->getPageSetup()->getPaperSize());
        $this->assertEquals(['A10', 'A20', 'A30', 'A40', 'A50'], array_keys($worksheet->getBreaks()));
    }

    public function testLoadXlsxConditionalFormatting()
    {
        $filename = './data/Reader/XLSX/conditionalFormattingTest.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $worksheet = $spreadsheet->getActiveSheet();

        $conditionalStyle = $worksheet->getCell('B2')->getStyle()->getConditionalStyles();

        $this->assertNotEmpty($conditionalStyle);
        $conditionalRule = $conditionalStyle[0];
        $this->assertNotEmpty($conditionalRule->getConditions());
        $this->assertEquals(Conditional::CONDITION_CELLIS, $conditionalRule->getConditionType());
        $this->assertEquals(Conditional::OPERATOR_BETWEEN, $conditionalRule->getOperatorType());
        $this->assertEquals(['200', '400'], $conditionalRule->getConditions());
        $this->assertInstanceOf(Style::class, $conditionalRule->getStyle());
    }

    public function testLoadXlsxDataValidation()
    {
        $filename = './data/Reader/XLSX/dataValidationTest.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $worksheet = $spreadsheet->getActiveSheet();

        $this->assertTrue($worksheet->getCell('B3')->hasDataValidation());
    }

    /**
     * Test load Xlsx file without cell reference.
     *
     * @doesNotPerformAssertions
     */
    public function testLoadXlsxWithoutCellReference()
    {
        $filename = './data/Reader/XLSX/without_cell_reference.xlsx';
        $reader = new Xlsx();
        $reader->load($filename);
    }

    /**
     * Test load Xlsx file and use a read filter.
     */
    public function testLoadWithReadFilter()
    {
        $filename = './data/Reader/XLSX/without_cell_reference.xlsx';
        $reader = new Xlsx();
        $reader->setReadFilter(new OddColumnReadFilter());
        $data = $reader->load($filename)->getActiveSheet()->toArray();
        $ref = [1.0, null, 3.0, null, 5.0, null, 7.0, null, 9.0, null];

        for ($i = 0; $i < 10; ++$i) {
            $this->assertEquals($ref, \array_slice($data[$i], 0, 10, true));
        }
    }

    /**
     * Test load Xlsx file with drawing having double attributes.
     *
     * @doesNotPerformAssertions
     */
    public function testLoadXlsxWithDoubleAttrDrawing()
    {
        if (version_compare(PHP_VERSION, '7.0.0', '<')) {
            $this->markTestSkipped('Only handled in PHP version >= 7.0.0');
        }
        $filename = './data/Reader/XLSX/double_attr_drawing.xlsx';
        $reader = new Xlsx();
        $reader->load($filename);
    }

    /**
     * Test correct save and load xlsx files with empty drawings.
     * Such files can be generated by Google Sheets.
     */
    public function testLoadSaveWithEmptyDrawings()
    {
        $filename = __DIR__ . '/../../data/Reader/XLSX/empty_drawing.xlsx';
        $reader = new Xlsx();
        $excel = $reader->load($filename);
        $resultFilename = tempnam(File::sysGetTempDir(), 'phpspreadsheet-test');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($excel);
        $writer->save($resultFilename);
        $excel = $reader->load($resultFilename);
        // Fake assert. The only thing we need is to ensure the file is loaded without exception
        $this->assertNotNull($excel);
    }
}
