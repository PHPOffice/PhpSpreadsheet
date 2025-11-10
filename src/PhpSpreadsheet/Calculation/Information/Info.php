<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Information;

use PhpOffice\PhpSpreadsheet\Cell\Cell;

class Info
{
    /**
     * INFO.
     *
     * Excel Function:
     *        =INFO(type_text)
     *
     * @param mixed $typeText String specifying the type of information to be returned
     * @param ?Cell $cell Cell from which spreadsheet information is retrieved
     *
     * @return int|string The requested information about the current operating environment
     */
    public static function getInfo(mixed $typeText = '', ?Cell $cell = null): int|string
    {
        return match (is_string($typeText) ? strtolower($typeText) : $typeText) {
            'numfile' => $cell?->getWorksheetOrNull()?->getParent()?->getSheetCount() ?? 1,
            'osversion' => 'PHP ' . PHP_VERSION,
            'recalc' => 'Automatic',
            'system' => 'PHP',
            default => ExcelError::VALUE(),
        };
    }
}
