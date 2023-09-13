<?php

namespace PhpOffice\PhpSpreadsheet\Shared;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Helper\Dimension;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Xls
{
    /**
     * Get the width of a column in pixels. We use the relationship y = ceil(7x) where
     * x is the width in intrinsic Excel units (measuring width in number of normal characters)
     * This holds for Arial 10.
     *
     * @param Worksheet $worksheet The sheet
     * @param string $col The column
     *
     * @return int The width in pixels
     */
    public static function sizeCol(Worksheet $worksheet, $col = 'A')
    {
        // default font of the workbook
        $font = $worksheet->getParentOrThrow()->getDefaultStyle()->getFont();

        $columnDimensions = $worksheet->getColumnDimensions();

        // first find the true column width in pixels (uncollapsed and unhidden)
        if (isset($columnDimensions[$col]) && $columnDimensions[$col]->getWidth() != -1) {
            // then we have column dimension with explicit width
            $columnDimension = $columnDimensions[$col];
            $width = $columnDimension->getWidth();
            $pixelWidth = Drawing::cellDimensionToPixels($width, $font);
        } elseif ($worksheet->getDefaultColumnDimension()->getWidth() != -1) {
            // then we have default column dimension with explicit width
            $defaultColumnDimension = $worksheet->getDefaultColumnDimension();
            $width = $defaultColumnDimension->getWidth();
            $pixelWidth = Drawing::cellDimensionToPixels($width, $font);
        } else {
            // we don't even have any default column dimension. Width depends on default font
            $pixelWidth = Font::getDefaultColumnWidthByFont($font, true);
        }

        // now find the effective column width in pixels
        if (isset($columnDimensions[$col]) && !$columnDimensions[$col]->getVisible()) {
            $effectivePixelWidth = 0;
        } else {
            $effectivePixelWidth = $pixelWidth;
        }

        return $effectivePixelWidth;
    }

    /**
     * Convert the height of a cell from user's units to pixels. By interpolation
     * the relationship is: y = 4/3x. If the height hasn't been set by the user we
     * use the default value. If the row is hidden we use a value of zero.
     *
     * @param Worksheet $worksheet The sheet
     * @param int $row The row index (1-based)
     *
     * @return int The width in pixels
     */
    public static function sizeRow(Worksheet $worksheet, $row = 1): int
    {
        // default font of the workbook
        $font = $worksheet->getParentOrThrow()->getDefaultStyle()->getFont();

        $rowDimensions = $worksheet->getRowDimensions();

        // first find the true row height in pixels (uncollapsed and unhidden)
        if (isset($rowDimensions[$row]) && $rowDimensions[$row]->getRowHeight() != -1) {
            // then we have a row dimension
            $rowDimension = $rowDimensions[$row];
            $rowHeight = $rowDimension->getRowHeight();
            $pixelRowHeight = (int) ceil(4 * $rowHeight / 3); // here we assume Arial 10
        } elseif ($worksheet->getDefaultRowDimension()->getRowHeight() != -1) {
            // then we have a default row dimension with explicit height
            $defaultRowDimension = $worksheet->getDefaultRowDimension();
            $pixelRowHeight = $defaultRowDimension->getRowHeight(Dimension::UOM_PIXELS);
        } else {
            // we don't even have any default row dimension. Height depends on default font
            $pointRowHeight = Font::getDefaultRowHeightByFont($font);
            $pixelRowHeight = Font::fontSizeToPixels((int) $pointRowHeight);
        }

        // now find the effective row height in pixels
        if (isset($rowDimensions[$row]) && !$rowDimensions[$row]->getVisible()) {
            $effectivePixelRowHeight = 0;
        } else {
            $effectivePixelRowHeight = $pixelRowHeight;
        }

        return (int) $effectivePixelRowHeight;
    }

    /**
     * Get the horizontal distance in pixels between two anchors
     * The distanceX is found as sum of all the spanning columns widths minus correction for the two offsets.
     *
     * @param string $startColumn
     * @param int $startOffsetX Offset within start cell measured in 1/1024 of the cell width
     * @param string $endColumn
     * @param int $endOffsetX Offset within end cell measured in 1/1024 of the cell width
     *
     * @return int Horizontal measured in pixels
     */
    public static function getDistanceX(Worksheet $worksheet, $startColumn = 'A', $startOffsetX = 0, $endColumn = 'A', $endOffsetX = 0): int
    {
        $distanceX = 0;

        // add the widths of the spanning columns
        $startColumnIndex = Coordinate::columnIndexFromString($startColumn);
        $endColumnIndex = Coordinate::columnIndexFromString($endColumn);
        for ($i = $startColumnIndex; $i <= $endColumnIndex; ++$i) {
            $distanceX += self::sizeCol($worksheet, Coordinate::stringFromColumnIndex($i));
        }

        // correct for offsetX in startcell
        $distanceX -= (int) floor(self::sizeCol($worksheet, $startColumn) * $startOffsetX / 1024);

        // correct for offsetX in endcell
        $distanceX -= (int) floor(self::sizeCol($worksheet, $endColumn) * (1 - $endOffsetX / 1024));

        return $distanceX;
    }

    /**
     * Get the vertical distance in pixels between two anchors
     * The distanceY is found as sum of all the spanning rows minus two offsets.
     *
     * @param int $startRow (1-based)
     * @param int $startOffsetY Offset within start cell measured in 1/256 of the cell height
     * @param int $endRow (1-based)
     * @param int $endOffsetY Offset within end cell measured in 1/256 of the cell height
     *
     * @return int Vertical distance measured in pixels
     */
    public static function getDistanceY(Worksheet $worksheet, $startRow = 1, $startOffsetY = 0, $endRow = 1, $endOffsetY = 0): int
    {
        $distanceY = 0;

        // add the widths of the spanning rows
        for ($row = $startRow; $row <= $endRow; ++$row) {
            $distanceY += self::sizeRow($worksheet, $row);
        }

        // correct for offsetX in startcell
        $distanceY -= (int) floor(self::sizeRow($worksheet, $startRow) * $startOffsetY / 256);

        // correct for offsetX in endcell
        $distanceY -= (int) floor(self::sizeRow($worksheet, $endRow) * (1 - $endOffsetY / 256));

        return $distanceY;
    }

    /**
     * Convert 1-cell anchor coordinates to 2-cell anchor coordinates
     * This function is ported from PEAR Spreadsheet_Writer_Excel with small modifications.
     *
     * Calculate the vertices that define the position of the image as required by
     * the OBJ record.
     *
     *         +------------+------------+
     *         |     A      |      B     |
     *   +-----+------------+------------+
     *   |     |(x1,y1)     |            |
     *   |  1  |(A1)._______|______      |
     *   |     |    |              |     |
     *   |     |    |              |     |
     *   +-----+----|    BITMAP    |-----+
     *   |     |    |              |     |
     *   |  2  |    |______________.     |
     *   |     |            |        (B2)|
     *   |     |            |     (x2,y2)|
     *   +---- +------------+------------+
     *
     * Example of a bitmap that covers some of the area from cell A1 to cell B2.
     *
     * Based on the width and height of the bitmap we need to calculate 8 vars:
     *     $col_start, $row_start, $col_end, $row_end, $x1, $y1, $x2, $y2.
     * The width and height of the cells are also variable and have to be taken into
     * account.
     * The values of $col_start and $row_start are passed in from the calling
     * function. The values of $col_end and $row_end are calculated by subtracting
     * the width and height of the bitmap from the width and height of the
     * underlying cells.
     * The vertices are expressed as a percentage of the underlying cell width as
     * follows (rhs values are in pixels):
     *
     *       x1 = X / W *1024
     *       y1 = Y / H *256
     *       x2 = (X-1) / W *1024
     *       y2 = (Y-1) / H *256
     *
     *       Where:  X is distance from the left side of the underlying cell
     *               Y is distance from the top of the underlying cell
     *               W is the width of the cell
     *               H is the height of the cell
     *
     * @param string $coordinates E.g. 'A1'
     * @param int $offsetX Horizontal offset in pixels
     * @param int $offsetY Vertical offset in pixels
     * @param int $width Width in pixels
     * @param int $height Height in pixels
     */
    public static function oneAnchor2twoAnchor(Worksheet $worksheet, string $coordinates, $offsetX, $offsetY, $width, $height): ?array
    {
        [$col_start, $row] = Coordinate::indexesFromString($coordinates);
        $row_start = $row - 1;

        $x1 = $offsetX;
        $y1 = $offsetY;

        // Initialise end cell to the same as the start cell
        $col_end = $col_start; // Col containing lower right corner of object
        $row_end = $row_start; // Row containing bottom right corner of object

        // Zero the specified offset if greater than the cell dimensions
        if ($x1 >= self::sizeCol($worksheet, Coordinate::stringFromColumnIndex($col_start))) {
            $x1 = 0;
        }
        if ($y1 >= self::sizeRow($worksheet, $row_start + 1)) {
            $y1 = 0;
        }

        $width = $width + $x1 - 1;
        $height = $height + $y1 - 1;

        // Subtract the underlying cell widths to find the end cell of the image
        while ($width >= self::sizeCol($worksheet, Coordinate::stringFromColumnIndex($col_end))) {
            $width -= self::sizeCol($worksheet, Coordinate::stringFromColumnIndex($col_end));
            ++$col_end;
        }

        // Subtract the underlying cell heights to find the end cell of the image
        while ($height >= self::sizeRow($worksheet, $row_end + 1)) {
            $height -= self::sizeRow($worksheet, $row_end + 1);
            ++$row_end;
        }

        // Bitmap isn't allowed to start or finish in a hidden cell, i.e. a cell
        // with zero height or width.
        if (self::sizeCol($worksheet, Coordinate::stringFromColumnIndex($col_start)) == 0) {
            return null;
        }
        if (self::sizeCol($worksheet, Coordinate::stringFromColumnIndex($col_end)) == 0) {
            return null;
        }
        if (self::sizeRow($worksheet, $row_start + 1) == 0) {
            return null;
        }
        if (self::sizeRow($worksheet, $row_end + 1) == 0) {
            return null;
        }

        // Convert the pixel values to the percentage value expected by Excel
        $x1 = $x1 / self::sizeCol($worksheet, Coordinate::stringFromColumnIndex($col_start)) * 1024;
        $y1 = $y1 / self::sizeRow($worksheet, $row_start + 1) * 256;
        $x2 = ($width + 1) / self::sizeCol($worksheet, Coordinate::stringFromColumnIndex($col_end)) * 1024; // Distance to right side of object
        $y2 = ($height + 1) / self::sizeRow($worksheet, $row_end + 1) * 256; // Distance to bottom of object

        $startCoordinates = Coordinate::stringFromColumnIndex($col_start) . ($row_start + 1);
        $endCoordinates = Coordinate::stringFromColumnIndex($col_end) . ($row_end + 1);

        return [
            'startCoordinates' => $startCoordinates,
            'startOffsetX' => $x1,
            'startOffsetY' => $y1,
            'endCoordinates' => $endCoordinates,
            'endOffsetX' => $x2,
            'endOffsetY' => $y2,
        ];
    }
}
