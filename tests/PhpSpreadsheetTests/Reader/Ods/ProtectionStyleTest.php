<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ProtectionStyleTest extends AbstractFunctional
{
    public function testReadProtectionStyles(): void
    {
        $spreadsheetOld = new Spreadsheet();
        $sheet = $spreadsheetOld->getActiveSheet();
        $sheet->getCell('A1')->setValue('no-no');
        $sheet->getCell('A2')->setValue('no-yes');
        $sheet->getCell('A3')->setValue('yes-no');
        $sheet->getCell('A4')->setValue('yes-yes');
        $sheet->getCell('A5')->setValue('default');

        $sheet->getStyle('A1')->getProtection()
            ->setLocked(Protection::PROTECTION_UNPROTECTED)
            ->setHidden(Protection::PROTECTION_UNPROTECTED);
        $sheet->getStyle('A2')->getProtection()
            ->setLocked(Protection::PROTECTION_UNPROTECTED)
            ->setHidden(Protection::PROTECTION_PROTECTED);
        $sheet->getStyle('A3')->getProtection()
            ->setLocked(Protection::PROTECTION_PROTECTED)
            ->setHidden(Protection::PROTECTION_UNPROTECTED);
        $sheet->getStyle('A4')->getProtection()
            ->setLocked(Protection::PROTECTION_PROTECTED)
            ->setHidden(Protection::PROTECTION_PROTECTED);

        $spreadsheet = $this->writeAndReload($spreadsheetOld, 'Ods');
        $spreadsheetOld->disconnectWorksheets();

        $newSheet = $spreadsheet->getActiveSheet();
        self::assertSame(Protection::PROTECTION_UNPROTECTED, $newSheet->getStyle('A1')->getProtection()->getLocked());
        self::assertSame(Protection::PROTECTION_UNPROTECTED, $newSheet->getStyle('A1')->getProtection()->getHidden());
        self::assertSame(Protection::PROTECTION_UNPROTECTED, $newSheet->getStyle('A2')->getProtection()->getLocked());
        self::assertSame(Protection::PROTECTION_PROTECTED, $newSheet->getStyle('A2')->getProtection()->getHidden());
        self::assertSame(Protection::PROTECTION_PROTECTED, $newSheet->getStyle('A3')->getProtection()->getLocked());
        self::assertSame(Protection::PROTECTION_UNPROTECTED, $newSheet->getStyle('A3')->getProtection()->getHidden());
        self::assertSame(Protection::PROTECTION_PROTECTED, $newSheet->getStyle('A4')->getProtection()->getLocked());
        self::assertSame(Protection::PROTECTION_PROTECTED, $newSheet->getStyle('A4')->getProtection()->getHidden());
        self::assertSame(Protection::PROTECTION_INHERIT, $newSheet->getStyle('A5')->getProtection()->getLocked());
        self::assertSame(Protection::PROTECTION_INHERIT, $newSheet->getStyle('A5')->getProtection()->getHidden());

        $spreadsheet->disconnectWorksheets();
    }
}
