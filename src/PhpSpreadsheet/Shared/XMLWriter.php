<?php

namespace PhpOffice\PhpSpreadsheet\Shared;

use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;

class XMLWriter extends \XMLWriter
{
    public static $debugEnabled = false;

    /** Temporary storage method */
    const STORAGE_MEMORY = 1;
    const STORAGE_DISK = 2;

    /**
     * Temporary filename.
     *
     * @var string
     */
    private $tempFileName = '';

    /**
     * Whether temporary file will be unlink when class is destructed.
     *
     * @var bool
     */
    public $needUnlink = true;

    /**
     * Create a new XMLWriter instance.
     *
     * @param int $temporaryStorage Temporary storage location
     * @param string $temporaryStorageFolder Temporary storage folder
     */
    public function __construct($temporaryStorage = self::STORAGE_MEMORY, $temporaryStorageFolder = null, bool $specifyPath = false)
    {
        // Open temporary storage
        if ($temporaryStorage == self::STORAGE_MEMORY) {
            $this->openMemory();
        } else {
            // Create temporary filename
            if ($temporaryStorageFolder === null) {
                $temporaryStorageFolder = File::sysGetTempDir();
            }
            $this->tempFileName = $specifyPath ? $temporaryStorageFolder : @tempnam($temporaryStorageFolder, 'xml');
            if (empty($this->tempFileName)) {
                throw new WriterException('can not open an empty file');
            }

            // Open storage
            if ($this->openUri($this->tempFileName) === false) {
                // Fallback to memory...
                $this->openMemory();
            }
        }

        // Set default values
        if (self::$debugEnabled) {
            $this->setIndent(true);
        }
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        // Unlink temporary files
        if ($this->tempFileName != '' && $this->needUnlink) {
            @unlink($this->tempFileName);
        }
    }

    /**
     * Get written data.
     *
     * @return string
     */
    public function getData()
    {
        if ($this->tempFileName == '') {
            return $this->outputMemory(true);
        }
        $this->flush();

        return file_get_contents($this->tempFileName);
    }

    /**
     * Wrapper method for writeRaw.
     *
     * @param null|string|string[] $rawTextData
     *
     * @return bool
     */
    public function writeRawData($rawTextData)
    {
        if (is_array($rawTextData)) {
            $rawTextData = implode("\n", $rawTextData);
        }

        return $this->writeRaw(htmlspecialchars($rawTextData ?? ''));
    }
}
