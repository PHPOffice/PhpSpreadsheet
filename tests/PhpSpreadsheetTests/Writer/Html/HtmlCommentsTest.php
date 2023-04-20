<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional;

class HtmlCommentsTest extends Functional\AbstractFunctional
{
    /**
     * @var Spreadsheet
     */
    private $spreadsheet;

    public static function providerCommentRichText(): array
    {
        $valueSingle = 'I am comment.';
        $valueMulti = 'I am ' . PHP_EOL . 'multi-line' . PHP_EOL . 'comment.';

        $plainSingle = new RichText();
        $plainSingle->createText($valueSingle);

        $plainMulti = new RichText();
        $plainMulti->createText($valueMulti);

        $richSingle = new RichText();
        $font = $richSingle->createTextRun($valueSingle)->getFont();
        self::assertNotNull($font);
        $font->setBold(true);

        $richMultiSimple = new RichText();
        $font = $richMultiSimple->createTextRun($valueMulti)->getFont();
        self::assertNotNull($font);
        $font->setBold(true);

        $richMultiMixed = new RichText();
        $richMultiMixed->createText('I am' . PHP_EOL);
        $font = $richMultiMixed->createTextRun('multi-line')->getFont();
        self::assertNotNull($font);
        $font->setBold(true);
        $richMultiMixed->createText(PHP_EOL . 'comment!');

        return [
            'single line plain text' => [$plainSingle],
            'multi-line plain text' => [$plainMulti],
            'single line simple rich text' => [$richSingle],
            'multi-line simple rich text' => [$richMultiSimple],
            'multi-line mixed rich text' => [$richMultiMixed],
        ];
    }

    /**
     * @dataProvider providerCommentRichText
     *
     * @param mixed $richText
     */
    public function testComments($richText): void
    {
        $this->spreadsheet = new Spreadsheet();

        $this->spreadsheet->getActiveSheet()->getCell('A1')->setValue('Comment');

        $this->spreadsheet->getActiveSheet()
            ->getComment('A1')
            ->setText($richText);

        $reloadedSpreadsheet = $this->writeAndReload($this->spreadsheet, 'Html');

        $actual = $reloadedSpreadsheet->getActiveSheet()->getComment('A1')->getText()->getPlainText();
        self::assertSame($richText->getPlainText(), $actual);
    }
}
