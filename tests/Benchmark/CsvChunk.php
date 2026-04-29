<?php

namespace PhpOffice\PhpSpreadsheetBenchmarks;

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class CsvChunk extends Csv
{
    /**
     * Size of each chunk when streaming encoding conversion.
     * Aligned to a multiple of 4 so UTF-16/UTF-32 character
     * boundaries are never split.
     */
    private const CHUNK_SIZE = 65536;

    protected int $chunkSize = self::CHUNK_SIZE;

    public function setChunkSize(int $chunkSize): void
    {
        $this->chunkSize = $chunkSize;
    }

    /**
     * Convert file encoding to UTF-8 using chunked streaming to avoid
     * loading the entire file into memory at once.
     */
    protected function convertNonUtf8(string $filename): void
    {
        $sourceHandle = null;
        $encoding = strtoupper($this->inputEncoding);
        if ($encoding === 'UTF-16' || $encoding === 'UCS-2') {
            $sourceHandle = fopen($filename, 'rb');
            if ($sourceHandle === false) {
                $sourceHandle = null;
            } else {
                $first2 = (string) fread($sourceHandle, 2);
                if ($first2 === "\xfe\xff") {
                    $encoding .= 'BE';
                } elseif ($first2 === "\xff\xfe") {
                    $encoding .= 'LE';
                } else {
                    fclose($sourceHandle);
                    $sourceHandle = null;
                }
            }
        }
        if (str_starts_with($encoding, 'UTF-7') || $encoding === 'UTF-16' || $encoding === 'UCS-2' || $encoding === 'UTF-32' || $encoding === 'UCS-4') {
            parent::convertNonUtf8($filename);

            return;
        }
        fclose($this->fileHandle);
        if ($sourceHandle === null) {
            $sourceHandle = fopen($filename, 'rb');
        }
        // Using php://temp instead of php://memory: spills to disk when data
        // exceeds 2MB, reducing peak memory for large files.
        $outputHandle = fopen('php://temp', 'r+b');
        if ($sourceHandle === false || $outputHandle === false) {
            // @codeCoverageIgnoreStart
            if ($sourceHandle !== false) {
                fclose($sourceHandle);
            }
            if ($outputHandle !== false) {
                fclose($outputHandle);
            }

            throw new ReaderException("Failed to open file for encoding conversion: {$filename}");
            // @codeCoverageIgnoreEnd
        }

        if ($encoding === 'UTF-16BE') {
            $checkdigit = -2;
        } elseif ($encoding === 'UTF-16LE') {
            $checkdigit = -1;
        } else {
            $checkdigit = 0;
        }
        $charWidth = $this->encodingCharWidth($encoding);
        // Ensure chunk size is aligned to character width
        $chunkSize = $this->chunkSize - ($this->chunkSize % $charWidth);

        $leftover = '';
        while (!feof($sourceHandle)) {
            $rawChunk = fread($sourceHandle, max(1, $chunkSize));
            if ($rawChunk === false || $rawChunk === '') {
                break; // @codeCoverageIgnore
            }
            if ($checkdigit !== 0) {
                $last1 = substr($rawChunk, $checkdigit, 1);
                if (in_array($last1, ["\xd8", "\xd9", "\xda", "\xdb"], true)) {
                    $newChunk = fread($sourceHandle, 2);
                    if ($newChunk === false) {
                        break; // @codeCoverageIgnore
                    }
                    $rawChunk .= $newChunk;
                }
            }

            $chunk = $leftover . $rawChunk;
            $leftover = '';

            if ($charWidth > 1) {
                // For fixed-width multi-byte encodings (UTF-16, UTF-32),
                // ensure we don't split in the middle of a character
                $remainder = strlen($chunk) % $charWidth;
                if ($remainder !== 0) {
                    $leftover = substr($chunk, -$remainder);
                    $chunk = substr($chunk, 0, -$remainder);
                }
            }
                // For variable-width encodings (e.g. UTF-8 source, though
                // this path is for non-UTF-8), and single-byte encodings
                // (ISO-8859-*, CP1252), no boundary adjustment needed.
                // Single-byte encodings have 1:1 byte-to-character mapping.

            if ($chunk !== '') {
                $converted = StringHelper::convertEncoding($chunk, 'UTF-8', $encoding);
                fwrite($outputHandle, $converted);
            }
        }

        // Flush any remaining bytes (incomplete multi-byte chars will throw)
        if ($leftover !== '') {
            $converted = StringHelper::convertEncoding($leftover, 'UTF-8', $encoding);
            fwrite($outputHandle, $converted); // @codeCoverageIgnore
        }

        fclose($sourceHandle);
        $this->fileHandle = $outputHandle;
        $this->skipBOM();
    }

    /**
     * Return the byte width of a single character in the given encoding.
     * Returns 1 for variable-width or single-byte encodings.
     */
    private function encodingCharWidth(string $encoding): int
    {
        return match ($encoding) {
            'UTF-32BE', 'UTF-32LE', 'UCS-4BE', 'UCS-4LE' => 4, // UTF-32 and UCS-4 are processed in parent
            'UTF-16BE', 'UTF-16LE', 'UCS-2BE', 'UCS-2LE' => 2, // UTF-16 and UCS-2 are given BE/LE suffix above
            default => 1,
        };
    }
}
