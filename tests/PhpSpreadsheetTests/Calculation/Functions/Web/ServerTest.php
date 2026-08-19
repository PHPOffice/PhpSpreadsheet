<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Web;

use Exception;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class ServerTest extends TestCase
{
    private static Process $httpServer;

    private const SERVER = 'localhost:8080';
    private const DIRECT = 'http://' . self::SERVER . '/direct.php';
    private const REDIRECT = 'http://' . self::SERVER . '/redirect.php';
    private const OLDVALUE = 'LOCAL_REDIRECT_SECRET2';
    private const NEWVALUE = 'LOCAL_REDIRECT_SECRET3';

    public static function setUpBeforeClass(): void
    {
        $commandLine = 'php -S ' . self::SERVER . ' -t ' . __DIR__;

        self::$httpServer = Process::fromShellCommandline($commandLine);
        self::$httpServer->start();
        while (!self::$httpServer->isRunning()) {
            usleep(1000);
        }
    }

    public static function tearDownAfterClass(): void
    {
        self::$httpServer->stop();
    }

    public function xtestServer(): void
    {
        try {
            self::assertSame(self::NEWVALUE, file_get_contents(self::DIRECT));
            self::assertSame(self::NEWVALUE, file_get_contents(self::REDIRECT));
        } catch (Exception $e) {
            $message = $e->getMessage();
            if (str_contains($message, 'Connection refused')) {
                self::markTestSkipped('Unable to use web server');
            }

            throw $e;
        }
    }

    public function xtestReadFile(): void
    {
        $reader = new XlsxReader();
        $spreadsheet = $reader->load(__DIR__ . '/redirect.xlsx');
        $spreadsheet->setDomainWhiteList(['localhost']);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame(self::OLDVALUE, $sheet->getCell('A1')->getOldCalculatedValue());
        self::assertSame(self::OLDVALUE, $sheet->getCell('A2')->getOldCalculatedValue());
        self::assertSame(self::NEWVALUE, $sheet->getCell('A1')->getCalculatedValue(), 'no redirect so recomputed');
        self::assertSame(self::OLDVALUE, $sheet->getCell('A2')->getCalculatedValue(), 'redirect so use old computed');
    }

    public function xtestNew(): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->setDomainWhiteList(['localhost']);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('G1')->setValue(self::DIRECT);
        $sheet->getCell('A1')->setValue('=WEBSERVICE(G1)');
        self::assertSame(self::NEWVALUE, $sheet->getCell('A1')->getCalculatedValue(), 'no redirect so computed');
        $sheet->getCell('G2')->setValue(self::REDIRECT);
        $sheet->getCell('A2')->setValue('=WEBSERVICE(G2)');
        self::assertNull(
            $sheet->getCell('A2')->getCalculatedValue(),
            'redirect so not computed'
        );
    }

    public function testAll(): void
    {
        $this->xtestServer();
        $this->xtestReadFile();
        $this->xtestNew();
    }
}
