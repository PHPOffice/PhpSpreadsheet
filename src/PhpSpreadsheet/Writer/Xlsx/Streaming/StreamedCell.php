<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx\Streaming;

/**
 * Immutable per-cell wrapper for StreamingSheet::appendRow(),
 * used to attach a style id or force a data type.
 */
final class StreamedCell
{
    public function __construct(
        public readonly mixed $value,
        public readonly ?int $styleId = null,
        public readonly ?string $dataType = null,
    ) {
    }
}
