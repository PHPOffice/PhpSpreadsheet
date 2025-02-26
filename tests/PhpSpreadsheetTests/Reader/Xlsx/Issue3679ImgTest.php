<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Issue3679ImgTest extends AbstractFunctional
{
    public function testCroppedPicture(): void
    {
        $file = 'tests/data/Reader/XLSX/issue.3679.img.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($file);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $sheet = $reloadedSpreadsheet->getActiveSheet();
        $drawings = $sheet->getDrawingCollection();
        self::assertCount(1, $drawings);
        if ($drawings[0] === null) {
            self::fail('Unexpected null drawing');
        } else {
            $srcRect = $drawings[0]->getSrcRect();
            self::assertSame('448', (string) ($srcRect['r'] ?? ''));
            self::assertSame('65769', (string) ($srcRect['b'] ?? ''));
        }

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
