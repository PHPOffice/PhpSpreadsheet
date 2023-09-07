<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Document;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class SecurityTest extends AbstractFunctional
{
    public function testSecurity(): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->getCell('A1')->setValue('Hello');
        $security = $spreadsheet->getSecurity();
        $security->setLockRevision(true);
        $revisionsPassword = 'revpasswd';
        $security->setRevisionsPassword($revisionsPassword);
        $hashedRevisionsPassword = $security->getRevisionsPassword();
        self::assertNotEquals($revisionsPassword, $hashedRevisionsPassword);
        $security->setLockWindows(true);
        $security->setLockStructure(true);
        $workbookPassword = 'wbpasswd';
        $security->setWorkbookPassword($workbookPassword);
        $hashedWorkbookPassword = $security->getWorkbookPassword();
        self::assertNotEquals($workbookPassword, $hashedWorkbookPassword);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $reloadedSecurity = $reloadedSpreadsheet->getSecurity();
        self::assertTrue($reloadedSecurity->getLockRevision());
        self::assertTrue($reloadedSecurity->getLockWindows());
        self::assertTrue($reloadedSecurity->getLockStructure());
        self::assertSame($hashedWorkbookPassword, $reloadedSecurity->getWorkbookPassword());
        self::assertSame($hashedRevisionsPassword, $reloadedSecurity->getRevisionsPassword());

        $reloadedSecurity->setRevisionsPassword($hashedWorkbookPassword, true);
        self::assertSame($hashedWorkbookPassword, $reloadedSecurity->getRevisionsPassword());
        $reloadedSecurity->setWorkbookPassword($hashedRevisionsPassword, true);
        self::assertSame($hashedRevisionsPassword, $reloadedSecurity->getWorkbookPassword());
    }

    public static function providerLocks(): array
    {
        return [
            [false, false, false],
            [false, false, true],
            [false, true, false],
            [false, true, true],
            [true, false, false],
            [true, false, true],
            [true, true, false],
            [true, true, true],
        ];
    }

    /**
     * @dataProvider providerLocks
     */
    public function testLocks(bool $revision, bool $windows, bool $structure): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->getCell('A1')->setValue('Hello');
        $security = $spreadsheet->getSecurity();
        $security->setLockRevision($revision);
        $security->setLockWindows($windows);
        $security->setLockStructure($structure);
        $enabled = $security->isSecurityEnabled();
        self::assertSame($enabled, $revision || $windows || $structure);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $reloadedSecurity = $reloadedSpreadsheet->getSecurity();
        self::assertSame($revision, $reloadedSecurity->getLockRevision());
        self::assertSame($windows, $reloadedSecurity->getLockWindows());
        self::assertSame($structure, $reloadedSecurity->getLockStructure());
    }
}
