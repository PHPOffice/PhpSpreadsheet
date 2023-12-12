<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Shared\PasswordHasher;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class PasswordReloadTest extends AbstractFunctional
{
    /**
     * @dataProvider providerPasswords
     */
    public function testPasswordReload(string $format, string $algorithm, bool $supported = true): void
    {
        $password = 'hello';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(1);
        $protection = $sheet->getProtection();
        $protection->setAlgorithm($algorithm);
        $protection->setPassword($password);
        $protection->setSheet(true);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format);
        $resheet = $reloadedSpreadsheet->getActiveSheet();
        $reprot = $resheet->getProtection();
        $repassword = $reprot->getPassword();
        $hash = '';
        if ($supported) {
            $readAlgorithm = $reprot->getAlgorithm();
            self::assertSame($algorithm, $readAlgorithm);
            $salt = $reprot->getSalt();
            $spin = $reprot->getSpinCount();
            $hash = PasswordHasher::hashPassword($password, $readAlgorithm, $salt, $spin);
        }
        self::assertSame($repassword, $hash);
        $spreadsheet->disconnectWorksheets();
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public static function providerPasswords(): array
    {
        return [
            'Xls basic algorithm' => ['Xls', ''],
            'Xls cannot use SHA512' => ['Xls', 'SHA-512', false],
            'Xlsx basic algorithm' => ['Xlsx', ''],
            'Xlsx can use SHA512' => ['Xlsx', 'SHA-512'],
        ];
    }
}
