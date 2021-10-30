<?php

namespace PhpOffice\PhpSpreadsheet\Shared;

use GdImage;
use SimpleXMLElement;

class Drawing
{
    /**
     * Convert pixels to EMU.
     *
     * @param int $pixelValue Value in pixels
     *
     * @return int Value in EMU
     */
    public static function pixelsToEMU($pixelValue)
    {
        return $pixelValue * 9525;
    }

    /**
     * Convert EMU to pixels.
     *
     * @param int|SimpleXMLElement $emuValue Value in EMU
     *
     * @return int Value in pixels
     */
    public static function EMUToPixels($emuValue)
    {
        $emuValue = (int) $emuValue;
        if ($emuValue != 0) {
            return (int) round($emuValue / 9525);
        }

        return 0;
    }

    /**
     * Convert pixels to column width. Exact algorithm not known.
     * By inspection of a real Excel file using Calibri 11, one finds 1000px ~ 142.85546875
     * This gives a conversion factor of 7. Also, we assume that pixels and font size are proportional.
     *
     * @param int $pixelValue Value in pixels
     * @param \PhpOffice\PhpSpreadsheet\Style\Font $pDefaultFont Default font of the workbook
     *
     * @return float|int Value in cell dimension
     */
    public static function pixelsToCellDimension($pixelValue, \PhpOffice\PhpSpreadsheet\Style\Font $pDefaultFont)
    {
        // Font name and size
        $name = $pDefaultFont->getName();
        $size = $pDefaultFont->getSize();

        if (isset(Font::$defaultColumnWidths[$name][$size])) {
            // Exact width can be determined
            return $pixelValue * Font::$defaultColumnWidths[$name][$size]['width']
                / Font::$defaultColumnWidths[$name][$size]['px'];
        }

        // We don't have data for this particular font and size, use approximation by
        // extrapolating from Calibri 11
        return $pixelValue * 11 * Font::$defaultColumnWidths['Calibri'][11]['width']
            / Font::$defaultColumnWidths['Calibri'][11]['px'] / $size;
    }

    /**
     * Convert column width from (intrinsic) Excel units to pixels.
     *
     * @param float $cellWidth Value in cell dimension
     * @param \PhpOffice\PhpSpreadsheet\Style\Font $pDefaultFont Default font of the workbook
     *
     * @return int Value in pixels
     */
    public static function cellDimensionToPixels($cellWidth, \PhpOffice\PhpSpreadsheet\Style\Font $pDefaultFont)
    {
        // Font name and size
        $name = $pDefaultFont->getName();
        $size = $pDefaultFont->getSize();

        if (isset(Font::$defaultColumnWidths[$name][$size])) {
            // Exact width can be determined
            $colWidth = $cellWidth * Font::$defaultColumnWidths[$name][$size]['px']
                / Font::$defaultColumnWidths[$name][$size]['width'];
        } else {
            // We don't have data for this particular font and size, use approximation by
            // extrapolating from Calibri 11
            $colWidth = $cellWidth * $size * Font::$defaultColumnWidths['Calibri'][11]['px']
                / Font::$defaultColumnWidths['Calibri'][11]['width'] / 11;
        }

        // Round pixels to closest integer
        $colWidth = (int) round($colWidth);

        return $colWidth;
    }

    /**
     * Convert pixels to points.
     *
     * @param int $pixelValue Value in pixels
     *
     * @return float Value in points
     */
    public static function pixelsToPoints($pixelValue)
    {
        return $pixelValue * 0.75;
    }

    /**
     * Convert points to pixels.
     *
     * @param int $pointValue Value in points
     *
     * @return int Value in pixels
     */
    public static function pointsToPixels($pointValue)
    {
        if ($pointValue != 0) {
            return (int) ceil($pointValue / 0.75);
        }

        return 0;
    }

    /**
     * Convert degrees to angle.
     *
     * @param int $pValue Degrees
     *
     * @return int Angle
     */
    public static function degreesToAngle($pValue)
    {
        return (int) round($pValue * 60000);
    }

    /**
     * Convert angle to degrees.
     *
     * @param int|SimpleXMLElement $pValue Angle
     *
     * @return int Degrees
     */
    public static function angleToDegrees($pValue)
    {
        $pValue = (int) $pValue;
        if ($pValue != 0) {
            return (int) round($pValue / 60000);
        }

        return 0;
    }

    /**
     * Create a new image from file. By alexander at alexauto dot nl.
     *
     * @see http://www.php.net/manual/en/function.imagecreatefromwbmp.php#86214
     *
     * @param string $p_sFile Path to Windows DIB (BMP) image
     *
     * @return GdImage|resource
     */
    public static function imagecreatefrombmp($p_sFile)
    {
        //    Load the image into a string
        $file = fopen($p_sFile, 'rb');
        $read = fread($file, 10);
        while (!feof($file) && ($read != '')) {
            $read .= fread($file, 1024);
        }

        $temp = unpack('H*', $read);
        $hex = $temp[1];
        $header = substr($hex, 0, 108);

        //    Process the header
        //    Structure: http://www.fastgraph.com/help/bmp_header_format.html
        $width = 0;
        $height = 0;
        if (substr($header, 0, 4) == '424d') {
            //    Cut it in parts of 2 bytes
            $header_parts = str_split($header, 2);

            //    Get the width        4 bytes
            $width = hexdec($header_parts[19] . $header_parts[18]);

            //    Get the height        4 bytes
            $height = hexdec($header_parts[23] . $header_parts[22]);

            //    Unset the header params
            unset($header_parts);
        }

        //    Define starting X and Y
        $x = 0;
        $y = 1;

        //    Create newimage
        $image = imagecreatetruecolor($width, $height);

        //    Grab the body from the image
        $body = substr($hex, 108);

        //    Calculate if padding at the end-line is needed
        //    Divided by two to keep overview.
        //    1 byte = 2 HEX-chars
        $body_size = (strlen($body) / 2);
        $header_size = ($width * $height);

        //    Use end-line padding? Only when needed
        $usePadding = ($body_size > ($header_size * 3) + 4);

        //    Using a for-loop with index-calculation instaid of str_split to avoid large memory consumption
        //    Calculate the next DWORD-position in the body
        for ($i = 0; $i < $body_size; $i += 3) {
            //    Calculate line-ending and padding
            if ($x >= $width) {
                // If padding needed, ignore image-padding
                // Shift i to the ending of the current 32-bit-block
                if ($usePadding) {
                    $i += $width % 4;
                }

                //    Reset horizontal position
                $x = 0;

                //    Raise the height-position (bottom-up)
                ++$y;

                //    Reached the image-height? Break the for-loop
                if ($y > $height) {
                    break;
                }
            }

            // Calculation of the RGB-pixel (defined as BGR in image-data)
            // Define $i_pos as absolute position in the body
            $i_pos = $i * 2;
            $r = hexdec($body[$i_pos + 4] . $body[$i_pos + 5]);
            $g = hexdec($body[$i_pos + 2] . $body[$i_pos + 3]);
            $b = hexdec($body[$i_pos] . $body[$i_pos + 1]);

            // Calculate and draw the pixel
            $color = imagecolorallocate($image, $r, $g, $b);
            imagesetpixel($image, $x, $height - $y, $color);

            // Raise the horizontal position
            ++$x;
        }

        // Unset the body / free the memory
        unset($body);

        //    Return image-object
        return $image;
    }
}
