<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 2/14/17
 * Time: 9:18 PM
 */

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PhpOffice\PhpSpreadsheet\Style\Font;

/*
 * @todo Fix sheet name (is not imported correctly)
 * @todo Sheets count is incorrect
 * @todo Support rich text: cells values with styles in them are not imported correctly (text missing!)
 * @todo The class doesn't read the bold/italic/underline properties
 */
class OdsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    public $spreadsheet;

    /**
     * @return \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    protected function load(){

        if(!$this->spreadsheet){
            $filename = __DIR__ . '/../../../samples/templates/OOCalcTest.ods';

            // Create new Spreadsheet
            $this->spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

            // Load into this instance
            $reader = new Ods();
            $this->spreadsheet = $reader->loadIntoExisting($filename, $this->spreadsheet);
        }

        return $this->spreadsheet;
    }

    public function testReadValueAndComments(){

        $spreadsheet = $this->load();

        $this->assertInstanceOf('PhpOffice\PhpSpreadsheet\Spreadsheet', $spreadsheet);

        $firstSheet = $spreadsheet->getSheet(0);

        $this->assertEquals(29, $firstSheet->getHighestRow());
        $this->assertEquals('N', $firstSheet->getHighestColumn());

        // Simple cell value
        $this->assertEquals("Test String 1", $firstSheet->getCell("A1")->getValue());

        // Merged cell
        $this->assertEquals("BOX", $firstSheet->getCell("B18")->getValue());

        // Comments/Annotations
        $this->assertEquals(
            "Test for a simple colour-formatted string",
            $firstSheet->getComment("A1")->getText()->getPlainText()
        );

        // Data types
        $this->assertEquals(DataType::TYPE_STRING, $firstSheet->getCell("A1")->getDataType());
        $this->assertEquals(DataType::TYPE_NUMERIC, $firstSheet->getCell("B1")->getDataType()); // Int

        $this->assertEquals(DataType::TYPE_NUMERIC, $firstSheet->getCell("B6")->getDataType()); // Float
        $this->assertEquals(1.23, $firstSheet->getCell("B6")->getValue());
        $this->assertEquals(0, $firstSheet->getCell("G10")->getValue());

        $this->assertEquals(DataType::TYPE_NUMERIC, $firstSheet->getCell("A10")->getDataType()); // Date
        $this->assertEquals(22269.0, $firstSheet->getCell("A10")->getValue());

        $this->assertEquals(DataType::TYPE_NUMERIC, $firstSheet->getCell("A13")->getDataType()); // Time
        $this->assertEquals(25569.0625, $firstSheet->getCell("A13")->getValue());

        $this->assertEquals(DataType::TYPE_NUMERIC, $firstSheet->getCell("A15")->getDataType()); // Date + Time
        $this->assertEquals(22269.0625, $firstSheet->getCell("A15")->getValue());

        $this->assertEquals(DataType::TYPE_NUMERIC, $firstSheet->getCell("A11")->getDataType()); // Fraction

        $this->assertEquals(DataType::TYPE_BOOL, $firstSheet->getCell("D6")->getDataType());
        $this->assertTrue($firstSheet->getCell("D6")->getValue());

        $this->assertEquals(DataType::TYPE_FORMULA, $firstSheet->getCell("C6")->getDataType()); // Formula
        $this->assertEquals("=TRUE()", $firstSheet->getCell("C6")->getValue()); // Formula

        /*
         * Percentage, Currency
         */

        $filename = __DIR__ . '/../../data/Reader/Ods/numbers.ods';

        // Create new Spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // Load into this instance
        $reader = new Ods();
        $spreadsheet = $reader->loadIntoExisting($filename, $spreadsheet);
        $firstSheet = $spreadsheet->getActiveSheet();

        $this->assertEquals(DataType::TYPE_NUMERIC, $firstSheet->getCell("A1")->getDataType()); // Percentage (10%)
        $this->assertEquals(0.1, $firstSheet->getCell("A1")->getValue());

        $this->assertEquals(DataType::TYPE_NUMERIC, $firstSheet->getCell("A2")->getDataType()); // Percentage (10.00%)
        $this->assertEquals(0.1, $firstSheet->getCell("A2")->getValue());

        $this->assertEquals(DataType::TYPE_NUMERIC, $firstSheet->getCell("A4")->getDataType()); // Currency (€10.00)
        $this->assertEquals(10, $firstSheet->getCell("A4")->getValue());

        $this->assertEquals(DataType::TYPE_NUMERIC, $firstSheet->getCell("A5")->getDataType()); // Currency ($20)
        $this->assertEquals(20, $firstSheet->getCell("A5")->getValue());
    }

    public function testReadColors()
    {
        $spreadsheet = $this->load();
        $firstSheet = $spreadsheet->getSheet(0);

        // Background color

        $style = $firstSheet->getCell("K3")->getStyle();

        $this->assertEquals("none", $style->getFill()->getFillType());
        $this->assertEquals("FFFFFFFF", $style->getFill()->getStartColor()->getARGB());
        $this->assertEquals("FF000000", $style->getFill()->getEndColor()->getARGB());
    }

    /*
     * Below some test for features not implemented yet
     */

    public function testReadBoldItalicUnderline()
    {
        $this->markTestSkipped("Features not implemented yet");

        $spreadsheet = $this->load();
        $firstSheet = $spreadsheet->getSheet(0);

        // Font styles

        $style = $firstSheet->getCell("A1")->getStyle();
        $this->assertEquals("FF000000", $style->getFont()->getColor()->getARGB());
        $this->assertEquals(11, $style->getFont()->getSize());
        $this->assertEquals(Font::UNDERLINE_NONE, $style->getFont()->getUnderline());

        $style = $firstSheet->getCell("E3")->getStyle();
        $this->assertEquals(Font::UNDERLINE_SINGLE, $style->getFont()->getUnderline());

        $style = $firstSheet->getCell("E1")->getStyle();
        $this->assertTrue($style->getFont()->getBold());
        $this->assertTrue($style->getFont()->getItalic());
    }

    public function testReadRichTExt(){

        $this->markTestSkipped("Features not implemented yet");

        $spreadsheet = $this->load();
        $firstSheet = $spreadsheet->getSheet(0);

        $this->assertEquals(
            "I don't know if OOCalc supports Rich Text in the same way as Excel, " .
            "And this row should be autofit height with text wrap",
            $firstSheet->getCell("A28")->getValue()
        );
    }

    public function testReadHyperlinks(){

        $this->markTestSkipped("Features not implemented fully");

        $spreadsheet = $this->load();
        $firstSheet = $spreadsheet->getSheet(0);

        $hyperlink = $firstSheet->getCell("A29");

        $this->assertEquals(DataType::TYPE_STRING, $hyperlink->getDataType());
        $this->assertEquals("PHPExcel", $hyperlink->getValue());
        $this->assertEquals("http://www.phpexcel.net/", $hyperlink->getHyperlink()->getUrl());
    }
}
