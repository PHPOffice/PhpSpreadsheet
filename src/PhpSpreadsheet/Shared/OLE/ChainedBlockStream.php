<?php

namespace PhpOffice\PhpSpreadsheet\Shared\OLE;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Shared\OLE;

class ChainedBlockStream
{
    /** @var mixed */
    public $context;

    /**
     * The OLE container of the file that is being read.
     */
    public ?OLE $ole = null;

    /**
     * Parameters specified by fopen().
     *
     * @var mixed[]
     */
    public array $params = [];

    /**
     * The binary data of the file.
     */
    public string $data;

    /**
     * The file pointer.
     *
     * @var int byte offset
     */
    public int $pos = 0;

    /**
     * Implements support for fopen().
     * For creating streams using this wrapper, use OLE_PPS_File::getStream().
     *
     * @param string $path resource name including scheme, e.g.
     *                                    ole-chainedblockstream://oleInstanceId=1
     * @param string $mode only "r" is supported
     * @param int $options mask of STREAM_REPORT_ERRORS and STREAM_USE_PATH
     * @param ?string $openedPath absolute path of the opened stream (out parameter)
     *
     * @return bool true on success
     */
    public function stream_open(string $path, string $mode, int $options, ?string &$openedPath): bool // @codingStandardsIgnoreLine
    {
        if ($mode[0] !== 'r') {
            if ($options & STREAM_REPORT_ERRORS) {
                trigger_error('Only reading is supported', E_USER_WARNING);
            }

            return false;
        }

        // 25 is length of "ole-chainedblockstream://"
        parse_str(substr($path, 25), $this->params);
        if (!isset($this->params['oleInstanceId'], $this->params['blockId'], $GLOBALS['_OLE_INSTANCES'][$this->params['oleInstanceId']])) { //* @phpstan-ignore offsetAccess.nonOffsetAccessible (I don't know how to fix this)
            if ($options & STREAM_REPORT_ERRORS) {
                trigger_error('OLE stream not found', E_USER_WARNING);
            }

            return false;
        }
        $this->ole = $GLOBALS['_OLE_INSTANCES'][$this->params['oleInstanceId']]; //* @phpstan-ignore assign.propertyType (I don't know how to fix this)
        if (!($this->ole instanceof OLE)) { //* @phpstan-ignore instanceof.alwaysTrue (I don't know how to fix this)
            throw new Exception('class is not OLE');
        }

        $blockId = (int) $this->params['blockId'];
        $size = isset($this->params['size']) ? (int) $this->params['size'] : null;
        $isRoot = isset($this->params['isRoot']) && $this->params['isRoot'] === '1';
        $this->data = '';
        if ($size !== null && $size < $this->ole->bigBlockThreshold && !$isRoot) {
            // Block id refers to small blocks
            $rootData = '';
            if ($this->ole->root->startBlock === null) {
                throw new Exception('Invalid OLE root mini-stream chain.');
            }
            $rootBlockId = (int) $this->ole->root->startBlock;
            $rootBlocks = [];
            while ($rootBlockId !== -2) {
                if (isset($rootBlocks[$rootBlockId], $this->ole->bbat[$rootBlockId])) {
                    throw new Exception('Invalid OLE root mini-stream chain.');
                }
                $rootBlocks[$rootBlockId] = true;
                fseek($this->ole->_file_handle, $this->ole->getBlockOffset($rootBlockId));
                $rootData .= fread($this->ole->_file_handle, $this->ole->bigBlockSize);
                $rootBlockId = self::nextBlock($this->ole->bbat, $rootBlockId, 'Invalid OLE root mini-stream chain.');
            }

            $smallBlocks = [];
            while ($blockId != -2) {
                if (isset($smallBlocks[$blockId], $this->ole->sbat[$blockId])) {
                    throw new Exception('Invalid OLE mini-stream chain.');
                }
                $smallBlocks[$blockId] = true;
                $pos = $blockId * $this->ole->smallBlockSize;
                $this->data .= substr($rootData, $pos, $this->ole->smallBlockSize);
                $blockId = self::nextBlock($this->ole->sbat, $blockId, 'Invalid OLE mini-stream chain.');
            }
        } else {
            // Block id refers to big blocks
            $bigBlocks = [];
            while ($blockId != -2) {
                if (isset($bigBlocks[$blockId], $this->ole->bbat[$blockId])) {
                    throw new Exception('Invalid OLE stream chain.');
                }
                $bigBlocks[$blockId] = true;
                $pos = $this->ole->getBlockOffset($blockId);
                fseek($this->ole->_file_handle, $pos);
                $this->data .= fread($this->ole->_file_handle, $this->ole->bigBlockSize);
                $blockId = self::nextBlock($this->ole->bbat, $blockId, 'Invalid OLE stream chain.');
            }
        }
        if ($size !== null) {
            $this->data = substr($this->data, 0, $size);
        }

        if ($options & STREAM_USE_PATH) {
            $openedPath = $path;
        }

        return true;
    }

    /** @param mixed[] $allocationTable */
    private static function nextBlock(array $allocationTable, int $blockId, string $message): int
    {
        $nextBlockId = $allocationTable[$blockId] ?? null;
        if (!is_int($nextBlockId)) {
            throw new Exception($message);
        }

        return $nextBlockId;
    }

    /**
     * Implements support for fclose().
     */
    public function stream_close(): void // @codingStandardsIgnoreLine
    {
        $this->ole = null;
        unset($GLOBALS['_OLE_INSTANCES']);
    }

    /**
     * Implements support for fread(), fgets() etc.
     *
     * @param int $count maximum number of bytes to read
     *
     * @return false|string
     */
    public function stream_read(int $count): bool|string // @codingStandardsIgnoreLine
    {
        if ($this->stream_eof()) {
            return false;
        }
        $s = substr($this->data, (int) $this->pos, $count);
        $this->pos += $count;

        return $s;
    }

    /**
     * Implements support for feof().
     *
     * @return bool TRUE if the file pointer is at EOF; otherwise FALSE
     */
    public function stream_eof(): bool // @codingStandardsIgnoreLine
    {
        return $this->pos >= strlen($this->data);
    }

    /**
     * Returns the position of the file pointer, i.e. its offset into the file
     * stream. Implements support for ftell().
     */
    public function stream_tell(): int // @codingStandardsIgnoreLine
    {
        return $this->pos;
    }

    /**
     * Implements support for fseek().
     * Note that the first condition is always true, at least in
     * the unit test suite. One consequence is that Phpstan's
     * correct flagging of count($this->data) below is never
     * executed, and would fail should it be executed.
     *
     * @param int $offset byte offset
     * @param int $whence SEEK_SET, SEEK_CUR or SEEK_END
     */
    public function stream_seek(int $offset, int $whence): bool // @codingStandardsIgnoreLine
    {
        if ($whence == SEEK_SET && $offset >= 0) {
            $this->pos = $offset;
        } elseif ($whence == SEEK_CUR && -$offset <= $this->pos) {
            $this->pos += $offset;
        } elseif ($whence == SEEK_END && -$offset <= count($this->data)) { // @phpstan-ignore argument.type (phpstan is correct - see docBlock above)
            $this->pos = strlen($this->data) + $offset;
        } else {
            return false;
        }

        return true;
    }

    /**
     * Implements support for fstat(). Currently the only supported field is
     * "size".
     *
     * @return array{size: int}
     */
    public function stream_stat(): array // @codingStandardsIgnoreLine
    {
        return [
            'size' => strlen($this->data),
        ];
    }

    // Methods used by stream_wrapper_register() that are not implemented:
    // bool stream_flush ( void )
    // int stream_write ( string data )
    // bool rename ( string path_from, string path_to )
    // bool mkdir ( string path, int mode, int options )
    // bool rmdir ( string path, int options )
    // bool dir_opendir ( string path, int options )
    // array url_stat ( string path, int flags )
    // string dir_readdir ( void )
    // bool dir_rewinddir ( void )
    // bool dir_closedir ( void )
}
