<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx\Streaming;

use Composer\Pcre\Preg;
use DateTimeInterface;
use PhpOffice\PhpSpreadsheet\Cell\AddressRange;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Namespaces;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\FunctionPrefix;
use Throwable;
use XMLWriter;

class StreamingSheet
{
    private const TEMP_STREAM = 'php://temp/maxmemory:2097152';

    /** @var resource */
    private $stream;

    private XMLWriter $xmlWriter;

    private bool $headerWritten = false;

    private bool $finished = false;

    private bool $broken = false;

    private int $rowNumber = 0;

    private int $maxColumn = 0;

    /** @var array<int, float> */
    private array $columnWidths = [];

    private ?string $freezeCell = null;

    private bool $autoFilter = false;

    public function __construct(private StreamingWriter $writer)
    {
        $stream = fopen(self::TEMP_STREAM, 'wb+');
        if ($stream === false) {
            // @codeCoverageIgnoreStart
            throw new WriterException('Could not open temporary stream for sheet data.');
            // @codeCoverageIgnoreEnd
        }
        $this->stream = $stream;
        $this->xmlWriter = new XMLWriter();
        $this->xmlWriter->openMemory();
    }

    /**
     * Close sheetData and worksheet, and hand the temp stream to the writer.
     *
     * @return resource
     *
     * @internal called by StreamingWriter only
     */
    public function finish()
    {
        $this->assertUsable();
        if (!$this->headerWritten) {
            $this->writeHeader();
        }
        $this->finished = true;
        $this->writeAll('</sheetData>');
        if ($this->autoFilter && $this->rowNumber > 0 && $this->maxColumn > 0) {
            $range = 'A1:' . Coordinate::stringFromColumnIndex($this->maxColumn) . $this->rowNumber;
            $this->writeAll('<autoFilter ref="' . $range . '"/>');
        }
        $this->writeAll('</worksheet>');

        return $this->stream;
    }

    /** @internal Release an active sheet when the writer is aborted or fails. */
    public function discard(): void
    {
        $this->finished = true;
        if (is_resource($this->stream)) {
            fclose($this->stream);
        }
    }

    private function writeAll(string $data): void
    {
        $length = strlen($data);
        $offset = 0;

        try {
            while ($offset < $length) {
                $written = fwrite($this->stream, substr($data, $offset));
                if ($written === false || $written === 0) {
                    throw new WriterException('Could not write all sheet data to the temporary stream.');
                }
                $offset += $written;
            }
        } catch (Throwable $e) {
            $this->broken = true;

            throw $e;
        }
    }

    private function writeHeader(): void
    {
        $this->headerWritten = true;
        $this->writeAll('<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n");
        $this->writeAll('<worksheet xmlns="' . Namespaces::MAIN . '" xmlns:r="' . Namespaces::SCHEMA_OFFICE_DOCUMENT . '">');
        $paneXml = '';
        if ($this->freezeCell !== null) {
            [$paneColumn, $paneRow] = Coordinate::indexesFromString($this->freezeCell);
            $xSplit = $paneColumn - 1;
            $ySplit = $paneRow - 1;
            $activePane = ($xSplit > 0 && $ySplit > 0) ? 'bottomRight' : ($ySplit > 0 ? 'bottomLeft' : 'topRight');
            $paneXml = '<pane'
                . ($xSplit > 0 ? ' xSplit="' . $xSplit . '"' : '')
                . ($ySplit > 0 ? ' ySplit="' . $ySplit . '"' : '')
                . ' topLeftCell="' . $this->freezeCell . '" activePane="' . $activePane . '" state="frozen"/>';
        }
        $this->writeAll('<sheetViews><sheetView workbookViewId="0">' . $paneXml . '</sheetView></sheetViews>');
        if ($this->columnWidths !== []) {
            $cols = '<cols>';
            ksort($this->columnWidths);
            foreach ($this->columnWidths as $columnNumber => $width) {
                $cols .= '<col min="' . $columnNumber . '" max="' . $columnNumber . '" width="' . $width . '" customWidth="1"/>';
            }
            $cols .= '</cols>';
            $this->writeAll($cols);
        }
        $this->writeAll('<sheetData>');
    }

    private function assertUsable(): void
    {
        if ($this->broken) {
            throw new WriterException('A failed write left this sheet in an undefined state; discard this writer.');
        }
        if ($this->finished) {
            throw new WriterException('This sheet has been finished; use the sheet returned by the most recent startSheet().');
        }
    }

    /** @param mixed[] $cells */
    public function appendRow(array $cells, ?int $styleId = null): void
    {
        $this->assertUsable();
        if ($this->rowNumber >= AddressRange::MAX_ROW) {
            throw new WriterException('Cannot append more than ' . AddressRange::MAX_ROW . ' rows to an Xlsx sheet.');
        }
        if (count($cells) > AddressRange::MAX_COLUMN_INT) {
            throw new WriterException('Cannot append more than ' . AddressRange::MAX_COLUMN_INT . ' columns to an Xlsx row.');
        }
        if ($styleId !== null) {
            $this->assertStyleId($styleId);
        }

        try {
            if (!$this->headerWritten) {
                $this->writeHeader();
            }
            ++$this->rowNumber;
            $xmlWriter = $this->xmlWriter;
            $xmlWriter->startElement('row');
            $xmlWriter->writeAttribute('r', (string) $this->rowNumber);
            $column = 0;
            foreach ($cells as $value) {
                ++$column;
                if ($value === null && ($styleId === null || $styleId === 0)) {
                    continue;
                }
                $this->writeCell($column, $value, $styleId);
            }
            $this->maxColumn = max($this->maxColumn, $column);
            $xmlWriter->endElement(); // row
            $flushed = $xmlWriter->flush();
            if (!is_string($flushed)) {
                // @codeCoverageIgnoreStart
                throw new WriterException('Unexpected non-string result from XMLWriter::flush().');
                // @codeCoverageIgnoreEnd
            }
            $this->writeAll($flushed);
        } catch (Throwable $e) {
            $this->broken = true;
            $this->xmlWriter->flush(); // discard the unclosed <row> left behind by the failure
            if ($e instanceof WriterException) {
                throw $e;
            }

            throw new WriterException('Failed to write the row: ' . $e->getMessage(), 0, $e);
        }
    }

    private function writeCell(int $column, mixed $value, ?int $rowStyleId): void
    {
        $cellStyleId = $rowStyleId;
        $forcedType = null;
        if ($value instanceof StreamedCell) {
            if ($value->styleId !== null) {
                $this->assertStyleId($value->styleId);
                $cellStyleId = $value->styleId;
            }
            $forcedType = $value->dataType;
            if ($forcedType !== null && $forcedType !== DataType::TYPE_STRING && $forcedType !== DataType::TYPE_STRING2) {
                throw new WriterException("Unsupported StreamedCell data type '$forcedType'; only DataType::TYPE_STRING and DataType::TYPE_STRING2 are supported.");
            }
            $value = $value->value;
        }
        if ($value === null && ($cellStyleId === null || $cellStyleId === 0)) {
            return;
        }

        $isDate = $value instanceof DateTimeInterface;
        if ($isDate && $cellStyleId === null) {
            $cellStyleId = $this->writer->getDefaultDateStyleId();
        }

        $xmlWriter = $this->xmlWriter;
        $xmlWriter->startElement('c');
        $xmlWriter->writeAttribute('r', Coordinate::stringFromColumnIndex($column) . $this->rowNumber);
        if ($cellStyleId !== null && $cellStyleId !== 0) {
            $xmlWriter->writeAttribute('s', (string) $cellStyleId);
        }

        if ($value === null) {
            $xmlWriter->endElement(); // styled blank cell

            return;
        }
        if ($forcedType === DataType::TYPE_STRING || $forcedType === DataType::TYPE_STRING2) {
            $this->writeInlineString(is_scalar($value) ? (string) $value : $this->rejectValue($value));
        } elseif ($isDate) {
            $excelDate = Date::PHPToExcel($value, $this->writer->getExcelCalendar());
            if ($excelDate === false) {
                $this->rejectValue($value); // @codeCoverageIgnore
            }
            $xmlWriter->writeElement('v', self::formatNumber($excelDate));
        } elseif (is_bool($value)) {
            $xmlWriter->writeAttribute('t', 'b');
            $xmlWriter->writeElement('v', $value ? '1' : '0');
        } elseif (is_int($value) || is_float($value)) {
            if (is_float($value) && !is_finite($value)) {
                throw new WriterException('Cell value is not a finite number; NAN and INF cannot be stored in an Xlsx file.');
            }
            $xmlWriter->writeElement('v', self::formatNumber($value));
        } elseif (is_string($value)) {
            if (strlen($value) > 1 && $value[0] === '=') {
                if (!StringHelper::isUTF8($value)) {
                    throw new WriterException('Cell value is not valid UTF-8; writing it would corrupt the sheet XML.');
                }
                if (Preg::isMatch('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', $value)) {
                    throw new WriterException('Formula contains a control character that cannot be stored in XML.');
                }
                $this->writer->noteFormulaWritten();
                $xmlWriter->startElement('f');
                $xmlWriter->text(FunctionPrefix::addFunctionPrefixStripEquals($value));
                $xmlWriter->endElement(); // f
            } else {
                $this->writeInlineString($value);
            }
        } else {
            $this->rejectValue($value);
        }
        $xmlWriter->endElement(); // c
    }

    private function writeInlineString(string $value): void
    {
        if (!StringHelper::isUTF8($value)) {
            throw new WriterException('Cell value is not valid UTF-8; writing it would corrupt the sheet XML.');
        }
        if (mb_strlen($value, 'UTF-8') > DataType::MAX_STRING_LENGTH) {
            throw new WriterException('Cell string value exceeds the Excel limit of ' . DataType::MAX_STRING_LENGTH . ' characters.');
        }
        $xmlWriter = $this->xmlWriter;
        $xmlWriter->writeAttribute('t', 'inlineStr');
        $xmlWriter->startElement('is');
        $xmlWriter->startElement('t');
        if (trim($value) !== $value) {
            $xmlWriter->writeAttribute('xml:space', 'preserve');
        }
        $xmlWriter->text(StringHelper::controlCharacterPHP2OOXML($value));
        $xmlWriter->endElement(); // t
        $xmlWriter->endElement(); // is
    }

    /**
     * Same serialization as Writer\Xlsx\Worksheet::writeCellNumeric(): full
     * float precision, and a ".0" marker so integral floats read back as float.
     */
    private static function formatNumber(float|int $value): string
    {
        $result = StringHelper::convertToString($value);
        if (is_float($value) && !str_contains($result, '.')) {
            $result .= '.0';
        }

        return $result;
    }

    private function rejectValue(mixed $value): never
    {
        throw new WriterException('Unsupported cell value of type ' . get_debug_type($value) . '.');
    }

    private function assertStyleId(int $styleId): void
    {
        if (!$this->writer->isStyleIdRegistered($styleId)) {
            throw new WriterException("Style id $styleId has not been registered with registerStyle().");
        }
    }

    /** @param array<int, float> $widths 1-based column number => width */
    public function setColumnWidths(array $widths): void
    {
        $this->assertBeforeFirstRow('setColumnWidths');
        foreach ($widths as $columnNumber => $width) {
            if ($columnNumber < 1 || $columnNumber > AddressRange::MAX_COLUMN_INT) {
                throw new WriterException("Column number $columnNumber is invalid; column numbers are 1-based and cannot exceed " . AddressRange::MAX_COLUMN_INT . '.');
            }
            if (!is_finite($width) || $width <= 0 || $width > 255) {
                $label = is_nan($width) ? 'NAN' : (is_infinite($width) ? ($width > 0 ? 'INF' : '-INF') : (string) $width);

                throw new WriterException("Column width $label is invalid; width must be positive, finite and no greater than 255.");
            }
        }
        // Validate the whole request before replacing any existing widths.
        $this->columnWidths = array_replace($this->columnWidths, $widths);
    }

    public function freezePane(string $cell): void
    {
        $this->assertBeforeFirstRow('freezePane');

        try {
            [$column, $row] = Coordinate::indexesFromString($cell);
        } catch (Throwable $e) {
            throw new WriterException("Invalid freeze pane cell '$cell': " . $e->getMessage(), 0, $e);
        }
        if ($row < 1) {
            throw new WriterException("Invalid freeze pane cell '$cell': row numbers are 1-based.");
        }
        $normalized = Coordinate::stringFromColumnIndex($column) . $row;
        if ($normalized === 'A1') {
            return;
        }
        $this->freezeCell = $normalized;
    }

    public function setAutoFilterToWrittenRange(): void
    {
        $this->assertUsable();
        $this->autoFilter = true;
    }

    private function assertBeforeFirstRow(string $method): void
    {
        $this->assertUsable();
        if ($this->headerWritten) {
            throw new WriterException("$method() must be called before the first appendRow().");
        }
    }
}
