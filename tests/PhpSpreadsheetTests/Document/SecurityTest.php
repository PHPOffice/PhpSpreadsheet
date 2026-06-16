<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Document;

use PhpOffice\PhpSpreadsheet\Document\Security;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\Shared\PasswordHasher;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Protection;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;
use PHPUnit\Framework\Attributes\DataProvider;

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

    public function testHashRatherThanPassword(): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->getCell('A1')->setValue('Hello');
        $security = $spreadsheet->getSecurity();
        $password = '12345';
        $algorithm = Protection::ALGORITHM_SHA_512;
        $salt = 'KX7zweex4Ay6KVZu9JU6Gw==';
        $spinCount = 100_000;
        $hash = PasswordHasher::hashPassword($password, $algorithm, $salt, $spinCount);
        $security->setLockStructure(true)
            ->setWorkbookAlgorithmName($algorithm)
            ->setWorkbookSaltValue($salt, false)
            ->setWorkbookSpinCount($spinCount)
            ->setWorkbookPassword($password);
        self::assertSame('', $security->getWorkbookPassword());
        self::assertSame($hash, $security->getWorkbookHashValue());

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $reloadedSecurity = $reloadedSpreadsheet->getSecurity();
        self::assertTrue($reloadedSecurity->getLockStructure());
        self::assertSame('', $reloadedSecurity->getWorkbookPassword());
        self::assertSame($hash, $reloadedSecurity->getWorkbookHashValue());
        self::assertSame($algorithm, $reloadedSecurity->getWorkbookAlgorithmName());
        self::assertSame($salt, $reloadedSecurity->getWorkbookSaltValue());
        self::assertSame($spinCount, $reloadedSecurity->getWorkbookSpinCount());
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testRevisionsHashRatherThanPassword(): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->getCell('A1')->setValue('Hello');
        $security = $spreadsheet->getSecurity();
        $password = '54321';
        $algorithm = Protection::ALGORITHM_SHA_512;
        $salt = 'ddXHG3GsaI5PnaiaVnFGkw==';
        $spinCount = 100_000;
        $hash = PasswordHasher::hashPassword($password, $algorithm, $salt, $spinCount);
        $security->setLockRevision(true)
            ->setRevisionsAlgorithmName($algorithm)
            ->setRevisionsSaltValue($salt, false)
            ->setRevisionsSpinCount($spinCount)
            ->setRevisionsPassword($password);
        self::assertSame('', $security->getRevisionsPassword());
        self::assertSame($hash, $security->getRevisionsHashValue());

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $reloadedSecurity = $reloadedSpreadsheet->getSecurity();
        self::assertTrue($reloadedSecurity->getLockRevision());
        self::assertSame('', $reloadedSecurity->getRevisionsPassword());
        self::assertSame($hash, $reloadedSecurity->getRevisionsHashValue());
        self::assertSame($algorithm, $reloadedSecurity->getRevisionsAlgorithmName());
        self::assertSame($salt, $reloadedSecurity->getRevisionsSaltValue());
        self::assertSame($spinCount, $reloadedSecurity->getRevisionsSpinCount());
        $reloadedSpreadsheet->disconnectWorksheets();
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

    #[DataProvider('providerLocks')]
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

    public function testBadAlgorithm(): void
    {
        $security = new Security();
        $password = '12345';
        $algorithm = 'SHA-513';
        $salt = 'KX7zweex4Ay6KVZu9JU6Gw==';
        $spinCount = 100_000;

        try {
            $hash = PasswordHasher::hashPassword($password, $algorithm, $salt, $spinCount);
            self::fail('hashPassword should have thrown exception');
        } catch (SpreadsheetException $e) {
            self::assertStringContainsString('Unsupported password algorithm', $e->getMessage());
        }
    }
}
