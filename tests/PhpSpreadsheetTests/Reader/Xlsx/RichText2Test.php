<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\RichText\Run;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class RichText2Test extends AbstractFunctional
{
    public function testRichTextStrikeSubSup(): void
    {
        $spreadsheetOld = new Spreadsheet();
        $sheet = $spreadsheetOld->getActiveSheet();

        $richText1 = new RichText();
        $richText1->createText('H');
        $textRun1 = $richText1->createTextRun('2');
        $textRun1->getFontOrThrow()->setSubscript(true);
        $richText1->createText('SO');
        $textRun2 = $richText1->createTextRun('4');
        $textRun2->getFontOrThrow()->setSubscript(true);
        $sheet->getCell('A1')->setValue($richText1);

        $richText2 = new RichText();
        $richText2->createText('y=x');
        $textRun2 = $richText2->createTextRun('2');
        $textRun2->getFontOrThrow()->setSuperscript(true);
        $sheet->getCell('A2')->setValue($richText2);

        $richText3 = new RichText();
        $richText3->createText('s');
        $textRun3 = $richText3->createTextRun('trik');
        $textRun3->getFontOrThrow()->setStrikethrough(true);
        $richText3->createText('e');
        $sheet->getCell('A3')->setValue($richText3);

        $sheet->getCell('A4')->setValueExplicit('#VALUE!', DataType::TYPE_ERROR);

        $spreadsheet = $this->writeAndReload($spreadsheetOld, 'Xlsx');
        $spreadsheetOld->disconnectWorksheets();
        $rsheet = $spreadsheet->getActiveSheet();

        $value = $rsheet->getCell('A1')->getValue();
        self::assertInstanceOf(RichText::class, $value);
        $elements = $value->getRichTextElements();
        self::assertCount(4, $elements);
        $element = $elements[0];
        self::assertInstanceOf(Run::class, $element);
        self::assertSame('H', $element->getText());
        $element = $elements[1];
        self::assertInstanceOf(Run::class, $element);
        self::assertSame('2', $element->getText());
        self::assertTrue(
            $element->getFontOrThrow()->getSubscript()
        );
        $element = $elements[2];
        self::assertInstanceOf(Run::class, $element);
        self::assertSame('SO', $element->getText());
        $element = $elements[3];
        self::assertInstanceOf(Run::class, $element);
        self::assertSame('4', $element->getText());
        self::assertTrue(
            $element->getFontOrThrow()->getSubscript()
        );

        $value = $rsheet->getCell('A2')->getValue();
        self::assertInstanceOf(RichText::class, $value);
        $elements = $value->getRichTextElements();
        self::assertCount(2, $elements);
        $element = $elements[0];
        self::assertInstanceOf(Run::class, $element);
        self::assertSame('y=x', $element->getText());
        $element = $elements[1];
        self::assertInstanceOf(Run::class, $element);
        self::assertSame('2', $element->getText());
        self::assertTrue(
            $element->getFontOrThrow()->getSuperscript()
        );

        $value = $rsheet->getCell('A3')->getValue();
        self::assertInstanceOf(RichText::class, $value);
        $elements = $value->getRichTextElements();
        self::assertCount(3, $elements);
        $element = $elements[0];
        self::assertInstanceOf(Run::class, $element);
        self::assertSame('s', $element->getText());
        $element = $elements[1];
        self::assertInstanceOf(Run::class, $element);
        self::assertSame('trik', $element->getText());
        self::assertTrue(
            $element->getFontOrThrow()->getStrikethrough()
        );
        $element = $elements[2];
        self::assertInstanceOf(Run::class, $element);
        self::assertSame('e', $element->getText());

        self::assertSame('#VALUE!', $rsheet->getCell('A4')->getValue());
        self::assertSame(DataType::TYPE_ERROR, $rsheet->getCell('A4')->getDataType());

        $spreadsheet->disconnectWorksheets();
    }
}
