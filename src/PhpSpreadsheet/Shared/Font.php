<?php

namespace PhpOffice\PhpSpreadsheet\Shared;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class Font
{
    // Methods for resolving autosize value
    const AUTOSIZE_METHOD_APPROX = 'approx';
    const AUTOSIZE_METHOD_EXACT = 'exact';

    private static $autoSizeMethods = [
        self::AUTOSIZE_METHOD_APPROX,
        self::AUTOSIZE_METHOD_EXACT,
    ];

    /** Character set codes used by BIFF5-8 in Font records */
    const CHARSET_ANSI_LATIN = 0x00;
    const CHARSET_SYSTEM_DEFAULT = 0x01;
    const CHARSET_SYMBOL = 0x02;
    const CHARSET_APPLE_ROMAN = 0x4D;
    const CHARSET_ANSI_JAPANESE_SHIFTJIS = 0x80;
    const CHARSET_ANSI_KOREAN_HANGUL = 0x81;
    const CHARSET_ANSI_KOREAN_JOHAB = 0x82;
    const CHARSET_ANSI_CHINESE_SIMIPLIFIED = 0x86; //    gb2312
    const CHARSET_ANSI_CHINESE_TRADITIONAL = 0x88; //    big5
    const CHARSET_ANSI_GREEK = 0xA1;
    const CHARSET_ANSI_TURKISH = 0xA2;
    const CHARSET_ANSI_VIETNAMESE = 0xA3;
    const CHARSET_ANSI_HEBREW = 0xB1;
    const CHARSET_ANSI_ARABIC = 0xB2;
    const CHARSET_ANSI_BALTIC = 0xBA;
    const CHARSET_ANSI_CYRILLIC = 0xCC;
    const CHARSET_ANSI_THAI = 0xDD;
    const CHARSET_ANSI_LATIN_II = 0xEE;
    const CHARSET_OEM_LATIN_I = 0xFF;

    //  XXX: Constants created!
    /** Font filenames */
    const ARIAL = 'arial.ttf';
    const ARIAL_BOLD = 'arialbd.ttf';
    const ARIAL_ITALIC = 'ariali.ttf';
    const ARIAL_BOLD_ITALIC = 'arialbi.ttf';

    const CALIBRI = 'CALIBRI.TTF';
    const CALIBRI_BOLD = 'CALIBRIB.TTF';
    const CALIBRI_ITALIC = 'CALIBRII.TTF';
    const CALIBRI_BOLD_ITALIC = 'CALIBRIZ.TTF';

    const COMIC_SANS_MS = 'comic.ttf';
    const COMIC_SANS_MS_BOLD = 'comicbd.ttf';

    const COURIER_NEW = 'cour.ttf';
    const COURIER_NEW_BOLD = 'courbd.ttf';
    const COURIER_NEW_ITALIC = 'couri.ttf';
    const COURIER_NEW_BOLD_ITALIC = 'courbi.ttf';

    const GEORGIA = 'georgia.ttf';
    const GEORGIA_BOLD = 'georgiab.ttf';
    const GEORGIA_ITALIC = 'georgiai.ttf';
    const GEORGIA_BOLD_ITALIC = 'georgiaz.ttf';

    const IMPACT = 'impact.ttf';

    const LIBERATION_SANS = 'LiberationSans-Regular.ttf';
    const LIBERATION_SANS_BOLD = 'LiberationSans-Bold.ttf';
    const LIBERATION_SANS_ITALIC = 'LiberationSans-Italic.ttf';
    const LIBERATION_SANS_BOLD_ITALIC = 'LiberationSans-BoldItalic.ttf';

    const LUCIDA_CONSOLE = 'lucon.ttf';
    const LUCIDA_SANS_UNICODE = 'l_10646.ttf';

    const MICROSOFT_SANS_SERIF = 'micross.ttf';

    const PALATINO_LINOTYPE = 'pala.ttf';
    const PALATINO_LINOTYPE_BOLD = 'palab.ttf';
    const PALATINO_LINOTYPE_ITALIC = 'palai.ttf';
    const PALATINO_LINOTYPE_BOLD_ITALIC = 'palabi.ttf';

    const SYMBOL = 'symbol.ttf';

    const TAHOMA = 'tahoma.ttf';
    const TAHOMA_BOLD = 'tahomabd.ttf';

    const TIMES_NEW_ROMAN = 'times.ttf';
    const TIMES_NEW_ROMAN_BOLD = 'timesbd.ttf';
    const TIMES_NEW_ROMAN_ITALIC = 'timesi.ttf';
    const TIMES_NEW_ROMAN_BOLD_ITALIC = 'timesbi.ttf';

    const TREBUCHET_MS = 'trebuc.ttf';
    const TREBUCHET_MS_BOLD = 'trebucbd.ttf';
    const TREBUCHET_MS_ITALIC = 'trebucit.ttf';
    const TREBUCHET_MS_BOLD_ITALIC = 'trebucbi.ttf';

    const VERDANA = 'verdana.ttf';
    const VERDANA_BOLD = 'verdanab.ttf';
    const VERDANA_ITALIC = 'verdanai.ttf';
    const VERDANA_BOLD_ITALIC = 'verdanaz.ttf';

    /**
     * AutoSize method.
     *
     * @var string
     */
    private static $autoSizeMethod = self::AUTOSIZE_METHOD_APPROX;

    /**
     * Path to folder containing TrueType font .ttf files.
     *
     * @var string
     */
    private static $trueTypeFontPath = null;

    /**
     * How wide is a default column for a given default font and size?
     * Empirical data found by inspecting real Excel files and reading off the pixel width
     * in Microsoft Office Excel 2007.
     *
     * @var array
     */
    public static $defaultColumnWidths = [
        'Arial' => [
            1 => ['px' => 24, 'width' => 12.00000000],
            2 => ['px' => 24, 'width' => 12.00000000],
            3 => ['px' => 32, 'width' => 10.66406250],
            4 => ['px' => 32, 'width' => 10.66406250],
            5 => ['px' => 40, 'width' => 10.00000000],
            6 => ['px' => 48, 'width' => 9.59765625],
            7 => ['px' => 48, 'width' => 9.59765625],
            8 => ['px' => 56, 'width' => 9.33203125],
            9 => ['px' => 64, 'width' => 9.14062500],
            10 => ['px' => 64, 'width' => 9.14062500],
        ],
        'Calibri' => [
            1 => ['px' => 24, 'width' => 12.00000000],
            2 => ['px' => 24, 'width' => 12.00000000],
            3 => ['px' => 32, 'width' => 10.66406250],
            4 => ['px' => 32, 'width' => 10.66406250],
            5 => ['px' => 40, 'width' => 10.00000000],
            6 => ['px' => 48, 'width' => 9.59765625],
            7 => ['px' => 48, 'width' => 9.59765625],
            8 => ['px' => 56, 'width' => 9.33203125],
            9 => ['px' => 56, 'width' => 9.33203125],
            10 => ['px' => 64, 'width' => 9.14062500],
            11 => ['px' => 64, 'width' => 9.14062500],
        ],
        'Verdana' => [
            1 => ['px' => 24, 'width' => 12.00000000],
            2 => ['px' => 24, 'width' => 12.00000000],
            3 => ['px' => 32, 'width' => 10.66406250],
            4 => ['px' => 32, 'width' => 10.66406250],
            5 => ['px' => 40, 'width' => 10.00000000],
            6 => ['px' => 48, 'width' => 9.59765625],
            7 => ['px' => 48, 'width' => 9.59765625],
            8 => ['px' => 64, 'width' => 9.14062500],
            9 => ['px' => 72, 'width' => 9.00000000],
            10 => ['px' => 72, 'width' => 9.00000000],
        ],
    ];

    /**
     * Set autoSize method.
     *
     * @param string $pValue see self::AUTOSIZE_METHOD_*
     *
     * @return bool Success or failure
     */
    public static function setAutoSizeMethod($pValue)
    {
        if (!in_array($pValue, self::$autoSizeMethods)) {
            return false;
        }
        self::$autoSizeMethod = $pValue;

        return true;
    }

    /**
     * Get autoSize method.
     *
     * @return string
     */
    public static function getAutoSizeMethod()
    {
        return self::$autoSizeMethod;
    }

    /**
     * Set the path to the folder containing .ttf files. There should be a trailing slash.
     * Typical locations on variout some platforms:
     *    <ul>
     *        <li>C:/Windows/Fonts/</li>
     *        <li>/usr/share/fonts/truetype/</li>
     *        <li>~/.fonts/</li>
     * </ul>.
     *
     * @param string $pValue
     */
    public static function setTrueTypeFontPath($pValue): void
    {
        self::$trueTypeFontPath = $pValue;
    }

    /**
     * Get the path to the folder containing .ttf files.
     *
     * @return string
     */
    public static function getTrueTypeFontPath()
    {
        return self::$trueTypeFontPath;
    }

    /**
     * Calculate an (approximate) OpenXML column width, based on font size and text contained.
     *
     * @param \PhpOffice\PhpSpreadsheet\Style\Font $font Font object
     * @param RichText|string $cellText Text to calculate width
     * @param int $rotation Rotation angle
     * @param null|\PhpOffice\PhpSpreadsheet\Style\Font $defaultFont Font object
     *
     * @return int Column width
     */
    public static function calculateColumnWidth(\PhpOffice\PhpSpreadsheet\Style\Font $font, $cellText = '', $rotation = 0, ?\PhpOffice\PhpSpreadsheet\Style\Font $defaultFont = null)
    {
        // If it is rich text, use plain text
        if ($cellText instanceof RichText) {
            $cellText = $cellText->getPlainText();
        }

        // Special case if there are one or more newline characters ("\n")
        if (strpos($cellText, "\n") !== false) {
            $lineTexts = explode("\n", $cellText);
            $lineWidths = [];
            foreach ($lineTexts as $lineText) {
                $lineWidths[] = self::calculateColumnWidth($font, $lineText, $rotation = 0, $defaultFont);
            }

            return max($lineWidths); // width of longest line in cell
        }

        // Try to get the exact text width in pixels
        $approximate = self::$autoSizeMethod == self::AUTOSIZE_METHOD_APPROX;
        if (!$approximate) {
            $columnWidthAdjust = ceil(self::getTextWidthPixelsExact('n', $font, 0) * 1.07);

            try {
                // Width of text in pixels excl. padding
                // and addition because Excel adds some padding, just use approx width of 'n' glyph
                $columnWidth = self::getTextWidthPixelsExact($cellText, $font, $rotation) + $columnWidthAdjust;
            } catch (PhpSpreadsheetException $e) {
                $approximate = true;
            }
        }

        if ($approximate) {
            $columnWidthAdjust = self::getTextWidthPixelsApprox('n', $font, 0);
            // Width of text in pixels excl. padding, approximation
            // and addition because Excel adds some padding, just use approx width of 'n' glyph
            $columnWidth = self::getTextWidthPixelsApprox($cellText, $font, $rotation) + $columnWidthAdjust;
        }

        // Convert from pixel width to column width
        $columnWidth = Drawing::pixelsToCellDimension($columnWidth, $defaultFont);

        // Return
        return round($columnWidth, 6);
    }

    /**
     * Get GD text width in pixels for a string of text in a certain font at a certain rotation angle.
     *
     * @param string $text
     * @param \PhpOffice\PhpSpreadsheet\Style\Font
     * @param int $rotation
     *
     * @return int
     */
    public static function getTextWidthPixelsExact($text, \PhpOffice\PhpSpreadsheet\Style\Font $font, $rotation = 0)
    {
        if (!function_exists('imagettfbbox')) {
            throw new PhpSpreadsheetException('GD library needs to be enabled');
        }

        // font size should really be supplied in pixels in GD2,
        // but since GD2 seems to assume 72dpi, pixels and points are the same
        $fontFile = self::getTrueTypeFontFileFromFont($font);
        $textBox = imagettfbbox($font->getSize(), $rotation, $fontFile, $text);

        // Get corners positions
        $lowerLeftCornerX = $textBox[0];
        $lowerRightCornerX = $textBox[2];
        $upperRightCornerX = $textBox[4];
        $upperLeftCornerX = $textBox[6];

        // Consider the rotation when calculating the width
        return max($lowerRightCornerX - $upperLeftCornerX, $upperRightCornerX - $lowerLeftCornerX);
    }

    /**
     * Get approximate width in pixels for a string of text in a certain font at a certain rotation angle.
     *
     * @param string $columnText
     * @param int $rotation
     *
     * @return int Text width in pixels (no padding added)
     */
    public static function getTextWidthPixelsApprox($columnText, \PhpOffice\PhpSpreadsheet\Style\Font $font, $rotation = 0)
    {
        $fontName = $font->getName();
        $fontSize = $font->getSize();

        // Calculate column width in pixels. We assume fixed glyph width. Result varies with font name and size.
        switch ($fontName) {
            case 'Calibri':
                // value 8.26 was found via interpolation by inspecting real Excel files with Calibri 11 font.
                $columnWidth = (int) (8.26 * StringHelper::countCharacters($columnText));
                $columnWidth = $columnWidth * $fontSize / 11; // extrapolate from font size
                break;
            case 'Arial':
                // value 8 was set because of experience in different exports at Arial 10 font.
                $columnWidth = (int) (8 * StringHelper::countCharacters($columnText));
                $columnWidth = $columnWidth * $fontSize / 10; // extrapolate from font size
                break;
            case 'Verdana':
                // value 8 was found via interpolation by inspecting real Excel files with Verdana 10 font.
                $columnWidth = (int) (8 * StringHelper::countCharacters($columnText));
                $columnWidth = $columnWidth * $fontSize / 10; // extrapolate from font size
                break;
            default:
                // just assume Calibri
                $columnWidth = (int) (8.26 * StringHelper::countCharacters($columnText));
                $columnWidth = $columnWidth * $fontSize / 11; // extrapolate from font size
                break;
        }

        // Calculate approximate rotated column width
        if ($rotation !== 0) {
            if ($rotation == -165) {
                // stacked text
                $columnWidth = 4; // approximation
            } else {
                // rotated text
                $columnWidth = $columnWidth * cos(deg2rad($rotation))
                                + $fontSize * abs(sin(deg2rad($rotation))) / 5; // approximation
            }
        }

        // pixel width is an integer
        return (int) $columnWidth;
    }

    /**
     * Calculate an (approximate) pixel size, based on a font points size.
     *
     * @param int $fontSizeInPoints Font size (in points)
     *
     * @return int Font size (in pixels)
     */
    public static function fontSizeToPixels($fontSizeInPoints)
    {
        return (int) ((4 / 3) * $fontSizeInPoints);
    }

    /**
     * Calculate an (approximate) pixel size, based on inch size.
     *
     * @param int $sizeInInch Font size (in inch)
     *
     * @return int Size (in pixels)
     */
    public static function inchSizeToPixels($sizeInInch)
    {
        return $sizeInInch * 96;
    }

    /**
     * Calculate an (approximate) pixel size, based on centimeter size.
     *
     * @param int $sizeInCm Font size (in centimeters)
     *
     * @return float Size (in pixels)
     */
    public static function centimeterSizeToPixels($sizeInCm)
    {
        return $sizeInCm * 37.795275591;
    }

    /**
     * Returns the font path given the font.
     *
     * @param \PhpOffice\PhpSpreadsheet\Style\Font $font
     *
     * @return string Path to TrueType font file
     */
    public static function getTrueTypeFontFileFromFont($font)
    {
        if (!file_exists(self::$trueTypeFontPath) || !is_dir(self::$trueTypeFontPath)) {
            throw new PhpSpreadsheetException('Valid directory to TrueType Font files not specified');
        }

        $name = $font->getName();
        $bold = $font->getBold();
        $italic = $font->getItalic();

        // Check if we can map font to true type font file
        switch ($name) {
            case 'Arial':
                $fontFile = (
                    $bold ? ($italic ? self::ARIAL_BOLD_ITALIC : self::ARIAL_BOLD)
                          : ($italic ? self::ARIAL_ITALIC : self::ARIAL)
                );

                break;
            case 'Calibri':
                $fontFile = (
                    $bold ? ($italic ? self::CALIBRI_BOLD_ITALIC : self::CALIBRI_BOLD)
                          : ($italic ? self::CALIBRI_ITALIC : self::CALIBRI)
                );

                break;
            case 'Courier New':
                $fontFile = (
                    $bold ? ($italic ? self::COURIER_NEW_BOLD_ITALIC : self::COURIER_NEW_BOLD)
                          : ($italic ? self::COURIER_NEW_ITALIC : self::COURIER_NEW)
                );

                break;
            case 'Comic Sans MS':
                $fontFile = (
                    $bold ? self::COMIC_SANS_MS_BOLD : self::COMIC_SANS_MS
                );

                break;
            case 'Georgia':
                $fontFile = (
                    $bold ? ($italic ? self::GEORGIA_BOLD_ITALIC : self::GEORGIA_BOLD)
                          : ($italic ? self::GEORGIA_ITALIC : self::GEORGIA)
                );

                break;
            case 'Impact':
                $fontFile = self::IMPACT;

                break;
            case 'Liberation Sans':
                $fontFile = (
                    $bold ? ($italic ? self::LIBERATION_SANS_BOLD_ITALIC : self::LIBERATION_SANS_BOLD)
                          : ($italic ? self::LIBERATION_SANS_ITALIC : self::LIBERATION_SANS)
                );

                break;
            case 'Lucida Console':
                $fontFile = self::LUCIDA_CONSOLE;

                break;
            case 'Lucida Sans Unicode':
                $fontFile = self::LUCIDA_SANS_UNICODE;

                break;
            case 'Microsoft Sans Serif':
                $fontFile = self::MICROSOFT_SANS_SERIF;

                break;
            case 'Palatino Linotype':
                $fontFile = (
                    $bold ? ($italic ? self::PALATINO_LINOTYPE_BOLD_ITALIC : self::PALATINO_LINOTYPE_BOLD)
                          : ($italic ? self::PALATINO_LINOTYPE_ITALIC : self::PALATINO_LINOTYPE)
                );

                break;
            case 'Symbol':
                $fontFile = self::SYMBOL;

                break;
            case 'Tahoma':
                $fontFile = (
                    $bold ? self::TAHOMA_BOLD : self::TAHOMA
                );

                break;
            case 'Times New Roman':
                $fontFile = (
                    $bold ? ($italic ? self::TIMES_NEW_ROMAN_BOLD_ITALIC : self::TIMES_NEW_ROMAN_BOLD)
                          : ($italic ? self::TIMES_NEW_ROMAN_ITALIC : self::TIMES_NEW_ROMAN)
                );

                break;
            case 'Trebuchet MS':
                $fontFile = (
                    $bold ? ($italic ? self::TREBUCHET_MS_BOLD_ITALIC : self::TREBUCHET_MS_BOLD)
                          : ($italic ? self::TREBUCHET_MS_ITALIC : self::TREBUCHET_MS)
                );

                break;
            case 'Verdana':
                $fontFile = (
                    $bold ? ($italic ? self::VERDANA_BOLD_ITALIC : self::VERDANA_BOLD)
                          : ($italic ? self::VERDANA_ITALIC : self::VERDANA)
                );

                break;
            default:
                throw new PhpSpreadsheetException('Unknown font name "' . $name . '". Cannot map to TrueType font file');

                break;
        }

        $fontFile = self::$trueTypeFontPath . $fontFile;

        // Check if file actually exists
        if (!file_exists($fontFile)) {
            throw new PhpSpreadsheetException('TrueType Font file not found');
        }

        return $fontFile;
    }

    /**
     * Returns the associated charset for the font name.
     *
     * @param string $name Font name
     *
     * @return int Character set code
     */
    public static function getCharsetFromFontName($name)
    {
        switch ($name) {
            // Add more cases. Check FONT records in real Excel files.
            case 'EucrosiaUPC':
                return self::CHARSET_ANSI_THAI;
            case 'Wingdings':
                return self::CHARSET_SYMBOL;
            case 'Wingdings 2':
                return self::CHARSET_SYMBOL;
            case 'Wingdings 3':
                return self::CHARSET_SYMBOL;
            default:
                return self::CHARSET_ANSI_LATIN;
        }
    }

    /**
     * Get the effective column width for columns without a column dimension or column with width -1
     * For example, for Calibri 11 this is 9.140625 (64 px).
     *
     * @param \PhpOffice\PhpSpreadsheet\Style\Font $font The workbooks default font
     * @param bool $pPixels true = return column width in pixels, false = return in OOXML units
     *
     * @return mixed Column width
     */
    public static function getDefaultColumnWidthByFont(\PhpOffice\PhpSpreadsheet\Style\Font $font, $pPixels = false)
    {
        if (isset(self::$defaultColumnWidths[$font->getName()][$font->getSize()])) {
            // Exact width can be determined
            $columnWidth = $pPixels ?
                self::$defaultColumnWidths[$font->getName()][$font->getSize()]['px']
                    : self::$defaultColumnWidths[$font->getName()][$font->getSize()]['width'];
        } else {
            // We don't have data for this particular font and size, use approximation by
            // extrapolating from Calibri 11
            $columnWidth = $pPixels ?
                self::$defaultColumnWidths['Calibri'][11]['px']
                    : self::$defaultColumnWidths['Calibri'][11]['width'];
            $columnWidth = $columnWidth * $font->getSize() / 11;

            // Round pixels to closest integer
            if ($pPixels) {
                $columnWidth = (int) round($columnWidth);
            }
        }

        return $columnWidth;
    }

    /**
     * Get the effective row height for rows without a row dimension or rows with height -1
     * For example, for Calibri 11 this is 15 points.
     *
     * @param \PhpOffice\PhpSpreadsheet\Style\Font $font The workbooks default font
     *
     * @return float Row height in points
     */
    public static function getDefaultRowHeightByFont(\PhpOffice\PhpSpreadsheet\Style\Font $font)
    {
        switch ($font->getName()) {
            case 'Arial':
                switch ($font->getSize()) {
                    case 10:
                        // inspection of Arial 10 workbook says 12.75pt ~17px
                        $rowHeight = 12.75;

                        break;
                    case 9:
                        // inspection of Arial 9 workbook says 12.00pt ~16px
                        $rowHeight = 12;

                        break;
                    case 8:
                        // inspection of Arial 8 workbook says 11.25pt ~15px
                        $rowHeight = 11.25;

                        break;
                    case 7:
                        // inspection of Arial 7 workbook says 9.00pt ~12px
                        $rowHeight = 9;

                        break;
                    case 6:
                    case 5:
                        // inspection of Arial 5,6 workbook says 8.25pt ~11px
                        $rowHeight = 8.25;

                        break;
                    case 4:
                        // inspection of Arial 4 workbook says 6.75pt ~9px
                        $rowHeight = 6.75;

                        break;
                    case 3:
                        // inspection of Arial 3 workbook says 6.00pt ~8px
                        $rowHeight = 6;

                        break;
                    case 2:
                    case 1:
                        // inspection of Arial 1,2 workbook says 5.25pt ~7px
                        $rowHeight = 5.25;

                        break;
                    default:
                        // use Arial 10 workbook as an approximation, extrapolation
                        $rowHeight = 12.75 * $font->getSize() / 10;

                        break;
                }

                break;
            case 'Calibri':
                switch ($font->getSize()) {
                    case 11:
                        // inspection of Calibri 11 workbook says 15.00pt ~20px
                        $rowHeight = 15;

                        break;
                    case 10:
                        // inspection of Calibri 10 workbook says 12.75pt ~17px
                        $rowHeight = 12.75;

                        break;
                    case 9:
                        // inspection of Calibri 9 workbook says 12.00pt ~16px
                        $rowHeight = 12;

                        break;
                    case 8:
                        // inspection of Calibri 8 workbook says 11.25pt ~15px
                        $rowHeight = 11.25;

                        break;
                    case 7:
                        // inspection of Calibri 7 workbook says 9.00pt ~12px
                        $rowHeight = 9;

                        break;
                    case 6:
                    case 5:
                        // inspection of Calibri 5,6 workbook says 8.25pt ~11px
                        $rowHeight = 8.25;

                        break;
                    case 4:
                        // inspection of Calibri 4 workbook says 6.75pt ~9px
                        $rowHeight = 6.75;

                        break;
                    case 3:
                        // inspection of Calibri 3 workbook says 6.00pt ~8px
                        $rowHeight = 6.00;

                        break;
                    case 2:
                    case 1:
                        // inspection of Calibri 1,2 workbook says 5.25pt ~7px
                        $rowHeight = 5.25;

                        break;
                    default:
                        // use Calibri 11 workbook as an approximation, extrapolation
                        $rowHeight = 15 * $font->getSize() / 11;

                        break;
                }

                break;
            case 'Verdana':
                switch ($font->getSize()) {
                    case 10:
                        // inspection of Verdana 10 workbook says 12.75pt ~17px
                        $rowHeight = 12.75;

                        break;
                    case 9:
                        // inspection of Verdana 9 workbook says 11.25pt ~15px
                        $rowHeight = 11.25;

                        break;
                    case 8:
                        // inspection of Verdana 8 workbook says 10.50pt ~14px
                        $rowHeight = 10.50;

                        break;
                    case 7:
                        // inspection of Verdana 7 workbook says 9.00pt ~12px
                        $rowHeight = 9.00;

                        break;
                    case 6:
                    case 5:
                        // inspection of Verdana 5,6 workbook says 8.25pt ~11px
                        $rowHeight = 8.25;

                        break;
                    case 4:
                        // inspection of Verdana 4 workbook says 6.75pt ~9px
                        $rowHeight = 6.75;

                        break;
                    case 3:
                        // inspection of Verdana 3 workbook says 6.00pt ~8px
                        $rowHeight = 6;

                        break;
                    case 2:
                    case 1:
                        // inspection of Verdana 1,2 workbook says 5.25pt ~7px
                        $rowHeight = 5.25;

                        break;
                    default:
                        // use Verdana 10 workbook as an approximation, extrapolation
                        $rowHeight = 12.75 * $font->getSize() / 10;

                        break;
                }

                break;
            default:
                // just use Calibri as an approximation
                $rowHeight = 15 * $font->getSize() / 11;

                break;
        }

        return $rowHeight;
    }
}
