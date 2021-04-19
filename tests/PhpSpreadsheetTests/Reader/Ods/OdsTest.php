<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Document\Properties;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PHPUnit\Framework\TestCase;

/**
 * @TODO The class doesn't read the bold/italic/underline properties (rich text)
 */
class OdsTest extends TestCase
{
    /**
     * @var string
     */
    private $timeZone;

    protected function setUp(): void
    {
        $this->timeZone = date_default_timezone_get();
        date_default_timezone_set('UTC');
    }

    protected function tearDown(): void
    {
        date_default_timezone_set($this->timeZone);
    }

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
        if (!isset($this->spreadsheetOdsTest)) {
            $filename = 'samples/templates/OOCalcTest.ods';

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
        if (!isset($this->spreadsheetData)) {
            $filename = 'tests/data/Reader/Ods/data.ods';

            // Load into this instance
            $reader = new Ods();
            $this->spreadsheetData = $reader->load($filename);
        }

        return $this->spreadsheetData;
    }

    public function testLoadWorksheets(): void
    {
        $spreadsheet = $this->loadDataFile();

        self::assertInstanceOf('PhpOffice\PhpSpreadsheet\Spreadsheet', $spreadsheet);

        self::assertEquals(2, $spreadsheet->getSheetCount());

        $firstSheet = $spreadsheet->getSheet(0);
        self::assertInstanceOf('PhpOffice\PhpSpreadsheet\Worksheet\Worksheet', $firstSheet);

        $secondSheet = $spreadsheet->getSheet(1);
        self::assertInstanceOf('PhpOffice\PhpSpreadsheet\Worksheet\Worksheet', $secondSheet);
        self::assertEquals('Sheet1', $spreadsheet->getSheet(0)->getTitle());
        self::assertEquals('Second Sheet', $spreadsheet->getSheet(1)->getTitle());
    }

    public function testLoadOneWorksheet(): void
    {
        $filename = 'tests/data/Reader/Ods/data.ods';

        // Load into this instance
        $reader = new Ods();
        $reader->setLoadSheetsOnly(['Sheet1']);
        $spreadsheet = $reader->load($filename);

        self::assertEquals(1, $spreadsheet->getSheetCount());

        self::assertEquals('Sheet1', $spreadsheet->getSheet(0)->getTitle());
    }

    public function testLoadOneWorksheetNotActive(): void
    {
        $filename = 'tests/data/Reader/Ods/data.ods';

        // Load into this instance
        $reader = new Ods();
        $reader->setLoadSheetsOnly(['Second Sheet']);
        $spreadsheet = $reader->load($filename);

        self::assertEquals(1, $spreadsheet->getSheetCount());

        self::assertEquals('Second Sheet', $spreadsheet->getSheet(0)->getTitle());
    }

    public function testLoadBadFile(): void
    {
        $this->expectException(ReaderException::class);
        $reader = new Ods();
        $spreadsheet = $reader->load(__FILE__);

        self::assertInstanceOf('PhpOffice\PhpSpreadsheet\Spreadsheet', $spreadsheet);

        self::assertEquals(2, $spreadsheet->getSheetCount());

        $firstSheet = $spreadsheet->getSheet(0);
        self::assertInstanceOf('PhpOffice\PhpSpreadsheet\Worksheet\Worksheet', $firstSheet);

        $secondSheet = $spreadsheet->getSheet(1);
        self::assertInstanceOf('PhpOffice\PhpSpreadsheet\Worksheet\Worksheet', $secondSheet);
    }

    public function testLoadCorruptFile(): void
    {
        $this->expectException(ReaderException::class);
        $filename = 'tests/data/Reader/Ods/corruptMeta.ods';
        $reader = new Ods();
        $spreadsheet = $reader->load($filename);

        self::assertInstanceOf('PhpOffice\PhpSpreadsheet\Spreadsheet', $spreadsheet);

        self::assertEquals(2, $spreadsheet->getSheetCount());

        $firstSheet = $spreadsheet->getSheet(0);
        self::assertInstanceOf('PhpOffice\PhpSpreadsheet\Worksheet\Worksheet', $firstSheet);

        $secondSheet = $spreadsheet->getSheet(1);
        self::assertInstanceOf('PhpOffice\PhpSpreadsheet\Worksheet\Worksheet', $secondSheet);
    }

    public function testReadValueAndComments(): void
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
        self::assertEquals('19-Dec-60', $firstSheet->getCell('A10')->getFormattedValue());

        self::assertEquals(DataType::TYPE_NUMERIC, $firstSheet->getCell('A13')->getDataType()); // Time
        self::assertEquals('2:30:00', $firstSheet->getCell('A13')->getFormattedValue());

        self::assertEquals(DataType::TYPE_NUMERIC, $firstSheet->getCell('A15')->getDataType()); // Date + Time
        self::assertEquals('19-Dec-60 1:30:00', $firstSheet->getCell('A15')->getFormattedValue());

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

    public function testReadColors(): void
    {
        $spreadsheet = $this->loadOdsTestFile();
        $firstSheet = $spreadsheet->getSheet(0);

        // Background color

        $style = $firstSheet->getCell('K3')->getStyle();

        self::assertEquals('none', $style->getFill()->getFillType());
        self::assertEquals('FFFFFFFF', $style->getFill()->getStartColor()->getARGB());
        self::assertEquals('FF000000', $style->getFill()->getEndColor()->getARGB());
    }

    public function testReadRichText(): void
    {
        $spreadsheet = $this->loadOdsTestFile();
        $firstSheet = $spreadsheet->getSheet(0);

        self::assertEquals(
            "I don't know if OOCalc supports Rich Text in the same way as Excel, " .
            'And this row should be autofit height with text wrap',
            $firstSheet->getCell('A28')->getValue()
        );
    }

    public function testReadCellsWithRepeatedSpaces(): void
    {
        $spreadsheet = $this->loadDataFile();
        $firstSheet = $spreadsheet->getSheet(0);

        self::assertEquals('This has    4 spaces before and 2 after  ', $firstSheet->getCell('A8')->getValue());
        self::assertEquals('This only one after ', $firstSheet->getCell('A9')->getValue());
        self::assertEquals('Test with DIFFERENT styles     and multiple spaces:  ', $firstSheet->getCell('A10')->getValue());
        self::assertEquals("test with new \nLines", $firstSheet->getCell('A11')->getValue());
    }

    public function testReadHyperlinks(): void
    {
        $spreadsheet = $this->loadOdsTestFile();
        $firstSheet = $spreadsheet->getSheet(0);

        $hyperlink = $firstSheet->getCell('A29');

        self::assertEquals(DataType::TYPE_STRING, $hyperlink->getDataType());
        self::assertEquals('PhpSpreadsheet', $hyperlink->getValue());
        self::assertEquals('https://github.com/PHPOffice/phpspreadsheet', $hyperlink->getHyperlink()->getUrl());
    }

    // Below some test for features not implemented yet

    public function testReadBoldItalicUnderline(): void
    {
        self::markTestIncomplete('Features not implemented yet');

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

    public function testLoadOdsWorkbookProperties(): void
    {
        $customPropertySet = [
            'Owner' => ['type' => Properties::PROPERTY_TYPE_STRING, 'value' => 'PHPOffice'],
            'Tested' => ['type' => Properties::PROPERTY_TYPE_BOOLEAN, 'value' => true],
            'Counter' => ['type' => Properties::PROPERTY_TYPE_FLOAT, 'value' => 10.0],
            'TestDate' => ['type' => Properties::PROPERTY_TYPE_DATE, 'value' => '2019-06-30'],
            'HereAndNow' => ['type' => Properties::PROPERTY_TYPE_DATE, 'value' => '2019-06-30'],
        ];

        $filename = 'tests/data/Reader/Ods/propertyTest.ods';
        $reader = new Ods();
        $spreadsheet = $reader->load($filename);

        $properties = $spreadsheet->getProperties();
        // Core Properties
//        self::assertSame('Mark Baker', $properties->getCreator());
        self::assertSame('Property Test File', $properties->getTitle());
        self::assertSame('Testing for Properties', $properties->getSubject());
        self::assertSame('TEST ODS PHPSpreadsheet', $properties->getKeywords());

        // Extended Properties
//        self::assertSame('PHPOffice', $properties->getCompany());
//        self::assertSame('The Big Boss', $properties->getManager());

        // Custom Properties
        $customProperties = $properties->getCustomProperties();
        self::assertIsArray($customProperties);
        $customProperties = array_flip($customProperties);
        self::assertArrayHasKey('TestDate', $customProperties);

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
}
