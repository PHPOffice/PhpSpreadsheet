<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Shared\OLE;
use PhpOffice\PhpSpreadsheet\Shared\Xlsx\AgileEncryption;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class AgileEncryptionTest extends TestCase
{
    public function testWriterUsesConfiguredEncryptionProfile(): void
    {
        $filename = File::temporaryFilename();

        try {
            $spreadsheet = new Spreadsheet();
            $spreadsheet->getActiveSheet()->setCellValue('A1', 'configured profile');
            (new Xlsx($spreadsheet))
                ->setEncryptionPassword('writer-password')
                ->setEncryptionProfile(128, 'SHA1', 10)
                ->save($filename);

            // CFB child entries are red-black trees; their left/right links
            // must preserve name order for readers which do name lookup.
            self::assertSame(1, self::directoryLinks($filename, 0)['child']);
            self::assertSame(['color' => 1, 'left' => 2, 'right' => 10, 'child' => 0xFFFFFFFF], self::directoryLinks($filename, 1));
            self::assertSame(['color' => 0, 'left' => 0xFFFFFFFF, 'right' => 0xFFFFFFFF, 'child' => 4], self::directoryLinks($filename, 2));
            self::assertSame(['color' => 0, 'left' => 0xFFFFFFFF, 'right' => 0xFFFFFFFF, 'child' => 0xFFFFFFFF], self::directoryLinks($filename, 10));
            self::assertSame(['color' => 1, 'left' => 5, 'right' => 3, 'child' => 0xFFFFFFFF], self::directoryLinks($filename, 4));
            self::assertSame(['color' => 0, 'left' => 0xFFFFFFFF, 'right' => 0xFFFFFFFF, 'child' => 0xFFFFFFFF], self::directoryLinks($filename, 3));
            self::assertSame(['color' => 0, 'left' => 0xFFFFFFFF, 'right' => 7, 'child' => 6], self::directoryLinks($filename, 5));

            $ole = new OLE();
            $ole->read($filename);
            $encryptionInfo = $ole->getDataByName('EncryptionInfo');
            $info = AgileEncryption::parse($encryptionInfo);
            self::assertSame(128, $info['keyBits']);
            self::assertSame('SHA1', $info['hashAlgorithm']);
            self::assertSame(10, $info['spinCount']);
            self::assertStringContainsString('hashAlgorithm="SHA1"', $encryptionInfo);
            self::assertDataSpacesStreams($ole);

            $loaded = (new XlsxReader())->setEncryptionPassword('writer-password')->load($filename);
            self::assertSame('configured profile', $loaded->getActiveSheet()->getCell('A1')->getValue());
            $loaded->disconnectWorksheets();
            $spreadsheet->disconnectWorksheets();
        } finally {
            if (file_exists($filename)) {
                unlink($filename);
            }
        }
    }

    /** @return array{color: int, left: int, right: int, child: int} */
    private static function directoryLinks(string $filename, int $entryId): array
    {
        $container = file_get_contents($filename);
        if ($container === false) {
            throw new RuntimeException('Could not read CFB container.');
        }
        $directorySector = unpack('Vsector', substr($container, 48, 4));
        if ($directorySector === false) {
            throw new RuntimeException('Could not read CFB directory location.');
        }
        $sector = $directorySector['sector'] ?? null;
        if (!is_int($sector)) {
            throw new RuntimeException('Invalid CFB directory location.');
        }
        $offset = ($sector + 1) * 512 + $entryId * 128;
        $links = unpack('Vleft/Vright/Vchild', substr($container, $offset + 68, 12));
        if ($links === false) {
            throw new RuntimeException('Could not read CFB directory entry.');
        }
        $left = $links['left'] ?? null;
        $right = $links['right'] ?? null;
        $child = $links['child'] ?? null;
        if (!is_int($left) || !is_int($right) || !is_int($child)) {
            throw new RuntimeException('Invalid CFB directory entry.');
        }

        return ['color' => ord($container[$offset + 67]), 'left' => $left, 'right' => $right, 'child' => $child];
    }

    private static function assertDataSpacesStreams(OLE $ole): void
    {
        $version = $ole->getDataByName('Version');
        self::assertSame(pack('V', 60), substr($version, 0, 4));
        self::assertSame(self::utf16le('Microsoft.Container.DataSpaces'), substr($version, 4, 60));
        self::assertSame(pack('V3', 1, 1, 1), substr($version, 64));

        $dataSpaceMap = $ole->getDataByName('DataSpaceMap');
        self::assertStringContainsString(self::utf16le('EncryptedPackage'), $dataSpaceMap);
        self::assertStringContainsString(self::utf16le('StrongEncryptionDataSpace'), $dataSpaceMap);

        $dataSpace = $ole->getDataByName('StrongEncryptionDataSpace');
        self::assertSame(pack('V3', 8, 1, 50), substr($dataSpace, 0, 12));
        self::assertStringContainsString(self::utf16le('StrongEncryptionTransform'), $dataSpace);

        $transform = $ole->getDataByName("\x06Primary");
        self::assertSame(pack('V3', 88, 1, 76) . self::utf16le('{FF9A3F03-56EF-4613-BDD5-5A41C1D07246}'), substr($transform, 0, 88));
        self::assertStringContainsString(self::utf16le('Microsoft.Container.EncryptionTransform'), $transform);
    }

    private static function utf16le(string $value): string
    {
        return mb_convert_encoding($value, 'UTF-16LE', 'UTF-8');
    }

    public function testContainerSupportsFatAndDifatSectors(): void
    {
        $encryptedPackageFilename = File::temporaryFilename();
        $containerFilename = File::temporaryFilename();

        try {
            // This needs more than 109 FAT sectors, so the CFB DIFAT chain is required.
            $plainPackage = str_repeat('x', 7 * 1024 * 1024);
            $package = AgileEncryption::encrypt($plainPackage, 'writer-password');
            file_put_contents($encryptedPackageFilename, $package['encryptedPackage']);
            $container = fopen($containerFilename, 'w+b');
            self::assertNotFalse($container);
            AgileEncryption::writeContainerFromFile($container, $package['encryptionInfo'], $encryptedPackageFilename);
            fclose($container);

            $header = (string) file_get_contents($containerFilename, false, null, 0, 512);
            $fatSectorCount = unpack('V', substr($header, 44, 4));
            self::assertNotFalse($fatSectorCount);
            self::assertGreaterThanOrEqual(2, $fatSectorCount[1]);
            $difatSectorCount = unpack('V', substr($header, 72, 4));
            self::assertNotFalse($difatSectorCount);
            self::assertGreaterThanOrEqual(1, $difatSectorCount[1]);

            $ole = new OLE();
            $ole->read($containerFilename);
            self::assertSame($package['encryptionInfo'], $ole->getDataByName('EncryptionInfo'));
            self::assertSame($package['encryptedPackage'], $ole->getDataByName('EncryptedPackage'));
            self::assertSame($plainPackage, AgileEncryption::decrypt(AgileEncryption::parse($package['encryptionInfo']), $ole->getDataByName('EncryptedPackage'), 'writer-password'));
        } finally {
            if (file_exists($encryptedPackageFilename)) {
                unlink($encryptedPackageFilename);
            }
            if (file_exists($containerFilename)) {
                unlink($containerFilename);
            }
        }
    }

    public function testContainerRejectsLargeEncryptionInfoMiniStream(): void
    {
        $encryptedPackageFilename = File::temporaryFilename();
        $containerFilename = File::temporaryFilename();

        try {
            $package = AgileEncryption::encrypt(str_repeat('x', 4096), 'writer-password');
            file_put_contents($encryptedPackageFilename, $package['encryptedPackage']);
            $container = fopen($containerFilename, 'w+b');
            self::assertNotFalse($container);

            $this->expectException(ReaderException::class);
            $this->expectExceptionMessage('Malformed XLSX encryption information');
            AgileEncryption::writeContainerFromFile($container, str_repeat('x', 4096), $encryptedPackageFilename);
        } finally {
            if (isset($container) && is_resource($container)) {
                fclose($container);
            }
            if (file_exists($encryptedPackageFilename)) {
                unlink($encryptedPackageFilename);
            }
            if (file_exists($containerFilename)) {
                unlink($containerFilename);
            }
        }
    }

    public function testContainerRejectsSmallEncryptedPackage(): void
    {
        $encryptedPackageFilename = File::temporaryFilename();
        $containerFilename = File::temporaryFilename();

        try {
            $package = AgileEncryption::encrypt('package', 'writer-password');
            file_put_contents($encryptedPackageFilename, $package['encryptedPackage']);
            $container = fopen($containerFilename, 'w+b');
            self::assertNotFalse($container);

            $this->expectException(ReaderException::class);
            $this->expectExceptionMessage('Malformed encrypted XLSX package');
            AgileEncryption::writeContainerFromFile($container, $package['encryptionInfo'], $encryptedPackageFilename);
        } finally {
            if (isset($container) && is_resource($container)) {
                fclose($container);
            }
            if (file_exists($encryptedPackageFilename)) {
                unlink($encryptedPackageFilename);
            }
            if (file_exists($containerFilename)) {
                unlink($containerFilename);
            }
        }
    }

    public function testEncryptFileRequiresPassword(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('XLSX encryption password required');
        AgileEncryption::encryptFile('unused.xlsx', '');
    }

    public function testEncryptFileRejectsUnsupportedProfile(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Unsupported XLSX encryption profile');
        AgileEncryption::encryptFile('unused.xlsx', 'writer-password', 64);
    }

    public function testEncryptFileRejectsMissingInput(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Could not determine XLSX package size');
        AgileEncryption::encryptFile('does-not-exist.xlsx', 'writer-password');
    }

    public function testWriterCleansEncryptedTemporaryPackageWhenOutputCannotOpen(): void
    {
        $temporaryFiles = self::spreadsheetTemporaryFiles();
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->setCellValue('A1', 'temporary package cleanup');

        try {
            $this->expectException(WriterException::class);
            $this->expectExceptionMessage('Could not open file "" for writing');
            (new Xlsx($spreadsheet))->setEncryptionPassword('writer-password')->save('');
        } finally {
            self::assertSame($temporaryFiles, self::spreadsheetTemporaryFiles());
            $spreadsheet->disconnectWorksheets();
        }
    }

    /** @return string[] */
    private static function spreadsheetTemporaryFiles(): array
    {
        $temporaryFiles = glob(File::sysGetTempDir() . '/phpspreadsheet*') ?: [];
        sort($temporaryFiles);

        return $temporaryFiles;
    }

    public function testWriteAgileEncryptedWorkbook(): void
    {
        $filename = File::temporaryFilename();

        try {
            $spreadsheet = new Spreadsheet();
            $spreadsheet->getActiveSheet()->setCellValue('A1', 'writer encryption fixture');
            $spreadsheet->getActiveSheet()->setCellValue('A2', 21);
            $spreadsheet->getActiveSheet()->setCellValue('B2', '=A2*2');
            $spreadsheet->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
            $spreadsheet->getProperties()->setTitle('Encrypted writer fixture');
            $drawing = new Drawing();
            $drawing->setPath('tests/data/Writer/XLSX/blue_square.png');
            $drawing->setCoordinates('D1');
            $drawing->setWorksheet($spreadsheet->getActiveSheet());
            (new Xlsx($spreadsheet))->setEncryptionPassword('writer-password')->save($filename);

            self::assertSame('d0cf11e0a1b11ae1', bin2hex((string) file_get_contents($filename, false, null, 0, 8)));
            $ole = new OLE();
            $ole->read($filename);
            $encryptionInfo = $ole->getDataByName('EncryptionInfo');
            $encryptedPackage = $ole->getDataByName('EncryptedPackage');
            self::assertDataSpacesStreams($ole);
            self::assertSame('0400040040000000', bin2hex(substr($encryptionInfo, 0, 8)));
            self::assertGreaterThan(8, strlen($encryptedPackage));
            self::assertSame(1, substr_count($encryptionInfo, 'spinCount="100000"'));
            self::assertStringStartsWith("PK\x03\x04", AgileEncryption::decrypt(AgileEncryption::parse($encryptionInfo), $encryptedPackage, 'writer-password'));

            $loaded = (new XlsxReader())->setEncryptionPassword('writer-password')->load($filename);
            self::assertSame('writer encryption fixture', $loaded->getActiveSheet()->getCell('A1')->getValue());
            self::assertSame('=A2*2', $loaded->getActiveSheet()->getCell('B2')->getValue());
            self::assertTrue($loaded->getActiveSheet()->getStyle('A1')->getFont()->getBold());
            self::assertSame('Encrypted writer fixture', $loaded->getProperties()->getTitle());
            self::assertCount(1, $loaded->getActiveSheet()->getDrawingCollection());
            $loaded->disconnectWorksheets();
            $spreadsheet->disconnectWorksheets();
        } finally {
            if (file_exists($filename)) {
                unlink($filename);
            }
        }
    }
}
