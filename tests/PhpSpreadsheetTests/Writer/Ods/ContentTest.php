<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Ods;

use DOMDocument;
use DOMXPath;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Content;
use PHPUnit\Framework\TestCase;

class ContentTest extends TestCase
{
    private string $samplesPath = 'tests/data/Writer/Ods';

    private string $compatibilityMode;

    protected function setUp(): void
    {
        parent::setUp();

        $this->compatibilityMode = Functions::getCompatibilityMode();
        Functions::setCompatibilityMode(
            Functions::COMPATIBILITY_OPENOFFICE
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Functions::setCompatibilityMode($this->compatibilityMode);
    }

    public function testWriteEmptySpreadsheet(): void
    {
        $content = new Content(new Ods(new Spreadsheet()));
        $xml = $content->write();

        self::assertXmlStringEqualsXmlFile($this->samplesPath . '/content-empty.xml', $xml);
    }

    public function testWriteSpreadsheet(): void
    {
        $workbook = new Spreadsheet();

        // Worksheet 1
        $worksheet1 = $workbook->getActiveSheet();
        $worksheet1->setCellValue('A1', 1); // Number
        $worksheet1->setCellValue('B1', 12345.6789); // Number
        $b1SimpleCast = '12345.6789';
        $b1AccurateCast = StringHelper::convertToString(12345.6789);
        $worksheet1->setCellValue('C1', '1'); // Number without cast
        $worksheet1->setCellValueExplicit('D1', '01234', DataType::TYPE_STRING); // Number casted to string
        $worksheet1->setCellValue('E1', 'Lorem ipsum'); // String

        $worksheet1->setCellValue('A2', true); // Boolean
        $worksheet1->setCellValue('B2', false); // Boolean

        $worksheet1->setCellValueExplicit(
            'C2',
            '=IF(A3, CONCAT(A1, " ", A2), CONCAT(A2, " ", A1))',
            DataType::TYPE_FORMULA
        ); // Formula

        $worksheet1->setCellValue('D2', Date::PHPToExcel(1488635026)); // Date
        $worksheet1->getStyle('D2')
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_DATE_DATETIME);
        /** @var float */
        $d2SimpleCast = $worksheet1->getCell('D2')->getValue();
        $d2SimpleCast = (string) $d2SimpleCast;
        $d2AccurateCast = $worksheet1
            ->getCell('D2')->getValueString();

        $worksheet1->setCellValueExplicit('F1', null, DataType::TYPE_ERROR);
        $worksheet1->setCellValueExplicit('G1', 'Lorem ipsum', DataType::TYPE_INLINE);

        // Styles
        $worksheet1->getStyle('A1')->getFont()->setBold(true);
        $worksheet1->getStyle('B1')->getFont()->setItalic(true);
        $worksheet1->getStyle('C1')->getFont()->setName('Courier');
        $worksheet1->getStyle('C1')->getFont()->setSize(14);
        $worksheet1->getStyle('C1')->getFont()->setColor(new Color(Color::COLOR_BLUE));

        $worksheet1->getStyle('C1')
            ->getFill()->setFillType(Fill::FILL_SOLID);
        $worksheet1->getStyle('C1')
            ->getFill()->setStartColor(new Color(Color::COLOR_RED));

        $worksheet1->getStyle('C1')->getFont()
            ->setUnderline(Font::UNDERLINE_SINGLE);
        $worksheet1->getStyle('C2')->getFont()
            ->setUnderline(Font::UNDERLINE_DOUBLE);
        $worksheet1->getStyle('D2')->getFont()
            ->setUnderline(Font::UNDERLINE_NONE);

        // Worksheet 2
        $worksheet2 = $workbook->createSheet();
        $worksheet2->setTitle('New Worksheet');
        $worksheet2->setCellValue('A1', 2);

        // Write
        $content = new Content(new Ods($workbook));
        $xml = $content->write();

        $xmlFile = $this->samplesPath . '/content-with-data.xml';
        $xmlContents = (string) file_get_contents($xmlFile);
        $xmlContents = str_replace($b1SimpleCast, $b1AccurateCast, $xmlContents);
        $xmlContents = str_replace($d2SimpleCast, $d2AccurateCast, $xmlContents);
        self::assertXmlStringEqualsXmlString($xmlContents, $xml);
        $workbook->disconnectWorksheets();
    }

    public function testWriteWithHiddenWorksheet(): void
    {
        $workbook = new Spreadsheet();

        // Worksheet 1
        $worksheet1 = $workbook->getActiveSheet();
        $worksheet1->setCellValue('A1', 1);

        // Worksheet 2
        $worksheet2 = $workbook->createSheet();
        $worksheet2->setTitle('New Worksheet');
        $worksheet2->setCellValue('A1', 2);

        $worksheet2->setSheetState(Worksheet::SHEETSTATE_HIDDEN);

        // Write
        $content = new Content(new Ods($workbook));
        $xml = $content->write();

        self::assertXmlStringEqualsXmlFile($this->samplesPath . '/content-hidden-worksheet.xml', $xml);
        $workbook->disconnectWorksheets();
    }

    public function testWriteBorderStyle(): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()
            ->getStyle('A1:B2')->applyFromArray([
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THICK,
                        'color' => ['argb' => 'AA22DD00'],
                    ],
                ],
            ]);

        $content = new Content(new Ods($spreadsheet));
        $xml = $content->write();

        $xmlDoc = new DOMDocument();
        $xmlDoc->loadXML($xml);
        $xmlPath = new DOMXPath($xmlDoc);

        foreach (['top', 'bottom'] as $keyRow => $row) {
            foreach (['left', 'right'] as $keyCell => $cell) {
                $styles = ['top' => '', 'bottom' => '', 'left' => '', 'right' => ''];
                $styles[$row] = '2.5pt solid #22DD00';
                $styles[$cell] = '2.5pt solid #22DD00';

                $query = 'string(//office:document-content/office:body/office:spreadsheet/table:table/table:table-row[position()=' . ($keyRow + 1) . ']/table:table-cell[position()=' . ($keyCell + 1) . ']/@table:style-name)';
                $idStyle = StringHelper::convertToString($xmlPath->evaluate($query));

                foreach ($styles as $direction => $value) {
                    $query = 'string(//office:document-content/office:automatic-styles/style:style[@style:name="' . $idStyle . '"]/style:table-cell-properties/@fo:border-' . $direction . ')';
                    $style = $xmlPath->evaluate($query);
                    self::assertEquals($style, $value);
                }
            }
        }
        $spreadsheet->disconnectWorksheets();
    }
}
