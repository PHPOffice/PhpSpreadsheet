<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PHPUnit\Framework\TestCase;

class MailtoTest extends TestCase
{
    public function testBadHyperlink(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setCellValue('A1', 'Mail Me!');
        $worksheet->getCell('A1')
            ->getHyperlink()
            ->setUrl('mailto:me@example.com');
        $worksheet->setCellValue('A2', 'Mail You!');
        $worksheet->getCell('A2')
            ->getHyperlink()
            ->setTooltip('go ahead')
            ->setUrl('mailto:you@example.com');
        $writer = new HtmlWriter($spreadsheet);
        $html = $writer->generateHtmlAll();
        self::assertStringContainsString('<a href="mailto:me@example.com">Mail Me!</a>', $html);
        self::assertStringContainsString('<a href="mailto:you@example.com" title="go ahead">Mail You!</a>', $html);
        $spreadsheet->disconnectWorksheets();
    }
}
