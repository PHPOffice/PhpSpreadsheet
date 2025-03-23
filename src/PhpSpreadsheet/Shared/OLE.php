<?php

namespace PhpOffice\PhpSpreadsheet\Shared;

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

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Shared\OLE\ChainedBlockStream;
use PhpOffice\PhpSpreadsheet\Shared\OLE\PPS\Root;

/*
 * Array for storing OLE instances that are accessed from
 * OLE_ChainedBlockStream::stream_open().
 *
 * @var array
 */
$GLOBALS['_OLE_INSTANCES'] = [];

/**
 * OLE package base class.
 *
 * @author   Xavier Noguer <xnoguer@php.net>
 * @author   Christian Schmidt <schmidt@php.net>
 */
class OLE
{
    const OLE_PPS_TYPE_ROOT = 5;
    const OLE_PPS_TYPE_DIR = 1;
    const OLE_PPS_TYPE_FILE = 2;
    const OLE_DATA_SIZE_SMALL = 0x1000;
    const OLE_LONG_INT_SIZE = 4;
    const OLE_PPS_SIZE = 0x80;

    /**
     * The file handle for reading an OLE container.
     *
     * @var resource
     */
    public $_file_handle;

    /**
     * Array of PPS's found on the OLE container.
     */
    public array $_list = [];

    /**
     * Root directory of OLE container.
     */
    public Root $root;

    /**
     * Big Block Allocation Table.
     *
     * @var array (blockId => nextBlockId)
     */
    public array $bbat;

    /**
     * Short Block Allocation Table.
     *
     * @var array (blockId => nextBlockId)
     */
    public array $sbat;

    /**
     * Size of big blocks. This is usually 512.
     *
     * @var int<1, max> number of octets per block
     */
    public int $bigBlockSize;

    /**
     * Size of small blocks. This is usually 64.
     *
     * @var int number of octets per block
     */
    public int $smallBlockSize;

    /**
     * Threshold for big blocks.
     */
    public int $bigBlockThreshold;

    /**
     * Reads an OLE container from the contents of the file given.
     *
     * @acces public
     *
     * @return bool true on success, PEAR_Error on failure
     */
    public function read(string $filename): bool
    {
        $fh = @fopen($filename, 'rb');
        if ($fh === false) {
            throw new ReaderException("Can't open file $filename");
        }
        $this->_file_handle = $fh;

        $signature = fread($fh, 8);
        if ("\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1" != $signature) {
            throw new ReaderException("File doesn't seem to be an OLE container.");
        }
        fseek($fh, 28);
        if (fread($fh, 2) != "\xFE\xFF") {
            // This shouldn't be a problem in practice
            throw new ReaderException('Only Little-Endian encoding is supported.');
        }
        // Size of blocks and short blocks in bytes
        /** @var int<1, max> */
        $temp = 2 ** self::readInt2($fh);
        $this->bigBlockSize = $temp;
        $this->smallBlockSize = 2 ** self::readInt2($fh);

        // Skip UID, revision number and version number
        fseek($fh, 44);
        // Number of blocks in Big Block Allocation Table
        $bbatBlockCount = self::readInt4($fh);

        // Root chain 1st block
        $directoryFirstBlockId = self::readInt4($fh);

        // Skip unused bytes
        fseek($fh, 56);
        // Streams shorter than this are stored using small blocks
        $this->bigBlockThreshold = self::readInt4($fh);
        // Block id of first sector in Short Block Allocation Table
        $sbatFirstBlockId = self::readInt4($fh);
        // Number of blocks in Short Block Allocation Table
        $sbbatBlockCount = self::readInt4($fh);
        // Block id of first sector in Master Block Allocation Table
        $mbatFirstBlockId = self::readInt4($fh);
        // Number of blocks in Master Block Allocation Table
        $mbbatBlockCount = self::readInt4($fh);
        $this->bbat = [];

        // Remaining 4 * 109 bytes of current block is beginning of Master
        // Block Allocation Table
        $mbatBlocks = [];
        for ($i = 0; $i < 109; ++$i) {
            $mbatBlocks[] = self::readInt4($fh);
        }

        // Read rest of Master Block Allocation Table (if any is left)
        $pos = $this->getBlockOffset($mbatFirstBlockId);
        for ($i = 0; $i < $mbbatBlockCount; ++$i) {
            fseek($fh, $pos);
            for ($j = 0; $j < $this->bigBlockSize / 4 - 1; ++$j) {
                $mbatBlocks[] = self::readInt4($fh);
            }
            // Last block id in each block points to next block
            $pos = $this->getBlockOffset(self::readInt4($fh));
        }

        // Read Big Block Allocation Table according to chain specified by $mbatBlocks
        for ($i = 0; $i < $bbatBlockCount; ++$i) {
            $pos = $this->getBlockOffset($mbatBlocks[$i]);
            fseek($fh, $pos);
            for ($j = 0; $j < $this->bigBlockSize / 4; ++$j) {
                $this->bbat[] = self::readInt4($fh);
            }
        }

        // Read short block allocation table (SBAT)
        $this->sbat = [];
        $shortBlockCount = $sbbatBlockCount * $this->bigBlockSize / 4;
        $sbatFh = $this->getStream($sbatFirstBlockId);
        for ($blockId = 0; $blockId < $shortBlockCount; ++$blockId) {
            $this->sbat[$blockId] = self::readInt4($sbatFh);
        }
        fclose($sbatFh);

        $this->readPpsWks($directoryFirstBlockId);

        return true;
    }

    /**
     * @param int $blockId byte offset from beginning of file
     */
    public function getBlockOffset(int $blockId): int
    {
        return 512 + $blockId * $this->bigBlockSize;
    }

    /**
     * Returns a stream for use with fread() etc. External callers should
     * use \PhpOffice\PhpSpreadsheet\Shared\OLE\PPS\File::getStream().
     *
     * @param int|OLE\PPS $blockIdOrPps block id or PPS
     *
     * @return resource read-only stream
     */
    public function getStream($blockIdOrPps)
    {
        static $isRegistered = false;
        if (!$isRegistered) {
            stream_wrapper_register('ole-chainedblockstream', ChainedBlockStream::class);
            $isRegistered = true;
        }

        // Store current instance in global array, so that it can be accessed
        // in OLE_ChainedBlockStream::stream_open().
        // Object is removed from self::$instances in OLE_Stream::close().
        $GLOBALS['_OLE_INSTANCES'][] = $this; //* @phpstan-ignore-line
        $keys = array_keys($GLOBALS['_OLE_INSTANCES']); //* @phpstan-ignore-line
        $instanceId = end($keys);

        $path = 'ole-chainedblockstream://oleInstanceId=' . $instanceId;
        if ($blockIdOrPps instanceof OLE\PPS) {
            $path .= '&blockId=' . $blockIdOrPps->startBlock;
            $path .= '&size=' . $blockIdOrPps->Size;
        } else {
            $path .= '&blockId=' . $blockIdOrPps;
        }

        $resource = fopen($path, 'rb');
        if ($resource === false) {
            throw new Exception("Unable to open stream $path");
        }

        return $resource;
    }

    /**
     * Reads a signed char.
     *
     * @param resource $fileHandle file handle
     */
    private static function readInt1($fileHandle): int
    {
        [, $tmp] = unpack('c', fread($fileHandle, 1) ?: '') ?: [0, 0];

        return $tmp;
    }

    /**
     * Reads an unsigned short (2 octets).
     *
     * @param resource $fileHandle file handle
     */
    private static function readInt2($fileHandle): int
    {
        [, $tmp] = unpack('v', fread($fileHandle, 2) ?: '') ?: [0, 0];

        return $tmp;
    }

    private const SIGNED_4OCTET_LIMIT = 2147483648;

    private const SIGNED_4OCTET_SUBTRACT = 2 * self::SIGNED_4OCTET_LIMIT;

    /**
     * Reads long (4 octets), interpreted as if signed on 32-bit system.
     *
     * @param resource $fileHandle file handle
     */
    private static function readInt4($fileHandle): int
    {
        [, $tmp] = unpack('V', fread($fileHandle, 4) ?: '') ?: [0, 0];
        if ($tmp >= self::SIGNED_4OCTET_LIMIT) {
            $tmp -= self::SIGNED_4OCTET_SUBTRACT;
        }

        return $tmp;
    }

    /**
     * Gets information about all PPS's on the OLE container from the PPS WK's
     * creates an OLE_PPS object for each one.
     *
     * @param int $blockId the block id of the first block
     *
     * @return bool true on success, PEAR_Error on failure
     */
    public function readPpsWks(int $blockId): bool
    {
        $fh = $this->getStream($blockId);
        for ($pos = 0; true; $pos += 128) {
            fseek($fh, $pos, SEEK_SET);
            $nameUtf16 = (string) fread($fh, 64);
            $nameLength = self::readInt2($fh);
            $nameUtf16 = substr($nameUtf16, 0, $nameLength - 2);
            // Simple conversion from UTF-16LE to ISO-8859-1
            $name = str_replace("\x00", '', $nameUtf16);
            $type = self::readInt1($fh);
            switch ($type) {
                case self::OLE_PPS_TYPE_ROOT:
                    $pps = new Root(null, null, []);
                    $this->root = $pps;

                    break;
                case self::OLE_PPS_TYPE_DIR:
                    $pps = new OLE\PPS(null, null, null, null, null, null, null, null, null, []);

                    break;
                case self::OLE_PPS_TYPE_FILE:
                    $pps = new OLE\PPS\File($name);

                    break;
                default:
                    throw new Exception('Unsupported PPS type');
            }
            fseek($fh, 1, SEEK_CUR);
            $pps->Type = $type;
            $pps->Name = $name;
            $pps->PrevPps = self::readInt4($fh);
            $pps->NextPps = self::readInt4($fh);
            $pps->DirPps = self::readInt4($fh);
            fseek($fh, 20, SEEK_CUR);
            $pps->Time1st = self::OLE2LocalDate((string) fread($fh, 8));
            $pps->Time2nd = self::OLE2LocalDate((string) fread($fh, 8));
            $pps->startBlock = self::readInt4($fh);
            $pps->Size = self::readInt4($fh);
            $pps->No = count($this->_list);
            $this->_list[] = $pps;

            // check if the PPS tree (starting from root) is complete
            if (isset($this->root) && $this->ppsTreeComplete($this->root->No)) {
                break;
            }
        }
        fclose($fh);

        // Initialize $pps->children on directories
        foreach ($this->_list as $pps) {
            if ($pps->Type == self::OLE_PPS_TYPE_DIR || $pps->Type == self::OLE_PPS_TYPE_ROOT) {
                $nos = [$pps->DirPps];
                $pps->children = [];
                while (!empty($nos)) {
                    $no = array_pop($nos);
                    if ($no != -1) {
                        $childPps = $this->_list[$no];
                        $nos[] = $childPps->PrevPps;
                        $nos[] = $childPps->NextPps;
                        $pps->children[] = $childPps;
                    }
                }
            }
        }

        return true;
    }

    /**
     * It checks whether the PPS tree is complete (all PPS's read)
     * starting with the given PPS (not necessarily root).
     *
     * @param int $index The index of the PPS from which we are checking
     *
     * @return bool Whether the PPS tree for the given PPS is complete
     */
    private function ppsTreeComplete(int $index): bool
    {
        return isset($this->_list[$index])
            && ($pps = $this->_list[$index])
            && ($pps->PrevPps == -1
                || $this->ppsTreeComplete($pps->PrevPps))
            && ($pps->NextPps == -1
                || $this->ppsTreeComplete($pps->NextPps))
            && ($pps->DirPps == -1
                || $this->ppsTreeComplete($pps->DirPps));
    }

    /**
     * Checks whether a PPS is a File PPS or not.
     * If there is no PPS for the index given, it will return false.
     *
     * @param int $index The index for the PPS
     *
     * @return bool true if it's a File PPS, false otherwise
     */
    public function isFile(int $index): bool
    {
        if (isset($this->_list[$index])) {
            return $this->_list[$index]->Type == self::OLE_PPS_TYPE_FILE;
        }

        return false;
    }

    /**
     * Checks whether a PPS is a Root PPS or not.
     * If there is no PPS for the index given, it will return false.
     *
     * @param int $index the index for the PPS
     *
     * @return bool true if it's a Root PPS, false otherwise
     */
    public function isRoot(int $index): bool
    {
        if (isset($this->_list[$index])) {
            return $this->_list[$index]->Type == self::OLE_PPS_TYPE_ROOT;
        }

        return false;
    }

    /**
     * Gives the total number of PPS's found in the OLE container.
     *
     * @return int The total number of PPS's found in the OLE container
     */
    public function ppsTotal(): int
    {
        return count($this->_list);
    }

    /**
     * Gets data from a PPS
     * If there is no PPS for the index given, it will return an empty string.
     *
     * @param int $index The index for the PPS
     * @param int $position The position from which to start reading
     *                          (relative to the PPS)
     * @param int $length The amount of bytes to read (at most)
     *
     * @return string The binary string containing the data requested
     *
     * @see OLE_PPS_File::getStream()
     */
    public function getData(int $index, int $position, int $length): string
    {
        // if position is not valid return empty string
        if (!isset($this->_list[$index]) || ($position >= $this->_list[$index]->Size) || ($position < 0)) {
            return '';
        }
        $fh = $this->getStream($this->_list[$index]);
        $data = (string) stream_get_contents($fh, $length, $position);
        fclose($fh);

        return $data;
    }

    /**
     * Gets the data length from a PPS
     * If there is no PPS for the index given, it will return 0.
     *
     * @param int $index The index for the PPS
     *
     * @return int The amount of bytes in data the PPS has
     */
    public function getDataLength(int $index): int
    {
        if (isset($this->_list[$index])) {
            return $this->_list[$index]->Size;
        }

        return 0;
    }

    /**
     * Utility function to transform ASCII text to Unicode.
     *
     * @param string $ascii The ASCII string to transform
     *
     * @return string The string in Unicode
     */
    public static function ascToUcs(string $ascii): string
    {
        $rawname = '';
        $iMax = strlen($ascii);
        for ($i = 0; $i < $iMax; ++$i) {
            $rawname .= $ascii[$i]
                . "\x00";
        }

        return $rawname;
    }

    /**
     * Utility function
     * Returns a string for the OLE container with the date given.
     *
     * @param float|int $date A timestamp
     *
     * @return string The string for the OLE container
     */
    public static function localDateToOLE($date): string
    {
        if (!$date) {
            return "\x00\x00\x00\x00\x00\x00\x00\x00";
        }
        $dateTime = Date::dateTimeFromTimestamp("$date");

        // days from 1-1-1601 until the beggining of UNIX era
        $days = 134774;
        // calculate seconds
        $big_date = $days * 24 * 3600 + (float) $dateTime->format('U');
        // multiply just to make MS happy
        $big_date *= 10000000;

        // Make HEX string
        $res = '';

        $factor = 2 ** 56;
        while ($factor >= 1) {
            $hex = (int) floor($big_date / $factor);
            $res = pack('c', $hex) . $res;
            $big_date = fmod($big_date, $factor);
            $factor /= 256;
        }

        return $res;
    }

    /**
     * Returns a timestamp from an OLE container's date.
     *
     * @param string $oleTimestamp A binary string with the encoded date
     *
     * @return float|int The Unix timestamp corresponding to the string
     */
    public static function OLE2LocalDate(string $oleTimestamp)
    {
        if (strlen($oleTimestamp) != 8) {
            throw new ReaderException('Expecting 8 byte string');
        }

        // convert to units of 100 ns since 1601:
        $unpackedTimestamp = unpack('v4', $oleTimestamp) ?: [];
        $timestampHigh = (float) $unpackedTimestamp[4] * 65536 + (float) $unpackedTimestamp[3];
        $timestampLow = (float) $unpackedTimestamp[2] * 65536 + (float) $unpackedTimestamp[1];

        // translate to seconds since 1601:
        $timestampHigh /= 10000000;
        $timestampLow /= 10000000;

        // days from 1601 to 1970:
        $days = 134774;

        // translate to seconds since 1970:
        $unixTimestamp = floor(65536.0 * 65536.0 * $timestampHigh + $timestampLow - $days * 24 * 3600 + 0.5);

        return IntOrFloat::evaluate($unixTimestamp);
    }
}
