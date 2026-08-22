<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Web;

use PhpOffice\PhpSpreadsheet\Calculation\Web\Service;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class WebServiceTest extends TestCase
{
    private ?Spreadsheet $spreadsheet = null;

    private const WHITELIST = [
        'www.example.com',
        'www.google.com',
        'www.invalid.com',
    ];

    protected function tearDown(): void
    {
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
    }

    #[DataProvider('providerWEBSERVICE')]
    public function testWEBSERVICE(string $expectedResult, string $url): void
    {
        if (str_starts_with($url, 'https') && getenv('SKIP_URL_IMAGE_TEST') === '1') {
            self::markTestSkipped('Skipped due to setting of environment variable');
        }
        $this->spreadsheet = new Spreadsheet();
        $this->spreadsheet->setDomainWhiteList(self::WHITELIST);
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->getCell('Z1')->setValue('http://www.example.com');
        $sheet->getCell('Z2')->setValue(2);
        if (str_starts_with($url, 'Z')) {
            $sheet->getCell('A1')->setValue("=WEBSERVICE($url)");
        } else {
            $sheet->getCell('A1')->setValue("=WEBSERVICE(\"$url\")");
        }
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertStringContainsString($expectedResult, $result);
    }

    public static function providerWEBSERVICE(): array
    {
        return require 'tests/data/Calculation/Web/WEBSERVICE.php';
    }

    public function testOldCalculated(): void
    {
        $reader = new XlsxReader();
        $this->spreadsheet = $reader->load('tests/data/Reader/XLSX/fakewebservice.xlsx');
        $this->spreadsheet->setDomainWhiteList(self::WHITELIST);
        $sheet = $this->spreadsheet->getActiveSheet();
        $a1Formula = $sheet->getCell('A1')->getValue();
        self::assertSame(
            '=WEBSERVICE("http://www.phonydomain.com")', // not in whitelist
            $a1Formula
        );
        self::assertSame(
            'phony result',
            $sheet->getCell('A1')->getCalculatedValue(),
            'result should be oldCalculatedValue'
        );
        $sheet->getCell('A2')->setValue($a1Formula);
        self::assertNull(
            $sheet->getCell('A2')->getCalculatedValue(),
            'no oldCalculatedValue to fall back on'
        );
        $sheet->getCell('A3')->setValue($a1Formula);
        $sheet->getCell('A3')->setCalculatedValue('random string');
        self::assertSame(
            'random string',
            $sheet->getCell('A3')->getCalculatedValue(),
            'oldCalculatedValue explicitly set above'
        );
        self::assertNull(
            Service::webService('http://www.example.com'),
            'no Spreadsheet so no whitelist'
        );
    }
}
