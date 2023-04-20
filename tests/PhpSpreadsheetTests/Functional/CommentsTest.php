<?php

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CommentsTest extends AbstractFunctional
{
    public static function providerFormats(): array
    {
        return [
            ['Html'],
            ['Xlsx'],
            ['Ods'],
        ];
    }

    /**
     * Test load file with comment in sheet to load proper
     * count of comments in correct coords.
     *
     * @dataProvider providerFormats
     */
    public function testComments(string $format): void
    {
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->getCell('E10')->setValue('Comment');
        $spreadsheet->getActiveSheet()
            ->getComment('E10')
            ->getText()
            ->createText('Comment to test');

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format);

        $sheet = $reloadedSpreadsheet->getSheet(0);
        $commentsLoaded = $sheet->getComments();
        self::assertCount(1, $commentsLoaded);

        $commentCoordinate = key($commentsLoaded);
        self::assertSame('E10', $commentCoordinate);
        self::assertSame('Comment', $sheet->getCell('E10')->getValue());
        $comment = $commentsLoaded[$commentCoordinate];
        self::assertSame('Comment to test', (string) $comment);
        $commentClone = clone $comment;
        self::assertEquals($comment, $commentClone);
        self::assertNotSame($comment, $commentClone);
        if ($format === 'Xlsx') {
            self::assertEquals('feb0c24b880a8130262dadf801f85e94', $comment->getHashCode());
            self::assertEquals(Alignment::HORIZONTAL_GENERAL, $comment->getAlignment());
            $comment->setAlignment(Alignment::HORIZONTAL_RIGHT);
            self::assertEquals(Alignment::HORIZONTAL_RIGHT, $comment->getAlignment());
        }
        $spreadsheet->disconnectWorksheets();
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
