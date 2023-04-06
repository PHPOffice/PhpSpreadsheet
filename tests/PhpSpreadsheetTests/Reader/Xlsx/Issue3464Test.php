<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class Issue3464Test extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    private static $testbook = 'tests/data/Reader/XLSX/issue.3464.xlsx';

    public function testReadFontColor(): void
    {
        $inputFileType = IOFactory::identify(self::$testbook);
        $objReader = IOFactory::createReader($inputFileType);
        $objReader->setReadEmptyCells(false);

        $spreadsheet = $objReader->load(self::$testbook);
        $sheet = $spreadsheet->getActiveSheet();
        $rickText = $sheet->getCell([1, 1])->getValue();
        self::assertInstanceOf(RichText::class, $rickText);

        $elements = $rickText->getRichTextElements();
        self::assertCount(2, $elements);

        self::assertEquals("产品介绍\n", $elements[0]->getText());
        $font = $elements[0]->getFont();
        self::assertNotNull($font);
        self::assertEquals('7f7f7f', $font->getColor()->getRGB());

        self::assertEquals('(这是一行示例数据，在导入时需要删除该行)', $elements[1]->getText());
        $font = $elements[1]->getFont();
        self::assertNotNull($font);
        self::assertEquals('ff2600', $font->getColor()->getRGB());
        $spreadsheet->disconnectWorksheets();
    }
}
