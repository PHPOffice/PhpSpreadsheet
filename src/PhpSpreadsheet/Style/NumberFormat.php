<?php

namespace PhpOffice\PhpSpreadsheet\Style;

class NumberFormat extends Supervisor
{
    // Pre-defined formats
    const FORMAT_GENERAL = 'General';

    const FORMAT_TEXT = '@';

    const FORMAT_NUMBER = '0';
    const FORMAT_NUMBER_0 = '0.0';
    const FORMAT_NUMBER_00 = '0.00';
    const FORMAT_NUMBER_COMMA_SEPARATED1 = '#,##0.00';
    const FORMAT_NUMBER_COMMA_SEPARATED2 = '#,##0.00_-';

    const FORMAT_PERCENTAGE = '0%';
    const FORMAT_PERCENTAGE_0 = '0.0%';
    const FORMAT_PERCENTAGE_00 = '0.00%';

    const FORMAT_DATE_YYYYMMDD2 = 'yyyy-mm-dd';
    const FORMAT_DATE_YYYYMMDD = 'yyyy-mm-dd';
    const FORMAT_DATE_DDMMYYYY = 'dd/mm/yyyy';
    const FORMAT_DATE_DMYSLASH = 'd/m/yy';
    const FORMAT_DATE_DMYMINUS = 'd-m-yy';
    const FORMAT_DATE_DMMINUS = 'd-m';
    const FORMAT_DATE_MYMINUS = 'm-yy';
    const FORMAT_DATE_XLSX14 = 'mm-dd-yy';
    const FORMAT_DATE_XLSX15 = 'd-mmm-yy';
    const FORMAT_DATE_XLSX16 = 'd-mmm';
    const FORMAT_DATE_XLSX17 = 'mmm-yy';
    const FORMAT_DATE_XLSX22 = 'm/d/yy h:mm';
    const FORMAT_DATE_DATETIME = 'd/m/yy h:mm';
    const FORMAT_DATE_TIME1 = 'h:mm AM/PM';
    const FORMAT_DATE_TIME2 = 'h:mm:ss AM/PM';
    const FORMAT_DATE_TIME3 = 'h:mm';
    const FORMAT_DATE_TIME4 = 'h:mm:ss';
    const FORMAT_DATE_TIME5 = 'mm:ss';
    const FORMAT_DATE_TIME6 = 'h:mm:ss';
    const FORMAT_DATE_TIME7 = 'i:s.S';
    const FORMAT_DATE_TIME8 = 'h:mm:ss;@';
    const FORMAT_DATE_YYYYMMDDSLASH = 'yyyy/mm/dd;@';

    const FORMAT_CURRENCY_USD_SIMPLE_CODE = '"USD"#,##0.00_-'; //Format for currency US Dollar simple using code
    const FORMAT_CURRENCY_USD_SIMPLE = '"$"#,##0.00_-'; //Format for currency US Dollar simple using symbol
    const FORMAT_CURRENCY_USD_CODE = 'USD#,##0_-'; //Format for currency US Dollar using code
    const FORMAT_CURRENCY_USD = '$#,##0_-'; //Format for currency US Dollar using symbol
    const FORMAT_CURRENCY_EUR_SIMPLE_CODE = '"EUR"#,##0.00_-'; //Format for currency Euro simple using code
    const FORMAT_CURRENCY_EUR_SIMPLE = '#,##0.00_-"€"'; //Format for currency Euro simple using symbol
    const FORMAT_CURRENCY_EUR_CODE = 'EUR#,##0_-'; //Format for currency Euro using code
    const FORMAT_CURRENCY_EUR = '#,##0_-"€"'; //Format for currency Euro using symbol
    const FORMAT_CURRENCY_IDR_SIMPLE_CODE = '"IDR"#,##0.00_-'; //Format for currency Indonesian Rupiah simple using code
    const FORMAT_CURRENCY_IDR_SIMPLE = '"Rp"#,##0.00_-'; //Format for currency Indonesian Rupiah simple using symbol
    const FORMAT_CURRENCY_IDR_CODE = 'IDR#,##0_-'; //Format for currency Indonesian Rupiah using code
    const FORMAT_CURRENCY_IDR = 'Rp#,##0_-'; //Format for currency Indonesian Rupiah using symbol
    const FORMAT_CURRENCY_MYR_SIMPLE_CODE = '"MYR"#,##0.00_-'; //Format for currency Malaysian Ringgit simple using code
    const FORMAT_CURRENCY_MYR_SIMPLE = '"RM"#,##0.00_-'; //Format for currency Malaysian Ringgit simple using symbol
    const FORMAT_CURRENCY_MYR_CODE = 'MYR#,##0_-'; //Format for currency Malaysian Ringgit using code
    const FORMAT_CURRENCY_MYR = 'RM#,##0_-'; //Format for currency Malaysian Ringgit using symbol
    const FORMAT_CURRENCY_ZAR_SIMPLE_CODE = '"ZAR"#,##0.00_-'; //Format for currency South Africa Rand simple using code
    const FORMAT_CURRENCY_ZAR_SIMPLE = '"R"#,##0.00_-'; //Format for currency South Africa Rand simple using symbol
    const FORMAT_CURRENCY_ZAR_CODE = 'ZAR#,##0_-'; //Format for currency South Africa Rand using code
    const FORMAT_CURRENCY_ZAR = 'R#,##0_-'; //Format for currency South Africa Rand using symbol
    const FORMAT_CURRENCY_JPY_SIMPLE_CODE = '"JPY"#,##0.00_-'; //Format for currency Japanese Yen simple using code
    const FORMAT_CURRENCY_JPY_SIMPLE = '"¥"#,##0.00_-'; //Format for currency Japanese Yen simple using symbol
    const FORMAT_CURRENCY_JPY_CODE = 'JPY#,##0_-'; //Format for currency Japanese Yen using code
    const FORMAT_CURRENCY_JPY = '¥#,##0_-'; //Format for currency Japanese Yen using symbol
    const FORMAT_CURRENCY_CNY_SIMPLE_CODE = '"CNY"#,##0.00_-'; //Format for currency China Yuan simple using code
    const FORMAT_CURRENCY_CNY_SIMPLE = '"¥"#,##0.00_-'; //Format for currency China Yuan simple using symbol
    const FORMAT_CURRENCY_CNY_CODE = 'CNY#,##0_-'; //Format for currency China Yuan using code
    const FORMAT_CURRENCY_CNY = '¥#,##0_-'; //Format for currency China Yuan using symbol
    const FORMAT_CURRENCY_RUB_SIMPLE_CODE = '"RUB"#,##0.00_-'; //Format for currency Russian Ruble simple using code
    const FORMAT_CURRENCY_RUB_SIMPLE = '"₽"#,##0.00_-'; //Format for currency Russian Ruble simple using symbol
    const FORMAT_CURRENCY_RUB_CODE = 'RUB#,##0_-'; //Format for currency Russian Ruble using code
    const FORMAT_CURRENCY_RUB = '₽#,##0_-'; //Format for currency Russian Ruble using symbol
    const FORMAT_CURRENCY_PHP_SIMPLE_CODE = '"PHP"#,##0.00_-'; //Format for currency Philippine Peso simple using code
    const FORMAT_CURRENCY_PHP_SIMPLE = '"₱"#,##0.00_-'; //Format for currency Philippine Peso simple using symbol
    const FORMAT_CURRENCY_PHP_CODE = 'PHP#,##0_-'; //Format for currency Philippine Peso using code
    const FORMAT_CURRENCY_PHP = '₱#,##0_-'; //Format for currency Philippine Peso using symbol
    const FORMAT_ACCOUNTING_USD = '_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)'; //Format for accounting US Dollar using symbol
    const FORMAT_ACCOUNTING_EUR = '_("€"* #,##0.00_);_("€"* \(#,##0.00\);_("€"* "-"??_);_(@_)'; //Format for accounting Euro using symbol
    const FORMAT_ACCOUNTING_IDR = '_("Rp"* #,##0.00_);_("Rp"* \(#,##0.00\);_("Rp"* "-"??_);_(@_)'; //Format for accounting Indonesian Rupiah using symbol
    const FORMAT_ACCOUNTING_MYR = '_("RM"* #,##0.00_);_("RM"* \(#,##0.00\);_("RM"* "-"??_);_(@_)'; //Format for accounting Malaysian Ringgit using symbol
    const FORMAT_ACCOUNTING_ZAR = '_("R"* #,##0.00_);_("R"* \(#,##0.00\);_("R"* "-"??_);_(@_)'; //Format for accounting South Africa Rand using symbol
    const FORMAT_ACCOUNTING_JPY = '_("¥"* #,##0.00_);_("¥"* \(#,##0.00\);_("¥"* "-"??_);_(@_)'; //Format for accounting Japanese Yen using symbol
    const FORMAT_ACCOUNTING_CNY = '_("¥"* #,##0.00_);_("¥"* \(#,##0.00\);_("¥"* "-"??_);_(@_)'; //Format for accounting China Yuan using symbol
    const FORMAT_ACCOUNTING_RUB = '_("₽"* #,##0.00_);_("₽"* \(#,##0.00\);_("₽"* "-"??_);_(@_)'; //Format for accounting Russian Ruble using symbol
    const FORMAT_ACCOUNTING_PHP = '_("₱"* #,##0.00_);_("₱"* \(#,##0.00\);_("₱"* "-"??_);_(@_)'; //Format for accounting Philippine Peso using symbol
    const FORMAT_ACCOUNTING_USD_CODE = '_("USD"* #,##0.00_);_("USD"* \(#,##0.00\);_("USD"* "-"??_);_(@_)'; //Format for accounting US Dollar using code
    const FORMAT_ACCOUNTING_EUR_CODE = '_("EUR"* #,##0.00_);_("EUR"* \(#,##0.00\);_("EUR"* "-"??_);_(@_)'; //Format for accounting Euro using code
    const FORMAT_ACCOUNTING_IDR_CODE = '_("IDR"* #,##0.00_);_("IDR"* \(#,##0.00\);_("IDR"* "-"??_);_(@_)'; //Format for accounting Indonesian Rupiah using code
    const FORMAT_ACCOUNTING_MYR_CODE = '_("MYR"* #,##0.00_);_("MYR"* \(#,##0.00\);_("MYR"* "-"??_);_(@_)'; //Format for accounting Malaysian Ringgit using code
    const FORMAT_ACCOUNTING_ZAR_CODE = '_("ZAR"* #,##0.00_);_("ZAR"* \(#,##0.00\);_("ZAR"* "-"??_);_(@_)'; //Format for accounting South Africa Rand using code
    const FORMAT_ACCOUNTING_JPY_CODE = '_("JPY"* #,##0.00_);_("JPY"* \(#,##0.00\);_("JPY"* "-"??_);_(@_)'; //Format for accounting Japanese Yen using code
    const FORMAT_ACCOUNTING_CNY_CODE = '_("CNY"* #,##0.00_);_("CNY"* \(#,##0.00\);_("CNY"* "-"??_);_(@_)'; //Format for accounting China Yuan using code
    const FORMAT_ACCOUNTING_RUB_CODE = '_("RUB"* #,##0.00_);_("RUB"* \(#,##0.00\);_("RUB"* "-"??_);_(@_)'; //Format for accounting Russian Ruble using code
    const FORMAT_ACCOUNTING_PHP_CODE = '_("PHP"* #,##0.00_);_("PHP"* \(#,##0.00\);_("PHP"* "-"??_);_(@_)'; //Format for accounting Philippine Peso using code

    /**
     * Excel built-in number formats.
     *
     * @var array
     */
    protected static $builtInFormats;

    /**
     * Excel built-in number formats (flipped, for faster lookups).
     *
     * @var array
     */
    protected static $flippedBuiltInFormats;

    /**
     * Format Code.
     *
     * @var null|string
     */
    protected $formatCode = self::FORMAT_GENERAL;

    /**
     * Built-in format Code.
     *
     * @var false|int
     */
    protected $builtInFormatCode = 0;

    /**
     * Create a new NumberFormat.
     *
     * @param bool $isSupervisor Flag indicating if this is a supervisor or not
     *                                    Leave this value at default unless you understand exactly what
     *                                        its ramifications are
     * @param bool $isConditional Flag indicating if this is a conditional style or not
     *                                    Leave this value at default unless you understand exactly what
     *                                        its ramifications are
     */
    public function __construct($isSupervisor = false, $isConditional = false)
    {
        // Supervisor?
        parent::__construct($isSupervisor);

        if ($isConditional) {
            $this->formatCode = null;
            $this->builtInFormatCode = false;
        }
    }

    /**
     * Get the shared style component for the currently active cell in currently active sheet.
     * Only used for style supervisor.
     *
     * @return NumberFormat
     */
    public function getSharedComponent()
    {
        /** @var Style */
        $parent = $this->parent;

        return $parent->getSharedComponent()->getNumberFormat();
    }

    /**
     * Build style array from subcomponents.
     *
     * @param array $array
     *
     * @return array
     */
    public function getStyleArray($array)
    {
        return ['numberFormat' => $array];
    }

    /**
     * Apply styles from array.
     *
     * <code>
     * $spreadsheet->getActiveSheet()->getStyle('B2')->getNumberFormat()->applyFromArray(
     *     [
     *         'formatCode' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
     *     ]
     * );
     * </code>
     *
     * @param array $styleArray Array containing style information
     *
     * @return $this
     */
    public function applyFromArray(array $styleArray)
    {
        if ($this->isSupervisor) {
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($this->getStyleArray($styleArray));
        } else {
            if (isset($styleArray['formatCode'])) {
                $this->setFormatCode($styleArray['formatCode']);
            }
        }

        return $this;
    }

    /**
     * Get Format Code.
     *
     * @return null|string
     */
    public function getFormatCode()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getFormatCode();
        }
        if (is_int($this->builtInFormatCode)) {
            return self::builtInFormatCode($this->builtInFormatCode);
        }

        return $this->formatCode;
    }

    /**
     * Set Format Code.
     *
     * @param string $formatCode see self::FORMAT_*
     *
     * @return $this
     */
    public function setFormatCode($formatCode)
    {
        if ($formatCode == '') {
            $formatCode = self::FORMAT_GENERAL;
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['formatCode' => $formatCode]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->formatCode = $formatCode;
            $this->builtInFormatCode = self::builtInFormatCodeIndex($formatCode);
        }

        return $this;
    }

    /**
     * Get Built-In Format Code.
     *
     * @return false|int
     */
    public function getBuiltInFormatCode()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getBuiltInFormatCode();
        }

        return $this->builtInFormatCode;
    }

    /**
     * Set Built-In Format Code.
     *
     * @param int $formatCodeIndex
     *
     * @return $this
     */
    public function setBuiltInFormatCode($formatCodeIndex)
    {
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['formatCode' => self::builtInFormatCode($formatCodeIndex)]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->builtInFormatCode = $formatCodeIndex;
            $this->formatCode = self::builtInFormatCode($formatCodeIndex);
        }

        return $this;
    }

    /**
     * Fill built-in format codes.
     */
    private static function fillBuiltInFormatCodes(): void
    {
        //  [MS-OI29500: Microsoft Office Implementation Information for ISO/IEC-29500 Standard Compliance]
        //  18.8.30. numFmt (Number Format)
        //
        //  The ECMA standard defines built-in format IDs
        //      14: "mm-dd-yy"
        //      22: "m/d/yy h:mm"
        //      37: "#,##0 ;(#,##0)"
        //      38: "#,##0 ;[Red](#,##0)"
        //      39: "#,##0.00;(#,##0.00)"
        //      40: "#,##0.00;[Red](#,##0.00)"
        //      47: "mmss.0"
        //      KOR fmt 55: "yyyy-mm-dd"
        //  Excel defines built-in format IDs
        //      14: "m/d/yyyy"
        //      22: "m/d/yyyy h:mm"
        //      37: "#,##0_);(#,##0)"
        //      38: "#,##0_);[Red](#,##0)"
        //      39: "#,##0.00_);(#,##0.00)"
        //      40: "#,##0.00_);[Red](#,##0.00)"
        //      47: "mm:ss.0"
        //      KOR fmt 55: "yyyy/mm/dd"

        // Built-in format codes
        if (self::$builtInFormats === null) {
            self::$builtInFormats = [];

            // General
            self::$builtInFormats[0] = self::FORMAT_GENERAL;
            self::$builtInFormats[1] = '0';
            self::$builtInFormats[2] = '0.00';
            self::$builtInFormats[3] = '#,##0';
            self::$builtInFormats[4] = '#,##0.00';

            self::$builtInFormats[9] = '0%';
            self::$builtInFormats[10] = '0.00%';
            self::$builtInFormats[11] = '0.00E+00';
            self::$builtInFormats[12] = '# ?/?';
            self::$builtInFormats[13] = '# ??/??';
            self::$builtInFormats[14] = 'm/d/yyyy'; // Despite ECMA 'mm-dd-yy';
            self::$builtInFormats[15] = 'd-mmm-yy';
            self::$builtInFormats[16] = 'd-mmm';
            self::$builtInFormats[17] = 'mmm-yy';
            self::$builtInFormats[18] = 'h:mm AM/PM';
            self::$builtInFormats[19] = 'h:mm:ss AM/PM';
            self::$builtInFormats[20] = 'h:mm';
            self::$builtInFormats[21] = 'h:mm:ss';
            self::$builtInFormats[22] = 'm/d/yyyy h:mm'; // Despite ECMA 'm/d/yy h:mm';

            self::$builtInFormats[37] = '#,##0_);(#,##0)'; //  Despite ECMA '#,##0 ;(#,##0)';
            self::$builtInFormats[38] = '#,##0_);[Red](#,##0)'; //  Despite ECMA '#,##0 ;[Red](#,##0)';
            self::$builtInFormats[39] = '#,##0.00_);(#,##0.00)'; //  Despite ECMA '#,##0.00;(#,##0.00)';
            self::$builtInFormats[40] = '#,##0.00_);[Red](#,##0.00)'; //  Despite ECMA '#,##0.00;[Red](#,##0.00)';

            self::$builtInFormats[44] = '_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)';
            self::$builtInFormats[45] = 'mm:ss';
            self::$builtInFormats[46] = '[h]:mm:ss';
            self::$builtInFormats[47] = 'mm:ss.0'; //  Despite ECMA 'mmss.0';
            self::$builtInFormats[48] = '##0.0E+0';
            self::$builtInFormats[49] = '@';

            // CHT
            self::$builtInFormats[27] = '[$-404]e/m/d';
            self::$builtInFormats[30] = 'm/d/yy';
            self::$builtInFormats[36] = '[$-404]e/m/d';
            self::$builtInFormats[50] = '[$-404]e/m/d';
            self::$builtInFormats[57] = '[$-404]e/m/d';

            // THA
            self::$builtInFormats[59] = 't0';
            self::$builtInFormats[60] = 't0.00';
            self::$builtInFormats[61] = 't#,##0';
            self::$builtInFormats[62] = 't#,##0.00';
            self::$builtInFormats[67] = 't0%';
            self::$builtInFormats[68] = 't0.00%';
            self::$builtInFormats[69] = 't# ?/?';
            self::$builtInFormats[70] = 't# ??/??';

            // JPN
            self::$builtInFormats[28] = '[$-411]ggge"年"m"月"d"日"';
            self::$builtInFormats[29] = '[$-411]ggge"年"m"月"d"日"';
            self::$builtInFormats[31] = 'yyyy"年"m"月"d"日"';
            self::$builtInFormats[32] = 'h"時"mm"分"';
            self::$builtInFormats[33] = 'h"時"mm"分"ss"秒"';
            self::$builtInFormats[34] = 'yyyy"年"m"月"';
            self::$builtInFormats[35] = 'm"月"d"日"';
            self::$builtInFormats[51] = '[$-411]ggge"年"m"月"d"日"';
            self::$builtInFormats[52] = 'yyyy"年"m"月"';
            self::$builtInFormats[53] = 'm"月"d"日"';
            self::$builtInFormats[54] = '[$-411]ggge"年"m"月"d"日"';
            self::$builtInFormats[55] = 'yyyy"年"m"月"';
            self::$builtInFormats[56] = 'm"月"d"日"';
            self::$builtInFormats[58] = '[$-411]ggge"年"m"月"d"日"';

            // Flip array (for faster lookups)
            self::$flippedBuiltInFormats = array_flip(self::$builtInFormats);
        }
    }

    /**
     * Get built-in format code.
     *
     * @param int $index
     *
     * @return string
     */
    public static function builtInFormatCode($index)
    {
        // Clean parameter
        $index = (int) $index;

        // Ensure built-in format codes are available
        self::fillBuiltInFormatCodes();

        // Lookup format code
        if (isset(self::$builtInFormats[$index])) {
            return self::$builtInFormats[$index];
        }

        return '';
    }

    /**
     * Get built-in format code index.
     *
     * @param string $formatCodeIndex
     *
     * @return false|int
     */
    public static function builtInFormatCodeIndex($formatCodeIndex)
    {
        // Ensure built-in format codes are available
        self::fillBuiltInFormatCodes();

        // Lookup format code
        if (array_key_exists($formatCodeIndex, self::$flippedBuiltInFormats)) {
            return self::$flippedBuiltInFormats[$formatCodeIndex];
        }

        return false;
    }

    /**
     * Get hash code.
     *
     * @return string Hash code
     */
    public function getHashCode()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getHashCode();
        }

        return md5(
            $this->formatCode .
            $this->builtInFormatCode .
            __CLASS__
        );
    }

    /**
     * Convert a value in a pre-defined format to a PHP string.
     *
     * @param mixed $value Value to format
     * @param string $format Format code, see = self::FORMAT_*
     * @param array $callBack Callback function for additional formatting of string
     *
     * @return string Formatted string
     */
    public static function toFormattedString($value, $format, $callBack = null)
    {
        return NumberFormat\Formatter::toFormattedString($value, $format, $callBack);
    }

    protected function exportArray1(): array
    {
        $exportedArray = [];
        $this->exportArray2($exportedArray, 'formatCode', $this->getFormatCode());

        return $exportedArray;
    }
}
