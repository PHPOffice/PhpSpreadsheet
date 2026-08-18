<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx\Streaming;

use DateTimeInterface;
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
        fwrite($this->stream, '</sheetData>');
        if ($this->autoFilter && $this->rowNumber > 0 && $this->maxColumn > 0) {
            $range = 'A1:' . Coordinate::stringFromColumnIndex($this->maxColumn) . $this->rowNumber;
            fwrite($this->stream, '<autoFilter ref="' . $range . '"/>');
        }
        fwrite($this->stream, '</worksheet>');

        return $this->stream;
    }

    private function writeHeader(): void
    {
        $this->headerWritten = true;
        fwrite($this->stream, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n");
        fwrite($this->stream, '<worksheet xmlns="' . Namespaces::MAIN . '" xmlns:r="' . Namespaces::SCHEMA_OFFICE_DOCUMENT . '">');
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
        fwrite($this->stream, '<sheetViews><sheetView workbookViewId="0">' . $paneXml . '</sheetView></sheetViews>');
        if ($this->columnWidths !== []) {
            $cols = '<cols>';
            ksort($this->columnWidths);
            foreach ($this->columnWidths as $columnNumber => $width) {
                $cols .= '<col min="' . $columnNumber . '" max="' . $columnNumber . '" width="' . $width . '" customWidth="1"/>';
            }
            $cols .= '</cols>';
            fwrite($this->stream, $cols);
        }
        fwrite($this->stream, '<sheetData>');
    }

    private function assertUsable(): void
    {
        if ($this->broken) {
            throw new WriterException('A failed appendRow() left this sheet in an undefined state; discard this writer.');
        }
        if ($this->finished) {
            throw new WriterException('This sheet has been finished; use the sheet returned by the most recent startSheet().');
        }
    }

    /** @param mixed[] $cells */
    public function appendRow(array $cells, ?int $styleId = null): void
    {
        $this->assertUsable();
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
                if ($value === null) {
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
            fwrite($this->stream, $flushed);
        } catch (Throwable $e) {
            $this->broken = true;
            $this->xmlWriter->flush(); // discard the unclosed <row> left behind by the failure

            throw $e;
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
            if ($value === null) {
                return;
            }
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

        if ($forcedType === DataType::TYPE_STRING || $forcedType === DataType::TYPE_STRING2) {
            $this->writeInlineString(is_scalar($value) ? (string) $value : $this->rejectValue($value));
        } elseif ($isDate) {
            $excelDate = Date::PHPToExcel($value);
            if ($excelDate === false) {
                $this->rejectValue($value); // @codeCoverageIgnore
            }
            $xmlWriter->writeElement('v', (string) $excelDate);
        } elseif (is_bool($value)) {
            $xmlWriter->writeAttribute('t', 'b');
            $xmlWriter->writeElement('v', $value ? '1' : '0');
        } elseif (is_int($value) || is_float($value)) {
            if (is_float($value) && !is_finite($value)) {
                throw new WriterException('Cell value is not a finite number; NAN and INF cannot be stored in an Xlsx file.');
            }
            $xmlWriter->writeElement('v', (string) $value);
        } elseif (is_string($value)) {
            if (!StringHelper::isUTF8($value)) {
                throw new WriterException('Cell value is not valid UTF-8; writing it would corrupt the sheet XML.');
            }
            if (strlen($value) > 1 && $value[0] === '=') {
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
            if ($columnNumber < 1) {
                throw new WriterException("Column number $columnNumber is invalid; column numbers are 1-based.");
            }
            if ($width <= 0) {
                throw new WriterException("Column width $width is invalid; width must be positive.");
            }
            $this->columnWidths[$columnNumber] = $width;
        }
    }

    public function freezePane(string $cell): void
    {
        $this->assertBeforeFirstRow('freezePane');
        if ($cell === 'A1') {
            return;
        }
        $this->freezeCell = $cell;
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
