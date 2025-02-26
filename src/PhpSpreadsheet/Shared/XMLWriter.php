<?php

namespace PhpOffice\PhpSpreadsheet\Shared;

use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;

class XMLWriter extends \XMLWriter
{
    public static bool $debugEnabled = false;

    /** Temporary storage method */
    const STORAGE_MEMORY = 1;
    const STORAGE_DISK = 2;

    /**
     * Temporary filename.
     */
    private string $tempFileName = '';

    /**
     * Create a new XMLWriter instance.
     *
     * @param int $temporaryStorage Temporary storage location
     * @param ?string $temporaryStorageFolder Temporary storage folder
     */
    public function __construct(int $temporaryStorage = self::STORAGE_MEMORY, ?string $temporaryStorageFolder = null)
    {
        // Open temporary storage
        if ($temporaryStorage == self::STORAGE_MEMORY) {
            $this->openMemory();
        } else {
            // Create temporary filename
            if ($temporaryStorageFolder === null) {
                $temporaryStorageFolder = File::sysGetTempDir();
            }
            $this->tempFileName = (string) @tempnam($temporaryStorageFolder, 'xml');

            // Open storage
            if (empty($this->tempFileName) || $this->openUri($this->tempFileName) === false) {
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
        // There is nothing reasonable to do if unlink fails.
        if ($this->tempFileName != '') {
            @unlink($this->tempFileName);
        }
    }

    public function __wakeup(): void
    {
        $this->tempFileName = '';

        throw new SpreadsheetException('Unserialize not permitted');
    }

    /**
     * Get written data.
     */
    public function getData(): string
    {
        if ($this->tempFileName == '') {
            return $this->outputMemory(true);
        }
        $this->flush();

        return file_get_contents($this->tempFileName) ?: '';
    }

    /**
     * Wrapper method for writeRaw.
     *
     * @param null|string|string[] $rawTextData
     */
    public function writeRawData($rawTextData): bool
    {
        if (is_array($rawTextData)) {
            $rawTextData = implode("\n", $rawTextData);
        }

        return $this->writeRaw(htmlspecialchars($rawTextData ?? ''));
    }
}
