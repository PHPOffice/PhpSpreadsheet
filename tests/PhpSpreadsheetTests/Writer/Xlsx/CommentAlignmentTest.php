<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Comment;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class CommentAlignmentTest extends AbstractFunctional
{
    public function testIssue4004(): void
    {
        $type = 'Xlsx';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A3')->setValue('A3');
        $sheet->getCell('A4')->setValue('A4');
        $sheet->getComment('A3')->getText()->createText('Comment');
        $sheet->getComment('A4')->getText()->createText('שלום');
        $sheet->getComment('A4')->setAlignment(Alignment::HORIZONTAL_RIGHT);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $type);
        $spreadsheet->disconnectWorksheets();

        self::assertCount(1, $reloadedSpreadsheet->getAllSheets());

        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        $comment1 = $rsheet->getComment('A3');
        self::assertSame('Comment', $comment1->getText()->getPlainText());
        self::assertSame('general', $comment1->getAlignment());
        $comment2 = $rsheet->getComment('A4');
        self::assertSame('שלום', $comment2->getText()->getPlainText());
        self::assertSame('right', $comment2->getAlignment());

        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testIssue4004td(): void
    {
        $type = 'Xlsx';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setRightToLeft(true);
        $sheet->getCell('A1')->setValue('ברקוד');
        $comment = $sheet->getComment('A1');
        $comment->setTextboxDirection(Comment::TEXTBOX_DIRECTION_RTL);
        $comment->setAlignment(Alignment::HORIZONTAL_RIGHT);
        $text = <<<EOF
            Report : ProductsExcel
            סטטוס הגדרות בזמן הרצת הדו"ח

            2024-06-04 21:07:04
            תאריך התחלה : 
            תאריך סיום : 
            berber@berber.co.il

            הצגת ברקוד מקוצר = 0
            הצגת חנויות אינטרנט בתוצאות = 1

            הצגת מחיר ליחידת מידה = 0
            הצגת כל רשומות המחיר לתאריך = 0
            רשומות עם מחיר בכל הרשתות = 0
            % נפיצות מינימלית = 0
            נפיצות מינימלית= 0
            % נפיצות מקסימלית = 0
            נפיצות מקסימלית= 0
            פער אחוזי = 0

            התעלמות מכלל המבצעים = 0
            התעלמות ממבצעי אשראי = 0
            התעלמות ממבצעי מועדון = 0
            התעלמות ממבצעים המותנים בסכום מעל 100 ₪. = 0
            התעלמות ממבצעים המותנים בקניה של 3 מוצרים ומעלה. = 0

            ניתוח מבצעים
            ============
            הצגת כל המבצעים = 0
            מבצעי מועדון = 0
            מבצעי אשראי = 0
            מבצעי ארנק = 0
            מחיר מוזל = 0
            X יחידות ב-Y ₪ = 0
            השני ב = 0
            X+Y מתנה = 0
            אחוז הנחה הפעלה = 0
            אחוז הנחה מספר = 0
            מוצרים חסרים - הגבלת חודשים - כמות= 0
            EOF;
        $comment->getText()->createTextRun($text);
        $comment->setWidth('300pt');
        $comment->setHeight('550pt');

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $type);
        $spreadsheet->disconnectWorksheets();

        self::assertCount(1, $reloadedSpreadsheet->getAllSheets());

        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        $comment1 = $rsheet->getComment('A1');
        self::assertSame($text, $comment1->getText()->getPlainText());
        $comment->setTextboxDirection(Comment::TEXTBOX_DIRECTION_RTL);
        self::assertSame('right', $comment1->getAlignment());
        self::assertSame('rtl', $comment1->getTextboxDirection());

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
