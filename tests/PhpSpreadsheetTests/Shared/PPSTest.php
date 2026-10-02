<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Shared\OLE;
use PhpOffice\PhpSpreadsheet\Shared\OLE\PPS;
use PhpOffice\PhpSpreadsheet\Shared\OLE\PPS\File;
use PhpOffice\PhpSpreadsheet\Shared\OLE\PPS\Root;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PPSTest extends TestCase
{
    public function testSiblingTreeUsesCfbNameOrderAndRedBlackColors(): void
    {
        $children = [];
        foreach (['delta', 'A', 'charlie', 'bb', 'c', 'Alpha'] as $name) {
            $children[] = $this->newPps($name, OLE::OLE_PPS_TYPE_FILE);
        }
        $root = $this->newPps('Root Entry', OLE::OLE_PPS_TYPE_ROOT, $children);
        $list = [];
        PPS::savePpsSetPnt($list, [$root]);

        self::assertSame(1, $list[$list[0]->DirPps]->Color);
        self::assertSame(['A', 'c', 'bb', 'Alpha', 'delta', 'charlie'], $this->inOrderNames($list, $list[0]->DirPps));
        $this->assertRedBlackTree($list, $list[0]->DirPps);
    }

    #[DataProvider('siblingCounts')]
    public function testSiblingTreeMaintainsRedBlackInvariantsAtShapeBoundaries(int $count): void
    {
        $names = [];
        for ($index = 0; $index < $count; ++$index) {
            $names[] = sprintf('entry%02d', $index);
        }
        $children = [];
        foreach (array_reverse($names) as $name) {
            $children[] = $this->newPps($name, OLE::OLE_PPS_TYPE_FILE);
        }
        $root = $this->newPps('Root Entry', OLE::OLE_PPS_TYPE_ROOT, $children);
        $list = [];
        PPS::savePpsSetPnt($list, [$root]);

        self::assertSame(1, $list[$list[0]->DirPps]->Color);
        self::assertSame($names, $this->inOrderNames($list, $list[0]->DirPps));
        $this->assertRedBlackTree($list, $list[0]->DirPps);
    }

    /** @return array<string, array{int}> */
    public static function siblingCounts(): array
    {
        $boundaryCounts = [1, 2, 3, 4, 7, 8, 15, 16, 31, 32];
        $counts = [];
        foreach ($boundaryCounts as $count) {
            $counts[\sprintf('%d entries', $count)] = [$count];
        }

        return $counts;
    }

    public function testSiblingTreesAreSerializedRecursivelyForStorageEntries(): void
    {
        $storage = $this->newPps('Storage', OLE::OLE_PPS_TYPE_DIR, [
            $this->newPps('delta', OLE::OLE_PPS_TYPE_FILE),
            $this->newPps('A', OLE::OLE_PPS_TYPE_FILE),
            $this->newPps('charlie', OLE::OLE_PPS_TYPE_FILE),
            $this->newPps('bb', OLE::OLE_PPS_TYPE_FILE),
            $this->newPps('c', OLE::OLE_PPS_TYPE_FILE),
            $this->newPps('Alpha', OLE::OLE_PPS_TYPE_FILE),
        ]);
        $root = $this->newPps('Root Entry', OLE::OLE_PPS_TYPE_ROOT, [$storage]);
        $list = [];
        PPS::savePpsSetPnt($list, [$root]);

        $storageIndex = array_key_first(array_filter($list, static fn (PPS $pps): bool => $pps->Name === OLE::ascToUcs('Storage')));
        self::assertIsInt($storageIndex);
        self::assertSame(['A', 'c', 'bb', 'Alpha', 'delta', 'charlie'], $this->inOrderNames($list, $list[$storageIndex]->DirPps));
        $this->assertRedBlackTree($list, $list[$storageIndex]->DirPps);
    }

    public function testDirectoryEntryUsesBoundedUtf16NameAndZeroClsidForStreams(): void
    {
        $pps = $this->newPps(str_repeat('a', 31), OLE::OLE_PPS_TYPE_FILE);
        $pps->PrevPps = -1;
        $pps->NextPps = -1;
        $pps->DirPps = -1;
        $entry = $pps->getPpsWk();

        self::assertSame(128, strlen($entry));
        self::assertSame(64, ord($entry[64]) + ord($entry[65]) * 256);
        self::assertSame(str_repeat("\x00", 16), substr($entry, 80, 16));
    }

    public function testRootDirectoryEntryHasZeroCreationTime(): void
    {
        $root = new Root(1_700_000_000, 1_700_000_000, []);
        $entry = $root->getPpsWk();

        self::assertSame(str_repeat("\x00", 8), substr($entry, 100, 8));
        self::assertSame(OLE::localDateToOLE(1_700_000_000), substr($entry, 108, 8));
    }

    public function testStreamDirectoryEntryHasZeroTimestamps(): void
    {
        $stream = $this->newPps('Stream', OLE::OLE_PPS_TYPE_FILE);
        $stream->Time1st = 1_700_000_000;
        $stream->Time2nd = 1_700_000_000;
        $entry = $stream->getPpsWk();

        self::assertSame(str_repeat("\x00", 8), substr($entry, 100, 8));
        self::assertSame(str_repeat("\x00", 8), substr($entry, 108, 8));
    }

    public function testStorageDirectoryEntryPreservesTimestamps(): void
    {
        $storage = $this->newPps('Storage', OLE::OLE_PPS_TYPE_DIR);
        $storage->Time1st = 1_700_000_000;
        $storage->Time2nd = 1_700_000_000;
        $entry = $storage->getPpsWk();

        self::assertSame(OLE::localDateToOLE(1_700_000_000), substr($entry, 100, 8));
        self::assertSame(OLE::localDateToOLE(1_700_000_000), substr($entry, 108, 8));
    }

    public function testEmptyOrNonArrayPpsSetHasNoStreamId(): void
    {
        $list = [];
        $noStream = PHP_INT_SIZE > 4 ? 0xFFFFFFFF : -1;

        self::assertSame($noStream, PPS::savePpsSetPnt($list, []));
        self::assertSame($noStream, PPS::savePpsSetPnt($list, null));
        self::assertSame([], $list);
    }

    public function testDirectoryEntryRejectsNamesLongerThan31Utf16CodeUnits(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->newPps(str_repeat('a', 32), OLE::OLE_PPS_TYPE_FILE)->getPpsWk();
    }

    public function testSiblingNamesMustBeUniqueUnderCfbComparison(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $root = $this->newPps('Root Entry', OLE::OLE_PPS_TYPE_ROOT, [
            $this->newPps('same', OLE::OLE_PPS_TYPE_FILE),
            $this->newPps('SAME', OLE::OLE_PPS_TYPE_FILE),
        ]);
        $list = [];
        PPS::savePpsSetPnt($list, [$root]);
    }

    public function testCfbNameComparisonUsesSimpleCaseMappingAndDoesNotMapSurrogates(): void
    {
        $root = $this->newRawPps('Root Entry', OLE::OLE_PPS_TYPE_ROOT, [
            $this->newRawPps('ẞ', OLE::OLE_PPS_TYPE_FILE),
            $this->newRawPps('ß', OLE::OLE_PPS_TYPE_FILE),
            $this->newRawPps('𐐨', OLE::OLE_PPS_TYPE_FILE),
            $this->newRawPps('𐐀', OLE::OLE_PPS_TYPE_FILE),
        ]);
        $list = [];
        PPS::savePpsSetPnt($list, [$root]);

        self::assertSame(['ß', 'ẞ', '𐐀', '𐐨'], $this->inOrderNames($list, $list[0]->DirPps));
    }

    #[DataProvider('invalidNames')]
    public function testDirectoryEntryRejectsInvalidNames(string $name): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->newPpsBytes($name, OLE::OLE_PPS_TYPE_FILE)->getPpsWk();
    }

    /** @return array<string, array{string}> */
    public static function invalidNames(): array
    {
        return [
            'odd byte count' => ["a"],
            'embedded null' => ["a\x00\x00\x00"],
            'forbidden slash' => [OLE::ascToUcs('a/b')],
            'forbidden backslash' => [OLE::ascToUcs('a\\b')],
            'forbidden colon' => [OLE::ascToUcs('a:b')],
            'forbidden exclamation mark' => [OLE::ascToUcs('a!b')],
            'unpaired high surrogate' => ["\x00\xD8"],
            'high surrogate followed by a non-low surrogate' => ["\x00\xD8a\x00"],
            'unpaired low surrogate' => ["\x00\xDC"],
        ];
    }

    public function testSavedDirectoryEntriesContainCfbFields(): void
    {
        $files = [];
        foreach (['delta', 'A', 'charlie', 'bb', 'c', 'Alpha'] as $name) {
            $file = new File(OLE::ascToUcs($name));
            $file->append('data');
            $files[] = $file;
        }
        $root = new Root(null, null, $files);
        $handle = fopen('php://memory', 'w+b');
        self::assertIsResource($handle);
        $root->save($handle);
        rewind($handle);
        $contents = stream_get_contents($handle);
        fclose($handle);
        self::assertIsString($contents);

        $directorySector = self::readUnsignedInt32($contents, 48);
        $fatSector = self::readUnsignedInt32($contents, 76);
        $entries = [];
        $directorySectorCount = 0;
        while ($directorySector !== 0xFFFFFFFE) {
            ++$directorySectorCount;
            $directory = substr($contents, 512 + $directorySector * 512, 512);
            for ($index = 0; $index < 4; ++$index) {
                $entry = substr($directory, $index * 128, 128);
                if (ord($entry[66]) !== 0) {
                    $nameLength = self::readUnsignedInt16($entry, 64);
                    $entries[] = [
                        'entry' => $entry,
                        'name' => mb_convert_encoding(substr($entry, 0, $nameLength - 2), 'UTF-8', 'UTF-16LE'),
                        'color' => ord($entry[67]),
                        'left' => self::readUnsignedInt32($entry, 68),
                        'right' => self::readUnsignedInt32($entry, 72),
                        'child' => self::readUnsignedInt32($entry, 76),
                    ];
                }
            }
            $directorySector = self::readUnsignedInt32($contents, 512 + $fatSector * 512 + $directorySector * 4);
        }

        self::assertSame(2, $directorySectorCount);
        self::assertCount(7, $entries);
        foreach ($entries as $entry) {
            $nameLength = self::readUnsignedInt16($entry['entry'], 64);
            self::assertGreaterThanOrEqual(2, $nameLength);
            self::assertSame("\x00\x00", substr($entry['entry'], $nameLength - 2, 2));
            self::assertContains(ord($entry['entry'][67]), [0, 1]);
            if (ord($entry['entry'][66]) === OLE::OLE_PPS_TYPE_FILE) {
                self::assertSame(str_repeat("\x00", 16), substr($entry['entry'], 80, 16));
            }
        }
        self::assertSame(['A', 'c', 'bb', 'Alpha', 'delta', 'charlie'], $this->inOrderEntryNames($entries, $entries[0]['child']));
        $this->assertSerializedRedBlackTree($entries, $entries[0]['child']);
    }

    /**
     * @param PPS[] $children
     */
    private function newPps(string $name, int $type, array $children = []): PPS
    {
        return new PPS(null, OLE::ascToUcs($name), $type, null, null, null, null, null, '', $children);
    }

    /**
     * @param PPS[] $children
     */
    private function newRawPps(string $name, int $type, array $children = []): PPS
    {
        return $this->newPpsBytes(mb_convert_encoding($name, 'UTF-16LE', 'UTF-8'), $type, $children);
    }

    /**
     * @param PPS[] $children
     */
    private function newPpsBytes(string $name, int $type, array $children = []): PPS
    {
        return new PPS(null, $name, $type, null, null, null, null, null, '', $children);
    }

    /**
     * @param PPS[] $list
     *
     * @return string[]
     */
    private function inOrderNames(array $list, int $index): array
    {
        if ($index === -1 || $index === 0xFFFFFFFF) {
            return [];
        }

        return [
            ...$this->inOrderNames($list, $list[$index]->PrevPps),
            mb_convert_encoding($list[$index]->Name, 'UTF-8', 'UTF-16LE'),
            ...$this->inOrderNames($list, $list[$index]->NextPps),
        ];
    }

    /**
     * @param PPS[] $list
     */
    private function assertRedBlackTree(array $list, int $index): int
    {
        if ($index === -1 || $index === 0xFFFFFFFF) {
            return 1;
        }

        $pps = $list[$index];
        self::assertContains($pps->Color, [0, 1]);
        if ($pps->Color === 0) {
            self::assertSame(1, $this->colorOf($list, $pps->PrevPps));
            self::assertSame(1, $this->colorOf($list, $pps->NextPps));
        }
        $leftHeight = $this->assertRedBlackTree($list, $pps->PrevPps);
        $rightHeight = $this->assertRedBlackTree($list, $pps->NextPps);
        self::assertSame($leftHeight, $rightHeight);

        return $leftHeight + ($pps->Color === 1 ? 1 : 0);
    }

    /**
     * @param PPS[] $list
     */
    private function colorOf(array $list, int $index): int
    {
        return ($index === -1 || $index === 0xFFFFFFFF) ? 1 : $list[$index]->Color;
    }

    private static function readUnsignedInt16(string $data, int $offset): int
    {
        return ord($data[$offset]) | (ord($data[$offset + 1]) << 8);
    }

    private static function readUnsignedInt32(string $data, int $offset): int
    {
        return self::readUnsignedInt16($data, $offset) | (self::readUnsignedInt16($data, $offset + 2) << 16);
    }

    /**
     * @param array<int, array{name: string, color: int, left: int, right: int, child: int, entry: string}> $entries
     *
     * @return string[]
     */
    private function inOrderEntryNames(array $entries, int $index): array
    {
        if ($index === 0xFFFFFFFF) {
            return [];
        }

        return [
            ...$this->inOrderEntryNames($entries, $entries[$index]['left']),
            $entries[$index]['name'],
            ...$this->inOrderEntryNames($entries, $entries[$index]['right']),
        ];
    }

    /**
     * @param array<int, array{name: string, color: int, left: int, right: int, child: int, entry: string}> $entries
     */
    private function assertSerializedRedBlackTree(array $entries, int $index): int
    {
        if ($index === 0xFFFFFFFF) {
            return 1;
        }

        $entry = $entries[$index];
        self::assertContains($entry['color'], [0, 1]);
        if ($entry['color'] === 0) {
            self::assertSame(1, $this->serializedColorOf($entries, $entry['left']));
            self::assertSame(1, $this->serializedColorOf($entries, $entry['right']));
        }
        $leftHeight = $this->assertSerializedRedBlackTree($entries, $entry['left']);
        $rightHeight = $this->assertSerializedRedBlackTree($entries, $entry['right']);
        self::assertSame($leftHeight, $rightHeight);

        return $leftHeight + ($entry['color'] === 1 ? 1 : 0);
    }

    /**
     * @param array<int, array{name: string, color: int, left: int, right: int, child: int, entry: string}> $entries
     */
    private function serializedColorOf(array $entries, int $index): int
    {
        return $index === 0xFFFFFFFF ? 1 : $entries[$index]['color'];
    }
}
