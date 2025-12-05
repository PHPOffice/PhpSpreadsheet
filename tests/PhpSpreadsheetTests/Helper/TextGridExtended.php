<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Helper;

use PhpOffice\PhpSpreadsheet\Helper\TextGrid;

class TextGridExtended extends TextGrid
{
    protected function rightAlign(string $displayCell): bool
    {
        // regexp is imperfect, but good enough for test purposes
        return $this->numbersRight && preg_match('/^[-+$,.0-9]+$/', $displayCell);
    }
}
