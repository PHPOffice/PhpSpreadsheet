<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Ods\Content;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Content;
use PhpOffice\PhpSpreadsheetTests\Worksheet\WorksheetColumnTest;

class ContentTest extends \PHPUnit_Framework_TestCase
{
    public $samplesPath = __DIR__ . '/../../../data/Writer/Ods';

    public function testWriteEmptySpreadsheet()
    {
        $content = new Content();
        $content->setParentWriter(new Ods(new Spreadsheet()));

        $xml = $content->write();

        $this->assertXmlStringEqualsXmlFile($this->samplesPath . "/content-empty.xml", $xml);
    }

    public function testWriteSpreadsheet()
    {
        $workbook = new Spreadsheet();

        // Worksheet 1
        $worksheet1 = $workbook->getActiveSheet();
        $worksheet1->setCellValue('A1', 1); // Number
        $worksheet1->setCellValue('B1', 12345.6789); // Number
        $worksheet1->setCellValue('C1', "1"); // Number without cast
        $worksheet1->setCellValueExplicit('D1', "01234", DataType::TYPE_STRING); // Number casted to string
        $worksheet1->setCellValue('E1', "Lorem ipsum"); // String

        $worksheet1->setCellValue('A2', true); // Boolean
        $worksheet1->setCellValue('B2', false); // Boolean
        $worksheet1->setCellValue('C2', '=IF(A3, CONCATENATE(A1, " ", A2), CONCATENATE(A2, " ", A1))'); // Formula

        $worksheet1->setCellValue('D2', Date::PHPToExcel(1488635026)); // Date
        $worksheet1->getStyle('D2')
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_DATE_DATETIME);

        // Worksheet 2
        $worksheet2 = $workbook->createSheet();
        $worksheet2->setTitle('New Worksheet');
        $worksheet2->setCellValue('A1', 2);

        // Write
        $content = new Content();
        $content->setParentWriter(new Ods($workbook));

        $xml = $content->write();

        $this->assertXmlStringEqualsXmlFile($this->samplesPath . "/content-with-data.xml", $xml);
    }
}
