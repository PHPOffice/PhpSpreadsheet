<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Ods\Content;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Content;
use PHPUnit\Framework\TestCase;

class ContentTest extends TestCase
{
    private $samplesPath = __DIR__ . '/../../../data/Writer/Ods';

    /**
     * @var string
     */
    private $compatibilityMode;

    protected function setUp()
    {
        parent::setUp();

        $this->compatibilityMode = Functions::getCompatibilityMode();
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);
    }

    protected function tearDown()
    {
        parent::tearDown();
        Functions::setCompatibilityMode($this->compatibilityMode);
    }

    public function testWriteEmptySpreadsheet()
    {
        $content = new Content(new Ods(new Spreadsheet()));
        $xml = $content->write();

        self::assertXmlStringEqualsXmlFile($this->samplesPath . '/content-empty.xml', $xml);
    }

    public function testWriteSpreadsheet()
    {
        $workbook = new Spreadsheet();

        // Worksheet 1
        $worksheet1 = $workbook->getActiveSheet();
        $worksheet1->setCellValue('A1', 1); // Number
        $worksheet1->setCellValue('B1', 12345.6789); // Number
        $worksheet1->setCellValue('C1', '1'); // Number without cast
        $worksheet1->setCellValueExplicit('D1', '01234', DataType::TYPE_STRING); // Number casted to string
        $worksheet1->setCellValue('E1', 'Lorem ipsum'); // String

        $worksheet1->setCellValue('A2', true); // Boolean
        $worksheet1->setCellValue('B2', false); // Boolean
        $worksheet1->setCellValueExplicit(
            'C2',
            '=IF(A3, CONCATENATE(A1, " ", A2), CONCATENATE(A2, " ", A1))',
            DataType::TYPE_FORMULA
        ); // Formula

        $worksheet1->setCellValue('D2', Date::PHPToExcel(1488635026)); // Date
        $worksheet1->getStyle('D2')
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_DATE_DATETIME);

        // Styles
        $worksheet1->getStyle('A1')->getFont()->setBold(true);
        $worksheet1->getStyle('B1')->getFont()->setItalic(true);
        $worksheet1->getStyle('C1')->getFont()->setName('Courier');
        $worksheet1->getStyle('C1')->getFont()->setSize(14);
        $worksheet1->getStyle('C1')->getFont()->setColor(new Color(Color::COLOR_BLUE));

        $worksheet1->getStyle('C1')->getFill()->setFillType(Fill::FILL_SOLID);
        $worksheet1->getStyle('C1')->getFill()->setStartColor(new Color(Color::COLOR_RED));

        $worksheet1->getStyle('C1')->getFont()->setUnderline(Font::UNDERLINE_SINGLE);
        $worksheet1->getStyle('C2')->getFont()->setUnderline(Font::UNDERLINE_DOUBLE);
        $worksheet1->getStyle('D2')->getFont()->setUnderline(Font::UNDERLINE_NONE);

        // Worksheet 2
        $worksheet2 = $workbook->createSheet();
        $worksheet2->setTitle('New Worksheet');
        $worksheet2->setCellValue('A1', 2);

        // Write
        $content = new Content(new Ods($workbook));
        $xml = $content->write();

        self::assertXmlStringEqualsXmlFile($this->samplesPath . '/content-with-data.xml', $xml);
    }
}
