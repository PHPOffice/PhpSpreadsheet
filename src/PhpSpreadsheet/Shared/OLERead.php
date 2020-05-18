<?php

namespace PhpOffice\PhpSpreadsheet\Shared;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;

class OLERead
{
    private $data = '';

    // Size of a sector = 512 bytes
    const BIG_BLOCK_SIZE = 0x200;

    // Size of a short sector = 64 bytes
    const SMALL_BLOCK_SIZE = 0x40;

    // Size of a directory entry always = 128 bytes
    const PROPERTY_STORAGE_BLOCK_SIZE = 0x80;

    // Minimum size of a standard stream = 4096 bytes, streams smaller than this are stored as short streams
    const SMALL_BLOCK_THRESHOLD = 0x1000;

    // header offsets
    const NUM_BIG_BLOCK_DEPOT_BLOCKS_POS = 0x2c;
    const ROOT_START_BLOCK_POS = 0x30;
    const SMALL_BLOCK_DEPOT_BLOCK_POS = 0x3c;
    const EXTENSION_BLOCK_POS = 0x44;
    const NUM_EXTENSION_BLOCK_POS = 0x48;
    const BIG_BLOCK_DEPOT_BLOCKS_POS = 0x4c;

    // property storage offsets (directory offsets)
    const SIZE_OF_NAME_POS = 0x40;
    const TYPE_POS = 0x42;
    const START_BLOCK_POS = 0x74;
    const SIZE_POS = 0x78;

    public $wrkbook;

    public $summaryInformation;

    public $documentSummaryInformation;

    /**
     * @var int
     */
    private $numBigBlockDepotBlocks;

    /**
     * @var int
     */
    private $rootStartBlock;

    /**
     * @var int
     */
    private $sbdStartBlock;

    /**
     * @var int
     */
    private $extensionBlock;

    /**
     * @var int
     */
    private $numExtensionBlocks;

    /**
     * @var string
     */
    private $bigBlockChain;

    /**
     * @var string
     */
    private $smallBlockChain;

    /**
     * @var string
     */
    private $entry;

    /**
     * @var int
     */
    private $rootentry;

    /**
     * @var array
     */
    private $props = [];

    /**
     * Read the file.
     *
     * @param $pFilename string Filename
     */
    public function read($pFilename): void
    {
        File::assertFile($pFilename);

        // Get the file identifier
        // Don't bother reading the whole file until we know it's a valid OLE file
        $this->data = file_get_contents($pFilename, false, null, 0, 8);

        // Check OLE identifier
        $identifierOle = pack('CCCCCCCC', 0xd0, 0xcf, 0x11, 0xe0, 0xa1, 0xb1, 0x1a, 0xe1);
        if ($this->data != $identifierOle) {
            throw new ReaderException('The filename ' . $pFilename . ' is not recognised as an OLE file');
        }

        // Get the file data
        $this->data = file_get_contents($pFilename);

        // Total number of sectors used for the SAT
        $this->numBigBlockDepotBlocks = self::getInt4d($this->data, self::NUM_BIG_BLOCK_DEPOT_BLOCKS_POS);

        // SecID of the first sector of the directory stream
        $this->rootStartBlock = self::getInt4d($this->data, self::ROOT_START_BLOCK_POS);

        // SecID of the first sector of the SSAT (or -2 if not extant)
        $this->sbdStartBlock = self::getInt4d($this->data, self::SMALL_BLOCK_DEPOT_BLOCK_POS);

        // SecID of the first sector of the MSAT (or -2 if no additional sectors are used)
        $this->extensionBlock = self::getInt4d($this->data, self::EXTENSION_BLOCK_POS);

        // Total number of sectors used by MSAT
        $this->numExtensionBlocks = self::getInt4d($this->data, self::NUM_EXTENSION_BLOCK_POS);

        $bigBlockDepotBlocks = [];
        $pos = self::BIG_BLOCK_DEPOT_BLOCKS_POS;

        $bbdBlocks = $this->numBigBlockDepotBlocks;

        if ($this->numExtensionBlocks != 0) {
            $bbdBlocks = (self::BIG_BLOCK_SIZE - self::BIG_BLOCK_DEPOT_BLOCKS_POS) / 4;
        }

        for ($i = 0; $i < $bbdBlocks; ++$i) {
            $bigBlockDepotBlocks[$i] = self::getInt4d($this->data, $pos);
            $pos += 4;
        }

        for ($j = 0; $j < $this->numExtensionBlocks; ++$j) {
            $pos = ($this->extensionBlock + 1) * self::BIG_BLOCK_SIZE;
            $blocksToRead = min($this->numBigBlockDepotBlocks - $bbdBlocks, self::BIG_BLOCK_SIZE / 4 - 1);

            for ($i = $bbdBlocks; $i < $bbdBlocks + $blocksToRead; ++$i) {
                $bigBlockDepotBlocks[$i] = self::getInt4d($this->data, $pos);
                $pos += 4;
            }

            $bbdBlocks += $blocksToRead;
            if ($bbdBlocks < $this->numBigBlockDepotBlocks) {
                $this->extensionBlock = self::getInt4d($this->data, $pos);
            }
        }

        $pos = 0;
        $this->bigBlockChain = '';
        $bbs = self::BIG_BLOCK_SIZE / 4;
        for ($i = 0; $i < $this->numBigBlockDepotBlocks; ++$i) {
            $pos = ($bigBlockDepotBlocks[$i] + 1) * self::BIG_BLOCK_SIZE;

            $this->bigBlockChain .= substr($this->data, $pos, 4 * $bbs);
            $pos += 4 * $bbs;
        }

        $pos = 0;
        $sbdBlock = $this->sbdStartBlock;
        $this->smallBlockChain = '';
        while ($sbdBlock != -2) {
            $pos = ($sbdBlock + 1) * self::BIG_BLOCK_SIZE;

            $this->smallBlockChain .= substr($this->data, $pos, 4 * $bbs);
            $pos += 4 * $bbs;

            $sbdBlock = self::getInt4d($this->bigBlockChain, $sbdBlock * 4);
        }

        // read the directory stream
        $block = $this->rootStartBlock;
        $this->entry = $this->_readData($block);

        $this->readPropertySets();
    }

    /**
     * Extract binary stream data.
     *
     * @param int $stream
     *
     * @return string
     */
    public function getStream($stream)
    {
        if ($stream === null) {
            return null;
        }

        $streamData = '';

        if ($this->props[$stream]['size'] < self::SMALL_BLOCK_THRESHOLD) {
            $rootdata = $this->_readData($this->props[$this->rootentry]['startBlock']);

            $block = $this->props[$stream]['startBlock'];

            while ($block != -2) {
                $pos = $block * self::SMALL_BLOCK_SIZE;
                $streamData .= substr($rootdata, $pos, self::SMALL_BLOCK_SIZE);

                $block = self::getInt4d($this->smallBlockChain, $block * 4);
            }

            return $streamData;
        }
        $numBlocks = $this->props[$stream]['size'] / self::BIG_BLOCK_SIZE;
        if ($this->props[$stream]['size'] % self::BIG_BLOCK_SIZE != 0) {
            ++$numBlocks;
        }

        if ($numBlocks == 0) {
            return '';
        }

        $block = $this->props[$stream]['startBlock'];

        while ($block != -2) {
            $pos = ($block + 1) * self::BIG_BLOCK_SIZE;
            $streamData .= substr($this->data, $pos, self::BIG_BLOCK_SIZE);
            $block = self::getInt4d($this->bigBlockChain, $block * 4);
        }

        return $streamData;
    }

    /**
     * Read a standard stream (by joining sectors using information from SAT).
     *
     * @param int $bl Sector ID where the stream starts
     *
     * @return string Data for standard stream
     */
    private function _readData($bl)
    {
        $block = $bl;
        $data = '';

        while ($block != -2) {
            $pos = ($block + 1) * self::BIG_BLOCK_SIZE;
            $data .= substr($this->data, $pos, self::BIG_BLOCK_SIZE);
            $block = self::getInt4d($this->bigBlockChain, $block * 4);
        }

        return $data;
    }

    /**
     * Read entries in the directory stream.
     */
    private function readPropertySets(): void
    {
        $offset = 0;

        // loop through entires, each entry is 128 bytes
        $entryLen = strlen($this->entry);
        while ($offset < $entryLen) {
            // entry data (128 bytes)
            $d = substr($this->entry, $offset, self::PROPERTY_STORAGE_BLOCK_SIZE);

            // size in bytes of name
            $nameSize = ord($d[self::SIZE_OF_NAME_POS]) | (ord($d[self::SIZE_OF_NAME_POS + 1]) << 8);

            // type of entry
            $type = ord($d[self::TYPE_POS]);

            // sectorID of first sector or short sector, if this entry refers to a stream (the case with workbook)
            // sectorID of first sector of the short-stream container stream, if this entry is root entry
            $startBlock = self::getInt4d($d, self::START_BLOCK_POS);

            $size = self::getInt4d($d, self::SIZE_POS);

            $name = str_replace("\x00", '', substr($d, 0, $nameSize));

            $this->props[] = [
                'name' => $name,
                'type' => $type,
                'startBlock' => $startBlock,
                'size' => $size,
            ];

            // tmp helper to simplify checks
            $upName = strtoupper($name);

            // Workbook directory entry (BIFF5 uses Book, BIFF8 uses Workbook)
            if (($upName === 'WORKBOOK') || ($upName === 'BOOK')) {
                $this->wrkbook = count($this->props) - 1;
            } elseif ($upName === 'ROOT ENTRY' || $upName === 'R') {
                // Root entry
                $this->rootentry = count($this->props) - 1;
            }

            // Summary information
            if ($name == chr(5) . 'SummaryInformation') {
                $this->summaryInformation = count($this->props) - 1;
            }

            // Additional Document Summary information
            if ($name == chr(5) . 'DocumentSummaryInformation') {
                $this->documentSummaryInformation = count($this->props) - 1;
            }

            $offset += self::PROPERTY_STORAGE_BLOCK_SIZE;
        }
    }

    /**
     * Read 4 bytes of data at specified position.
     *
     * @param string $data
     * @param int $pos
     *
     * @return int
     */
    private static function getInt4d($data, $pos)
    {
        if ($pos < 0) {
            // Invalid position
            throw new ReaderException('Parameter pos=' . $pos . ' is invalid.');
        }

        $len = strlen($data);
        if ($len < $pos + 4) {
            $data .= str_repeat("\0", $pos + 4 - $len);
        }

        // FIX: represent numbers correctly on 64-bit system
        // http://sourceforge.net/tracker/index.php?func=detail&aid=1487372&group_id=99160&atid=623334
        // Changed by Andreas Rehm 2006 to ensure correct result of the <<24 block on 32 and 64bit systems
        $_or_24 = ord($data[$pos + 3]);
        if ($_or_24 >= 128) {
            // negative number
            $_ord_24 = -abs((256 - $_or_24) << 24);
        } else {
            $_ord_24 = ($_or_24 & 127) << 24;
        }

        return ord($data[$pos]) | (ord($data[$pos + 1]) << 8) | (ord($data[$pos + 2]) << 16) | $_ord_24;
    }
}
