<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPUnit\Framework\TestCase;

class Issue1790Test extends TestCase
{
    public function testCommentStylingOutput(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $comment = $sheet->getComment('A1');

        $comment->getFillColor()->setRGB('FF5733');
        $comment->setBorderColor(new Color('0000FF'));
        $comment->setFillOpacity(0.6);

        $comment->setShapeType(203);

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx');
        $writer->save($tempFile);

        $zip = new \ZipArchive();
        $zip->open($tempFile);
        $vml = $zip->getFromName('xl/drawings/vmlDrawing1.vml');
        $zip->close();
        unlink($tempFile);

        $this->assertStringContainsString('fillcolor="#FF5733"', $vml);
        $this->assertStringContainsString('strokecolor="#0000FF"', $vml);
        $this->assertStringContainsString('opacity="0.6"', $vml);
        $this->assertStringContainsString('type="#_x0000_t203"', $vml);
    }
}
