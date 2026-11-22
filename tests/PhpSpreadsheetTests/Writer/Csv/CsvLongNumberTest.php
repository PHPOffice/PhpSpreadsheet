<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Csv;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CsvLongNumberTest extends TestCase
{
    private static function saveToString(Spreadsheet $spreadsheet): string
    {
        $writer = new Csv($spreadsheet);
        $writer->setLineEnding("\n");
        ob_start();
        $writer->save('php://output');

        return (string) ob_get_clean();
    }

    #[DataProvider('providerTextCell')]
    public function testTextCellIsWrittenAsIs(string $text, string $dataType): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->getCell('A1')->setValueExplicit($text, $dataType);

        self::assertSame("\"$text\"\n", self::saveToString($spreadsheet));
        $spreadsheet->disconnectWorksheets();
    }

    /** @return array<string, array{string, string}> */
    public static function providerTextCell(): array
    {
        return [
            'text after long number' => ['3.14159265358979323 is pi', DataType::TYPE_STRING],
            'text after number with long whole part' => ['12345678901234.56 kg', DataType::TYPE_STRING],
            'long number as string' => ['12345678901234.56', DataType::TYPE_STRING],
            'long number as inline string' => ['3.14159265358979323', DataType::TYPE_INLINE],
            'several dots' => ['1.2345678901234567.8.9', DataType::TYPE_STRING],
            'trailing newline' => ["3.14159265358979323\n", DataType::TYPE_STRING],
        ];
    }

    public function testRichTextIsWrittenAsIs(): void
    {
        $richText = new RichText();
        $richText->createText('3.14159265358979323');
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->getCell('A1')->setValue($richText);

        self::assertSame("\"3.14159265358979323\"\n", self::saveToString($spreadsheet));
        $spreadsheet->disconnectWorksheets();
    }

    public function testFormulaWithTextResultIsWrittenAsIs(): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->getCell('A1')->setValue('=CONCAT("3.14159265358979323"," is pi")');

        self::assertSame("\"3.14159265358979323 is pi\"\n", self::saveToString($spreadsheet));
        $spreadsheet->disconnectWorksheets();
    }

    public function testNumberFormatSuffixIsKept(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(1.5);
        $sheet->getStyle('A1')->getNumberFormat()->setFormatCode('0.0000000000000000 "kg"');

        self::assertSame("\"1.5000000000000000 kg\"\n", self::saveToString($spreadsheet));
        $spreadsheet->disconnectWorksheets();
    }

    public function testNumbersAreStillLimitedTo15Digits(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(3.141592653589793);
        $sheet->getCell('B1')->setValue('=PI()');
        $sheet->getCell('C1')->setValue(-123456.123456789012);

        self::assertSame("\"3.14159265358979\",\"3.14159265358979\",\"-123456.123456789\"\n", self::saveToString($spreadsheet));
        $spreadsheet->disconnectWorksheets();
    }
}
