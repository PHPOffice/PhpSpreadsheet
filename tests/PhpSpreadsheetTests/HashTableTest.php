<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Comment; // need Comparable object
use PhpOffice\PhpSpreadsheet\HashTable;
use PHPUnit\Framework\TestCase;

class HashTableTest extends TestCase
{
    public static function createArray(): array
    {
        $comment1 = new Comment();
        $comment1->setAuthor('Author1');
        $comment2 = new Comment();
        $comment2->setAuthor('Author2');

        return [$comment1, $comment2];
    }

    /**
     * @param mixed $comment
     */
    public static function getAuthor($comment): string
    {
        return ($comment instanceof Comment) ? $comment->getAuthor() : '';
    }

    public function testAddRemoveClear(): void
    {
        $array1 = self::createArray();
        $hash1 = new HashTable($array1);
        self::assertSame(2, $hash1->count());
        $comment3 = new Comment();
        $comment3->setAuthor('Author3');
        $hash1->add($comment3);
        $comment4 = new Comment();
        $comment4->setAuthor('Author4');
        $hash1->add($comment4);
        $comment5 = new Comment();
        $comment5->setAuthor('Author5');
        // don't add comment5
        self::assertSame(4, $hash1->count());
        self::assertNull($hash1->getByIndex(10));
        $comment = $hash1->getByIndex(2);
        self::assertSame('Author3', self::getAuthor($comment));
        $hash1->remove($comment3);
        self::assertSame(3, $hash1->count());
        $comment = $hash1->getByIndex(2);
        self::assertSame('Author4', self::getAuthor($comment));
        $hash1->remove($comment5);
        self::assertSame(3, $hash1->count(), 'Remove non-hash member');
        $comment = $hash1->getByIndex(2);
        self::assertSame('Author4', self::getAuthor($comment));
        self::assertNull($hash1->getByHashCode('xyz'));
        $hash1->clear();
        self::AssertSame(0, $hash1->count());
    }

    public function testToArray(): void
    {
        $array1 = self::createArray();
        $count1 = count($array1);
        $hash1 = new HashTable($array1);
        $array2 = $hash1->toArray();
        self::assertCount($count1, $array2);
        $idx = 0;
        foreach ($array2 as $key => $value) {
            self::assertEquals($array1[$idx], $value, "Item $idx");
            self::assertSame($idx, $hash1->getIndexForHashCode($key));
            ++$idx;
        }
    }

    public function testClone(): void
    {
        $array1 = self::createArray();
        $hash1 = new HashTable($array1);
        $hash2 = new HashTable();
        self::assertSame(0, $hash2->count());
        $hash2->addFromSource();
        self::assertSame(0, $hash2->count());
        $hash2->addFromSource($array1);
        self::assertSame(2, $hash2->count());
        self::assertEquals($hash1, $hash2, 'Add in constructor same as addFromSource');
        $hash3 = clone $hash1;
        self::assertEquals($hash1, $hash3, 'Clone equal to original');
        self::assertSame($hash1->getByIndex(0), $hash2->getByIndex(0));
        self::assertEquals($hash1->getByIndex(0), $hash3->getByIndex(0));
        self::assertNotSame($hash1->getByIndex(0), $hash3->getByIndex(0));
    }
}
