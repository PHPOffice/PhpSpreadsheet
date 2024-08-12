<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PhpOffice\PhpSpreadsheetTests\Functional;

class HtmlCommentsTest extends Functional\AbstractFunctional
{
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

        $richMultiMixed = new RichText();
        $richMultiMixed->createText('I am' . PHP_EOL);
        $font = $richMultiMixed->createTextRun('multi-line')->getFont();
        self::assertNotNull($font);
        $font->setBold(true);
        $richMultiMixed->createText(PHP_EOL . 'comment!');

        $scriptTextSingle = 'I am <script>alert("Hello")</script>';
        $scriptSingle = new RichText();
        $scriptSingle->createText($scriptTextSingle);

        return [
            'single line plain text' => [$plainSingle, '<td class="column0 style0 s"><a class="comment-indicator"></a><div class="comment">'
                . 'I am comment.</div>' . PHP_EOL
                . 'Comment</td>'],
            'multi-line plain text' => [$plainMulti, '<td class="column0 style0 s"><a class="comment-indicator"></a><div class="comment">'
                . 'I am <br />' . PHP_EOL
                . 'multi-line<br />' . PHP_EOL
                . 'comment.</div>' . PHP_EOL
                . 'Comment</td>'],
            'single line simple rich text' => [$richSingle, '<td class="column0 style0 s"><a class="comment-indicator"></a><div class="comment">'
                . "<span style=\"font-weight:bold; color:#000000; font-family:'Calibri'; font-size:11pt\">I am comment.</span></div>" . PHP_EOL
                . 'Comment</td>'],
            'multi-line simple rich text' => [$richMultiSimple, '<td class="column0 style0 s"><a class="comment-indicator"></a><div class="comment">'
                . "<span style=\"font-weight:bold; color:#000000; font-family:'Calibri'; font-size:11pt\">I am <br />" . PHP_EOL
                . 'multi-line<br />' . PHP_EOL
                . 'comment.</span></div>' . PHP_EOL
                . 'Comment</td>'],
            'multi-line mixed rich text' => [$richMultiMixed, '<td class="column0 style0 s"><a class="comment-indicator"></a><div class="comment">I am<br />' . PHP_EOL
                . "<span style=\"font-weight:bold; color:#000000; font-family:'Calibri'; font-size:11pt\">multi-line</span><br />" . PHP_EOL
                . 'comment!</div>' . PHP_EOL
                . 'Comment</td>'],
            'script single' => [$scriptSingle, '<td class="column0 style0 s"><a class="comment-indicator"></a><div class="comment">'
                . 'I am &lt;script&gt;alert(&quot;Hello&quot;)&lt;/script&gt;</div>' . PHP_EOL
                . 'Comment</td>'],
        ];
    }

    /**
     * @dataProvider providerCommentRichText
     */
    public function testComments(RichText $richText, string $expected): void
    {
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->getCell('A1')->setValue('Comment');

        $spreadsheet->getActiveSheet()
            ->getComment('A1')
            ->setText($richText);
        $writer = new Html($spreadsheet);
        $output = $writer->generateHtmlAll();
        self::assertStringContainsString($expected, $output);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Html');
        $spreadsheet->disconnectWorksheets();

        $actual = $reloadedSpreadsheet->getActiveSheet()->getComment('A1')->getText()->getPlainText();
        self::assertSame($richText->getPlainText(), $actual);
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
