<?php

namespace PhpOffice\PhpSpreadsheet\Shared\OLE;

// vim: set expandtab tabstop=4 shiftwidth=4:
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Author: Xavier Noguer <xnoguer@php.net>                              |
// | Based on OLE::Storage_Lite by Kawai, Takanori                        |
// +----------------------------------------------------------------------+
//
use PhpOffice\PhpSpreadsheet\Shared\OLE;

/**
 * Class for creating PPS's for OLE containers.
 *
 * @author   Xavier Noguer <xnoguer@php.net>
 */
class PPS
{
    private const ALL_ONE_BITS = (PHP_INT_SIZE > 4) ? 0xFFFFFFFF : -1;

    private const COLOR_RED = 0;

    private const COLOR_BLACK = 1;

    /**
     * The PPS index.
     */
    public int $No;

    /**
     * The PPS name (in Unicode).
     */
    public string $Name;

    /**
     * The PPS type. Dir, Root or File.
     */
    public int $Type;

    /**
     * The index of the previous PPS.
     */
    public int $PrevPps;

    /**
     * The index of the next PPS.
     */
    public int $NextPps;

    /**
     * The index of it's first child if this is a Dir or Root PPS.
     */
    public int $DirPps;

    /**
     * The color in the directory entry red-black tree.
     */
    public int $Color = self::COLOR_BLACK;

    /**
     * A timestamp.
     */
    public float|int $Time1st;

    /**
     * A timestamp.
     */
    public float|int $Time2nd;

    /**
     * Starting block (small or big) for this PPS's data  inside the container.
     */
    public ?int $startBlock = null;

    /**
     * The size of the PPS's data (in bytes).
     */
    public int $Size;

    /**
     * The PPS's data (only used if it's not using a temporary file).
     */
    public string $_data = '';

    /**
     * Array of child PPS's (only used by Root and Dir PPS's).
     *
     * @var mixed[]
     */
    public array $children = [];

    /**
     * Pointer to OLE container.
     */
    public OLE $ole;

    /**
     * The constructor.
     *
     * @param ?int $No The PPS index
     * @param ?string $name The PPS name
     * @param ?int $type The PPS type. Dir, Root or File
     * @param ?int $prev The index of the previous PPS
     * @param ?int $next The index of the next PPS
     * @param ?int $dir The index of it's first child if this is a Dir or Root PPS
     * @param null|float|int $time_1st A timestamp
     * @param null|float|int $time_2nd A timestamp
     * @param ?string $data The (usually binary) source data of the PPS
     * @param mixed[] $children Array containing children PPS for this PPS
     */
    public function __construct(?int $No, ?string $name, ?int $type, ?int $prev, ?int $next, ?int $dir, $time_1st, $time_2nd, ?string $data, array $children)
    {
        $this->No = (int) $No;
        $this->Name = (string) $name;
        $this->Type = (int) $type;
        $this->PrevPps = (int) $prev;
        $this->NextPps = (int) $next;
        $this->DirPps = (int) $dir;
        $this->Time1st = $time_1st ?? 0;
        $this->Time2nd = $time_2nd ?? 0;
        $this->_data = (string) $data;
        $this->children = $children;
        $this->Size = strlen((string) $data);
    }

    /**
     * Returns the amount of data saved for this PPS.
     *
     * @return int The amount of data (in bytes)
     */
    public function getDataLen(): int
    {
        //if (!isset($this->_data)) {
        //    return 0;
        //}

        return strlen($this->_data);
    }

    /**
     * Returns a string with the PPS's WK (What is a WK?).
     *
     * @return string The binary string
     */
    public function getPpsWk(): string
    {
        $name = self::validateName($this->Name);
        $creationTime = $this->Type === OLE::OLE_PPS_TYPE_FILE || $this->Type === OLE::OLE_PPS_TYPE_ROOT
            ? str_repeat("\x00", 8)
            : OLE::localDateToOLE($this->Time1st);
        $modifiedTime = $this->Type === OLE::OLE_PPS_TYPE_FILE
            ? str_repeat("\x00", 8)
            : OLE::localDateToOLE($this->Time2nd);
        $ret = str_pad($name . "\x00\x00", 64, "\x00");

        $ret .= pack('v', strlen($name) + 2)       // 66
            . pack('C', $this->Type)               // 67
            . pack('C', $this->Color)              // 68
            . pack('V', $this->PrevPps) //Prev    // 72
            . pack('V', $this->NextPps) //Next    // 76
            . pack('V', $this->DirPps)  //Dir     // 80
            . str_repeat("\x00", 16)             // 96 CLSID
            . pack('V', 0)                         // 100 State bits
            . $creationTime                               // 108
            . $modifiedTime                               // 116
            . pack('V', $this->startBlock ?? 0)  // 120
            . pack('V', $this->Size)               // 124
            . pack('V', 0); // 128

        return $ret;
    }

    /**
     * Updates index and pointers to previous, next and children PPS's for this
     * PPS. I don't think it'll work with Dir PPS's.
     *
     * @param self[] $raList Reference to the array of PPS's for the whole OLE
     *                          container
     *
     * @return int The index for this PPS
     */
    public static function savePpsSetPnt(array &$raList, mixed $to_save, int $depth = 0): int
    {
        if (!is_array($to_save) || (empty($to_save))) {
            return self::ALL_ONE_BITS;
        }
        /** @var self[] $to_save */

        $tree = self::buildSiblingTree($to_save);

        return self::saveSiblingTree($raList, $tree, $depth);
    }

    /** @param self[] $ppsEntries */
    private static function buildSiblingTree(array $ppsEntries): PPSTreeNode
    {
        foreach ($ppsEntries as $pps) {
            self::validateName($pps->Name);
        }
        usort($ppsEntries, [self::class, 'comparePpsNames']);
        for ($index = 1, $count = count($ppsEntries); $index < $count; ++$index) {
            if (self::comparePpsNames($ppsEntries[$index - 1], $ppsEntries[$index]) === 0) {
                throw new \InvalidArgumentException('OLE PPS sibling names must be unique.');
            }
        }

        return self::buildTreeFromSortedEntries(
            $ppsEntries,
            0,
            count($ppsEntries) - 1,
            0,
            self::redLevel(count($ppsEntries))
        );
    }

    /**
     * @param self[] $ppsEntries
     */
    private static function buildTreeFromSortedEntries(array $ppsEntries, int $first, int $last, int $level, int $redLevel): PPSTreeNode
    {
        $middle = $first + intdiv($last - $first, 2);
        $node = new PPSTreeNode($ppsEntries[$middle]);
        $node->color = $level === $redLevel ? self::COLOR_RED : self::COLOR_BLACK;
        if ($first < $middle) {
            $node->left = self::buildTreeFromSortedEntries($ppsEntries, $first, $middle - 1, $level + 1, $redLevel);
        }
        if ($middle < $last) {
            $node->right = self::buildTreeFromSortedEntries($ppsEntries, $middle + 1, $last, $level + 1, $redLevel);
        }

        return $node;
    }

    private static function redLevel(int $entryCount): int
    {
        $level = 0;
        for ($nodes = $entryCount - 1; $nodes >= 0; $nodes = intdiv($nodes, 2) - 1) {
            ++$level;
        }

        return $level;
    }

    /** @param self[] $raList */
    private static function saveSiblingTree(array &$raList, PPSTreeNode $node, int $depth): int
    {
        $cnt = count($raList);
        $raList[$cnt] = ($depth === 0) ? $node->pps : clone $node->pps;
        $raList[$cnt]->No = $cnt;
        $raList[$cnt]->Color = $node->color;
        $raList[$cnt]->PrevPps = $node->left === null ? self::ALL_ONE_BITS : self::saveSiblingTree($raList, $node->left, $depth + 1);
        $raList[$cnt]->NextPps = $node->right === null ? self::ALL_ONE_BITS : self::saveSiblingTree($raList, $node->right, $depth + 1);
        $raList[$cnt]->DirPps = self::savePpsSetPnt($raList, $raList[$cnt]->children, $depth + 1);

        return $cnt;
    }

    private static function comparePpsNames(self $left, self $right): int
    {
        $leftName = self::uppercaseCodeUnits($left->Name);
        $rightName = self::uppercaseCodeUnits($right->Name);
        $length = count($leftName) <=> count($rightName);
        if ($length !== 0) {
            return $length;
        }

        foreach ($leftName as $index => $codeUnit) {
            $comparison = $codeUnit <=> $rightName[$index];
            if ($comparison !== 0) {
                return $comparison;
            }
        }

        return 0;
    }

    /** @return list<int> */
    private static function uppercaseCodeUnits(string $name): array
    {
        $codeUnits = [];
        for ($offset = 0, $length = strlen($name); $offset < $length; $offset += 2) {
            $codeUnit = ord($name[$offset]) | (ord($name[$offset + 1]) << 8);
            if ($codeUnit >= 0xD800 && $codeUnit <= 0xDFFF) {
                // CFB compares surrogate code units without applying case conversion.
                $codeUnits[] = $codeUnit;

                continue;
            }
            $character = mb_convert_encoding(pack('v', $codeUnit), 'UTF-8', 'UTF-16LE');
            $uppercase = mb_convert_case($character, MB_CASE_UPPER_SIMPLE, 'UTF-8');
            $utf16 = mb_convert_encoding($uppercase, 'UTF-16LE', 'UTF-8');
            $codeUnits[] = ord($utf16[0]) | (ord($utf16[1]) << 8);
        }

        return $codeUnits;
    }

    private static function validateName(string $name): string
    {
        if ((strlen($name) % 2) !== 0 || strlen($name) > 62) {
            throw new \InvalidArgumentException('OLE PPS names must contain at most 31 UTF-16LE code units.');
        }
        for ($offset = 0, $length = strlen($name); $offset < $length; $offset += 2) {
            $codeUnit = ord($name[$offset]) | (ord($name[$offset + 1]) << 8);
            if ($codeUnit === 0 || in_array($codeUnit, [0x2F, 0x5C, 0x3A, 0x21], true)) {
                throw new \InvalidArgumentException('OLE PPS names contain an invalid character.');
            }
            if ($codeUnit >= 0xD800 && $codeUnit <= 0xDBFF) {
                $offset += 2;
                if ($offset >= $length) {
                    throw new \InvalidArgumentException('OLE PPS names must be well-formed UTF-16LE.');
                }
                $followingCodeUnit = ord($name[$offset]) | (ord($name[$offset + 1]) << 8);
                if ($followingCodeUnit < 0xDC00 || $followingCodeUnit > 0xDFFF) {
                    throw new \InvalidArgumentException('OLE PPS names must be well-formed UTF-16LE.');
                }
            } elseif ($codeUnit >= 0xDC00 && $codeUnit <= 0xDFFF) {
                throw new \InvalidArgumentException('OLE PPS names must be well-formed UTF-16LE.');
            }
        }

        return $name;
    }
}
