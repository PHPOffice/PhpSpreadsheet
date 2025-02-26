<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xls;

use Composer\Pcre\Preg;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet as PhpspreadsheetWorksheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;

// Original file header of PEAR::Spreadsheet_Excel_Writer_Parser (used as the base for this class):
// -----------------------------------------------------------------------------------------
// *  Class for parsing Excel formulas
// *
// *  License Information:
// *
// *    Spreadsheet_Excel_Writer:  A library for generating Excel Spreadsheets
// *    Copyright (c) 2002-2003 Xavier Noguer xnoguer@rezebra.com
// *
// *    This library is free software; you can redistribute it and/or
// *    modify it under the terms of the GNU Lesser General Public
// *    License as published by the Free Software Foundation; either
// *    version 2.1 of the License, or (at your option) any later version.
// *
// *    This library is distributed in the hope that it will be useful,
// *    but WITHOUT ANY WARRANTY; without even the implied warranty of
// *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
// *    Lesser General Public License for more details.
// *
// *    You should have received a copy of the GNU Lesser General Public
// *    License along with this library; if not, write to the Free Software
// *    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
// */
class Parser
{
    /**    Constants                */
    // Sheet title in unquoted form
    // Invalid sheet title characters cannot occur in the sheet title:
    //         *:/\?[]
    // Moreover, there are valid sheet title characters that cannot occur in unquoted form (there may be more?)
    // +-% '^&<>=,;#()"{}
    const REGEX_SHEET_TITLE_UNQUOTED = '[^\*\:\/\\\\\?\[\]\+\-\% \\\'\^\&\<\>\=\,\;\#\(\)\"\{\}]+';

    // Sheet title in quoted form (without surrounding quotes)
    // Invalid sheet title characters cannot occur in the sheet title:
    // *:/\?[]                    (usual invalid sheet title characters)
    // Single quote is represented as a pair ''
    // Former value for this constant led to "catastrophic backtracking",
    //     unable to handle double apostrophes.
    //     (*COMMIT) should prevent this.
    const REGEX_SHEET_TITLE_QUOTED = "([^*:/\\\\?\\[\\]']|'')+";

    const REGEX_CELL_TITLE_QUOTED = "~^'"
        . self::REGEX_SHEET_TITLE_QUOTED
        . '(:' . self::REGEX_SHEET_TITLE_QUOTED . ')?'
        . "'!(*COMMIT)"
        . '[$]?[A-Ia-i]?[A-Za-z][$]?(\d+)'
        . '$~u';

    const REGEX_RANGE_TITLE_QUOTED = "~^'"
        . self::REGEX_SHEET_TITLE_QUOTED
        . '(:' . self::REGEX_SHEET_TITLE_QUOTED . ')?'
        . "'!(*COMMIT)"
        . '[$]?[A-Ia-i]?[A-Za-z][$]?(\d+)'
        . ':'
        . '[$]?[A-Ia-i]?[A-Za-z][$]?(\d+)'
        . '$~u';

    private const UTF8 = 'UTF-8';

    /**
     * The index of the character we are currently looking at.
     */
    public int $currentCharacter;

    /**
     * The token we are working on.
     */
    public string $currentToken;

    /**
     * The formula to parse.
     */
    private string $formula;

    /**
     * The character ahead of the current char.
     */
    public string $lookAhead;

    /**
     * The parse tree to be generated.
     */
    public array|string $parseTree;

    /**
     * Array of external sheets.
     */
    private array $externalSheets;

    /**
     * Array of sheet references in the form of REF structures.
     */
    public array $references;

    /**
     * The Excel ptg indices.
     */
    private array $ptg = [
        'ptgExp' => 0x01,
        'ptgTbl' => 0x02,
        'ptgAdd' => 0x03,
        'ptgSub' => 0x04,
        'ptgMul' => 0x05,
        'ptgDiv' => 0x06,
        'ptgPower' => 0x07,
        'ptgConcat' => 0x08,
        'ptgLT' => 0x09,
        'ptgLE' => 0x0A,
        'ptgEQ' => 0x0B,
        'ptgGE' => 0x0C,
        'ptgGT' => 0x0D,
        'ptgNE' => 0x0E,
        'ptgIsect' => 0x0F,
        'ptgUnion' => 0x10,
        'ptgRange' => 0x11,
        'ptgUplus' => 0x12,
        'ptgUminus' => 0x13,
        'ptgPercent' => 0x14,
        'ptgParen' => 0x15,
        'ptgMissArg' => 0x16,
        'ptgStr' => 0x17,
        'ptgAttr' => 0x19,
        'ptgSheet' => 0x1A,
        'ptgEndSheet' => 0x1B,
        'ptgErr' => 0x1C,
        'ptgBool' => 0x1D,
        'ptgInt' => 0x1E,
        'ptgNum' => 0x1F,
        'ptgArray' => 0x20,
        'ptgFunc' => 0x21,
        'ptgFuncVar' => 0x22,
        'ptgName' => 0x23,
        'ptgRef' => 0x24,
        'ptgArea' => 0x25,
        'ptgMemArea' => 0x26,
        'ptgMemErr' => 0x27,
        'ptgMemNoMem' => 0x28,
        'ptgMemFunc' => 0x29,
        'ptgRefErr' => 0x2A,
        'ptgAreaErr' => 0x2B,
        'ptgRefN' => 0x2C,
        'ptgAreaN' => 0x2D,
        'ptgMemAreaN' => 0x2E,
        'ptgMemNoMemN' => 0x2F,
        'ptgNameX' => 0x39,
        'ptgRef3d' => 0x3A,
        'ptgArea3d' => 0x3B,
        'ptgRefErr3d' => 0x3C,
        'ptgAreaErr3d' => 0x3D,
        'ptgArrayV' => 0x40,
        'ptgFuncV' => 0x41,
        'ptgFuncVarV' => 0x42,
        'ptgNameV' => 0x43,
        'ptgRefV' => 0x44,
        'ptgAreaV' => 0x45,
        'ptgMemAreaV' => 0x46,
        'ptgMemErrV' => 0x47,
        'ptgMemNoMemV' => 0x48,
        'ptgMemFuncV' => 0x49,
        'ptgRefErrV' => 0x4A,
        'ptgAreaErrV' => 0x4B,
        'ptgRefNV' => 0x4C,
        'ptgAreaNV' => 0x4D,
        'ptgMemAreaNV' => 0x4E,
        'ptgMemNoMemNV' => 0x4F,
        'ptgFuncCEV' => 0x58,
        'ptgNameXV' => 0x59,
        'ptgRef3dV' => 0x5A,
        'ptgArea3dV' => 0x5B,
        'ptgRefErr3dV' => 0x5C,
        'ptgAreaErr3dV' => 0x5D,
        'ptgArrayA' => 0x60,
        'ptgFuncA' => 0x61,
        'ptgFuncVarA' => 0x62,
        'ptgNameA' => 0x63,
        'ptgRefA' => 0x64,
        'ptgAreaA' => 0x65,
        'ptgMemAreaA' => 0x66,
        'ptgMemErrA' => 0x67,
        'ptgMemNoMemA' => 0x68,
        'ptgMemFuncA' => 0x69,
        'ptgRefErrA' => 0x6A,
        'ptgAreaErrA' => 0x6B,
        'ptgRefNA' => 0x6C,
        'ptgAreaNA' => 0x6D,
        'ptgMemAreaNA' => 0x6E,
        'ptgMemNoMemNA' => 0x6F,
        'ptgFuncCEA' => 0x78,
        'ptgNameXA' => 0x79,
        'ptgRef3dA' => 0x7A,
        'ptgArea3dA' => 0x7B,
        'ptgRefErr3dA' => 0x7C,
        'ptgAreaErr3dA' => 0x7D,
    ];

    /**
     * Thanks to Michael Meeks and Gnumeric for the initial arg values.
     *
     * The following hash was generated by "function_locale.pl" in the distro.
     * Refer to function_locale.pl for non-English function names.
     *
     * The array elements are as follow:
     * ptg:   The Excel function ptg code.
     * args:  The number of arguments that the function takes:
     *           >=0 is a fixed number of arguments.
     *           -1  is a variable  number of arguments.
     * class: The reference, value or array class of the function args.
     * vol:   The function is volatile.
     */
    private array $functions = [
        // function                  ptg  args  class  vol
        'COUNT' => [0, -1, 0, 0],
        'IF' => [1, -1, 1, 0],
        'ISNA' => [2, 1, 1, 0],
        'ISERROR' => [3, 1, 1, 0],
        'SUM' => [4, -1, 0, 0],
        'AVERAGE' => [5, -1, 0, 0],
        'MIN' => [6, -1, 0, 0],
        'MAX' => [7, -1, 0, 0],
        'ROW' => [8, -1, 0, 0],
        'COLUMN' => [9, -1, 0, 0],
        'NA' => [10, 0, 0, 0],
        'NPV' => [11, -1, 1, 0],
        'STDEV' => [12, -1, 0, 0],
        'DOLLAR' => [13, -1, 1, 0],
        'FIXED' => [14, -1, 1, 0],
        'SIN' => [15, 1, 1, 0],
        'COS' => [16, 1, 1, 0],
        'TAN' => [17, 1, 1, 0],
        'ATAN' => [18, 1, 1, 0],
        'PI' => [19, 0, 1, 0],
        'SQRT' => [20, 1, 1, 0],
        'EXP' => [21, 1, 1, 0],
        'LN' => [22, 1, 1, 0],
        'LOG10' => [23, 1, 1, 0],
        'ABS' => [24, 1, 1, 0],
        'INT' => [25, 1, 1, 0],
        'SIGN' => [26, 1, 1, 0],
        'ROUND' => [27, 2, 1, 0],
        'LOOKUP' => [28, -1, 0, 0],
        'INDEX' => [29, -1, 0, 1],
        'REPT' => [30, 2, 1, 0],
        'MID' => [31, 3, 1, 0],
        'LEN' => [32, 1, 1, 0],
        'VALUE' => [33, 1, 1, 0],
        'TRUE' => [34, 0, 1, 0],
        'FALSE' => [35, 0, 1, 0],
        'AND' => [36, -1, 0, 0],
        'OR' => [37, -1, 0, 0],
        'NOT' => [38, 1, 1, 0],
        'MOD' => [39, 2, 1, 0],
        'DCOUNT' => [40, 3, 0, 0],
        'DSUM' => [41, 3, 0, 0],
        'DAVERAGE' => [42, 3, 0, 0],
        'DMIN' => [43, 3, 0, 0],
        'DMAX' => [44, 3, 0, 0],
        'DSTDEV' => [45, 3, 0, 0],
        'VAR' => [46, -1, 0, 0],
        'DVAR' => [47, 3, 0, 0],
        'TEXT' => [48, 2, 1, 0],
        'LINEST' => [49, -1, 0, 0],
        'TREND' => [50, -1, 0, 0],
        'LOGEST' => [51, -1, 0, 0],
        'GROWTH' => [52, -1, 0, 0],
        'PV' => [56, -1, 1, 0],
        'FV' => [57, -1, 1, 0],
        'NPER' => [58, -1, 1, 0],
        'PMT' => [59, -1, 1, 0],
        'RATE' => [60, -1, 1, 0],
        'MIRR' => [61, 3, 0, 0],
        'IRR' => [62, -1, 0, 0],
        'RAND' => [63, 0, 1, 1],
        'MATCH' => [64, -1, 0, 0],
        'DATE' => [65, 3, 1, 0],
        'TIME' => [66, 3, 1, 0],
        'DAY' => [67, 1, 1, 0],
        'MONTH' => [68, 1, 1, 0],
        'YEAR' => [69, 1, 1, 0],
        'WEEKDAY' => [70, -1, 1, 0],
        'HOUR' => [71, 1, 1, 0],
        'MINUTE' => [72, 1, 1, 0],
        'SECOND' => [73, 1, 1, 0],
        'NOW' => [74, 0, 1, 1],
        'AREAS' => [75, 1, 0, 1],
        'ROWS' => [76, 1, 0, 1],
        'COLUMNS' => [77, 1, 0, 1],
        'OFFSET' => [78, -1, 0, 1],
        'SEARCH' => [82, -1, 1, 0],
        'TRANSPOSE' => [83, 1, 1, 0],
        'TYPE' => [86, 1, 1, 0],
        'ATAN2' => [97, 2, 1, 0],
        'ASIN' => [98, 1, 1, 0],
        'ACOS' => [99, 1, 1, 0],
        'CHOOSE' => [100, -1, 1, 0],
        'HLOOKUP' => [101, -1, 0, 0],
        'VLOOKUP' => [102, -1, 0, 0],
        'ISREF' => [105, 1, 0, 0],
        'LOG' => [109, -1, 1, 0],
        'CHAR' => [111, 1, 1, 0],
        'LOWER' => [112, 1, 1, 0],
        'UPPER' => [113, 1, 1, 0],
        'PROPER' => [114, 1, 1, 0],
        'LEFT' => [115, -1, 1, 0],
        'RIGHT' => [116, -1, 1, 0],
        'EXACT' => [117, 2, 1, 0],
        'TRIM' => [118, 1, 1, 0],
        'REPLACE' => [119, 4, 1, 0],
        'SUBSTITUTE' => [120, -1, 1, 0],
        'CODE' => [121, 1, 1, 0],
        'FIND' => [124, -1, 1, 0],
        'CELL' => [125, -1, 0, 1],
        'ISERR' => [126, 1, 1, 0],
        'ISTEXT' => [127, 1, 1, 0],
        'ISNUMBER' => [128, 1, 1, 0],
        'ISBLANK' => [129, 1, 1, 0],
        'T' => [130, 1, 0, 0],
        'N' => [131, 1, 0, 0],
        'DATEVALUE' => [140, 1, 1, 0],
        'TIMEVALUE' => [141, 1, 1, 0],
        'SLN' => [142, 3, 1, 0],
        'SYD' => [143, 4, 1, 0],
        'DDB' => [144, -1, 1, 0],
        'INDIRECT' => [148, -1, 1, 1],
        'CALL' => [150, -1, 1, 0],
        'CLEAN' => [162, 1, 1, 0],
        'MDETERM' => [163, 1, 2, 0],
        'MINVERSE' => [164, 1, 2, 0],
        'MMULT' => [165, 2, 2, 0],
        'IPMT' => [167, -1, 1, 0],
        'PPMT' => [168, -1, 1, 0],
        'COUNTA' => [169, -1, 0, 0],
        'PRODUCT' => [183, -1, 0, 0],
        'FACT' => [184, 1, 1, 0],
        'DPRODUCT' => [189, 3, 0, 0],
        'ISNONTEXT' => [190, 1, 1, 0],
        'STDEVP' => [193, -1, 0, 0],
        'VARP' => [194, -1, 0, 0],
        'DSTDEVP' => [195, 3, 0, 0],
        'DVARP' => [196, 3, 0, 0],
        'TRUNC' => [197, -1, 1, 0],
        'ISLOGICAL' => [198, 1, 1, 0],
        'DCOUNTA' => [199, 3, 0, 0],
        'USDOLLAR' => [204, -1, 1, 0],
        'FINDB' => [205, -1, 1, 0],
        'SEARCHB' => [206, -1, 1, 0],
        'REPLACEB' => [207, 4, 1, 0],
        'LEFTB' => [208, -1, 1, 0],
        'RIGHTB' => [209, -1, 1, 0],
        'MIDB' => [210, 3, 1, 0],
        'LENB' => [211, 1, 1, 0],
        'ROUNDUP' => [212, 2, 1, 0],
        'ROUNDDOWN' => [213, 2, 1, 0],
        'ASC' => [214, 1, 1, 0],
        'DBCS' => [215, 1, 1, 0],
        'RANK' => [216, -1, 0, 0],
        'ADDRESS' => [219, -1, 1, 0],
        'DAYS360' => [220, -1, 1, 0],
        'TODAY' => [221, 0, 1, 1],
        'VDB' => [222, -1, 1, 0],
        'MEDIAN' => [227, -1, 0, 0],
        'SUMPRODUCT' => [228, -1, 2, 0],
        'SINH' => [229, 1, 1, 0],
        'COSH' => [230, 1, 1, 0],
        'TANH' => [231, 1, 1, 0],
        'ASINH' => [232, 1, 1, 0],
        'ACOSH' => [233, 1, 1, 0],
        'ATANH' => [234, 1, 1, 0],
        'DGET' => [235, 3, 0, 0],
        'INFO' => [244, 1, 1, 1],
        'DB' => [247, -1, 1, 0],
        'FREQUENCY' => [252, 2, 0, 0],
        'ERROR.TYPE' => [261, 1, 1, 0],
        'REGISTER.ID' => [267, -1, 1, 0],
        'AVEDEV' => [269, -1, 0, 0],
        'BETADIST' => [270, -1, 1, 0],
        'GAMMALN' => [271, 1, 1, 0],
        'BETAINV' => [272, -1, 1, 0],
        'BINOMDIST' => [273, 4, 1, 0],
        'CHIDIST' => [274, 2, 1, 0],
        'CHIINV' => [275, 2, 1, 0],
        'COMBIN' => [276, 2, 1, 0],
        'CONFIDENCE' => [277, 3, 1, 0],
        'CRITBINOM' => [278, 3, 1, 0],
        'EVEN' => [279, 1, 1, 0],
        'EXPONDIST' => [280, 3, 1, 0],
        'FDIST' => [281, 3, 1, 0],
        'FINV' => [282, 3, 1, 0],
        'FISHER' => [283, 1, 1, 0],
        'FISHERINV' => [284, 1, 1, 0],
        'FLOOR' => [285, 2, 1, 0],
        'GAMMADIST' => [286, 4, 1, 0],
        'GAMMAINV' => [287, 3, 1, 0],
        'CEILING' => [288, 2, 1, 0],
        'HYPGEOMDIST' => [289, 4, 1, 0],
        'LOGNORMDIST' => [290, 3, 1, 0],
        'LOGINV' => [291, 3, 1, 0],
        'NEGBINOMDIST' => [292, 3, 1, 0],
        'NORMDIST' => [293, 4, 1, 0],
        'NORMSDIST' => [294, 1, 1, 0],
        'NORMINV' => [295, 3, 1, 0],
        'NORMSINV' => [296, 1, 1, 0],
        'STANDARDIZE' => [297, 3, 1, 0],
        'ODD' => [298, 1, 1, 0],
        'PERMUT' => [299, 2, 1, 0],
        'POISSON' => [300, 3, 1, 0],
        'TDIST' => [301, 3, 1, 0],
        'WEIBULL' => [302, 4, 1, 0],
        'SUMXMY2' => [303, 2, 2, 0],
        'SUMX2MY2' => [304, 2, 2, 0],
        'SUMX2PY2' => [305, 2, 2, 0],
        'CHITEST' => [306, 2, 2, 0],
        'CORREL' => [307, 2, 2, 0],
        'COVAR' => [308, 2, 2, 0],
        'FORECAST' => [309, 3, 2, 0],
        'FTEST' => [310, 2, 2, 0],
        'INTERCEPT' => [311, 2, 2, 0],
        'PEARSON' => [312, 2, 2, 0],
        'RSQ' => [313, 2, 2, 0],
        'STEYX' => [314, 2, 2, 0],
        'SLOPE' => [315, 2, 2, 0],
        'TTEST' => [316, 4, 2, 0],
        'PROB' => [317, -1, 2, 0],
        'DEVSQ' => [318, -1, 0, 0],
        'GEOMEAN' => [319, -1, 0, 0],
        'HARMEAN' => [320, -1, 0, 0],
        'SUMSQ' => [321, -1, 0, 0],
        'KURT' => [322, -1, 0, 0],
        'SKEW' => [323, -1, 0, 0],
        'ZTEST' => [324, -1, 0, 0],
        'LARGE' => [325, 2, 0, 0],
        'SMALL' => [326, 2, 0, 0],
        'QUARTILE' => [327, 2, 0, 0],
        'PERCENTILE' => [328, 2, 0, 0],
        'PERCENTRANK' => [329, -1, 0, 0],
        'MODE' => [330, -1, 2, 0],
        'TRIMMEAN' => [331, 2, 0, 0],
        'TINV' => [332, 2, 1, 0],
        'CONCATENATE' => [336, -1, 1, 0],
        'POWER' => [337, 2, 1, 0],
        'RADIANS' => [342, 1, 1, 0],
        'DEGREES' => [343, 1, 1, 0],
        'SUBTOTAL' => [344, -1, 0, 0],
        'SUMIF' => [345, -1, 0, 0],
        'COUNTIF' => [346, 2, 0, 0],
        'COUNTBLANK' => [347, 1, 0, 0],
        'ISPMT' => [350, 4, 1, 0],
        'DATEDIF' => [351, 3, 1, 0],
        'DATESTRING' => [352, 1, 1, 0],
        'NUMBERSTRING' => [353, 2, 1, 0],
        'ROMAN' => [354, -1, 1, 0],
        'GETPIVOTDATA' => [358, -1, 0, 0],
        'HYPERLINK' => [359, -1, 1, 0],
        'PHONETIC' => [360, 1, 0, 0],
        'AVERAGEA' => [361, -1, 0, 0],
        'MAXA' => [362, -1, 0, 0],
        'MINA' => [363, -1, 0, 0],
        'STDEVPA' => [364, -1, 0, 0],
        'VARPA' => [365, -1, 0, 0],
        'STDEVA' => [366, -1, 0, 0],
        'VARA' => [367, -1, 0, 0],
        'BAHTTEXT' => [368, 1, 0, 0],
    ];

    private Spreadsheet $spreadsheet;

    /**
     * The class constructor.
     */
    public function __construct(Spreadsheet $spreadsheet)
    {
        $this->spreadsheet = $spreadsheet;

        $this->currentCharacter = 0;
        $this->currentToken = ''; // The token we are working on.
        $this->formula = ''; // The formula to parse.
        $this->lookAhead = ''; // The character ahead of the current char.
        $this->parseTree = ''; // The parse tree to be generated.
        $this->externalSheets = [];
        $this->references = [];
    }

    /**
     * Convert a token to the proper ptg value.
     *
     * @param string $token the token to convert
     *
     * @return string the converted token on success
     */
    private function convert(string $token): string
    {
        if (Preg::isMatch('/"([^"]|""){0,255}"/', $token)) {
            return $this->convertString($token);
        }
        if (is_numeric($token)) {
            return $this->convertNumber($token);
        }
        // match references like A1 or $A$1
        if (Preg::isMatch('/^\$?([A-Ia-i]?[A-Za-z])\$?(\d+)$/', $token)) {
            return $this->convertRef2d($token);
        }
        // match external references like Sheet1!A1 or Sheet1:Sheet2!A1 or Sheet1!$A$1 or Sheet1:Sheet2!$A$1
        if (Preg::isMatch('/^' . self::REGEX_SHEET_TITLE_UNQUOTED . '(\:' . self::REGEX_SHEET_TITLE_UNQUOTED . ')?\!\$?[A-Ia-i]?[A-Za-z]\$?(\d+)$/u', $token)) {
            return $this->convertRef3d($token);
        }
        // match external references like 'Sheet1'!A1 or 'Sheet1:Sheet2'!A1 or 'Sheet1'!$A$1 or 'Sheet1:Sheet2'!$A$1
        if (self::matchCellSheetnameQuoted($token)) {
            return $this->convertRef3d($token);
        }
        // match ranges like A1:B2 or $A$1:$B$2
        if (Preg::isMatch('/^(\$)?[A-Ia-i]?[A-Za-z](\$)?(\d+)\:(\$)?[A-Ia-i]?[A-Za-z](\$)?(\d+)$/', $token)) {
            return $this->convertRange2d($token);
        }
        // match external ranges like Sheet1!A1:B2 or Sheet1:Sheet2!A1:B2 or Sheet1!$A$1:$B$2 or Sheet1:Sheet2!$A$1:$B$2
        if (Preg::isMatch('/^' . self::REGEX_SHEET_TITLE_UNQUOTED . '(\:' . self::REGEX_SHEET_TITLE_UNQUOTED . ')?\!\$?([A-Ia-i]?[A-Za-z])?\$?(\d+)\:\$?([A-Ia-i]?[A-Za-z])?\$?(\d+)$/u', $token)) {
            return $this->convertRange3d($token);
        }
        // match external ranges like 'Sheet1'!A1:B2 or 'Sheet1:Sheet2'!A1:B2 or 'Sheet1'!$A$1:$B$2 or 'Sheet1:Sheet2'!$A$1:$B$2
        if (self::matchRangeSheetnameQuoted($token)) {
            return $this->convertRange3d($token);
        }
        // operators (including parentheses)
        if (isset($this->ptg[$token])) {
            return pack('C', $this->ptg[$token]);
        }
        // match error codes
        if (Preg::isMatch('/^#[A-Z0\/]{3,5}[!?]{1}$/', $token) || $token == '#N/A') {
            return $this->convertError($token);
        }
        if (Preg::isMatch('/^' . Calculation::CALCULATION_REGEXP_DEFINEDNAME . '$/mui', $token) && $this->spreadsheet->getDefinedName($token) !== null) {
            return $this->convertDefinedName($token);
        }
        // commented so argument number can be processed correctly. See toReversePolish().
        /*if (Preg::isMatch("/[A-Z0-9\xc0-\xdc\.]+/", $token))
        {
            return($this->convertFunction($token, $this->_func_args));
        }*/
        // if it's an argument, ignore the token (the argument remains)
        if ($token == 'arg') {
            return '';
        }
        if (Preg::isMatch('/^true$/i', $token)) {
            return $this->convertBool(1);
        }
        if (Preg::isMatch('/^false$/i', $token)) {
            return $this->convertBool(0);
        }

        // TODO: use real error codes
        throw new WriterException("Unknown token $token");
    }

    /**
     * Convert a number token to ptgInt or ptgNum.
     *
     * @param float|int|string $num an integer or double for conversion to its ptg value
     */
    private function convertNumber(mixed $num): string
    {
        // Integer in the range 0..2**16-1
        if ((Preg::isMatch('/^\d+$/', (string) $num)) && ($num <= 65535)) {
            return pack('Cv', $this->ptg['ptgInt'], $num);
        }

        // A float
        if (BIFFwriter::getByteOrder()) { // if it's Big Endian
            $num = strrev((string) $num);
        }

        return pack('Cd', $this->ptg['ptgNum'], $num);
    }

    private function convertBool(int $num): string
    {
        return pack('CC', $this->ptg['ptgBool'], $num);
    }

    /**
     * Convert a string token to ptgStr.
     *
     * @param string $string a string for conversion to its ptg value
     *
     * @return string the converted token
     */
    private function convertString(string $string): string
    {
        // chop away beggining and ending quotes
        $string = substr($string, 1, -1);
        if (strlen($string) > 255) {
            throw new WriterException('String is too long');
        }

        return pack('C', $this->ptg['ptgStr']) . StringHelper::UTF8toBIFF8UnicodeShort($string);
    }

    /**
     * Convert a function to a ptgFunc or ptgFuncVarV depending on the number of
     * args that it takes.
     *
     * @param string $token the name of the function for convertion to ptg value
     * @param int $num_args the number of arguments the function receives
     *
     * @return string The packed ptg for the function
     */
    private function convertFunction(string $token, int $num_args): string
    {
        $args = $this->functions[$token][1];

        // Fixed number of args eg. TIME($i, $j, $k).
        if ($args >= 0) {
            return pack('Cv', $this->ptg['ptgFuncV'], $this->functions[$token][0]);
        }

        // Variable number of args eg. SUM($i, $j, $k, ..).
        return pack('CCv', $this->ptg['ptgFuncVarV'], $num_args, $this->functions[$token][0]);
    }

    /**
     * Convert an Excel range such as A1:D4 to a ptgRefV.
     *
     * @param string $range An Excel range in the A1:A2
     */
    private function convertRange2d(string $range, int $class = 0): string
    {
        // TODO: possible class value 0,1,2 check Formula.pm
        // Split the range into 2 cell refs
        if (Preg::isMatch('/^(\$)?([A-Ia-i]?[A-Za-z])(\$)?(\d+)\:(\$)?([A-Ia-i]?[A-Za-z])(\$)?(\d+)$/', $range)) {
            [$cell1, $cell2] = explode(':', $range);
        } else {
            // TODO: use real error codes
            throw new WriterException('Unknown range separator');
        }
        // Convert the cell references
        [$row1, $col1] = $this->cellToPackedRowcol($cell1);
        [$row2, $col2] = $this->cellToPackedRowcol($cell2);

        // The ptg value depends on the class of the ptg.
        if ($class == 0) {
            $ptgArea = pack('C', $this->ptg['ptgArea']);
        } elseif ($class == 1) {
            $ptgArea = pack('C', $this->ptg['ptgAreaV']);
        } elseif ($class == 2) {
            $ptgArea = pack('C', $this->ptg['ptgAreaA']);
        } else {
            // TODO: use real error codes
            throw new WriterException("Unknown class $class");
        }

        return $ptgArea . $row1 . $row2 . $col1 . $col2;
    }

    /**
     * Convert an Excel 3d range such as "Sheet1!A1:D4" or "Sheet1:Sheet2!A1:D4" to
     * a ptgArea3d.
     *
     * @param string $token an Excel range in the Sheet1!A1:A2 format
     *
     * @return string the packed ptgArea3d token on success
     */
    private function convertRange3d(string $token): string
    {
        // Split the ref at the ! symbol
        [$ext_ref, $range] = PhpspreadsheetWorksheet::extractSheetTitle($token, true, true);

        // Convert the external reference part (different for BIFF8)
        $ext_ref = $this->getRefIndex($ext_ref ?? '');

        // Split the range into 2 cell refs
        [$cell1, $cell2] = explode(':', $range ?? '');

        // Convert the cell references
        if (Preg::isMatch('/^(\$)?[A-Ia-i]?[A-Za-z](\$)?(\d+)$/', $cell1)) {
            [$row1, $col1] = $this->cellToPackedRowcol($cell1);
            [$row2, $col2] = $this->cellToPackedRowcol($cell2);
        } else { // It's a rows range (like 26:27)
            [$row1, $col1, $row2, $col2] = $this->rangeToPackedRange($cell1 . ':' . $cell2);
        }

        // The ptg value depends on the class of the ptg.
        $ptgArea = pack('C', $this->ptg['ptgArea3d']);

        return $ptgArea . $ext_ref . $row1 . $row2 . $col1 . $col2;
    }

    /**
     * Convert an Excel reference such as A1, $B2, C$3 or $D$4 to a ptgRefV.
     *
     * @param string $cell An Excel cell reference
     *
     * @return string The cell in packed() format with the corresponding ptg
     */
    private function convertRef2d(string $cell): string
    {
        // Convert the cell reference
        $cell_array = $this->cellToPackedRowcol($cell);
        [$row, $col] = $cell_array;

        // The ptg value depends on the class of the ptg.
        $ptgRef = pack('C', $this->ptg['ptgRefA']);

        return $ptgRef . $row . $col;
    }

    /**
     * Convert an Excel 3d reference such as "Sheet1!A1" or "Sheet1:Sheet2!A1" to a
     * ptgRef3d.
     *
     * @param string $cell An Excel cell reference
     *
     * @return string the packed ptgRef3d token on success
     */
    private function convertRef3d(string $cell): string
    {
        // Split the ref at the ! symbol
        [$ext_ref, $cell] = PhpspreadsheetWorksheet::extractSheetTitle($cell, true, true);

        // Convert the external reference part (different for BIFF8)
        $ext_ref = $this->getRefIndex($ext_ref ?? '');

        // Convert the cell reference part
        [$row, $col] = $this->cellToPackedRowcol($cell ?? '');

        // The ptg value depends on the class of the ptg.
        $ptgRef = pack('C', $this->ptg['ptgRef3dA']);

        return $ptgRef . $ext_ref . $row . $col;
    }

    /**
     * Convert an error code to a ptgErr.
     *
     * @param string $errorCode The error code for conversion to its ptg value
     *
     * @return string The error code ptgErr
     */
    private function convertError(string $errorCode): string
    {
        return match ($errorCode) {
            '#NULL!' => pack('C', 0x00),
            '#DIV/0!' => pack('C', 0x07),
            '#VALUE!' => pack('C', 0x0F),
            '#REF!' => pack('C', 0x17),
            '#NAME?' => pack('C', 0x1D),
            '#NUM!' => pack('C', 0x24),
            '#N/A' => pack('C', 0x2A),
            default => pack('C', 0xFF),
        };
    }

    private bool $tryDefinedName = false;

    private function convertDefinedName(string $name): string
    {
        if (strlen($name) > 255) {
            throw new WriterException('Defined Name is too long');
        }

        if ($this->tryDefinedName) {
            // @codeCoverageIgnoreStart
            $nameReference = 1;
            foreach ($this->spreadsheet->getDefinedNames() as $definedName) {
                if ($name === $definedName->getName()) {
                    break;
                }
                ++$nameReference;
            }

            $ptgRef = pack('Cvxx', $this->ptg['ptgName'], $nameReference);

            return $ptgRef;
            // @codeCoverageIgnoreEnd
        }

        throw new WriterException('Cannot yet write formulae with defined names to Xls');
    }

    /**
     * Look up the REF index that corresponds to an external sheet name
     * (or range). If it doesn't exist yet add it to the workbook's references
     * array. It assumes all sheet names given must exist.
     *
     * @param string $ext_ref The name of the external reference
     *
     * @return string The reference index in packed() format on success
     */
    private function getRefIndex(string $ext_ref): string
    {
        $ext_ref = Preg::replace(["/^'/", "/'$/"], ['', ''], $ext_ref); // Remove leading and trailing ' if any.
        $ext_ref = str_replace('\'\'', '\'', $ext_ref); // Replace escaped '' with '

        // Check if there is a sheet range eg., Sheet1:Sheet2.
        if (Preg::isMatch('/:/', $ext_ref)) {
            [$sheet_name1, $sheet_name2] = explode(':', $ext_ref);

            $sheet1 = $this->getSheetIndex($sheet_name1);
            if ($sheet1 == -1) {
                throw new WriterException("Unknown sheet name $sheet_name1 in formula");
            }
            $sheet2 = $this->getSheetIndex($sheet_name2);
            if ($sheet2 == -1) {
                throw new WriterException("Unknown sheet name $sheet_name2 in formula");
            }

            // Reverse max and min sheet numbers if necessary
            if ($sheet1 > $sheet2) {
                [$sheet1, $sheet2] = [$sheet2, $sheet1];
            }
        } else { // Single sheet name only.
            $sheet1 = $this->getSheetIndex($ext_ref);
            if ($sheet1 == -1) {
                throw new WriterException("Unknown sheet name $ext_ref in formula");
            }
            $sheet2 = $sheet1;
        }

        // assume all references belong to this document
        $supbook_index = 0x00;
        $ref = pack('vvv', $supbook_index, $sheet1, $sheet2);
        $totalreferences = count($this->references);
        $index = -1;
        for ($i = 0; $i < $totalreferences; ++$i) {
            if ($ref == $this->references[$i]) {
                $index = $i;

                break;
            }
        }
        // if REF was not found add it to references array
        if ($index == -1) {
            $this->references[$totalreferences] = $ref;
            $index = $totalreferences;
        }

        return pack('v', $index);
    }

    /**
     * Look up the index that corresponds to an external sheet name. The hash of
     * sheet names is updated by the addworksheet() method of the
     * \PhpOffice\PhpSpreadsheet\Writer\Xls\Workbook class.
     *
     * @param string $sheet_name Sheet name
     *
     * @return int The sheet index, -1 if the sheet was not found
     */
    private function getSheetIndex(string $sheet_name): int
    {
        if (!isset($this->externalSheets[$sheet_name])) {
            return -1;
        }

        return $this->externalSheets[$sheet_name];
    }

    /**
     * This method is used to update the array of sheet names. It is
     * called by the addWorksheet() method of the
     * \PhpOffice\PhpSpreadsheet\Writer\Xls\Workbook class.
     *
     * @param string $name The name of the worksheet being added
     * @param int $index The index of the worksheet being added
     *
     * @see Workbook::addWorksheet
     */
    public function setExtSheet(string $name, int $index): void
    {
        $this->externalSheets[$name] = $index;
    }

    /**
     * pack() row and column into the required 3 or 4 byte format.
     *
     * @param string $cell The Excel cell reference to be packed
     *
     * @return array Array containing the row and column in packed() format
     */
    private function cellToPackedRowcol(string $cell): array
    {
        $cell = strtoupper($cell);
        [$row, $col, $row_rel, $col_rel] = $this->cellToRowcol($cell);
        if ($col >= 256) {
            throw new WriterException("Column in: $cell greater than 255");
        }
        if ($row >= 65536) {
            throw new WriterException("Row in: $cell greater than 65536 ");
        }

        // Set the high bits to indicate if row or col are relative.
        $col |= $col_rel << 14;
        $col |= $row_rel << 15;
        $col = pack('v', $col);

        $row = pack('v', $row);

        return [$row, $col];
    }

    /**
     * pack() row range into the required 3 or 4 byte format.
     * Just using maximum col/rows, which is probably not the correct solution.
     *
     * @param string $range The Excel range to be packed
     *
     * @return array Array containing (row1,col1,row2,col2) in packed() format
     */
    private function rangeToPackedRange(string $range): array
    {
        if (!Preg::isMatch('/(\$)?(\d+)\:(\$)?(\d+)/', $range, $match)) {
            // @codeCoverageIgnoreStart
            throw new WriterException('Regexp failure in rangeToPackedRange');
            // @codeCoverageIgnoreEnd
        }
        // return absolute rows if there is a $ in the ref
        $row1_rel = empty($match[1]) ? 1 : 0;
        $row1 = $match[2];
        $row2_rel = empty($match[3]) ? 1 : 0;
        $row2 = $match[4];
        // Convert 1-index to zero-index
        --$row1;
        --$row2;
        // Trick poor inocent Excel
        $col1 = 0;
        $col2 = 65535; // FIXME: maximum possible value for Excel 5 (change this!!!)

        // FIXME: this changes for BIFF8
        if (($row1 >= 65536) || ($row2 >= 65536)) {
            throw new WriterException("Row in: $range greater than 65536 ");
        }

        // Set the high bits to indicate if rows are relative.
        $col1 |= $row1_rel << 15;
        $col2 |= $row2_rel << 15;
        $col1 = pack('v', $col1);
        $col2 = pack('v', $col2);

        $row1 = pack('v', $row1);
        $row2 = pack('v', $row2);

        return [$row1, $col1, $row2, $col2];
    }

    /**
     * Convert an Excel cell reference such as A1 or $B2 or C$3 or $D$4 to a zero
     * indexed row and column number. Also returns two (0,1) values to indicate
     * whether the row or column are relative references.
     *
     * @param string $cell the Excel cell reference in A1 format
     */
    private function cellToRowcol(string $cell): array
    {
        if (!Preg::isMatch('/(\$)?([A-I]?[A-Z])(\$)?(\d+)/', $cell, $match)) {
            // @codeCoverageIgnoreStart
            throw new WriterException('Regexp failure in cellToRowcol');
            // @codeCoverageIgnoreEnd
        }
        // return absolute column if there is a $ in the ref
        $col_rel = empty($match[1]) ? 1 : 0;
        $col_ref = $match[2];
        $row_rel = empty($match[3]) ? 1 : 0;
        $row = $match[4];

        // Convert base26 column string to a number.
        $expn = strlen($col_ref) - 1;
        $col = 0;
        $col_ref_length = strlen($col_ref);
        for ($i = 0; $i < $col_ref_length; ++$i) {
            $col += (ord($col_ref[$i]) - 64) * 26 ** $expn;
            --$expn;
        }

        // Convert 1-index to zero-index
        --$row;
        --$col;

        return [$row, $col, $row_rel, $col_rel];
    }

    /**
     * Advance to the next valid token.
     */
    private function advance(): void
    {
        $token = '';
        $i = $this->currentCharacter;
        $formula = mb_str_split($this->formula, 1, self::UTF8);
        $formula_length = count($formula);
        // eat up white spaces
        if ($i < $formula_length) {
            while ($formula[$i] === ' ') {
                ++$i;
            }

            if ($i < ($formula_length - 1)) {
                $this->lookAhead = $formula[$i + 1];
            }
            $token = '';
        }

        while ($i < $formula_length) {
            $token .= $formula[$i];

            if ($i < ($formula_length - 1)) {
                $this->lookAhead = $formula[$i + 1];
            } else {
                $this->lookAhead = '';
            }

            if ($this->match($token) !== '') {
                $this->currentCharacter = $i + 1;
                $this->currentToken = $token;

                return;
            }

            if ($i < ($formula_length - 2)) {
                $this->lookAhead = $formula[$i + 2];
            } else { // if we run out of characters lookAhead becomes empty
                $this->lookAhead = '';
            }
            ++$i;
        }
    }

    /**
     * Checks if it's a valid token.
     *
     * @param string $token the token to check
     *
     * @return string The checked token or empty string on failure
     */
    private function match(string $token): string
    {
        switch ($token) {
            case '+':
            case '-':
            case '*':
            case '/':
            case '(':
            case ')':
            case ',':
            case ';':
            case '>=':
            case '<=':
            case '=':
            case '<>':
            case '^':
            case '&':
            case '%':
                return $token;

            case '>':
                if ($this->lookAhead === '=') { // it's a GE token
                    break;
                }

                return $token;

            case '<':
                // it's a LE or a NE token
                if (($this->lookAhead === '=') || ($this->lookAhead === '>')) {
                    break;
                }

                return $token;
        }

        // if it's a reference A1 or $A$1 or $A1 or A$1
        if (
            Preg::isMatch('/^\$?[A-Ia-i]?[A-Za-z]\$?\d+$/', $token)
            && !Preg::isMatch('/\d/', $this->lookAhead)
            && ($this->lookAhead !== ':')
            && ($this->lookAhead !== '.')
            && ($this->lookAhead !== '!')
        ) {
            return $token;
        }
        // If it's an external reference (Sheet1!A1 or Sheet1:Sheet2!A1 or Sheet1!$A$1 or Sheet1:Sheet2!$A$1)
        if (
            Preg::isMatch('/^' . self::REGEX_SHEET_TITLE_UNQUOTED . '(\:' . self::REGEX_SHEET_TITLE_UNQUOTED . ')?\!\$?[A-Ia-i]?[A-Za-z]\$?\d+$/u', $token)
            && !Preg::isMatch('/\d/', $this->lookAhead)
            && ($this->lookAhead !== ':')
            && ($this->lookAhead !== '.')
        ) {
            return $token;
        }
        // If it's an external reference ('Sheet1'!A1 or 'Sheet1:Sheet2'!A1 or 'Sheet1'!$A$1 or 'Sheet1:Sheet2'!$A$1)
        if (
            self::matchCellSheetnameQuoted($token)
            && !Preg::isMatch('/\d/', $this->lookAhead)
            && ($this->lookAhead !== ':') && ($this->lookAhead !== '.')
        ) {
            return $token;
        }
        // if it's a range A1:A2 or $A$1:$A$2
        if (
            Preg::isMatch(
                '/^(\$)?[A-Ia-i]?[A-Za-z](\$)?\d+:(\$)?[A-Ia-i]?[A-Za-z](\$)?\d+$/',
                $token
            )
            && !Preg::isMatch('/\d/', $this->lookAhead)
        ) {
            return $token;
        }
        // If it's an external range like Sheet1!A1:B2 or Sheet1:Sheet2!A1:B2 or Sheet1!$A$1:$B$2 or Sheet1:Sheet2!$A$1:$B$2
        if (
            Preg::isMatch(
                '/^'
                . self::REGEX_SHEET_TITLE_UNQUOTED
                . '(\:' . self::REGEX_SHEET_TITLE_UNQUOTED
                . ')?\!\$?([A-Ia-i]?[A-Za-z])?\$?\d+:\$?([A-Ia-i]?[A-Za-z])?\$?\d+$/u',
                $token
            )
            && !Preg::isMatch('/\d/', $this->lookAhead)
        ) {
            return $token;
        }
        // If it's an external range like 'Sheet1'!A1:B2 or 'Sheet1:Sheet2'!A1:B2 or 'Sheet1'!$A$1:$B$2 or 'Sheet1:Sheet2'!$A$1:$B$2
        if (
            self::matchRangeSheetnameQuoted($token)
            && !Preg::isMatch('/\d/', $this->lookAhead)
        ) {
            return $token;
        }
        // If it's a number (check that it's not a sheet name or range)
        if (is_numeric($token) && (!is_numeric($token . $this->lookAhead) || ($this->lookAhead == '')) && ($this->lookAhead !== '!') && ($this->lookAhead !== ':')) {
            return $token;
        }
        if (
            Preg::isMatch('/"([^"]|""){0,255}"/', $token)
            && $this->lookAhead !== '"'
            && (substr_count($token, '"') % 2 == 0)
        ) {
            // If it's a string (of maximum 255 characters)
            return $token;
        }
        // If it's an error code
        if (
            Preg::isMatch('/^#[A-Z0\/]{3,5}[!?]{1}$/', $token)
            || $token === '#N/A'
        ) {
            return $token;
        }
        // if it's a function call
        if (
            Preg::isMatch("/^[A-Z0-9\xc0-\xdc\\.]+$/i", $token)
            && ($this->lookAhead === '(')
        ) {
            return $token;
        }
        if (
            Preg::isMatch(
                '/^'
                . Calculation::CALCULATION_REGEXP_DEFINEDNAME
                . '$/miu',
                $token
            )
            && $this->spreadsheet->getDefinedName($token) !== null
        ) {
            return $token;
        }
        if (
            Preg::isMatch('/^true$/i', $token)
            && ($this->lookAhead === ')' || $this->lookAhead === ',')
        ) {
            return $token;
        }
        if (
            Preg::isMatch('/^false$/i', $token)
            && ($this->lookAhead === ')' || $this->lookAhead === ',')
        ) {
            return $token;
        }
        if (str_ends_with($token, ')')) {
            //    It's an argument of some description (e.g. a named range),
            //        precise nature yet to be determined
            return $token;
        }

        return '';
    }

    /**
     * The parsing method. It parses a formula.
     *
     * @param string $formula the formula to parse, without the initial equal
     *                        sign (=)
     *
     * @return bool true on success
     */
    public function parse(string $formula): bool
    {
        $this->currentCharacter = 0;
        $this->formula = $formula;
        $this->lookAhead = mb_substr($formula, 1, 1, self::UTF8);
        $this->advance();
        $this->parseTree = $this->condition();

        return true;
    }

    /**
     * It parses a condition. It assumes the following rule:
     * Cond -> Expr [(">" | "<") Expr].
     *
     * @return array The parsed ptg'd tree on success
     */
    private function condition(): array
    {
        $result = $this->expression();
        if ($this->currentToken == '<') {
            $this->advance();
            $result2 = $this->expression();
            $result = $this->createTree('ptgLT', $result, $result2);
        } elseif ($this->currentToken == '>') {
            $this->advance();
            $result2 = $this->expression();
            $result = $this->createTree('ptgGT', $result, $result2);
        } elseif ($this->currentToken == '<=') {
            $this->advance();
            $result2 = $this->expression();
            $result = $this->createTree('ptgLE', $result, $result2);
        } elseif ($this->currentToken == '>=') {
            $this->advance();
            $result2 = $this->expression();
            $result = $this->createTree('ptgGE', $result, $result2);
        } elseif ($this->currentToken == '=') {
            $this->advance();
            $result2 = $this->expression();
            $result = $this->createTree('ptgEQ', $result, $result2);
        } elseif ($this->currentToken == '<>') {
            $this->advance();
            $result2 = $this->expression();
            $result = $this->createTree('ptgNE', $result, $result2);
        }

        return $result;
    }

    /**
     * It parses a expression. It assumes the following rule:
     * Expr -> Term [("+" | "-") Term]
     *      -> "string"
     *      -> "-" Term : Negative value
     *      -> "+" Term : Positive value
     *      -> Error code.
     *
     * @return array The parsed ptg'd tree on success
     */
    private function expression(): array
    {
        // If it's a string return a string node
        if (Preg::isMatch('/"([^"]|""){0,255}"/', $this->currentToken)) {
            $tmp = str_replace('""', '"', $this->currentToken);
            if (($tmp == '"') || ($tmp == '')) {
                //    Trap for "" that has been used for an empty string
                $tmp = '""';
            }
            $result = $this->createTree($tmp, '', '');
            $this->advance();

            return $result;
        }
        if (
            Preg::isMatch('/^#[A-Z0\/]{3,5}[!?]{1}$/', $this->currentToken)
            || $this->currentToken == '#N/A'
        ) { // error code
            $result = $this->createTree($this->currentToken, 'ptgErr', '');
            $this->advance();

            return $result;
        }
        if ($this->currentToken == '-') { // negative value
            // catch "-" Term
            $this->advance();
            $result2 = $this->expression();

            return $this->createTree('ptgUminus', $result2, '');
        } elseif ($this->currentToken == '+') { // positive value
            // catch "+" Term
            $this->advance();
            $result2 = $this->expression();

            return $this->createTree('ptgUplus', $result2, '');
        }
        $result = $this->term();
        while ($this->currentToken === '&') {
            $this->advance();
            $result2 = $this->expression();
            $result = $this->createTree('ptgConcat', $result, $result2);
        }
        while (
            ($this->currentToken == '+')
            || ($this->currentToken == '-')
            || ($this->currentToken == '^')
        ) {
            if ($this->currentToken == '+') {
                $this->advance();
                $result2 = $this->term();
                $result = $this->createTree('ptgAdd', $result, $result2);
            } elseif ($this->currentToken == '-') {
                $this->advance();
                $result2 = $this->term();
                $result = $this->createTree('ptgSub', $result, $result2);
            } else {
                $this->advance();
                $result2 = $this->term();
                $result = $this->createTree('ptgPower', $result, $result2);
            }
        }

        return $result;
    }

    /**
     * This function just introduces a ptgParen element in the tree, so that Excel
     * doesn't get confused when working with a parenthesized formula afterwards.
     *
     * @return array The parsed ptg'd tree
     *
     * @see fact()
     */
    private function parenthesizedExpression(): array
    {
        return $this->createTree('ptgParen', $this->expression(), '');
    }

    /**
     * It parses a term. It assumes the following rule:
     * Term -> Fact [("*" | "/") Fact].
     *
     * @return array The parsed ptg'd tree on success
     */
    private function term(): array
    {
        $result = $this->fact();
        while (
            ($this->currentToken == '*')
            || ($this->currentToken == '/')
        ) {
            if ($this->currentToken == '*') {
                $this->advance();
                $result2 = $this->fact();
                $result = $this->createTree('ptgMul', $result, $result2);
            } else {
                $this->advance();
                $result2 = $this->fact();
                $result = $this->createTree('ptgDiv', $result, $result2);
            }
        }

        return $result;
    }

    /**
     * It parses a factor. It assumes the following rule:
     * Fact -> ( Expr )
     *       | CellRef
     *       | CellRange
     *       | Number
     *       | Function.
     *
     * @return array The parsed ptg'd tree on success
     */
    private function fact(): array
    {
        $currentToken = $this->currentToken;
        if ($currentToken === '(') {
            $this->advance(); // eat the "("
            $result = $this->parenthesizedExpression();
            if ($this->currentToken !== ')') {
                throw new WriterException("')' token expected.");
            }
            $this->advance(); // eat the ")"

            return $result;
        }
        // if it's a reference
        if (Preg::isMatch('/^\$?[A-Ia-i]?[A-Za-z]\$?\d+$/', $this->currentToken)) {
            $result = $this->createTree($this->currentToken, '', '');
            $this->advance();

            return $result;
        }
        if (
            Preg::isMatch(
                '/^'
                . self::REGEX_SHEET_TITLE_UNQUOTED
                . '(\:' . self::REGEX_SHEET_TITLE_UNQUOTED
                . ')?\!\$?[A-Ia-i]?[A-Za-z]\$?\d+$/u',
                $this->currentToken
            )
        ) {
            // If it's an external reference (Sheet1!A1 or Sheet1:Sheet2!A1 or Sheet1!$A$1 or Sheet1:Sheet2!$A$1)
            $result = $this->createTree($this->currentToken, '', '');
            $this->advance();

            return $result;
        }
        if (self::matchCellSheetnameQuoted($this->currentToken)) {
            // If it's an external reference ('Sheet1'!A1 or 'Sheet1:Sheet2'!A1 or 'Sheet1'!$A$1 or 'Sheet1:Sheet2'!$A$1)
            $result = $this->createTree($this->currentToken, '', '');
            $this->advance();

            return $result;
        }
        if (
            Preg::isMatch(
                '/^(\$)?[A-Ia-i]?[A-Za-z](\$)?\d+:(\$)?[A-Ia-i]?[A-Za-z](\$)?\d+$/',
                $this->currentToken
            )
            || Preg::isMatch(
                '/^(\$)?[A-Ia-i]?[A-Za-z](\$)?\d+\.\.(\$)?[A-Ia-i]?[A-Za-z](\$)?\d+$/',
                $this->currentToken
            )
        ) {
            // if it's a range A1:B2 or $A$1:$B$2
            // must be an error?
            $result = $this->createTree($this->currentToken, '', '');
            $this->advance();

            return $result;
        }
        if (
            Preg::isMatch(
                '/^'
                . self::REGEX_SHEET_TITLE_UNQUOTED
                . '(\:'
                . self::REGEX_SHEET_TITLE_UNQUOTED
                . ')?\!\$?([A-Ia-i]?[A-Za-z])?\$?\d+:\$?([A-Ia-i]?[A-Za-z])?\$?\d+$/u',
                $this->currentToken
            )
        ) {
            // If it's an external range (Sheet1!A1:B2 or Sheet1:Sheet2!A1:B2 or Sheet1!$A$1:$B$2 or Sheet1:Sheet2!$A$1:$B$2)
            // must be an error?
            $result = $this->createTree($this->currentToken, '', '');
            $this->advance();

            return $result;
        }
        if (self::matchRangeSheetnameQuoted($this->currentToken)) {
            // If it's an external range ('Sheet1'!A1:B2 or 'Sheet1'!A1:B2 or 'Sheet1'!$A$1:$B$2 or 'Sheet1'!$A$1:$B$2)
            // must be an error?
            $result = $this->createTree($this->currentToken, '', '');
            $this->advance();

            return $result;
        }
        if (is_numeric($this->currentToken)) {
            // If it's a number or a percent
            if ($this->lookAhead === '%') {
                $result = $this->createTree('ptgPercent', $this->currentToken, '');
                $this->advance(); // Skip the percentage operator once we've pre-built that tree
            } else {
                $result = $this->createTree($this->currentToken, '', '');
            }
            $this->advance();

            return $result;
        }
        if (
            Preg::isMatch("/^[A-Z0-9\xc0-\xdc\\.]+$/i", $this->currentToken)
            && ($this->lookAhead === '(')
        ) {
            // if it's a function call
            return $this->func();
        }
        if (
            Preg::isMatch(
                '/^'
                . Calculation::CALCULATION_REGEXP_DEFINEDNAME
                . '$/miu',
                $this->currentToken
            )
            && $this->spreadsheet->getDefinedName($this->currentToken) !== null
        ) {
            $result = $this->createTree('ptgName', $this->currentToken, '');
            $this->advance();

            return $result;
        }
        if (Preg::isMatch('/^true|false$/i', $this->currentToken)) {
            $result = $this->createTree($this->currentToken, '', '');
            $this->advance();

            return $result;
        }

        throw new WriterException('Syntax error: ' . $this->currentToken . ', lookahead: ' . $this->lookAhead . ', current char: ' . $this->currentCharacter);
    }

    /**
     * It parses a function call. It assumes the following rule:
     * Func -> ( Expr [,Expr]* ).
     *
     * @return array The parsed ptg'd tree on success
     */
    private function func(): array
    {
        $num_args = 0; // number of arguments received
        $function = strtoupper($this->currentToken);
        $result = ''; // initialize result
        $this->advance();
        $this->advance(); // eat the "("
        while ($this->currentToken !== ')') {
            if ($num_args > 0) {
                if ($this->currentToken === ',' || $this->currentToken === ';') {
                    $this->advance(); // eat the "," or ";"
                } else {
                    throw new WriterException("Syntax error: comma expected in function $function, arg #{$num_args}");
                }
                $result2 = $this->condition();
                $result = $this->createTree('arg', $result, $result2);
            } else { // first argument
                $result2 = $this->condition();
                $result = $this->createTree('arg', '', $result2);
            }
            ++$num_args;
        }
        if (!isset($this->functions[$function])) {
            throw new WriterException("Function $function() doesn't exist");
        }
        $args = $this->functions[$function][1];
        // If fixed number of args eg. TIME($i, $j, $k). Check that the number of args is valid.
        if (($args >= 0) && ($args != $num_args)) {
            throw new WriterException("Incorrect number of arguments in function $function() ");
        }

        $result = $this->createTree($function, $result, $num_args);
        $this->advance(); // eat the ")"

        return $result;
    }

    /**
     * Creates a tree. In fact an array which may have one or two arrays (sub-trees)
     * as elements.
     *
     * @param mixed $value the value of this node
     * @param mixed $left the left array (sub-tree) or a final node
     * @param mixed $right the right array (sub-tree) or a final node
     *
     * @return array A tree
     */
    private function createTree(mixed $value, mixed $left, mixed $right): array
    {
        return ['value' => $value, 'left' => $left, 'right' => $right];
    }

    /**
     * Builds a string containing the tree in reverse polish notation (What you
     * would use in a HP calculator stack).
     * The following tree:.
     *
     *    +
     *   / \
     *  2   3
     *
     * produces: "23+"
     *
     * The following tree:
     *
     *    +
     *   / \
     *  3   *
     *     / \
     *    6   A1
     *
     * produces: "36A1*+"
     *
     * In fact all operands, functions, references, etc... are written as ptg's
     *
     * @param array $tree the optional tree to convert
     *
     * @return string The tree in reverse polish notation
     */
    public function toReversePolish(array $tree = []): string
    {
        $polish = ''; // the string we are going to return
        if (empty($tree)) { // If it's the first call use parseTree
            $tree = $this->parseTree;
        }
        if (!is_array($tree) || !isset($tree['left'], $tree['right'], $tree['value'])) {
            throw new WriterException('Unexpected non-array');
        }

        if (is_array($tree['left'])) {
            $converted_tree = $this->toReversePolish($tree['left']);
            $polish .= $converted_tree;
        } elseif ($tree['left'] != '') { // It's a final node
            $converted_tree = $this->convert($tree['left']);
            $polish .= $converted_tree;
        }
        if (is_array($tree['right'])) {
            $converted_tree = $this->toReversePolish($tree['right']);
            $polish .= $converted_tree;
        } elseif ($tree['right'] != '') { // It's a final node
            $converted_tree = $this->convert($tree['right']);
            $polish .= $converted_tree;
        }
        // if it's a function convert it here (so we can set it's arguments)
        if (
            Preg::isMatch("/^[A-Z0-9\xc0-\xdc\\.]+$/", $tree['value'])
            && !Preg::isMatch('/^([A-Ia-i]?[A-Za-z])(\d+)$/', $tree['value'])
            && !Preg::isMatch(
                '/^[A-Ia-i]?[A-Za-z](\d+)\.\.[A-Ia-i]?[A-Za-z](\d+)$/',
                $tree['value']
            )
            && !is_numeric($tree['value'])
            && !isset($this->ptg[$tree['value']])
        ) {
            // left subtree for a function is always an array.
            if ($tree['left'] != '') {
                $left_tree = $this->toReversePolish($tree['left']);
            } else {
                $left_tree = '';
            }

            // add its left subtree and return.
            if ($left_tree !== '' || $tree['right'] !== '') {
                return $left_tree . $this->convertFunction($tree['value'], $tree['right'] ?: 0);
            }
        }
        $converted_tree = $this->convert($tree['value']);

        return $polish . $converted_tree;
    }

    public static function matchCellSheetnameQuoted(string $token): bool
    {
        return Preg::isMatch(
            self::REGEX_CELL_TITLE_QUOTED,
            $token
        );
    }

    public static function matchRangeSheetnameQuoted(string $token): bool
    {
        return Preg::isMatch(
            self::REGEX_RANGE_TITLE_QUOTED,
            $token
        );
    }
}
