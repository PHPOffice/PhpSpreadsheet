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
    private const ODS_TEST_FILE = 'samples/templates/OOCalcTest.ods';

    private const ODS_DATA_FILE = 'tests/data/Reader/Ods/data.ods';

    /** @var string */
    private $incompleteMessage = 'Features not implemented yet';

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
     * @return Spreadsheet
     */
    private function loadOdsTestFile()
    {
        $reader = new Ods();

        return $reader->loadIntoExisting(self::ODS_TEST_FILE, new Spreadsheet());
    }

    /**
     * @return Spreadsheet
     */
    protected function loadDataFile()
    {
        $reader = new Ods();

        return $reader->load(self::ODS_DATA_FILE);
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
        $spreadsheet->disconnectWorksheets();
    }

    public function testLoadOneWorksheet(): void
    {
        $reader = new Ods();
        $reader->setLoadSheetsOnly(['Sheet1']);
        $spreadsheet = $reader->load(self::ODS_DATA_FILE);

        self::assertEquals(1, $spreadsheet->getSheetCount());

        self::assertEquals('Sheet1', $spreadsheet->getSheet(0)->getTitle());
        $spreadsheet->disconnectWorksheets();
    }

    public function testLoadOneWorksheetNotActive(): void
    {
        $reader = new Ods();
        $reader->setLoadSheetsOnly(['Second Sheet']);
        $spreadsheet = $reader->load(self::ODS_DATA_FILE);

        self::assertEquals(1, $spreadsheet->getSheetCount());

        self::assertEquals('Second Sheet', $spreadsheet->getSheet(0)->getTitle());
        $spreadsheet->disconnectWorksheets();
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
        $spreadsheet->disconnectWorksheets();
    }

    public function testReadPercentageAndCurrency(): void
    {
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
        $spreadsheet->disconnectWorksheets();
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
        $spreadsheet->disconnectWorksheets();
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
        $spreadsheet->disconnectWorksheets();
    }

    public function testReadCellsWithRepeatedSpaces(): void
    {
        $spreadsheet = $this->loadDataFile();
        $firstSheet = $spreadsheet->getSheet(0);

        self::assertEquals('This has    4 spaces before and 2 after  ', $firstSheet->getCell('A8')->getValue());
        self::assertEquals('This only one after ', $firstSheet->getCell('A9')->getValue());
        self::assertEquals('Test with DIFFERENT styles     and multiple spaces:  ', $firstSheet->getCell('A10')->getValue());
        self::assertEquals("test with new \nLines", $firstSheet->getCell('A11')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testReadHyperlinks(): void
    {
        $spreadsheet = $this->loadOdsTestFile();
        $firstSheet = $spreadsheet->getSheet(0);

        $hyperlink = $firstSheet->getCell('A29');

        self::assertEquals(DataType::TYPE_STRING, $hyperlink->getDataType());
        self::assertEquals('PhpSpreadsheet', $hyperlink->getValue());
        self::assertEquals('https://github.com/PHPOffice/phpspreadsheet', $hyperlink->getHyperlink()->getUrl());
        $spreadsheet->disconnectWorksheets();
    }

    // Below some test for features not implemented yet

    public function testReadBoldItalicUnderline(): void
    {
        if ($this->incompleteMessage !== '') {
            self::markTestIncomplete($this->incompleteMessage);
        }
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
        $spreadsheet->disconnectWorksheets();
    }
}
