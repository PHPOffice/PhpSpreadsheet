<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Collection;

use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\Collection\Memory\SimpleCache3;
use PHPUnit\Framework\TestCase;

class SimpleCacheLruTest extends TestCase
{
    public function testUnlimitedCacheDefaultBehavior(): void
    {
        $cache = new SimpleCache3();

        $cache->set('a', 1);
        $cache->set('b', 2);
        $cache->set('c', 3);
        $cache->set('d', 4);
        $cache->set('e', 5);

        self::assertTrue($cache->has('a'));
        self::assertTrue($cache->has('e'));
        self::assertSame(1, $cache->get('a'));
        self::assertSame(5, $cache->get('e'));
    }

    public function testUnlimitedCacheExplicitZero(): void
    {
        $cache = new SimpleCache3(0);

        for ($i = 0; $i < 100; ++$i) {
            $cache->set("key{$i}", $i);
        }

        // All 100 entries should still be present
        for ($i = 0; $i < 100; ++$i) {
            self::assertTrue($cache->has("key{$i}"));
            self::assertSame($i, $cache->get("key{$i}"));
        }
    }

    public function testLruEvictionOnSet(): void
    {
        $cache = new SimpleCache3(3);

        $cache->set('a', 1);
        $cache->set('b', 2);
        $cache->set('c', 3);

        // Cache is full (a, b, c). Adding d should evict a (least recently used).
        $cache->set('d', 4);

        self::assertFalse($cache->has('a'), 'LRU entry "a" should have been evicted');
        self::assertNull($cache->get('a'));
        self::assertTrue($cache->has('b'));
        self::assertTrue($cache->has('c'));
        self::assertTrue($cache->has('d'));
    }

    public function testGetPromotesEntry(): void
    {
        $cache = new SimpleCache3(3);

        $cache->set('a', 1);
        $cache->set('b', 2);
        $cache->set('c', 3);

        // Access 'a' to promote it — now LRU order is b, c, a
        self::assertSame(1, $cache->get('a'));

        // Adding d should evict b (now the least recently used)
        $cache->set('d', 4);

        self::assertFalse($cache->has('b'), 'LRU entry "b" should have been evicted after "a" was accessed');
        self::assertTrue($cache->has('a'), '"a" should survive because it was recently accessed');
        self::assertTrue($cache->has('c'));
        self::assertTrue($cache->has('d'));
    }

    public function testSetExistingKeyDoesNotEvict(): void
    {
        $cache = new SimpleCache3(3);

        $cache->set('a', 1);
        $cache->set('b', 2);
        $cache->set('c', 3);

        // Updating an existing key should not evict anything
        $cache->set('a', 10);

        self::assertSame(10, $cache->get('a'));
        self::assertTrue($cache->has('b'));
        self::assertTrue($cache->has('c'));
    }

    public function testSetExistingKeyPromotesToMostRecent(): void
    {
        $cache = new SimpleCache3(3);

        $cache->set('a', 1);
        $cache->set('b', 2);
        $cache->set('c', 3);

        // Re-set 'a' to promote it — LRU order is now b, c, a
        $cache->set('a', 10);

        // Adding d should evict b
        $cache->set('d', 4);

        self::assertFalse($cache->has('b'), '"b" should be evicted as LRU');
        self::assertTrue($cache->has('a'));
        self::assertSame(10, $cache->get('a'));
        self::assertTrue($cache->has('c'));
        self::assertTrue($cache->has('d'));
    }

    public function testDeleteFreesSlot(): void
    {
        $cache = new SimpleCache3(3);

        $cache->set('a', 1);
        $cache->set('b', 2);
        $cache->set('c', 3);

        $cache->delete('b');

        // Now there's room for d without evicting anything
        $cache->set('d', 4);

        self::assertTrue($cache->has('a'));
        self::assertFalse($cache->has('b'));
        self::assertTrue($cache->has('c'));
        self::assertTrue($cache->has('d'));
    }

    public function testDeleteReturnsTrue(): void
    {
        $cache = new SimpleCache3(3);

        self::assertTrue($cache->delete('nonexistent'));

        $cache->set('a', 1);
        self::assertTrue($cache->delete('a'));
        self::assertFalse($cache->has('a'));
    }

    public function testClearResetsCache(): void
    {
        $cache = new SimpleCache3(3);

        $cache->set('a', 1);
        $cache->set('b', 2);
        $cache->set('c', 3);

        self::assertTrue($cache->clear());
        self::assertFalse($cache->has('a'));
        self::assertFalse($cache->has('b'));
        self::assertFalse($cache->has('c'));

        // After clear, we can add 3 new entries without eviction
        $cache->set('x', 10);
        $cache->set('y', 20);
        $cache->set('z', 30);

        self::assertTrue($cache->has('x'));
        self::assertTrue($cache->has('y'));
        self::assertTrue($cache->has('z'));
    }

    public function testGetReturnsDefaultForMissing(): void
    {
        $cache = new SimpleCache3(3);

        self::assertNull($cache->get('missing'));
        self::assertSame('fallback', $cache->get('missing', 'fallback'));
    }

    public function testHasReportsPresenceAndAbsence(): void
    {
        $cache = new SimpleCache3(2);

        self::assertFalse($cache->has('a'));

        $cache->set('a', 1);
        self::assertTrue($cache->has('a'));

        $cache->set('b', 2);
        self::assertTrue($cache->has('a'));
        self::assertTrue($cache->has('b'));

        // Adding c evicts a
        $cache->set('c', 3);
        self::assertFalse($cache->has('a'));
        self::assertTrue($cache->has('b'));
        self::assertTrue($cache->has('c'));
    }

    public function testGetMultiple(): void
    {
        $cache = new SimpleCache3(5);

        $cache->set('a', 1);
        $cache->set('b', 2);
        $cache->set('c', 3);

        $results = $cache->getMultiple(['a', 'b', 'missing']);
        self::assertSame(['a' => 1, 'b' => 2, 'missing' => null], $results);

        $results = $cache->getMultiple(['missing1', 'missing2'], 'default');
        self::assertSame(['missing1' => 'default', 'missing2' => 'default'], $results);
    }

    public function testGetMultiplePromotesAccessed(): void
    {
        $cache = new SimpleCache3(3);

        $cache->set('a', 1);
        $cache->set('b', 2);
        $cache->set('c', 3);

        // Access a and b via getMultiple — promotes them, LRU order: c, a, b
        $cache->getMultiple(['a', 'b']);

        // Adding d should evict c (least recently used)
        $cache->set('d', 4);

        self::assertFalse($cache->has('c'), '"c" should be evicted as LRU');
        self::assertTrue($cache->has('a'));
        self::assertTrue($cache->has('b'));
        self::assertTrue($cache->has('d'));
    }

    public function testSetMultiple(): void
    {
        $cache = new SimpleCache3(5);

        self::assertTrue($cache->setMultiple(['a' => 1, 'b' => 2, 'c' => 3]));

        self::assertSame(1, $cache->get('a'));
        self::assertSame(2, $cache->get('b'));
        self::assertSame(3, $cache->get('c'));
    }

    public function testSetMultipleWithEviction(): void
    {
        $cache = new SimpleCache3(3);

        $cache->set('a', 1);
        $cache->set('b', 2);
        $cache->set('c', 3);

        // setMultiple with new keys should cause eviction of oldest entries
        $cache->setMultiple(['d' => 4, 'e' => 5]);

        self::assertFalse($cache->has('a'), '"a" should have been evicted');
        self::assertFalse($cache->has('b'), '"b" should have been evicted');
        self::assertTrue($cache->has('c'));
        self::assertTrue($cache->has('d'));
        self::assertTrue($cache->has('e'));
    }

    public function testDeleteMultiple(): void
    {
        $cache = new SimpleCache3(5);

        $cache->set('a', 1);
        $cache->set('b', 2);
        $cache->set('c', 3);

        self::assertTrue($cache->deleteMultiple(['a', 'c']));

        self::assertFalse($cache->has('a'));
        self::assertTrue($cache->has('b'));
        self::assertFalse($cache->has('c'));
    }

    public function testSetReturnsTrue(): void
    {
        $cache = new SimpleCache3(2);

        self::assertTrue($cache->set('a', 1));
        self::assertTrue($cache->set('b', 2));
        self::assertTrue($cache->set('c', 3)); // triggers eviction, still returns true
    }

    public function testMaxSizeOne(): void
    {
        $cache = new SimpleCache3(1);

        $cache->set('a', 1);
        self::assertTrue($cache->has('a'));

        $cache->set('b', 2);
        self::assertFalse($cache->has('a'), '"a" should be evicted with maxSize=1');
        self::assertTrue($cache->has('b'));
        self::assertSame(2, $cache->get('b'));
    }

    public function testNegativeMaxSizeThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('maxSize must be >= 0');

        new SimpleCache3(-1);
    }

    public function testSequentialEvictionOrder(): void
    {
        $cache = new SimpleCache3(3);

        $cache->set('a', 1);
        $cache->set('b', 2);
        $cache->set('c', 3);

        // Evict a
        $cache->set('d', 4);
        self::assertFalse($cache->has('a'));

        // Evict b
        $cache->set('e', 5);
        self::assertFalse($cache->has('b'));

        // Evict c
        $cache->set('f', 6);
        self::assertFalse($cache->has('c'));

        // Only d, e, f should remain
        self::assertTrue($cache->has('d'));
        self::assertTrue($cache->has('e'));
        self::assertTrue($cache->has('f'));
    }
}
