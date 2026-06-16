<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Helper;

use PhpOffice\PhpSpreadsheet\Helper\TextGrid;
use PhpOffice\PhpSpreadsheet\Helper\TextGridRightAlign;

class TextGridExtended extends TextGrid
{
    protected function rightAlign(string $displayCell, mixed $cell = null): bool
    {
        // regexp is imperfect, but good enough for test purposes
        return is_int($cell) || is_float($cell) || ($this->numbersRight === TextGridRightAlign::numeric && preg_match('/^[-+$,.0-9]+$/', $displayCell));
    }
}
