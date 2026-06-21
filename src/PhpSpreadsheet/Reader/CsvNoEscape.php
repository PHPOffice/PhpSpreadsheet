<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;

class CsvNoEscape extends Csv
{
    /**
     * The character that can escape the enclosure.
     * Non-null-string will probably become unsupported in Php 9.
     * It must always be null-string in this class.
     */
    protected ?string $escapeCharacter = '';

    /**
     * Should not be needed in this class, but we will permit it.
     *
     * @var ?callable
     */
    protected static $constructorCallback;

    protected bool $testAutodetect = false;

    /**
     * Changing escape character not allowed in this class.
     */
    public function setEscapeCharacter(string $escapeCharacter, int $version = PHP_VERSION_ID): self
    {
        if ($escapeCharacter !== '') {
            throw new ReaderException('Escape character must be null string');
        }

        $this->escapeCharacter = $escapeCharacter;

        return $this;
    }

    /**
     * Changing auto-detect not allowed in this class.
     */
    public function setTestAutoDetect(bool $value): self
    {
        if ($value !== false) {
            throw new ReaderException('This class requires that testAutoDetect be false');
        }
        $this->testAutodetect = $value;

        return $this;
    }
}
