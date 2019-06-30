<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Document\Properties;
use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PHPUnit\Framework\TestCase;

/**
 * @todo The class doesn't read the bold/italic/underline properties (rich text)
 */
class OdsTest extends TestCase
{
    /**
     * @var Spreadsheet
     */
    private $spreadsheetOdsTest;

    /**
     * @var Spreadsheet
     */
    private $spreadsheetData;

    /**
     * @return Spreadsheet
     */
    private function loadOdsTestFile()
    {
        if (!$this->spreadsheetOdsTest) {
            $filename = __DIR__ . '/../../../samples/templates/OOCalcTest.ods';

            // Load into this instance
            $reader = new Ods();
            $this->spreadsheetOdsTest = $reader->loadIntoExisting($filename, new Spreadsheet());
        }

        return $this->spreadsheetOdsTest;
    }

    /**
     * @return Spreadsheet
     */
    protected function loadDataFile()
    {
        if (!$this->spreadsheetData) {
            $filename = __DIR__ . '/../../data/Reader/Ods/data.ods';

            // Load into this instance
            $reader = new Ods();
            $this->spreadsheetData = $reader->loadIntoExisting($filename, new Spreadsheet());
        }

        return $this->spreadsheetData;
    }

    public function testReadFileProperties()
    {
        $filename = __DIR__ . '/../../data/Reader/Ods/data.ods';

        // Load into this instance
        $reader = new Ods();

        // Test "listWorksheetNames" method

        self::assertEquals([
            'Sheet1',
            'Second Sheet',
        ], $reader->listWorksheetNames($filename));
    }

    public function testLoadWorksheets()
    {
        $spreadsheet = $this->loadDataFile();

        self::assertInstanceOf('PhpOffice\PhpSpreadsheet\Spreadsheet', $spreadsheet);

        self::assertEquals(2, $spreadsheet->getSheetCount());

        $firstSheet = $spreadsheet->getSheet(0);
        self::assertInstanceOf('PhpOffice\PhpSpreadsheet\Worksheet\Worksheet', $firstSheet);

        $secondSheet = $spreadsheet->getSheet(1);
        self::assertInstanceOf('PhpOffice\PhpSpreadsheet\Worksheet\Worksheet', $secondSheet);
    }

    public function testReadValueAndComments()
    {
        $spreadsheet = $this->loadOdsTestFile();

        $firstSheet = $spreadsheet->getSheet(0);

        self::assertEquals(29, $firstSheet->getHighestRow());
        self::assertEquals('N', $firstSheet->getHighestColumn());

        // Simple cell value
        self::assertEquals('Test String 1', $firstSheet->getCell('A1')->getValue());

        // Merged cell
        self::assertEquals('BOX', $firstSheet->getCell('B18')->getValue());

        // Comments/Annotations
        self::assertEquals(
            'Test for a simple colour-formatted string',
            $firstSheet->getComment('A1')->getText()->getPlainText()
        );

        // Data types
        self::assertEquals(DataType::TYPE_STRING, $firstSheet->getCell('A1')->getDataType());
        self::assertEquals(DataType::TYPE_NUMERIC, $firstSheet->getCell('B1')->getDataType()); // Int

        self::assertEquals(DataType::TYPE_NUMERIC, $firstSheet->getCell('B6')->getDataType()); // Float
        self::assertEquals(1.23, $firstSheet->getCell('B6')->getValue());
        self::assertEquals(0, $firstSheet->getCell('G10')->getValue());

        self::assertEquals(DataType::TYPE_NUMERIC, $firstSheet->getCell('A10')->getDataType()); // Date
        self::assertEquals(22269.0, $firstSheet->getCell('A10')->getValue());

        self::assertEquals(DataType::TYPE_NUMERIC, $firstSheet->getCell('A13')->getDataType()); // Time
        self::assertEquals(25569.0625, $firstSheet->getCell('A13')->getValue());

        self::assertEquals(DataType::TYPE_NUMERIC, $firstSheet->getCell('A15')->getDataType()); // Date + Time
        self::assertEquals(22269.0625, $firstSheet->getCell('A15')->getValue());

        self::assertEquals(DataType::TYPE_NUMERIC, $firstSheet->getCell('A11')->getDataType()); // Fraction

        self::assertEquals(DataType::TYPE_BOOL, $firstSheet->getCell('D6')->getDataType());
        self::assertTrue($firstSheet->getCell('D6')->getValue());

        self::assertEquals(DataType::TYPE_FORMULA, $firstSheet->getCell('C6')->getDataType()); // Formula
        self::assertEquals('=TRUE()', $firstSheet->getCell('C6')->getValue()); // Formula

        // Percentage, Currency

        $spreadsheet = $this->loadDataFile();

        $firstSheet = $spreadsheet->getSheet(0);

        self::assertEquals(DataType::TYPE_NUMERIC, $firstSheet->getCell('A1')->getDataType()); // Percentage (10%)
        self::assertEquals(0.1, $firstSheet->getCell('A1')->getValue());

        self::assertEquals(DataType::TYPE_NUMERIC, $firstSheet->getCell('A2')->getDataType()); // Percentage (10.00%)
        self::assertEquals(0.1, $firstSheet->getCell('A2')->getValue());

        self::assertEquals(DataType::TYPE_NUMERIC, $firstSheet->getCell('A4')->getDataType()); // Currency (€10.00)
        self::assertEquals(10, $firstSheet->getCell('A4')->getValue());

        self::assertEquals(DataType::TYPE_NUMERIC, $firstSheet->getCell('A5')->getDataType()); // Currency ($20)
        self::assertEquals(20, $firstSheet->getCell('A5')->getValue());
    }

    public function testReadColors()
    {
        $spreadsheet = $this->loadOdsTestFile();
        $firstSheet = $spreadsheet->getSheet(0);

        // Background color

        $style = $firstSheet->getCell('K3')->getStyle();

        self::assertEquals('none', $style->getFill()->getFillType());
        self::assertEquals('FFFFFFFF', $style->getFill()->getStartColor()->getARGB());
        self::assertEquals('FF000000', $style->getFill()->getEndColor()->getARGB());
    }

    public function testReadRichText()
    {
        $spreadsheet = $this->loadOdsTestFile();
        $firstSheet = $spreadsheet->getSheet(0);

        self::assertEquals(
            "I don't know if OOCalc supports Rich Text in the same way as Excel, " .
            'And this row should be autofit height with text wrap',
            $firstSheet->getCell('A28')->getValue()
        );
    }

    public function testReadCellsWithRepeatedSpaces()
    {
        $spreadsheet = $this->loadDataFile();
        $firstSheet = $spreadsheet->getSheet(0);

        self::assertEquals('This has    4 spaces before and 2 after  ', $firstSheet->getCell('A8')->getValue());
        self::assertEquals('This only one after ', $firstSheet->getCell('A9')->getValue());
        self::assertEquals('Test with DIFFERENT styles     and multiple spaces:  ', $firstSheet->getCell('A10')->getValue());
        self::assertEquals("test with new \nLines", $firstSheet->getCell('A11')->getValue());
    }

    public function testReadHyperlinks()
    {
        $spreadsheet = $this->loadOdsTestFile();
        $firstSheet = $spreadsheet->getSheet(0);

        $hyperlink = $firstSheet->getCell('A29');

        self::assertEquals(DataType::TYPE_STRING, $hyperlink->getDataType());
        self::assertEquals('PhpSpreadsheet', $hyperlink->getValue());
        self::assertEquals('https://github.com/PHPOffice/phpspreadsheet', $hyperlink->getHyperlink()->getUrl());
    }

    // Below some test for features not implemented yet

    public function testReadBoldItalicUnderline()
    {
        $this->markTestIncomplete('Features not implemented yet');

        $spreadsheet = $this->loadOdsTestFile();
        $firstSheet = $spreadsheet->getSheet(0);

        // Font styles

        $style = $firstSheet->getCell('A1')->getStyle();
        self::assertEquals('FF000000', $style->getFont()->getColor()->getARGB());
        self::assertEquals(11, $style->getFont()->getSize());
        self::assertEquals(Font::UNDERLINE_NONE, $style->getFont()->getUnderline());

        $style = $firstSheet->getCell('E3')->getStyle();
        self::assertEquals(Font::UNDERLINE_SINGLE, $style->getFont()->getUnderline());

        $style = $firstSheet->getCell('E1')->getStyle();
        self::assertTrue($style->getFont()->getBold());
        self::assertTrue($style->getFont()->getItalic());
    }

    public function testLoadOdsWorkbookProperties()
    {
        $customPropertySet = [
            'Owner' => ['type' => Properties::PROPERTY_TYPE_STRING, 'value' => 'PHPOffice'],
            'Tested' => ['type' => Properties::PROPERTY_TYPE_BOOLEAN, 'value' => true],
            'Counter' => ['type' => Properties::PROPERTY_TYPE_FLOAT, 'value' => 10.0],
            'TestDate' => ['type' => Properties::PROPERTY_TYPE_DATE, 'value' => '2019-06-30'],
            'HereAndNow' => ['type' => Properties::PROPERTY_TYPE_DATE, 'value' => '2019-06-30'],
        ];

        $filename = './data/Reader/Ods/propertyTest.ods';
        $reader = new Ods();
        $spreadsheet = $reader->load($filename);

        $properties = $spreadsheet->getProperties();
        // Core Properties
//        $this->assertSame('Mark Baker', $properties->getCreator());
        $this->assertSame('Property Test File', $properties->getTitle());
        $this->assertSame('Testing for Properties', $properties->getSubject());
        $this->assertSame('TEST ODS PHPSpreadsheet', $properties->getKeywords());

        // Extended Properties
//        $this->assertSame('PHPOffice', $properties->getCompany());
//        $this->assertSame('The Big Boss', $properties->getManager());

        // Custom Properties
        $customProperties = $properties->getCustomProperties();
        $this->assertInternalType('array', $customProperties);
        $customProperties = array_flip($customProperties);
        $this->assertArrayHasKey('TestDate', $customProperties);

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
}
