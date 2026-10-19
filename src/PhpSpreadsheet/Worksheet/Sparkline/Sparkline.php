<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet\Sparkline;

use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use Stringable;

/**
 * A single sparkline: a miniature chart drawn inside one cell.
 *
 * In OOXML this is the `x14:sparkline` element, holding the source data range
 * (`xm:f`) and the cell it is drawn in (`xm:sqref`).
 */
class Sparkline implements Stringable
{
    /**
     * The cell the sparkline is drawn in, e.g. `G2`. Stored uppercased and
     * without any `$` absolute-reference markers.
     */
    private string $location = '';

    /**
     * The range supplying the data plotted by the sparkline, e.g. `Sheet1!B2:F2`
     * or `B2:F2`.
     */
    private string $dataRange = '';

    /**
     * @param CellAddress|string $location the cell the sparkline is drawn in (e.g. 'G2')
     * @param string $dataRange the range supplying the plotted data (e.g. 'B2:F2')
     */
    public function __construct(CellAddress|string $location = '', string $dataRange = '')
    {
        if ($location !== '') {
            $this->setLocation($location);
        }
        $this->setDataRange($dataRange);
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * Set the cell the sparkline is drawn in. Must be a single cell, not a range.
     */
    public function setLocation(CellAddress|string $location): self
    {
        $location = (string) $location;
        if (str_contains($location, ':')) {
            throw new PhpSpreadsheetException('Sparkline location must be a single cell, not a range');
        }
        // Normalise: strip absolute markers and any sheet qualifier.
        $location = str_replace('$', '', $location);
        if (str_contains($location, '!')) {
            $location = substr($location, strrpos($location, '!') + 1);
        }
        // Validate by attempting to resolve the coordinate.
        Coordinate::indexesFromString($location);
        $this->location = strtoupper($location);

        return $this;
    }

    public function getDataRange(): string
    {
        return $this->dataRange;
    }

    public function setDataRange(string $dataRange): self
    {
        $this->dataRange = $dataRange;

        return $this;
    }

    public function __toString(): string
    {
        return $this->location;
    }
}
