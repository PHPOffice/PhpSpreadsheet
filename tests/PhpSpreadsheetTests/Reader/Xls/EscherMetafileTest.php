<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls\Escher;
use PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer\BstoreContainer;
use PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer\BstoreContainer\BSE;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class EscherMetafileTest extends TestCase
{
    private const DIRECTORY = 'tests/data/Worksheet/Metafile/';

    /** @return array<string, array{string, int, int, int, bool}> */
    public static function providerBlips(): array
    {
        return [
            'emf compressed' => ['metafile.emf', BSE::BLIPTYPE_EMF, 0xF01A, 0x3D4, true],
            'emf uncompressed, 2 uids' => ['metafile.emf', BSE::BLIPTYPE_EMF, 0xF01A, 0x3D5, false],
            'emf+ compressed' => ['metafile_emfplus.emf', BSE::BLIPTYPE_EMF, 0xF01A, 0x3D4, true],
            'wmf compressed' => ['metafile.wmf', BSE::BLIPTYPE_WMF, 0xF01B, 0x216, true],
            'wmf uncompressed, 2 uids' => ['metafile.wmf', BSE::BLIPTYPE_WMF, 0xF01B, 0x217, false],
        ];
    }

    /**
     * Build an OfficeArtFBSE record holding an OfficeArtBlipEMF or OfficeArtBlipWMF record ([MS-ODRAW]).
     */
    private static function buildBse(string $metafile, int $blipType, int $recType, int $recInstance, bool $compressed): string
    {
        $data = $compressed ? (string) gzcompress($metafile) : $metafile;
        $uids = str_repeat("\x00", in_array($recInstance, [0x3D5, 0x217], true) ? 32 : 16);
        $metafileHeader = pack('V', strlen($metafile)) // cbSize
            . pack('V4', 0, 0, 100, 100) // rcBounds
            . pack('V2', 952500, 952500) // ptSize
            . pack('V', strlen($data)) // cbSave
            . ($compressed ? "\x00" : "\xFE") // compression
            . "\xFE"; // filter
        $blipData = $uids . $metafileHeader . $data;
        $blip = pack('vvV', $recInstance << 4, $recType, strlen($blipData)) . $blipData;

        $bseData = chr($blipType) . chr($blipType) . str_repeat("\x00", 16) // btWin32, btMacOS, rgbUid
            . pack('v', 0xFF) . pack('V', strlen($blip)) . pack('V', 1) . pack('V', 0) // tag, size, cRef, foDelay
            . "\x00\x00\x00\x00" // unused1, cbName, unused2, unused3
            . $blip;

        return pack('vvV', ($blipType << 4) | 0x2, 0xF007, strlen($bseData)) . $bseData;
    }

    #[DataProvider('providerBlips')]
    public function testReadMetafileBlip(string $file, int $blipType, int $recType, int $recInstance, bool $compressed): void
    {
        $metafile = (string) file_get_contents(self::DIRECTORY . $file);
        $bstoreContainer = new BstoreContainer();
        $reader = new Escher($bstoreContainer);
        $reader->load(self::buildBse($metafile, $blipType, $recType, $recInstance, $compressed));

        $collection = $bstoreContainer->getBSECollection();
        self::assertCount(1, $collection);
        self::assertSame($blipType, $collection[0]->getBlipType());
        $blip = $collection[0]->getBlip();
        self::assertNotNull($blip);
        self::assertSame($metafile, $blip->getData());
    }

    public function testReadCorruptedCompressedBlip(): void
    {
        $bse = self::buildBse('metafile', BSE::BLIPTYPE_EMF, 0xF01A, 0x3D4, false);
        // Flag the uncompressed data as compressed
        $bse = substr_replace($bse, "\x00", 8 + 36 + 8 + 16 + 32, 1);
        $bstoreContainer = new BstoreContainer();
        $reader = new Escher($bstoreContainer);
        $reader->load($bse);

        $collection = $bstoreContainer->getBSECollection();
        self::assertCount(1, $collection);
        self::assertNull($collection[0]->getBlip());
    }
}
