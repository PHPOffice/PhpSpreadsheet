<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Engine\BranchPruner;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\CyclicReferenceStack;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\Logger;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\Operands;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Calculation\Token\Stack;
use PhpOffice\PhpSpreadsheet\Cell\AddressRange;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\DefinedName;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\ReferenceHelper;
use PhpOffice\PhpSpreadsheet\Shared;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use ReflectionClassConstant;
use ReflectionMethod;
use ReflectionParameter;
use Throwable;

class Calculation
{
    /** Constants                */
    /** Regular Expressions        */
    //    Numeric operand
    const CALCULATION_REGEXP_NUMBER = '[-+]?\d*\.?\d+(e[-+]?\d+)?';
    //    String operand
    const CALCULATION_REGEXP_STRING = '"(?:[^"]|"")*"';
    //    Opening bracket
    const CALCULATION_REGEXP_OPENBRACE = '\(';
    //    Function (allow for the old @ symbol that could be used to prefix a function, but we'll ignore it)
    const CALCULATION_REGEXP_FUNCTION = '@?(?:_xlfn\.)?(?:_xlws\.)?([\p{L}][\p{L}\p{N}\.]*)[\s]*\(';
    //    Strip xlfn and xlws prefixes from function name
    const CALCULATION_REGEXP_STRIP_XLFN_XLWS = '/(_xlfn[.])?(_xlws[.])?(?=[\p{L}][\p{L}\p{N}\.]*[\s]*[(])/';
    //    Cell reference (cell or range of cells, with or without a sheet reference)
    const CALCULATION_REGEXP_CELLREF = '((([^\s,!&%^\/\*\+<>=:`-]*)|(\'(?:[^\']|\'[^!])+?\')|(\"(?:[^\"]|\"[^!])+?\"))!)?\$?\b([a-z]{1,3})\$?(\d{1,7})(?![\w.])';
    // Used only to detect spill operator #
    const CALCULATION_REGEXP_CELLREF_SPILL = '/' . self::CALCULATION_REGEXP_CELLREF . '#/i';
    //    Cell reference (with or without a sheet reference) ensuring absolute/relative
    const CALCULATION_REGEXP_CELLREF_RELATIVE = '((([^\s\(,!&%^\/\*\+<>=:`-]*)|(\'(?:[^\']|\'[^!])+?\')|(\"(?:[^\"]|\"[^!])+?\"))!)?(\$?\b[a-z]{1,3})(\$?\d{1,7})(?![\w.])';
    const CALCULATION_REGEXP_COLUMN_RANGE = '(((([^\s\(,!&%^\/\*\+<>=:`-]*)|(\'(?:[^\']|\'[^!])+?\')|(\".(?:[^\"]|\"[^!])?\"))!)?(\$?[a-z]{1,3})):(?![.*])';
    const CALCULATION_REGEXP_ROW_RANGE = '(((([^\s\(,!&%^\/\*\+<>=:`-]*)|(\'(?:[^\']|\'[^!])+?\')|(\"(?:[^\"]|\"[^!])+?\"))!)?(\$?[1-9][0-9]{0,6})):(?![.*])';
    //    Cell reference (with or without a sheet reference) ensuring absolute/relative
    //    Cell ranges ensuring absolute/relative
    const CALCULATION_REGEXP_COLUMNRANGE_RELATIVE = '(\$?[a-z]{1,3}):(\$?[a-z]{1,3})';
    const CALCULATION_REGEXP_ROWRANGE_RELATIVE = '(\$?\d{1,7}):(\$?\d{1,7})';
    //    Defined Names: Named Range of cells, or Named Formulae
    const CALCULATION_REGEXP_DEFINEDNAME = '((([^\s,!&%^\/\*\+<>=-]*)|(\'(?:[^\']|\'[^!])+?\')|(\"(?:[^\"]|\"[^!])+?\"))!)?([_\p{L}][_\p{L}\p{N}\.]*)';
    // Structured Reference (Fully Qualified and Unqualified)
    const CALCULATION_REGEXP_STRUCTURED_REFERENCE = '([\p{L}_\\\\][\p{L}\p{N}\._]+)?(\[(?:[^\d\]+-])?)';
    //    Error
    const CALCULATION_REGEXP_ERROR = '\#[A-Z][A-Z0_\/]*[!\?]?';

    /** constants */
    const RETURN_ARRAY_AS_ERROR = 'error';
    const RETURN_ARRAY_AS_VALUE = 'value';
    const RETURN_ARRAY_AS_ARRAY = 'array';

    const FORMULA_OPEN_FUNCTION_BRACE = '(';
    const FORMULA_CLOSE_FUNCTION_BRACE = ')';
    const FORMULA_OPEN_MATRIX_BRACE = '{';
    const FORMULA_CLOSE_MATRIX_BRACE = '}';
    const FORMULA_STRING_QUOTE = '"';

    /** Preferable to use instance variable instanceArrayReturnType rather than this static property. */
    private static string $returnArrayAsType = self::RETURN_ARRAY_AS_VALUE;

    /** Preferable to use this instance variable rather than static returnArrayAsType */
    private ?string $instanceArrayReturnType = null;

    /**
     * Instance of this class.
     */
    private static ?Calculation $instance = null;

    /**
     * Instance of the spreadsheet this Calculation Engine is using.
     */
    private ?Spreadsheet $spreadsheet;

    /**
     * Calculation cache.
     */
    private array $calculationCache = [];

    /**
     * Calculation cache enabled.
     */
    private bool $calculationCacheEnabled = true;

    private BranchPruner $branchPruner;

    private bool $branchPruningEnabled = true;

    /**
     * List of operators that can be used within formulae
     * The true/false value indicates whether it is a binary operator or a unary operator.
     */
    private const CALCULATION_OPERATORS = [
        '+' => true, '-' => true, '*' => true, '/' => true,
        '^' => true, '&' => true, '%' => false, '~' => false,
        '>' => true, '<' => true, '=' => true, '>=' => true,
        '<=' => true, '<>' => true, '∩' => true, '∪' => true,
        ':' => true,
    ];

    /**
     * List of binary operators (those that expect two operands).
     */
    private const BINARY_OPERATORS = [
        '+' => true, '-' => true, '*' => true, '/' => true,
        '^' => true, '&' => true, '>' => true, '<' => true,
        '=' => true, '>=' => true, '<=' => true, '<>' => true,
        '∩' => true, '∪' => true, ':' => true,
    ];

    /**
     * The debug log generated by the calculation engine.
     */
    private Logger $debugLog;

    private bool $suppressFormulaErrors = false;

    private bool $processingAnchorArray = false;

    /**
     * Error message for any error that was raised/thrown by the calculation engine.
     */
    public ?string $formulaError = null;

    /**
     * Reference Helper.
     */
    private static ReferenceHelper $referenceHelper;

    /**
     * An array of the nested cell references accessed by the calculation engine, used for the debug log.
     */
    private CyclicReferenceStack $cyclicReferenceStack;

    private array $cellStack = [];

    /**
     * Current iteration counter for cyclic formulae
     * If the value is 0 (or less) then cyclic formulae will throw an exception,
     * otherwise they will iterate to the limit defined here before returning a result.
     */
    private int $cyclicFormulaCounter = 1;

    private string $cyclicFormulaCell = '';

    /**
     * Number of iterations for cyclic formulae.
     */
    public int $cyclicFormulaCount = 1;

    /**
     * The current locale setting.
     */
    private static string $localeLanguage = 'en_us'; //    US English    (default locale)

    /**
     * List of available locale settings
     * Note that this is read for the locale subdirectory only when requested.
     *
     * @var string[]
     */
    private static array $validLocaleLanguages = [
        'en', //    English        (default language)
    ];

    /**
     * Locale-specific argument separator for function arguments.
     */
    private static string $localeArgumentSeparator = ',';

    private static array $localeFunctions = [];

    /**
     * Locale-specific translations for Excel constants (True, False and Null).
     *
     * @var array<string, string>
     */
    private static array $localeBoolean = [
        'TRUE' => 'TRUE',
        'FALSE' => 'FALSE',
        'NULL' => 'NULL',
    ];

    public static function getLocaleBoolean(string $index): string
    {
        return self::$localeBoolean[$index];
    }

    /**
     * Excel constant string translations to their PHP equivalents
     * Constant conversion from text name/value to actual (datatyped) value.
     *
     * @var array<string, null|bool>
     */
    private static array $excelConstants = [
        'TRUE' => true,
        'FALSE' => false,
        'NULL' => null,
    ];

    public static function keyInExcelConstants(string $key): bool
    {
        return array_key_exists($key, self::$excelConstants);
    }

    public static function getExcelConstants(string $key): bool|null
    {
        return self::$excelConstants[$key];
    }

    /**
     * Array of functions usable on Spreadsheet.
     * In theory, this could be const rather than static;
     *   however, Phpstan breaks trying to analyze it when attempted.
     */
    private static array $phpSpreadsheetFunctions = [
        'ABS' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Absolute::class, 'evaluate'],
            'argumentCount' => '1',
        ],
        'ACCRINT' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\Securities\AccruedInterest::class, 'periodic'],
            'argumentCount' => '4-8',
        ],
        'ACCRINTM' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\Securities\AccruedInterest::class, 'atMaturity'],
            'argumentCount' => '3-5',
        ],
        'ACOS' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Trig\Cosine::class, 'acos'],
            'argumentCount' => '1',
        ],
        'ACOSH' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Trig\Cosine::class, 'acosh'],
            'argumentCount' => '1',
        ],
        'ACOT' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Trig\Cotangent::class, 'acot'],
            'argumentCount' => '1',
        ],
        'ACOTH' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Trig\Cotangent::class, 'acoth'],
            'argumentCount' => '1',
        ],
        'ADDRESS' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef\Address::class, 'cell'],
            'argumentCount' => '2-5',
        ],
        'AGGREGATE' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3+',
        ],
        'AMORDEGRC' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\Amortization::class, 'AMORDEGRC'],
            'argumentCount' => '6,7',
        ],
        'AMORLINC' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\Amortization::class, 'AMORLINC'],
            'argumentCount' => '6,7',
        ],
        'ANCHORARRAY' => [
            'category' => Category::CATEGORY_MICROSOFT_INTERNAL,
            'functionCall' => [Internal\ExcelArrayPseudoFunctions::class, 'anchorArray'],
            'argumentCount' => '1',
            'passCellReference' => true,
            'passByReference' => [true],
        ],
        'AND' => [
            'category' => Category::CATEGORY_LOGICAL,
            'functionCall' => [Logical\Operations::class, 'logicalAnd'],
            'argumentCount' => '1+',
        ],
        'ARABIC' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Arabic::class, 'evaluate'],
            'argumentCount' => '1',
        ],
        'AREAS' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1',
        ],
        'ARRAYTOTEXT' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Text::class, 'fromArray'],
            'argumentCount' => '1,2',
        ],
        'ASC' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1',
        ],
        'ASIN' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Trig\Sine::class, 'asin'],
            'argumentCount' => '1',
        ],
        'ASINH' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Trig\Sine::class, 'asinh'],
            'argumentCount' => '1',
        ],
        'ATAN' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Trig\Tangent::class, 'atan'],
            'argumentCount' => '1',
        ],
        'ATAN2' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Trig\Tangent::class, 'atan2'],
            'argumentCount' => '2',
        ],
        'ATANH' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Trig\Tangent::class, 'atanh'],
            'argumentCount' => '1',
        ],
        'AVEDEV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Averages::class, 'averageDeviations'],
            'argumentCount' => '1+',
        ],
        'AVERAGE' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Averages::class, 'average'],
            'argumentCount' => '1+',
        ],
        'AVERAGEA' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Averages::class, 'averageA'],
            'argumentCount' => '1+',
        ],
        'AVERAGEIF' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Conditional::class, 'AVERAGEIF'],
            'argumentCount' => '2,3',
        ],
        'AVERAGEIFS' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Conditional::class, 'AVERAGEIFS'],
            'argumentCount' => '3+',
        ],
        'BAHTTEXT' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1',
        ],
        'BASE' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Base::class, 'evaluate'],
            'argumentCount' => '2,3',
        ],
        'BESSELI' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\BesselI::class, 'BESSELI'],
            'argumentCount' => '2',
        ],
        'BESSELJ' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\BesselJ::class, 'BESSELJ'],
            'argumentCount' => '2',
        ],
        'BESSELK' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\BesselK::class, 'BESSELK'],
            'argumentCount' => '2',
        ],
        'BESSELY' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\BesselY::class, 'BESSELY'],
            'argumentCount' => '2',
        ],
        'BETADIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\Beta::class, 'distribution'],
            'argumentCount' => '3-5',
        ],
        'BETA.DIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '4-6',
        ],
        'BETAINV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\Beta::class, 'inverse'],
            'argumentCount' => '3-5',
        ],
        'BETA.INV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\Beta::class, 'inverse'],
            'argumentCount' => '3-5',
        ],
        'BIN2DEC' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ConvertBinary::class, 'toDecimal'],
            'argumentCount' => '1',
        ],
        'BIN2HEX' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ConvertBinary::class, 'toHex'],
            'argumentCount' => '1,2',
        ],
        'BIN2OCT' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ConvertBinary::class, 'toOctal'],
            'argumentCount' => '1,2',
        ],
        'BINOMDIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\Binomial::class, 'distribution'],
            'argumentCount' => '4',
        ],
        'BINOM.DIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\Binomial::class, 'distribution'],
            'argumentCount' => '4',
        ],
        'BINOM.DIST.RANGE' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\Binomial::class, 'range'],
            'argumentCount' => '3,4',
        ],
        'BINOM.INV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\Binomial::class, 'inverse'],
            'argumentCount' => '3',
        ],
        'BITAND' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\BitWise::class, 'BITAND'],
            'argumentCount' => '2',
        ],
        'BITOR' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\BitWise::class, 'BITOR'],
            'argumentCount' => '2',
        ],
        'BITXOR' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\BitWise::class, 'BITXOR'],
            'argumentCount' => '2',
        ],
        'BITLSHIFT' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\BitWise::class, 'BITLSHIFT'],
            'argumentCount' => '2',
        ],
        'BITRSHIFT' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\BitWise::class, 'BITRSHIFT'],
            'argumentCount' => '2',
        ],
        'BYCOL' => [
            'category' => Category::CATEGORY_LOGICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '*',
        ],
        'BYROW' => [
            'category' => Category::CATEGORY_LOGICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '*',
        ],
        'CEILING' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Ceiling::class, 'ceiling'],
            'argumentCount' => '1-2', // 2 for Excel, 1-2 for Ods/Gnumeric
        ],
        'CEILING.MATH' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Ceiling::class, 'math'],
            'argumentCount' => '1-3',
        ],
        'CEILING.PRECISE' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Ceiling::class, 'precise'],
            'argumentCount' => '1,2',
        ],
        'CELL' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1,2',
        ],
        'CHAR' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\CharacterConvert::class, 'character'],
            'argumentCount' => '1',
        ],
        'CHIDIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\ChiSquared::class, 'distributionRightTail'],
            'argumentCount' => '2',
        ],
        'CHISQ.DIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\ChiSquared::class, 'distributionLeftTail'],
            'argumentCount' => '3',
        ],
        'CHISQ.DIST.RT' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\ChiSquared::class, 'distributionRightTail'],
            'argumentCount' => '2',
        ],
        'CHIINV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\ChiSquared::class, 'inverseRightTail'],
            'argumentCount' => '2',
        ],
        'CHISQ.INV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\ChiSquared::class, 'inverseLeftTail'],
            'argumentCount' => '2',
        ],
        'CHISQ.INV.RT' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\ChiSquared::class, 'inverseRightTail'],
            'argumentCount' => '2',
        ],
        'CHITEST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\ChiSquared::class, 'test'],
            'argumentCount' => '2',
        ],
        'CHISQ.TEST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\ChiSquared::class, 'test'],
            'argumentCount' => '2',
        ],
        'CHOOSE' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef\Selection::class, 'CHOOSE'],
            'argumentCount' => '2+',
        ],
        'CHOOSECOLS' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2+',
        ],
        'CHOOSEROWS' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2+',
        ],
        'CLEAN' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Trim::class, 'nonPrintable'],
            'argumentCount' => '1',
        ],
        'CODE' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\CharacterConvert::class, 'code'],
            'argumentCount' => '1',
        ],
        'COLUMN' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef\RowColumnInformation::class, 'COLUMN'],
            'argumentCount' => '-1',
            'passCellReference' => true,
            'passByReference' => [true],
        ],
        'COLUMNS' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef\RowColumnInformation::class, 'COLUMNS'],
            'argumentCount' => '1',
        ],
        'COMBIN' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Combinations::class, 'withoutRepetition'],
            'argumentCount' => '2',
        ],
        'COMBINA' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Combinations::class, 'withRepetition'],
            'argumentCount' => '2',
        ],
        'COMPLEX' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\Complex::class, 'COMPLEX'],
            'argumentCount' => '2,3',
        ],
        'CONCAT' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Concatenate::class, 'CONCATENATE'],
            'argumentCount' => '1+',
        ],
        'CONCATENATE' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Concatenate::class, 'actualCONCATENATE'],
            'argumentCount' => '1+',
        ],
        'CONFIDENCE' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Confidence::class, 'CONFIDENCE'],
            'argumentCount' => '3',
        ],
        'CONFIDENCE.NORM' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Confidence::class, 'CONFIDENCE'],
            'argumentCount' => '3',
        ],
        'CONFIDENCE.T' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3',
        ],
        'CONVERT' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ConvertUOM::class, 'CONVERT'],
            'argumentCount' => '3',
        ],
        'CORREL' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Trends::class, 'CORREL'],
            'argumentCount' => '2',
        ],
        'COS' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Trig\Cosine::class, 'cos'],
            'argumentCount' => '1',
        ],
        'COSH' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Trig\Cosine::class, 'cosh'],
            'argumentCount' => '1',
        ],
        'COT' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Trig\Cotangent::class, 'cot'],
            'argumentCount' => '1',
        ],
        'COTH' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Trig\Cotangent::class, 'coth'],
            'argumentCount' => '1',
        ],
        'COUNT' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Counts::class, 'COUNT'],
            'argumentCount' => '1+',
        ],
        'COUNTA' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Counts::class, 'COUNTA'],
            'argumentCount' => '1+',
        ],
        'COUNTBLANK' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Counts::class, 'COUNTBLANK'],
            'argumentCount' => '1',
        ],
        'COUNTIF' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Conditional::class, 'COUNTIF'],
            'argumentCount' => '2',
        ],
        'COUNTIFS' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Conditional::class, 'COUNTIFS'],
            'argumentCount' => '2+',
        ],
        'COUPDAYBS' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\Coupons::class, 'COUPDAYBS'],
            'argumentCount' => '3,4',
        ],
        'COUPDAYS' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\Coupons::class, 'COUPDAYS'],
            'argumentCount' => '3,4',
        ],
        'COUPDAYSNC' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\Coupons::class, 'COUPDAYSNC'],
            'argumentCount' => '3,4',
        ],
        'COUPNCD' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\Coupons::class, 'COUPNCD'],
            'argumentCount' => '3,4',
        ],
        'COUPNUM' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\Coupons::class, 'COUPNUM'],
            'argumentCount' => '3,4',
        ],
        'COUPPCD' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\Coupons::class, 'COUPPCD'],
            'argumentCount' => '3,4',
        ],
        'COVAR' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Trends::class, 'COVAR'],
            'argumentCount' => '2',
        ],
        'COVARIANCE.P' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Trends::class, 'COVAR'],
            'argumentCount' => '2',
        ],
        'COVARIANCE.S' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2',
        ],
        'CRITBINOM' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\Binomial::class, 'inverse'],
            'argumentCount' => '3',
        ],
        'CSC' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Trig\Cosecant::class, 'csc'],
            'argumentCount' => '1',
        ],
        'CSCH' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Trig\Cosecant::class, 'csch'],
            'argumentCount' => '1',
        ],
        'CUBEKPIMEMBER' => [
            'category' => Category::CATEGORY_CUBE,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '?',
        ],
        'CUBEMEMBER' => [
            'category' => Category::CATEGORY_CUBE,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '?',
        ],
        'CUBEMEMBERPROPERTY' => [
            'category' => Category::CATEGORY_CUBE,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '?',
        ],
        'CUBERANKEDMEMBER' => [
            'category' => Category::CATEGORY_CUBE,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '?',
        ],
        'CUBESET' => [
            'category' => Category::CATEGORY_CUBE,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '?',
        ],
        'CUBESETCOUNT' => [
            'category' => Category::CATEGORY_CUBE,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '?',
        ],
        'CUBEVALUE' => [
            'category' => Category::CATEGORY_CUBE,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '?',
        ],
        'CUMIPMT' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\CashFlow\Constant\Periodic\Cumulative::class, 'interest'],
            'argumentCount' => '6',
        ],
        'CUMPRINC' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\CashFlow\Constant\Periodic\Cumulative::class, 'principal'],
            'argumentCount' => '6',
        ],
        'DATE' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTimeExcel\Date::class, 'fromYMD'],
            'argumentCount' => '3',
        ],
        'DATEDIF' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTimeExcel\Difference::class, 'interval'],
            'argumentCount' => '2,3',
        ],
        'DATESTRING' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '?',
        ],
        'DATEVALUE' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTimeExcel\DateValue::class, 'fromString'],
            'argumentCount' => '1',
        ],
        'DAVERAGE' => [
            'category' => Category::CATEGORY_DATABASE,
            'functionCall' => [Database\DAverage::class, 'evaluate'],
            'argumentCount' => '3',
        ],
        'DAY' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTimeExcel\DateParts::class, 'day'],
            'argumentCount' => '1',
        ],
        'DAYS' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTimeExcel\Days::class, 'between'],
            'argumentCount' => '2',
        ],
        'DAYS360' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTimeExcel\Days360::class, 'between'],
            'argumentCount' => '2,3',
        ],
        'DB' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\Depreciation::class, 'DB'],
            'argumentCount' => '4,5',
        ],
        'DBCS' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1',
        ],
        'DCOUNT' => [
            'category' => Category::CATEGORY_DATABASE,
            'functionCall' => [Database\DCount::class, 'evaluate'],
            'argumentCount' => '3',
        ],
        'DCOUNTA' => [
            'category' => Category::CATEGORY_DATABASE,
            'functionCall' => [Database\DCountA::class, 'evaluate'],
            'argumentCount' => '3',
        ],
        'DDB' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\Depreciation::class, 'DDB'],
            'argumentCount' => '4,5',
        ],
        'DEC2BIN' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ConvertDecimal::class, 'toBinary'],
            'argumentCount' => '1,2',
        ],
        'DEC2HEX' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ConvertDecimal::class, 'toHex'],
            'argumentCount' => '1,2',
        ],
        'DEC2OCT' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ConvertDecimal::class, 'toOctal'],
            'argumentCount' => '1,2',
        ],
        'DECIMAL' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2',
        ],
        'DEGREES' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Angle::class, 'toDegrees'],
            'argumentCount' => '1',
        ],
        'DELTA' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\Compare::class, 'DELTA'],
            'argumentCount' => '1,2',
        ],
        'DEVSQ' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Deviations::class, 'sumSquares'],
            'argumentCount' => '1+',
        ],
        'DGET' => [
            'category' => Category::CATEGORY_DATABASE,
            'functionCall' => [Database\DGet::class, 'evaluate'],
            'argumentCount' => '3',
        ],
        'DISC' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\Securities\Rates::class, 'discount'],
            'argumentCount' => '4,5',
        ],
        'DMAX' => [
            'category' => Category::CATEGORY_DATABASE,
            'functionCall' => [Database\DMax::class, 'evaluate'],
            'argumentCount' => '3',
        ],
        'DMIN' => [
            'category' => Category::CATEGORY_DATABASE,
            'functionCall' => [Database\DMin::class, 'evaluate'],
            'argumentCount' => '3',
        ],
        'DOLLAR' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Format::class, 'DOLLAR'],
            'argumentCount' => '1,2',
        ],
        'DOLLARDE' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\Dollar::class, 'decimal'],
            'argumentCount' => '2',
        ],
        'DOLLARFR' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\Dollar::class, 'fractional'],
            'argumentCount' => '2',
        ],
        'DPRODUCT' => [
            'category' => Category::CATEGORY_DATABASE,
            'functionCall' => [Database\DProduct::class, 'evaluate'],
            'argumentCount' => '3',
        ],
        'DROP' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2-3',
        ],
        'DSTDEV' => [
            'category' => Category::CATEGORY_DATABASE,
            'functionCall' => [Database\DStDev::class, 'evaluate'],
            'argumentCount' => '3',
        ],
        'DSTDEVP' => [
            'category' => Category::CATEGORY_DATABASE,
            'functionCall' => [Database\DStDevP::class, 'evaluate'],
            'argumentCount' => '3',
        ],
        'DSUM' => [
            'category' => Category::CATEGORY_DATABASE,
            'functionCall' => [Database\DSum::class, 'evaluate'],
            'argumentCount' => '3',
        ],
        'DURATION' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '5,6',
        ],
        'DVAR' => [
            'category' => Category::CATEGORY_DATABASE,
            'functionCall' => [Database\DVar::class, 'evaluate'],
            'argumentCount' => '3',
        ],
        'DVARP' => [
            'category' => Category::CATEGORY_DATABASE,
            'functionCall' => [Database\DVarP::class, 'evaluate'],
            'argumentCount' => '3',
        ],
        'ECMA.CEILING' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1,2',
        ],
        'EDATE' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTimeExcel\Month::class, 'adjust'],
            'argumentCount' => '2',
        ],
        'EFFECT' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\InterestRate::class, 'effective'],
            'argumentCount' => '2',
        ],
        'ENCODEURL' => [
            'category' => Category::CATEGORY_WEB,
            'functionCall' => [Web\Service::class, 'urlEncode'],
            'argumentCount' => '1',
        ],
        'EOMONTH' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTimeExcel\Month::class, 'lastDay'],
            'argumentCount' => '2',
        ],
        'ERF' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\Erf::class, 'ERF'],
            'argumentCount' => '1,2',
        ],
        'ERF.PRECISE' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\Erf::class, 'ERFPRECISE'],
            'argumentCount' => '1',
        ],
        'ERFC' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ErfC::class, 'ERFC'],
            'argumentCount' => '1',
        ],
        'ERFC.PRECISE' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ErfC::class, 'ERFC'],
            'argumentCount' => '1',
        ],
        'ERROR.TYPE' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [ExcelError::class, 'type'],
            'argumentCount' => '1',
        ],
        'EVEN' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Round::class, 'even'],
            'argumentCount' => '1',
        ],
        'EXACT' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Text::class, 'exact'],
            'argumentCount' => '2',
        ],
        'EXP' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Exp::class, 'evaluate'],
            'argumentCount' => '1',
        ],
        'EXPAND' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2-4',
        ],
        'EXPONDIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\Exponential::class, 'distribution'],
            'argumentCount' => '3',
        ],
        'EXPON.DIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\Exponential::class, 'distribution'],
            'argumentCount' => '3',
        ],
        'FACT' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Factorial::class, 'fact'],
            'argumentCount' => '1',
        ],
        'FACTDOUBLE' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Factorial::class, 'factDouble'],
            'argumentCount' => '1',
        ],
        'FALSE' => [
            'category' => Category::CATEGORY_LOGICAL,
            'functionCall' => [Logical\Boolean::class, 'FALSE'],
            'argumentCount' => '0',
        ],
        'FDIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3',
        ],
        'F.DIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\F::class, 'distribution'],
            'argumentCount' => '4',
        ],
        'F.DIST.RT' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3',
        ],
        'FILTER' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef\Filter::class, 'filter'],
            'argumentCount' => '2-3',
        ],
        'FILTERXML' => [
            'category' => Category::CATEGORY_WEB,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2',
        ],
        'FIND' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Search::class, 'sensitive'],
            'argumentCount' => '2,3',
        ],
        'FINDB' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Search::class, 'sensitive'],
            'argumentCount' => '2,3',
        ],
        'FINV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3',
        ],
        'F.INV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3',
        ],
        'F.INV.RT' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3',
        ],
        'FISHER' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\Fisher::class, 'distribution'],
            'argumentCount' => '1',
        ],
        'FISHERINV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\Fisher::class, 'inverse'],
            'argumentCount' => '1',
        ],
        'FIXED' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Format::class, 'FIXEDFORMAT'],
            'argumentCount' => '1-3',
        ],
        'FLOOR' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Floor::class, 'floor'],
            'argumentCount' => '1-2', // Excel requries 2, Ods/Gnumeric 1-2
        ],
        'FLOOR.MATH' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Floor::class, 'math'],
            'argumentCount' => '1-3',
        ],
        'FLOOR.PRECISE' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Floor::class, 'precise'],
            'argumentCount' => '1-2',
        ],
        'FORECAST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Trends::class, 'FORECAST'],
            'argumentCount' => '3',
        ],
        'FORECAST.ETS' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3-6',
        ],
        'FORECAST.ETS.CONFINT' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3-6',
        ],
        'FORECAST.ETS.SEASONALITY' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2-4',
        ],
        'FORECAST.ETS.STAT' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3-6',
        ],
        'FORECAST.LINEAR' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Trends::class, 'FORECAST'],
            'argumentCount' => '3',
        ],
        'FORMULATEXT' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef\Formula::class, 'text'],
            'argumentCount' => '1',
            'passCellReference' => true,
            'passByReference' => [true],
        ],
        'FREQUENCY' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2',
        ],
        'FTEST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2',
        ],
        'F.TEST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2',
        ],
        'FV' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\CashFlow\Constant\Periodic::class, 'futureValue'],
            'argumentCount' => '3-5',
        ],
        'FVSCHEDULE' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\CashFlow\Single::class, 'futureValue'],
            'argumentCount' => '2',
        ],
        'GAMMA' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\Gamma::class, 'gamma'],
            'argumentCount' => '1',
        ],
        'GAMMADIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\Gamma::class, 'distribution'],
            'argumentCount' => '4',
        ],
        'GAMMA.DIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\Gamma::class, 'distribution'],
            'argumentCount' => '4',
        ],
        'GAMMAINV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\Gamma::class, 'inverse'],
            'argumentCount' => '3',
        ],
        'GAMMA.INV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\Gamma::class, 'inverse'],
            'argumentCount' => '3',
        ],
        'GAMMALN' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\Gamma::class, 'ln'],
            'argumentCount' => '1',
        ],
        'GAMMALN.PRECISE' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\Gamma::class, 'ln'],
            'argumentCount' => '1',
        ],
        'GAUSS' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\StandardNormal::class, 'gauss'],
            'argumentCount' => '1',
        ],
        'GCD' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Gcd::class, 'evaluate'],
            'argumentCount' => '1+',
        ],
        'GEOMEAN' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Averages\Mean::class, 'geometric'],
            'argumentCount' => '1+',
        ],
        'GESTEP' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\Compare::class, 'GESTEP'],
            'argumentCount' => '1,2',
        ],
        'GETPIVOTDATA' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2+',
        ],
        'GROWTH' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Trends::class, 'GROWTH'],
            'argumentCount' => '1-4',
        ],
        'HARMEAN' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Averages\Mean::class, 'harmonic'],
            'argumentCount' => '1+',
        ],
        'HEX2BIN' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ConvertHex::class, 'toBinary'],
            'argumentCount' => '1,2',
        ],
        'HEX2DEC' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ConvertHex::class, 'toDecimal'],
            'argumentCount' => '1',
        ],
        'HEX2OCT' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ConvertHex::class, 'toOctal'],
            'argumentCount' => '1,2',
        ],
        'HLOOKUP' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef\HLookup::class, 'lookup'],
            'argumentCount' => '3,4',
        ],
        'HOUR' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTimeExcel\TimeParts::class, 'hour'],
            'argumentCount' => '1',
        ],
        'HSTACK' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1+',
        ],
        'HYPERLINK' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef\Hyperlink::class, 'set'],
            'argumentCount' => '1,2',
            'passCellReference' => true,
        ],
        'HYPGEOMDIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\HyperGeometric::class, 'distribution'],
            'argumentCount' => '4',
        ],
        'HYPGEOM.DIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '5',
        ],
        'IF' => [
            'category' => Category::CATEGORY_LOGICAL,
            'functionCall' => [Logical\Conditional::class, 'statementIf'],
            'argumentCount' => '2-3',
        ],
        'IFERROR' => [
            'category' => Category::CATEGORY_LOGICAL,
            'functionCall' => [Logical\Conditional::class, 'IFERROR'],
            'argumentCount' => '2',
        ],
        'IFNA' => [
            'category' => Category::CATEGORY_LOGICAL,
            'functionCall' => [Logical\Conditional::class, 'IFNA'],
            'argumentCount' => '2',
        ],
        'IFS' => [
            'category' => Category::CATEGORY_LOGICAL,
            'functionCall' => [Logical\Conditional::class, 'IFS'],
            'argumentCount' => '2+',
        ],
        'IMABS' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ComplexFunctions::class, 'IMABS'],
            'argumentCount' => '1',
        ],
        'IMAGINARY' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\Complex::class, 'IMAGINARY'],
            'argumentCount' => '1',
        ],
        'IMARGUMENT' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ComplexFunctions::class, 'IMARGUMENT'],
            'argumentCount' => '1',
        ],
        'IMCONJUGATE' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ComplexFunctions::class, 'IMCONJUGATE'],
            'argumentCount' => '1',
        ],
        'IMCOS' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ComplexFunctions::class, 'IMCOS'],
            'argumentCount' => '1',
        ],
        'IMCOSH' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ComplexFunctions::class, 'IMCOSH'],
            'argumentCount' => '1',
        ],
        'IMCOT' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ComplexFunctions::class, 'IMCOT'],
            'argumentCount' => '1',
        ],
        'IMCSC' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ComplexFunctions::class, 'IMCSC'],
            'argumentCount' => '1',
        ],
        'IMCSCH' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ComplexFunctions::class, 'IMCSCH'],
            'argumentCount' => '1',
        ],
        'IMDIV' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ComplexOperations::class, 'IMDIV'],
            'argumentCount' => '2',
        ],
        'IMEXP' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ComplexFunctions::class, 'IMEXP'],
            'argumentCount' => '1',
        ],
        'IMLN' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ComplexFunctions::class, 'IMLN'],
            'argumentCount' => '1',
        ],
        'IMLOG10' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ComplexFunctions::class, 'IMLOG10'],
            'argumentCount' => '1',
        ],
        'IMLOG2' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ComplexFunctions::class, 'IMLOG2'],
            'argumentCount' => '1',
        ],
        'IMPOWER' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ComplexFunctions::class, 'IMPOWER'],
            'argumentCount' => '2',
        ],
        'IMPRODUCT' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ComplexOperations::class, 'IMPRODUCT'],
            'argumentCount' => '1+',
        ],
        'IMREAL' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\Complex::class, 'IMREAL'],
            'argumentCount' => '1',
        ],
        'IMSEC' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ComplexFunctions::class, 'IMSEC'],
            'argumentCount' => '1',
        ],
        'IMSECH' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ComplexFunctions::class, 'IMSECH'],
            'argumentCount' => '1',
        ],
        'IMSIN' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ComplexFunctions::class, 'IMSIN'],
            'argumentCount' => '1',
        ],
        'IMSINH' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ComplexFunctions::class, 'IMSINH'],
            'argumentCount' => '1',
        ],
        'IMSQRT' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ComplexFunctions::class, 'IMSQRT'],
            'argumentCount' => '1',
        ],
        'IMSUB' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ComplexOperations::class, 'IMSUB'],
            'argumentCount' => '2',
        ],
        'IMSUM' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ComplexOperations::class, 'IMSUM'],
            'argumentCount' => '1+',
        ],
        'IMTAN' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ComplexFunctions::class, 'IMTAN'],
            'argumentCount' => '1',
        ],
        'INDEX' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef\Matrix::class, 'index'],
            'argumentCount' => '2-4',
        ],
        'INDIRECT' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef\Indirect::class, 'INDIRECT'],
            'argumentCount' => '1,2',
            'passCellReference' => true,
        ],
        'INFO' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1',
        ],
        'INT' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\IntClass::class, 'evaluate'],
            'argumentCount' => '1',
        ],
        'INTERCEPT' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Trends::class, 'INTERCEPT'],
            'argumentCount' => '2',
        ],
        'INTRATE' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\Securities\Rates::class, 'interest'],
            'argumentCount' => '4,5',
        ],
        'IPMT' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\CashFlow\Constant\Periodic\Interest::class, 'payment'],
            'argumentCount' => '4-6',
        ],
        'IRR' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\CashFlow\Variable\Periodic::class, 'rate'],
            'argumentCount' => '1,2',
        ],
        'ISBLANK' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Information\Value::class, 'isBlank'],
            'argumentCount' => '1',
        ],
        'ISERR' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Information\ErrorValue::class, 'isErr'],
            'argumentCount' => '1',
        ],
        'ISERROR' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Information\ErrorValue::class, 'isError'],
            'argumentCount' => '1',
        ],
        'ISEVEN' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Information\Value::class, 'isEven'],
            'argumentCount' => '1',
        ],
        'ISFORMULA' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Information\Value::class, 'isFormula'],
            'argumentCount' => '1',
            'passCellReference' => true,
            'passByReference' => [true],
        ],
        'ISLOGICAL' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Information\Value::class, 'isLogical'],
            'argumentCount' => '1',
        ],
        'ISNA' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Information\ErrorValue::class, 'isNa'],
            'argumentCount' => '1',
        ],
        'ISNONTEXT' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Information\Value::class, 'isNonText'],
            'argumentCount' => '1',
        ],
        'ISNUMBER' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Information\Value::class, 'isNumber'],
            'argumentCount' => '1',
        ],
        'ISO.CEILING' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1,2',
        ],
        'ISODD' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Information\Value::class, 'isOdd'],
            'argumentCount' => '1',
        ],
        'ISOMITTED' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '*',
        ],
        'ISOWEEKNUM' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTimeExcel\Week::class, 'isoWeekNumber'],
            'argumentCount' => '1',
        ],
        'ISPMT' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\CashFlow\Constant\Periodic\Interest::class, 'schedulePayment'],
            'argumentCount' => '4',
        ],
        'ISREF' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Information\Value::class, 'isRef'],
            'argumentCount' => '1',
            'passCellReference' => true,
            'passByReference' => [true],
        ],
        'ISTEXT' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Information\Value::class, 'isText'],
            'argumentCount' => '1',
        ],
        'ISTHAIDIGIT' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '?',
        ],
        'JIS' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1',
        ],
        'KURT' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Deviations::class, 'kurtosis'],
            'argumentCount' => '1+',
        ],
        'LAMBDA' => [
            'category' => Category::CATEGORY_LOGICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '*',
        ],
        'LARGE' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Size::class, 'large'],
            'argumentCount' => '2',
        ],
        'LCM' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Lcm::class, 'evaluate'],
            'argumentCount' => '1+',
        ],
        'LEFT' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Extract::class, 'left'],
            'argumentCount' => '1,2',
        ],
        'LEFTB' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Extract::class, 'left'],
            'argumentCount' => '1,2',
        ],
        'LEN' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Text::class, 'length'],
            'argumentCount' => '1',
        ],
        'LENB' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Text::class, 'length'],
            'argumentCount' => '1',
        ],
        'LET' => [
            'category' => Category::CATEGORY_LOGICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '*',
        ],
        'LINEST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Trends::class, 'LINEST'],
            'argumentCount' => '1-4',
        ],
        'LN' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Logarithms::class, 'natural'],
            'argumentCount' => '1',
        ],
        'LOG' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Logarithms::class, 'withBase'],
            'argumentCount' => '1,2',
        ],
        'LOG10' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Logarithms::class, 'base10'],
            'argumentCount' => '1',
        ],
        'LOGEST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Trends::class, 'LOGEST'],
            'argumentCount' => '1-4',
        ],
        'LOGINV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\LogNormal::class, 'inverse'],
            'argumentCount' => '3',
        ],
        'LOGNORMDIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\LogNormal::class, 'cumulative'],
            'argumentCount' => '3',
        ],
        'LOGNORM.DIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\LogNormal::class, 'distribution'],
            'argumentCount' => '4',
        ],
        'LOGNORM.INV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\LogNormal::class, 'inverse'],
            'argumentCount' => '3',
        ],
        'LOOKUP' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef\Lookup::class, 'lookup'],
            'argumentCount' => '2,3',
        ],
        'LOWER' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\CaseConvert::class, 'lower'],
            'argumentCount' => '1',
        ],
        'MAKEARRAY' => [
            'category' => Category::CATEGORY_LOGICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '*',
        ],
        'MAP' => [
            'category' => Category::CATEGORY_LOGICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '*',
        ],
        'MATCH' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef\ExcelMatch::class, 'MATCH'],
            'argumentCount' => '2,3',
        ],
        'MAX' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Maximum::class, 'max'],
            'argumentCount' => '1+',
        ],
        'MAXA' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Maximum::class, 'maxA'],
            'argumentCount' => '1+',
        ],
        'MAXIFS' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Conditional::class, 'MAXIFS'],
            'argumentCount' => '3+',
        ],
        'MDETERM' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\MatrixFunctions::class, 'determinant'],
            'argumentCount' => '1',
        ],
        'MDURATION' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '5,6',
        ],
        'MEDIAN' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Averages::class, 'median'],
            'argumentCount' => '1+',
        ],
        'MEDIANIF' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2+',
        ],
        'MID' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Extract::class, 'mid'],
            'argumentCount' => '3',
        ],
        'MIDB' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Extract::class, 'mid'],
            'argumentCount' => '3',
        ],
        'MIN' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Minimum::class, 'min'],
            'argumentCount' => '1+',
        ],
        'MINA' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Minimum::class, 'minA'],
            'argumentCount' => '1+',
        ],
        'MINIFS' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Conditional::class, 'MINIFS'],
            'argumentCount' => '3+',
        ],
        'MINUTE' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTimeExcel\TimeParts::class, 'minute'],
            'argumentCount' => '1',
        ],
        'MINVERSE' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\MatrixFunctions::class, 'inverse'],
            'argumentCount' => '1',
        ],
        'MIRR' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\CashFlow\Variable\Periodic::class, 'modifiedRate'],
            'argumentCount' => '3',
        ],
        'MMULT' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\MatrixFunctions::class, 'multiply'],
            'argumentCount' => '2',
        ],
        'MOD' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Operations::class, 'mod'],
            'argumentCount' => '2',
        ],
        'MODE' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Averages::class, 'mode'],
            'argumentCount' => '1+',
        ],
        'MODE.MULT' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1+',
        ],
        'MODE.SNGL' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Averages::class, 'mode'],
            'argumentCount' => '1+',
        ],
        'MONTH' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTimeExcel\DateParts::class, 'month'],
            'argumentCount' => '1',
        ],
        'MROUND' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Round::class, 'multiple'],
            'argumentCount' => '2',
        ],
        'MULTINOMIAL' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Factorial::class, 'multinomial'],
            'argumentCount' => '1+',
        ],
        'MUNIT' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\MatrixFunctions::class, 'identity'],
            'argumentCount' => '1',
        ],
        'N' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Information\Value::class, 'asNumber'],
            'argumentCount' => '1',
        ],
        'NA' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [ExcelError::class, 'NA'],
            'argumentCount' => '0',
        ],
        'NEGBINOMDIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\Binomial::class, 'negative'],
            'argumentCount' => '3',
        ],
        'NEGBINOM.DIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '4',
        ],
        'NETWORKDAYS' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTimeExcel\NetworkDays::class, 'count'],
            'argumentCount' => '2-3',
        ],
        'NETWORKDAYS.INTL' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2-4',
        ],
        'NOMINAL' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\InterestRate::class, 'nominal'],
            'argumentCount' => '2',
        ],
        'NORMDIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\Normal::class, 'distribution'],
            'argumentCount' => '4',
        ],
        'NORM.DIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\Normal::class, 'distribution'],
            'argumentCount' => '4',
        ],
        'NORMINV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\Normal::class, 'inverse'],
            'argumentCount' => '3',
        ],
        'NORM.INV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\Normal::class, 'inverse'],
            'argumentCount' => '3',
        ],
        'NORMSDIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\StandardNormal::class, 'cumulative'],
            'argumentCount' => '1',
        ],
        'NORM.S.DIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\StandardNormal::class, 'distribution'],
            'argumentCount' => '1,2',
        ],
        'NORMSINV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\StandardNormal::class, 'inverse'],
            'argumentCount' => '1',
        ],
        'NORM.S.INV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\StandardNormal::class, 'inverse'],
            'argumentCount' => '1',
        ],
        'NOT' => [
            'category' => Category::CATEGORY_LOGICAL,
            'functionCall' => [Logical\Operations::class, 'NOT'],
            'argumentCount' => '1',
        ],
        'NOW' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTimeExcel\Current::class, 'now'],
            'argumentCount' => '0',
        ],
        'NPER' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\CashFlow\Constant\Periodic::class, 'periods'],
            'argumentCount' => '3-5',
        ],
        'NPV' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\CashFlow\Variable\Periodic::class, 'presentValue'],
            'argumentCount' => '2+',
        ],
        'NUMBERSTRING' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '?',
        ],
        'NUMBERVALUE' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Format::class, 'NUMBERVALUE'],
            'argumentCount' => '1+',
        ],
        'OCT2BIN' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ConvertOctal::class, 'toBinary'],
            'argumentCount' => '1,2',
        ],
        'OCT2DEC' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ConvertOctal::class, 'toDecimal'],
            'argumentCount' => '1',
        ],
        'OCT2HEX' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering\ConvertOctal::class, 'toHex'],
            'argumentCount' => '1,2',
        ],
        'ODD' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Round::class, 'odd'],
            'argumentCount' => '1',
        ],
        'ODDFPRICE' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '8,9',
        ],
        'ODDFYIELD' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '8,9',
        ],
        'ODDLPRICE' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '7,8',
        ],
        'ODDLYIELD' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '7,8',
        ],
        'OFFSET' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef\Offset::class, 'OFFSET'],
            'argumentCount' => '3-5',
            'passCellReference' => true,
            'passByReference' => [true],
        ],
        'OR' => [
            'category' => Category::CATEGORY_LOGICAL,
            'functionCall' => [Logical\Operations::class, 'logicalOr'],
            'argumentCount' => '1+',
        ],
        'PDURATION' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\CashFlow\Single::class, 'periods'],
            'argumentCount' => '3',
        ],
        'PEARSON' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Trends::class, 'CORREL'],
            'argumentCount' => '2',
        ],
        'PERCENTILE' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Percentiles::class, 'PERCENTILE'],
            'argumentCount' => '2',
        ],
        'PERCENTILE.EXC' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2',
        ],
        'PERCENTILE.INC' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Percentiles::class, 'PERCENTILE'],
            'argumentCount' => '2',
        ],
        'PERCENTRANK' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Percentiles::class, 'PERCENTRANK'],
            'argumentCount' => '2,3',
        ],
        'PERCENTRANK.EXC' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2,3',
        ],
        'PERCENTRANK.INC' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Percentiles::class, 'PERCENTRANK'],
            'argumentCount' => '2,3',
        ],
        'PERMUT' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Permutations::class, 'PERMUT'],
            'argumentCount' => '2',
        ],
        'PERMUTATIONA' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Permutations::class, 'PERMUTATIONA'],
            'argumentCount' => '2',
        ],
        'PHONETIC' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1',
        ],
        'PHI' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1',
        ],
        'PI' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'pi',
            'argumentCount' => '0',
        ],
        'PMT' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\CashFlow\Constant\Periodic\Payments::class, 'annuity'],
            'argumentCount' => '3-5',
        ],
        'POISSON' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\Poisson::class, 'distribution'],
            'argumentCount' => '3',
        ],
        'POISSON.DIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\Poisson::class, 'distribution'],
            'argumentCount' => '3',
        ],
        'POWER' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Operations::class, 'power'],
            'argumentCount' => '2',
        ],
        'PPMT' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\CashFlow\Constant\Periodic\Payments::class, 'interestPayment'],
            'argumentCount' => '4-6',
        ],
        'PRICE' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\Securities\Price::class, 'price'],
            'argumentCount' => '6,7',
        ],
        'PRICEDISC' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\Securities\Price::class, 'priceDiscounted'],
            'argumentCount' => '4,5',
        ],
        'PRICEMAT' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\Securities\Price::class, 'priceAtMaturity'],
            'argumentCount' => '5,6',
        ],
        'PROB' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3,4',
        ],
        'PRODUCT' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Operations::class, 'product'],
            'argumentCount' => '1+',
        ],
        'PROPER' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\CaseConvert::class, 'proper'],
            'argumentCount' => '1',
        ],
        'PV' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\CashFlow\Constant\Periodic::class, 'presentValue'],
            'argumentCount' => '3-5',
        ],
        'QUARTILE' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Percentiles::class, 'QUARTILE'],
            'argumentCount' => '2',
        ],
        'QUARTILE.EXC' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2',
        ],
        'QUARTILE.INC' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Percentiles::class, 'QUARTILE'],
            'argumentCount' => '2',
        ],
        'QUOTIENT' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Operations::class, 'quotient'],
            'argumentCount' => '2',
        ],
        'RADIANS' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Angle::class, 'toRadians'],
            'argumentCount' => '1',
        ],
        'RAND' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Random::class, 'rand'],
            'argumentCount' => '0',
        ],
        'RANDARRAY' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Random::class, 'randArray'],
            'argumentCount' => '0-5',
        ],
        'RANDBETWEEN' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Random::class, 'randBetween'],
            'argumentCount' => '2',
        ],
        'RANK' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Percentiles::class, 'RANK'],
            'argumentCount' => '2,3',
        ],
        'RANK.AVG' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2,3',
        ],
        'RANK.EQ' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Percentiles::class, 'RANK'],
            'argumentCount' => '2,3',
        ],
        'RATE' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\CashFlow\Constant\Periodic\Interest::class, 'rate'],
            'argumentCount' => '3-6',
        ],
        'RECEIVED' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\Securities\Price::class, 'received'],
            'argumentCount' => '4-5',
        ],
        'REDUCE' => [
            'category' => Category::CATEGORY_LOGICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '*',
        ],
        'REPLACE' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Replace::class, 'replace'],
            'argumentCount' => '4',
        ],
        'REPLACEB' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Replace::class, 'replace'],
            'argumentCount' => '4',
        ],
        'REPT' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Concatenate::class, 'builtinREPT'],
            'argumentCount' => '2',
        ],
        'RIGHT' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Extract::class, 'right'],
            'argumentCount' => '1,2',
        ],
        'RIGHTB' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Extract::class, 'right'],
            'argumentCount' => '1,2',
        ],
        'ROMAN' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Roman::class, 'evaluate'],
            'argumentCount' => '1,2',
        ],
        'ROUND' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Round::class, 'round'],
            'argumentCount' => '2',
        ],
        'ROUNDBAHTDOWN' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '?',
        ],
        'ROUNDBAHTUP' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '?',
        ],
        'ROUNDDOWN' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Round::class, 'down'],
            'argumentCount' => '2',
        ],
        'ROUNDUP' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Round::class, 'up'],
            'argumentCount' => '2',
        ],
        'ROW' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef\RowColumnInformation::class, 'ROW'],
            'argumentCount' => '-1',
            'passCellReference' => true,
            'passByReference' => [true],
        ],
        'ROWS' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef\RowColumnInformation::class, 'ROWS'],
            'argumentCount' => '1',
        ],
        'RRI' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\CashFlow\Single::class, 'interestRate'],
            'argumentCount' => '3',
        ],
        'RSQ' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Trends::class, 'RSQ'],
            'argumentCount' => '2',
        ],
        'RTD' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1+',
        ],
        'SEARCH' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Search::class, 'insensitive'],
            'argumentCount' => '2,3',
        ],
        'SCAN' => [
            'category' => Category::CATEGORY_LOGICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '*',
        ],
        'SEARCHB' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Search::class, 'insensitive'],
            'argumentCount' => '2,3',
        ],
        'SEC' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Trig\Secant::class, 'sec'],
            'argumentCount' => '1',
        ],
        'SECH' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Trig\Secant::class, 'sech'],
            'argumentCount' => '1',
        ],
        'SECOND' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTimeExcel\TimeParts::class, 'second'],
            'argumentCount' => '1',
        ],
        'SEQUENCE' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\MatrixFunctions::class, 'sequence'],
            'argumentCount' => '1-4',
        ],
        'SERIESSUM' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\SeriesSum::class, 'evaluate'],
            'argumentCount' => '4',
        ],
        'SHEET' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '0,1',
        ],
        'SHEETS' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '0,1',
        ],
        'SIGN' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Sign::class, 'evaluate'],
            'argumentCount' => '1',
        ],
        'SIN' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Trig\Sine::class, 'sin'],
            'argumentCount' => '1',
        ],
        'SINGLE' => [
            'category' => Category::CATEGORY_MICROSOFT_INTERNAL,
            'functionCall' => [Internal\ExcelArrayPseudoFunctions::class, 'single'],
            'argumentCount' => '1',
            'passCellReference' => true,
            'passByReference' => [true],
        ],
        'SINH' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Trig\Sine::class, 'sinh'],
            'argumentCount' => '1',
        ],
        'SKEW' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Deviations::class, 'skew'],
            'argumentCount' => '1+',
        ],
        'SKEW.P' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1+',
        ],
        'SLN' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\Depreciation::class, 'SLN'],
            'argumentCount' => '3',
        ],
        'SLOPE' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Trends::class, 'SLOPE'],
            'argumentCount' => '2',
        ],
        'SMALL' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Size::class, 'small'],
            'argumentCount' => '2',
        ],
        'SORT' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef\Sort::class, 'sort'],
            'argumentCount' => '1-4',
        ],
        'SORTBY' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef\Sort::class, 'sortBy'],
            'argumentCount' => '2+',
        ],
        'SQRT' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Sqrt::class, 'sqrt'],
            'argumentCount' => '1',
        ],
        'SQRTPI' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Sqrt::class, 'pi'],
            'argumentCount' => '1',
        ],
        'STANDARDIZE' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Standardize::class, 'execute'],
            'argumentCount' => '3',
        ],
        'STDEV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\StandardDeviations::class, 'STDEV'],
            'argumentCount' => '1+',
        ],
        'STDEV.S' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\StandardDeviations::class, 'STDEV'],
            'argumentCount' => '1+',
        ],
        'STDEV.P' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\StandardDeviations::class, 'STDEVP'],
            'argumentCount' => '1+',
        ],
        'STDEVA' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\StandardDeviations::class, 'STDEVA'],
            'argumentCount' => '1+',
        ],
        'STDEVP' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\StandardDeviations::class, 'STDEVP'],
            'argumentCount' => '1+',
        ],
        'STDEVPA' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\StandardDeviations::class, 'STDEVPA'],
            'argumentCount' => '1+',
        ],
        'STEYX' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Trends::class, 'STEYX'],
            'argumentCount' => '2',
        ],
        'SUBSTITUTE' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Replace::class, 'substitute'],
            'argumentCount' => '3,4',
        ],
        'SUBTOTAL' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Subtotal::class, 'evaluate'],
            'argumentCount' => '2+',
            'passCellReference' => true,
        ],
        'SUM' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Sum::class, 'sumErroringStrings'],
            'argumentCount' => '1+',
        ],
        'SUMIF' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [Statistical\Conditional::class, 'SUMIF'],
            'argumentCount' => '2,3',
        ],
        'SUMIFS' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [Statistical\Conditional::class, 'SUMIFS'],
            'argumentCount' => '3+',
        ],
        'SUMPRODUCT' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Sum::class, 'product'],
            'argumentCount' => '1+',
        ],
        'SUMSQ' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\SumSquares::class, 'sumSquare'],
            'argumentCount' => '1+',
        ],
        'SUMX2MY2' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\SumSquares::class, 'sumXSquaredMinusYSquared'],
            'argumentCount' => '2',
        ],
        'SUMX2PY2' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\SumSquares::class, 'sumXSquaredPlusYSquared'],
            'argumentCount' => '2',
        ],
        'SUMXMY2' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\SumSquares::class, 'sumXMinusYSquared'],
            'argumentCount' => '2',
        ],
        'SWITCH' => [
            'category' => Category::CATEGORY_LOGICAL,
            'functionCall' => [Logical\Conditional::class, 'statementSwitch'],
            'argumentCount' => '3+',
        ],
        'SYD' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\Depreciation::class, 'SYD'],
            'argumentCount' => '4',
        ],
        'T' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Text::class, 'test'],
            'argumentCount' => '1',
        ],
        'TAKE' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2-3',
        ],
        'TAN' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Trig\Tangent::class, 'tan'],
            'argumentCount' => '1',
        ],
        'TANH' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Trig\Tangent::class, 'tanh'],
            'argumentCount' => '1',
        ],
        'TBILLEQ' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\TreasuryBill::class, 'bondEquivalentYield'],
            'argumentCount' => '3',
        ],
        'TBILLPRICE' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\TreasuryBill::class, 'price'],
            'argumentCount' => '3',
        ],
        'TBILLYIELD' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\TreasuryBill::class, 'yield'],
            'argumentCount' => '3',
        ],
        'TDIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\StudentT::class, 'distribution'],
            'argumentCount' => '3',
        ],
        'T.DIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3',
        ],
        'T.DIST.2T' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2',
        ],
        'T.DIST.RT' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2',
        ],
        'TEXT' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Format::class, 'TEXTFORMAT'],
            'argumentCount' => '2',
        ],
        'TEXTAFTER' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Extract::class, 'after'],
            'argumentCount' => '2-6',
        ],
        'TEXTBEFORE' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Extract::class, 'before'],
            'argumentCount' => '2-6',
        ],
        'TEXTJOIN' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Concatenate::class, 'TEXTJOIN'],
            'argumentCount' => '3+',
        ],
        'TEXTSPLIT' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Text::class, 'split'],
            'argumentCount' => '2-6',
        ],
        'THAIDAYOFWEEK' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '?',
        ],
        'THAIDIGIT' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '?',
        ],
        'THAIMONTHOFYEAR' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '?',
        ],
        'THAINUMSOUND' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '?',
        ],
        'THAINUMSTRING' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '?',
        ],
        'THAISTRINGLENGTH' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '?',
        ],
        'THAIYEAR' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '?',
        ],
        'TIME' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTimeExcel\Time::class, 'fromHMS'],
            'argumentCount' => '3',
        ],
        'TIMEVALUE' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTimeExcel\TimeValue::class, 'fromString'],
            'argumentCount' => '1',
        ],
        'TINV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\StudentT::class, 'inverse'],
            'argumentCount' => '2',
        ],
        'T.INV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\StudentT::class, 'inverse'],
            'argumentCount' => '2',
        ],
        'T.INV.2T' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2',
        ],
        'TODAY' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTimeExcel\Current::class, 'today'],
            'argumentCount' => '0',
        ],
        'TOCOL' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1-3',
        ],
        'TOROW' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1-3',
        ],
        'TRANSPOSE' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef\Matrix::class, 'transpose'],
            'argumentCount' => '1',
        ],
        'TREND' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Trends::class, 'TREND'],
            'argumentCount' => '1-4',
        ],
        'TRIM' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Trim::class, 'spaces'],
            'argumentCount' => '1',
        ],
        'TRIMMEAN' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Averages\Mean::class, 'trim'],
            'argumentCount' => '2',
        ],
        'TRUE' => [
            'category' => Category::CATEGORY_LOGICAL,
            'functionCall' => [Logical\Boolean::class, 'TRUE'],
            'argumentCount' => '0',
        ],
        'TRUNC' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Trunc::class, 'evaluate'],
            'argumentCount' => '1,2',
        ],
        'TTEST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '4',
        ],
        'T.TEST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '4',
        ],
        'TYPE' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Information\Value::class, 'type'],
            'argumentCount' => '1',
        ],
        'UNICHAR' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\CharacterConvert::class, 'character'],
            'argumentCount' => '1',
        ],
        'UNICODE' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\CharacterConvert::class, 'code'],
            'argumentCount' => '1',
        ],
        'UNIQUE' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef\Unique::class, 'unique'],
            'argumentCount' => '1+',
        ],
        'UPPER' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\CaseConvert::class, 'upper'],
            'argumentCount' => '1',
        ],
        'USDOLLAR' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\Dollar::class, 'format'],
            'argumentCount' => '2',
        ],
        'VALUE' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Format::class, 'VALUE'],
            'argumentCount' => '1',
        ],
        'VALUETOTEXT' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData\Format::class, 'valueToText'],
            'argumentCount' => '1,2',
        ],
        'VAR' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Variances::class, 'VAR'],
            'argumentCount' => '1+',
        ],
        'VAR.P' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Variances::class, 'VARP'],
            'argumentCount' => '1+',
        ],
        'VAR.S' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Variances::class, 'VAR'],
            'argumentCount' => '1+',
        ],
        'VARA' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Variances::class, 'VARA'],
            'argumentCount' => '1+',
        ],
        'VARP' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Variances::class, 'VARP'],
            'argumentCount' => '1+',
        ],
        'VARPA' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Variances::class, 'VARPA'],
            'argumentCount' => '1+',
        ],
        'VDB' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '5-7',
        ],
        'VLOOKUP' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef\VLookup::class, 'lookup'],
            'argumentCount' => '3,4',
        ],
        'VSTACK' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1+',
        ],
        'WEBSERVICE' => [
            'category' => Category::CATEGORY_WEB,
            'functionCall' => [Web\Service::class, 'webService'],
            'argumentCount' => '1',
        ],
        'WEEKDAY' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTimeExcel\Week::class, 'day'],
            'argumentCount' => '1,2',
        ],
        'WEEKNUM' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTimeExcel\Week::class, 'number'],
            'argumentCount' => '1,2',
        ],
        'WEIBULL' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\Weibull::class, 'distribution'],
            'argumentCount' => '4',
        ],
        'WEIBULL.DIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\Weibull::class, 'distribution'],
            'argumentCount' => '4',
        ],
        'WORKDAY' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTimeExcel\WorkDay::class, 'date'],
            'argumentCount' => '2-3',
        ],
        'WORKDAY.INTL' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2-4',
        ],
        'WRAPCOLS' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2-3',
        ],
        'WRAPROWS' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2-3',
        ],
        'XIRR' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\CashFlow\Variable\NonPeriodic::class, 'rate'],
            'argumentCount' => '2,3',
        ],
        'XLOOKUP' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3-6',
        ],
        'XNPV' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\CashFlow\Variable\NonPeriodic::class, 'presentValue'],
            'argumentCount' => '3',
        ],
        'XMATCH' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2,3',
        ],
        'XOR' => [
            'category' => Category::CATEGORY_LOGICAL,
            'functionCall' => [Logical\Operations::class, 'logicalXor'],
            'argumentCount' => '1+',
        ],
        'YEAR' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTimeExcel\DateParts::class, 'year'],
            'argumentCount' => '1',
        ],
        'YEARFRAC' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTimeExcel\YearFrac::class, 'fraction'],
            'argumentCount' => '2,3',
        ],
        'YIELD' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '6,7',
        ],
        'YIELDDISC' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\Securities\Yields::class, 'yieldDiscounted'],
            'argumentCount' => '4,5',
        ],
        'YIELDMAT' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial\Securities\Yields::class, 'yieldAtMaturity'],
            'argumentCount' => '5,6',
        ],
        'ZTEST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\StandardNormal::class, 'zTest'],
            'argumentCount' => '2-3',
        ],
        'Z.TEST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical\Distributions\StandardNormal::class, 'zTest'],
            'argumentCount' => '2-3',
        ],
    ];

    /**
     *    Internal functions used for special control purposes.
     */
    private static array $controlFunctions = [
        'MKMATRIX' => [
            'argumentCount' => '*',
            'functionCall' => [Internal\MakeMatrix::class, 'make'],
        ],
        'NAME.ERROR' => [
            'argumentCount' => '*',
            'functionCall' => [ExcelError::class, 'NAME'],
        ],
        'WILDCARDMATCH' => [
            'argumentCount' => '2',
            'functionCall' => [Internal\WildcardMatch::class, 'compare'],
        ],
    ];

    public function __construct(?Spreadsheet $spreadsheet = null)
    {
        $this->spreadsheet = $spreadsheet;
        $this->cyclicReferenceStack = new CyclicReferenceStack();
        $this->debugLog = new Logger($this->cyclicReferenceStack);
        $this->branchPruner = new BranchPruner($this->branchPruningEnabled);
        self::$referenceHelper = ReferenceHelper::getInstance();
    }

    private static function loadLocales(): void
    {
        $localeFileDirectory = __DIR__ . '/locale/';
        $localeFileNames = glob($localeFileDirectory . '*', GLOB_ONLYDIR) ?: [];
        foreach ($localeFileNames as $filename) {
            $filename = substr($filename, strlen($localeFileDirectory));
            if ($filename != 'en') {
                self::$validLocaleLanguages[] = $filename;
            }
        }
    }

    /**
     * Get an instance of this class.
     *
     * @param ?Spreadsheet $spreadsheet Injected spreadsheet for working with a PhpSpreadsheet Spreadsheet object,
     *                                    or NULL to create a standalone calculation engine
     */
    public static function getInstance(?Spreadsheet $spreadsheet = null): self
    {
        if ($spreadsheet !== null) {
            $instance = $spreadsheet->getCalculationEngine();
            if (isset($instance)) {
                return $instance;
            }
        }

        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Flush the calculation cache for any existing instance of this class
     *        but only if a Calculation instance exists.
     */
    public function flushInstance(): void
    {
        $this->clearCalculationCache();
        $this->branchPruner->clearBranchStore();
    }

    /**
     * Get the Logger for this calculation engine instance.
     */
    public function getDebugLog(): Logger
    {
        return $this->debugLog;
    }

    /**
     * __clone implementation. Cloning should not be allowed in a Singleton!
     */
    final public function __clone()
    {
        throw new Exception('Cloning the calculation engine is not allowed!');
    }

    /**
     * Return the locale-specific translation of TRUE.
     *
     * @return string locale-specific translation of TRUE
     */
    public static function getTRUE(): string
    {
        return self::$localeBoolean['TRUE'];
    }

    /**
     * Return the locale-specific translation of FALSE.
     *
     * @return string locale-specific translation of FALSE
     */
    public static function getFALSE(): string
    {
        return self::$localeBoolean['FALSE'];
    }

    /**
     * Set the Array Return Type (Array or Value of first element in the array).
     *
     * @param string $returnType Array return type
     *
     * @return bool Success or failure
     */
    public static function setArrayReturnType(string $returnType): bool
    {
        if (
            ($returnType == self::RETURN_ARRAY_AS_VALUE)
            || ($returnType == self::RETURN_ARRAY_AS_ERROR)
            || ($returnType == self::RETURN_ARRAY_AS_ARRAY)
        ) {
            self::$returnArrayAsType = $returnType;

            return true;
        }

        return false;
    }

    /**
     * Return the Array Return Type (Array or Value of first element in the array).
     *
     * @return string $returnType Array return type
     */
    public static function getArrayReturnType(): string
    {
        return self::$returnArrayAsType;
    }

    /**
     * Set the Instance Array Return Type (Array or Value of first element in the array).
     *
     * @param string $returnType Array return type
     *
     * @return bool Success or failure
     */
    public function setInstanceArrayReturnType(string $returnType): bool
    {
        if (
            ($returnType == self::RETURN_ARRAY_AS_VALUE)
            || ($returnType == self::RETURN_ARRAY_AS_ERROR)
            || ($returnType == self::RETURN_ARRAY_AS_ARRAY)
        ) {
            $this->instanceArrayReturnType = $returnType;

            return true;
        }

        return false;
    }

    /**
     * Return the Array Return Type (Array or Value of first element in the array).
     *
     * @return string $returnType Array return type for instance if non-null, otherwise static property
     */
    public function getInstanceArrayReturnType(): string
    {
        return $this->instanceArrayReturnType ?? self::$returnArrayAsType;
    }

    /**
     * Is calculation caching enabled?
     */
    public function getCalculationCacheEnabled(): bool
    {
        return $this->calculationCacheEnabled;
    }

    /**
     * Enable/disable calculation cache.
     */
    public function setCalculationCacheEnabled(bool $calculationCacheEnabled): void
    {
        $this->calculationCacheEnabled = $calculationCacheEnabled;
        $this->clearCalculationCache();
    }

    /**
     * Enable calculation cache.
     */
    public function enableCalculationCache(): void
    {
        $this->setCalculationCacheEnabled(true);
    }

    /**
     * Disable calculation cache.
     */
    public function disableCalculationCache(): void
    {
        $this->setCalculationCacheEnabled(false);
    }

    /**
     * Clear calculation cache.
     */
    public function clearCalculationCache(): void
    {
        $this->calculationCache = [];
    }

    /**
     * Clear calculation cache for a specified worksheet.
     */
    public function clearCalculationCacheForWorksheet(string $worksheetName): void
    {
        if (isset($this->calculationCache[$worksheetName])) {
            unset($this->calculationCache[$worksheetName]);
        }
    }

    /**
     * Rename calculation cache for a specified worksheet.
     */
    public function renameCalculationCacheForWorksheet(string $fromWorksheetName, string $toWorksheetName): void
    {
        if (isset($this->calculationCache[$fromWorksheetName])) {
            $this->calculationCache[$toWorksheetName] = &$this->calculationCache[$fromWorksheetName];
            unset($this->calculationCache[$fromWorksheetName]);
        }
    }

    /**
     * Enable/disable calculation cache.
     */
    public function setBranchPruningEnabled(mixed $enabled): void
    {
        $this->branchPruningEnabled = $enabled;
        $this->branchPruner = new BranchPruner($this->branchPruningEnabled);
    }

    public function enableBranchPruning(): void
    {
        $this->setBranchPruningEnabled(true);
    }

    public function disableBranchPruning(): void
    {
        $this->setBranchPruningEnabled(false);
    }

    /**
     * Get the currently defined locale code.
     */
    public function getLocale(): string
    {
        return self::$localeLanguage;
    }

    private function getLocaleFile(string $localeDir, string $locale, string $language, string $file): string
    {
        $localeFileName = $localeDir . str_replace('_', DIRECTORY_SEPARATOR, $locale)
            . DIRECTORY_SEPARATOR . $file;
        if (!file_exists($localeFileName)) {
            //    If there isn't a locale specific file, look for a language specific file
            $localeFileName = $localeDir . $language . DIRECTORY_SEPARATOR . $file;
            if (!file_exists($localeFileName)) {
                throw new Exception('Locale file not found');
            }
        }

        return $localeFileName;
    }

    /** @var array<int, array<int, string>> */
    private static array $falseTrueArray = [];

    /** @return array<int, array<int, string>> */
    public function getFalseTrueArray(): array
    {
        if (!empty(self::$falseTrueArray)) {
            return self::$falseTrueArray;
        }
        if (count(self::$validLocaleLanguages) == 1) {
            self::loadLocales();
        }
        $falseTrueArray = [['FALSE'], ['TRUE']];
        foreach (self::$validLocaleLanguages as $language) {
            if (str_starts_with($language, 'en')) {
                continue;
            }
            $locale = $language;
            if (str_contains($locale, '_')) {
                [$language] = explode('_', $locale);
            }
            $localeDir = implode(DIRECTORY_SEPARATOR, [__DIR__, 'locale', null]);

            try {
                $functionNamesFile = $this->getLocaleFile($localeDir, $locale, $language, 'functions');
            } catch (Exception $e) {
                continue;
            }
            //    Retrieve the list of locale or language specific function names
            $localeFunctions = file($functionNamesFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
            foreach ($localeFunctions as $localeFunction) {
                [$localeFunction] = explode('##', $localeFunction); //    Strip out comments
                if (str_contains($localeFunction, '=')) {
                    [$fName, $lfName] = array_map('trim', explode('=', $localeFunction));
                    if ($fName === 'FALSE') {
                        $falseTrueArray[0][] = $lfName;
                    } elseif ($fName === 'TRUE') {
                        $falseTrueArray[1][] = $lfName;
                    }
                }
            }
        }
        self::$falseTrueArray = $falseTrueArray;

        return $falseTrueArray;
    }

    /**
     * Set the locale code.
     *
     * @param string $locale The locale to use for formula translation, eg: 'en_us'
     */
    public function setLocale(string $locale): bool
    {
        //    Identify our locale and language
        $language = $locale = strtolower($locale);
        if (str_contains($locale, '_')) {
            [$language] = explode('_', $locale);
        }
        if (count(self::$validLocaleLanguages) == 1) {
            self::loadLocales();
        }

        //    Test whether we have any language data for this language (any locale)
        if (in_array($language, self::$validLocaleLanguages, true)) {
            //    initialise language/locale settings
            self::$localeFunctions = [];
            self::$localeArgumentSeparator = ',';
            self::$localeBoolean = ['TRUE' => 'TRUE', 'FALSE' => 'FALSE', 'NULL' => 'NULL'];

            //    Default is US English, if user isn't requesting US english, then read the necessary data from the locale files
            if ($locale !== 'en_us') {
                $localeDir = implode(DIRECTORY_SEPARATOR, [__DIR__, 'locale', null]);

                //    Search for a file with a list of function names for locale
                try {
                    $functionNamesFile = $this->getLocaleFile($localeDir, $locale, $language, 'functions');
                } catch (Exception $e) {
                    return false;
                }

                //    Retrieve the list of locale or language specific function names
                $localeFunctions = file($functionNamesFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
                foreach ($localeFunctions as $localeFunction) {
                    [$localeFunction] = explode('##', $localeFunction); //    Strip out comments
                    if (str_contains($localeFunction, '=')) {
                        [$fName, $lfName] = array_map('trim', explode('=', $localeFunction));
                        if ((str_starts_with($fName, '*') || isset(self::$phpSpreadsheetFunctions[$fName])) && ($lfName != '') && ($fName != $lfName)) {
                            self::$localeFunctions[$fName] = $lfName;
                        }
                    }
                }
                //    Default the TRUE and FALSE constants to the locale names of the TRUE() and FALSE() functions
                if (isset(self::$localeFunctions['TRUE'])) {
                    self::$localeBoolean['TRUE'] = self::$localeFunctions['TRUE'];
                }
                if (isset(self::$localeFunctions['FALSE'])) {
                    self::$localeBoolean['FALSE'] = self::$localeFunctions['FALSE'];
                }

                try {
                    $configFile = $this->getLocaleFile($localeDir, $locale, $language, 'config');
                } catch (Exception) {
                    return false;
                }

                $localeSettings = file($configFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
                foreach ($localeSettings as $localeSetting) {
                    [$localeSetting] = explode('##', $localeSetting); //    Strip out comments
                    if (str_contains($localeSetting, '=')) {
                        [$settingName, $settingValue] = array_map('trim', explode('=', $localeSetting));
                        $settingName = strtoupper($settingName);
                        if ($settingValue !== '') {
                            switch ($settingName) {
                                case 'ARGUMENTSEPARATOR':
                                    self::$localeArgumentSeparator = $settingValue;

                                    break;
                            }
                        }
                    }
                }
            }

            self::$functionReplaceFromExcel = self::$functionReplaceToExcel
            = self::$functionReplaceFromLocale = self::$functionReplaceToLocale = null;
            self::$localeLanguage = $locale;

            return true;
        }

        return false;
    }

    public static function translateSeparator(
        string $fromSeparator,
        string $toSeparator,
        string $formula,
        int &$inBracesLevel,
        string $openBrace = self::FORMULA_OPEN_FUNCTION_BRACE,
        string $closeBrace = self::FORMULA_CLOSE_FUNCTION_BRACE
    ): string {
        $strlen = mb_strlen($formula);
        for ($i = 0; $i < $strlen; ++$i) {
            $chr = mb_substr($formula, $i, 1);
            switch ($chr) {
                case $openBrace:
                    ++$inBracesLevel;

                    break;
                case $closeBrace:
                    --$inBracesLevel;

                    break;
                case $fromSeparator:
                    if ($inBracesLevel > 0) {
                        $formula = mb_substr($formula, 0, $i) . $toSeparator . mb_substr($formula, $i + 1);
                    }
            }
        }

        return $formula;
    }

    private static function translateFormulaBlock(
        array $from,
        array $to,
        string $formula,
        int &$inFunctionBracesLevel,
        int &$inMatrixBracesLevel,
        string $fromSeparator,
        string $toSeparator
    ): string {
        // Function Names
        $formula = (string) preg_replace($from, $to, $formula);

        // Temporarily adjust matrix separators so that they won't be confused with function arguments
        $formula = self::translateSeparator(';', '|', $formula, $inMatrixBracesLevel, self::FORMULA_OPEN_MATRIX_BRACE, self::FORMULA_CLOSE_MATRIX_BRACE);
        $formula = self::translateSeparator(',', '!', $formula, $inMatrixBracesLevel, self::FORMULA_OPEN_MATRIX_BRACE, self::FORMULA_CLOSE_MATRIX_BRACE);
        // Function Argument Separators
        $formula = self::translateSeparator($fromSeparator, $toSeparator, $formula, $inFunctionBracesLevel);
        // Restore matrix separators
        $formula = self::translateSeparator('|', ';', $formula, $inMatrixBracesLevel, self::FORMULA_OPEN_MATRIX_BRACE, self::FORMULA_CLOSE_MATRIX_BRACE);
        $formula = self::translateSeparator('!', ',', $formula, $inMatrixBracesLevel, self::FORMULA_OPEN_MATRIX_BRACE, self::FORMULA_CLOSE_MATRIX_BRACE);

        return $formula;
    }

    private static function translateFormula(array $from, array $to, string $formula, string $fromSeparator, string $toSeparator): string
    {
        // Convert any Excel function names and constant names to the required language;
        //     and adjust function argument separators
        if (self::$localeLanguage !== 'en_us') {
            $inFunctionBracesLevel = 0;
            $inMatrixBracesLevel = 0;
            //    If there is the possibility of separators within a quoted string, then we treat them as literals
            if (str_contains($formula, self::FORMULA_STRING_QUOTE)) {
                //    So instead we skip replacing in any quoted strings by only replacing in every other array element
                //       after we've exploded the formula
                $temp = explode(self::FORMULA_STRING_QUOTE, $formula);
                $notWithinQuotes = false;
                foreach ($temp as &$value) {
                    //    Only adjust in alternating array entries
                    $notWithinQuotes = $notWithinQuotes === false;
                    if ($notWithinQuotes === true) {
                        $value = self::translateFormulaBlock($from, $to, $value, $inFunctionBracesLevel, $inMatrixBracesLevel, $fromSeparator, $toSeparator);
                    }
                }
                unset($value);
                //    Then rebuild the formula string
                $formula = implode(self::FORMULA_STRING_QUOTE, $temp);
            } else {
                //    If there's no quoted strings, then we do a simple count/replace
                $formula = self::translateFormulaBlock($from, $to, $formula, $inFunctionBracesLevel, $inMatrixBracesLevel, $fromSeparator, $toSeparator);
            }
        }

        return $formula;
    }

    private static ?array $functionReplaceFromExcel;

    private static ?array $functionReplaceToLocale;

    public function translateFormulaToLocale(string $formula): string
    {
        $formula = preg_replace(self::CALCULATION_REGEXP_STRIP_XLFN_XLWS, '', $formula) ?? '';
        // Build list of function names and constants for translation
        if (self::$functionReplaceFromExcel === null) {
            self::$functionReplaceFromExcel = [];
            foreach (array_keys(self::$localeFunctions) as $excelFunctionName) {
                self::$functionReplaceFromExcel[] = '/(@?[^\w\.])' . preg_quote($excelFunctionName, '/') . '([\s]*\()/ui';
            }
            foreach (array_keys(self::$localeBoolean) as $excelBoolean) {
                self::$functionReplaceFromExcel[] = '/(@?[^\w\.])' . preg_quote($excelBoolean, '/') . '([^\w\.])/ui';
            }
        }

        if (self::$functionReplaceToLocale === null) {
            self::$functionReplaceToLocale = [];
            foreach (self::$localeFunctions as $localeFunctionName) {
                self::$functionReplaceToLocale[] = '$1' . trim($localeFunctionName) . '$2';
            }
            foreach (self::$localeBoolean as $localeBoolean) {
                self::$functionReplaceToLocale[] = '$1' . trim($localeBoolean) . '$2';
            }
        }

        return self::translateFormula(
            self::$functionReplaceFromExcel,
            self::$functionReplaceToLocale,
            $formula,
            ',',
            self::$localeArgumentSeparator
        );
    }

    private static ?array $functionReplaceFromLocale;

    private static ?array $functionReplaceToExcel;

    public function translateFormulaToEnglish(string $formula): string
    {
        if (self::$functionReplaceFromLocale === null) {
            self::$functionReplaceFromLocale = [];
            foreach (self::$localeFunctions as $localeFunctionName) {
                self::$functionReplaceFromLocale[] = '/(@?[^\w\.])' . preg_quote($localeFunctionName, '/') . '([\s]*\()/ui';
            }
            foreach (self::$localeBoolean as $excelBoolean) {
                self::$functionReplaceFromLocale[] = '/(@?[^\w\.])' . preg_quote($excelBoolean, '/') . '([^\w\.])/ui';
            }
        }

        if (self::$functionReplaceToExcel === null) {
            self::$functionReplaceToExcel = [];
            foreach (array_keys(self::$localeFunctions) as $excelFunctionName) {
                self::$functionReplaceToExcel[] = '$1' . trim($excelFunctionName) . '$2';
            }
            foreach (array_keys(self::$localeBoolean) as $excelBoolean) {
                self::$functionReplaceToExcel[] = '$1' . trim($excelBoolean) . '$2';
            }
        }

        return self::translateFormula(self::$functionReplaceFromLocale, self::$functionReplaceToExcel, $formula, self::$localeArgumentSeparator, ',');
    }

    public static function localeFunc(string $function): string
    {
        if (self::$localeLanguage !== 'en_us') {
            $functionName = trim($function, '(');
            if (isset(self::$localeFunctions[$functionName])) {
                $brace = ($functionName != $function);
                $function = self::$localeFunctions[$functionName];
                if ($brace) {
                    $function .= '(';
                }
            }
        }

        return $function;
    }

    /**
     * Wrap string values in quotes.
     */
    public static function wrapResult(mixed $value): mixed
    {
        if (is_string($value)) {
            //    Error values cannot be "wrapped"
            if (preg_match('/^' . self::CALCULATION_REGEXP_ERROR . '$/i', $value, $match)) {
                //    Return Excel errors "as is"
                return $value;
            }

            //    Return strings wrapped in quotes
            return self::FORMULA_STRING_QUOTE . $value . self::FORMULA_STRING_QUOTE;
        } elseif ((is_float($value)) && ((is_nan($value)) || (is_infinite($value)))) {
            //    Convert numeric errors to NaN error
            return ExcelError::NAN();
        }

        return $value;
    }

    /**
     * Remove quotes used as a wrapper to identify string values.
     */
    public static function unwrapResult(mixed $value): mixed
    {
        if (is_string($value)) {
            if ((isset($value[0])) && ($value[0] == self::FORMULA_STRING_QUOTE) && (substr($value, -1) == self::FORMULA_STRING_QUOTE)) {
                return substr($value, 1, -1);
            }
            //    Convert numeric errors to NAN error
        } elseif ((is_float($value)) && ((is_nan($value)) || (is_infinite($value)))) {
            return ExcelError::NAN();
        }

        return $value;
    }

    /**
     * Calculate cell value (using formula from a cell ID)
     * Retained for backward compatibility.
     *
     * @param ?Cell $cell Cell to calculate
     */
    public function calculate(?Cell $cell = null): mixed
    {
        try {
            return $this->calculateCellValue($cell);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Calculate the value of a cell formula.
     *
     * @param ?Cell $cell Cell to calculate
     * @param bool $resetLog Flag indicating whether the debug log should be reset or not
     */
    public function calculateCellValue(?Cell $cell = null, bool $resetLog = true): mixed
    {
        if ($cell === null) {
            return null;
        }

        if ($resetLog) {
            //    Initialise the logging settings if requested
            $this->formulaError = null;
            $this->debugLog->clearLog();
            $this->cyclicReferenceStack->clear();
            $this->cyclicFormulaCounter = 1;
        }

        //    Execute the calculation for the cell formula
        $this->cellStack[] = [
            'sheet' => $cell->getWorksheet()->getTitle(),
            'cell' => $cell->getCoordinate(),
        ];

        $cellAddressAttempted = false;
        $cellAddress = null;

        try {
            $value = $cell->getValue();
            if ($cell->getDataType() === DataType::TYPE_FORMULA) {
                $value = preg_replace_callback(
                    self::CALCULATION_REGEXP_CELLREF_SPILL,
                    fn (array $matches) => 'ANCHORARRAY(' . substr($matches[0], 0, -1) . ')',
                    $value
                );
            }
            $result = self::unwrapResult($this->_calculateFormulaValue($value, $cell->getCoordinate(), $cell));
            if ($this->spreadsheet === null) {
                throw new Exception('null spreadsheet in calculateCellValue');
            }
            $cellAddressAttempted = true;
            $cellAddress = array_pop($this->cellStack);
            if ($cellAddress === null) {
                throw new Exception('null cellAddress in calculateCellValue');
            }
            $testSheet = $this->spreadsheet->getSheetByName($cellAddress['sheet']);
            if ($testSheet === null) {
                throw new Exception('worksheet not found in calculateCellValue');
            }
            $testSheet->getCell($cellAddress['cell']);
        } catch (\Exception $e) {
            if (!$cellAddressAttempted) {
                $cellAddress = array_pop($this->cellStack);
            }
            if ($this->spreadsheet !== null && is_array($cellAddress) && array_key_exists('sheet', $cellAddress)) {
                $testSheet = $this->spreadsheet->getSheetByName($cellAddress['sheet']);
                if ($testSheet !== null && array_key_exists('cell', $cellAddress)) {
                    $testSheet->getCell($cellAddress['cell']);
                }
            }

            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        if (is_array($result) && $this->getInstanceArrayReturnType() !== self::RETURN_ARRAY_AS_ARRAY) {
            $testResult = Functions::flattenArray($result);
            if ($this->getInstanceArrayReturnType() == self::RETURN_ARRAY_AS_ERROR) {
                return ExcelError::VALUE();
            }
            $result = array_shift($testResult);
        }

        if ($result === null && $cell->getWorksheet()->getSheetView()->getShowZeros()) {
            return 0;
        } elseif ((is_float($result)) && ((is_nan($result)) || (is_infinite($result)))) {
            return ExcelError::NAN();
        }

        return $result;
    }

    /**
     * Validate and parse a formula string.
     *
     * @param string $formula Formula to parse
     */
    public function parseFormula(string $formula): array|bool
    {
        $formula = preg_replace_callback(
            self::CALCULATION_REGEXP_CELLREF_SPILL,
            fn (array $matches) => 'ANCHORARRAY(' . substr($matches[0], 0, -1) . ')',
            $formula
        ) ?? $formula;
        //    Basic validation that this is indeed a formula
        //    We return an empty array if not
        $formula = trim($formula);
        if ((!isset($formula[0])) || ($formula[0] != '=')) {
            return [];
        }
        $formula = ltrim(substr($formula, 1));
        if (!isset($formula[0])) {
            return [];
        }

        //    Parse the formula and return the token stack
        return $this->internalParseFormula($formula);
    }

    /**
     * Calculate the value of a formula.
     *
     * @param string $formula Formula to parse
     * @param ?string $cellID Address of the cell to calculate
     * @param ?Cell $cell Cell to calculate
     */
    public function calculateFormula(string $formula, ?string $cellID = null, ?Cell $cell = null): mixed
    {
        //    Initialise the logging settings
        $this->formulaError = null;
        $this->debugLog->clearLog();
        $this->cyclicReferenceStack->clear();

        $resetCache = $this->getCalculationCacheEnabled();
        if ($this->spreadsheet !== null && $cellID === null && $cell === null) {
            $cellID = 'A1';
            $cell = $this->spreadsheet->getActiveSheet()->getCell($cellID);
        } else {
            //    Disable calculation cacheing because it only applies to cell calculations, not straight formulae
            //    But don't actually flush any cache
            $this->calculationCacheEnabled = false;
        }

        //    Execute the calculation
        try {
            $result = self::unwrapResult($this->_calculateFormulaValue($formula, $cellID, $cell));
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }

        if ($this->spreadsheet === null) {
            //    Reset calculation cacheing to its previous state
            $this->calculationCacheEnabled = $resetCache;
        }

        return $result;
    }

    public function getValueFromCache(string $cellReference, mixed &$cellValue): bool
    {
        $this->debugLog->writeDebugLog('Testing cache value for cell %s', $cellReference);
        // Is calculation cacheing enabled?
        // If so, is the required value present in calculation cache?
        if (($this->calculationCacheEnabled) && (isset($this->calculationCache[$cellReference]))) {
            $this->debugLog->writeDebugLog('Retrieving value for cell %s from cache', $cellReference);
            // Return the cached result

            $cellValue = $this->calculationCache[$cellReference];

            return true;
        }

        return false;
    }

    public function saveValueToCache(string $cellReference, mixed $cellValue): void
    {
        if ($this->calculationCacheEnabled) {
            $this->calculationCache[$cellReference] = $cellValue;
        }
    }

    /**
     * Parse a cell formula and calculate its value.
     *
     * @param string $formula The formula to parse and calculate
     * @param ?string $cellID The ID (e.g. A3) of the cell that we are calculating
     * @param ?Cell $cell Cell to calculate
     * @param bool $ignoreQuotePrefix If set to true, evaluate the formyla even if the referenced cell is quote prefixed
     */
    public function _calculateFormulaValue(string $formula, ?string $cellID = null, ?Cell $cell = null, bool $ignoreQuotePrefix = false): mixed
    {
        $cellValue = null;

        //  Quote-Prefixed cell values cannot be formulae, but are treated as strings
        if ($cell !== null && $ignoreQuotePrefix === false && $cell->getStyle()->getQuotePrefix() === true) {
            return self::wrapResult((string) $formula);
        }

        if (preg_match('/^=\s*cmd\s*\|/miu', $formula) !== 0) {
            return self::wrapResult($formula);
        }

        //    Basic validation that this is indeed a formula
        //    We simply return the cell value if not
        $formula = trim($formula);
        if ($formula === '' || $formula[0] !== '=') {
            return self::wrapResult($formula);
        }
        $formula = ltrim(substr($formula, 1));
        if (!isset($formula[0])) {
            return self::wrapResult($formula);
        }

        $pCellParent = ($cell !== null) ? $cell->getWorksheet() : null;
        $wsTitle = ($pCellParent !== null) ? $pCellParent->getTitle() : "\x00Wrk";
        $wsCellReference = $wsTitle . '!' . $cellID;

        if (($cellID !== null) && ($this->getValueFromCache($wsCellReference, $cellValue))) {
            return $cellValue;
        }
        $this->debugLog->writeDebugLog('Evaluating formula for cell %s', $wsCellReference);

        if (($wsTitle[0] !== "\x00") && ($this->cyclicReferenceStack->onStack($wsCellReference))) {
            if ($this->cyclicFormulaCount <= 0) {
                $this->cyclicFormulaCell = '';

                return $this->raiseFormulaError('Cyclic Reference in Formula');
            } elseif ($this->cyclicFormulaCell === $wsCellReference) {
                ++$this->cyclicFormulaCounter;
                if ($this->cyclicFormulaCounter >= $this->cyclicFormulaCount) {
                    $this->cyclicFormulaCell = '';

                    return $cellValue;
                }
            } elseif ($this->cyclicFormulaCell == '') {
                if ($this->cyclicFormulaCounter >= $this->cyclicFormulaCount) {
                    return $cellValue;
                }
                $this->cyclicFormulaCell = $wsCellReference;
            }
        }

        $this->debugLog->writeDebugLog('Formula for cell %s is %s', $wsCellReference, $formula);
        //    Parse the formula onto the token stack and calculate the value
        $this->cyclicReferenceStack->push($wsCellReference);

        $cellValue = $this->processTokenStack($this->internalParseFormula($formula, $cell), $cellID, $cell);
        $this->cyclicReferenceStack->pop();

        // Save to calculation cache
        if ($cellID !== null) {
            $this->saveValueToCache($wsCellReference, $cellValue);
        }

        //    Return the calculated value
        return $cellValue;
    }

    /**
     * Ensure that paired matrix operands are both matrices and of the same size.
     *
     * @param mixed $operand1 First matrix operand
     * @param mixed $operand2 Second matrix operand
     * @param int $resize Flag indicating whether the matrices should be resized to match
     *                                        and (if so), whether the smaller dimension should grow or the
     *                                        larger should shrink.
     *                                            0 = no resize
     *                                            1 = shrink to fit
     *                                            2 = extend to fit
     */
    public static function checkMatrixOperands(mixed &$operand1, mixed &$operand2, int $resize = 1): array
    {
        //    Examine each of the two operands, and turn them into an array if they aren't one already
        //    Note that this function should only be called if one or both of the operand is already an array
        if (!is_array($operand1)) {
            [$matrixRows, $matrixColumns] = self::getMatrixDimensions($operand2);
            $operand1 = array_fill(0, $matrixRows, array_fill(0, $matrixColumns, $operand1));
            $resize = 0;
        } elseif (!is_array($operand2)) {
            [$matrixRows, $matrixColumns] = self::getMatrixDimensions($operand1);
            $operand2 = array_fill(0, $matrixRows, array_fill(0, $matrixColumns, $operand2));
            $resize = 0;
        }

        [$matrix1Rows, $matrix1Columns] = self::getMatrixDimensions($operand1);
        [$matrix2Rows, $matrix2Columns] = self::getMatrixDimensions($operand2);
        if ($resize === 3) {
            $resize = 2;
        } elseif (($matrix1Rows == $matrix2Columns) && ($matrix2Rows == $matrix1Columns)) {
            $resize = 1;
        }

        if ($resize == 2) {
            //    Given two matrices of (potentially) unequal size, convert the smaller in each dimension to match the larger
            self::resizeMatricesExtend($operand1, $operand2, $matrix1Rows, $matrix1Columns, $matrix2Rows, $matrix2Columns);
        } elseif ($resize == 1) {
            //    Given two matrices of (potentially) unequal size, convert the larger in each dimension to match the smaller
            self::resizeMatricesShrink($operand1, $operand2, $matrix1Rows, $matrix1Columns, $matrix2Rows, $matrix2Columns);
        }
        [$matrix1Rows, $matrix1Columns] = self::getMatrixDimensions($operand1);
        [$matrix2Rows, $matrix2Columns] = self::getMatrixDimensions($operand2);

        return [$matrix1Rows, $matrix1Columns, $matrix2Rows, $matrix2Columns];
    }

    /**
     * Read the dimensions of a matrix, and re-index it with straight numeric keys starting from row 0, column 0.
     *
     * @param array $matrix matrix operand
     *
     * @return int[] An array comprising the number of rows, and number of columns
     */
    public static function getMatrixDimensions(array &$matrix): array
    {
        $matrixRows = count($matrix);
        $matrixColumns = 0;
        foreach ($matrix as $rowKey => $rowValue) {
            if (!is_array($rowValue)) {
                $matrix[$rowKey] = [$rowValue];
                $matrixColumns = max(1, $matrixColumns);
            } else {
                $matrix[$rowKey] = array_values($rowValue);
                $matrixColumns = max(count($rowValue), $matrixColumns);
            }
        }
        $matrix = array_values($matrix);

        return [$matrixRows, $matrixColumns];
    }

    /**
     * Ensure that paired matrix operands are both matrices of the same size.
     *
     * @param array $matrix1 First matrix operand
     * @param array $matrix2 Second matrix operand
     * @param int $matrix1Rows Row size of first matrix operand
     * @param int $matrix1Columns Column size of first matrix operand
     * @param int $matrix2Rows Row size of second matrix operand
     * @param int $matrix2Columns Column size of second matrix operand
     */
    private static function resizeMatricesShrink(array &$matrix1, array &$matrix2, int $matrix1Rows, int $matrix1Columns, int $matrix2Rows, int $matrix2Columns): void
    {
        if (($matrix2Columns < $matrix1Columns) || ($matrix2Rows < $matrix1Rows)) {
            if ($matrix2Rows < $matrix1Rows) {
                for ($i = $matrix2Rows; $i < $matrix1Rows; ++$i) {
                    unset($matrix1[$i]);
                }
            }
            if ($matrix2Columns < $matrix1Columns) {
                for ($i = 0; $i < $matrix1Rows; ++$i) {
                    for ($j = $matrix2Columns; $j < $matrix1Columns; ++$j) {
                        unset($matrix1[$i][$j]);
                    }
                }
            }
        }

        if (($matrix1Columns < $matrix2Columns) || ($matrix1Rows < $matrix2Rows)) {
            if ($matrix1Rows < $matrix2Rows) {
                for ($i = $matrix1Rows; $i < $matrix2Rows; ++$i) {
                    unset($matrix2[$i]);
                }
            }
            if ($matrix1Columns < $matrix2Columns) {
                for ($i = 0; $i < $matrix2Rows; ++$i) {
                    for ($j = $matrix1Columns; $j < $matrix2Columns; ++$j) {
                        unset($matrix2[$i][$j]);
                    }
                }
            }
        }
    }

    /**
     * Ensure that paired matrix operands are both matrices of the same size.
     *
     * @param array $matrix1 First matrix operand
     * @param array $matrix2 Second matrix operand
     * @param int $matrix1Rows Row size of first matrix operand
     * @param int $matrix1Columns Column size of first matrix operand
     * @param int $matrix2Rows Row size of second matrix operand
     * @param int $matrix2Columns Column size of second matrix operand
     */
    private static function resizeMatricesExtend(array &$matrix1, array &$matrix2, int $matrix1Rows, int $matrix1Columns, int $matrix2Rows, int $matrix2Columns): void
    {
        if (($matrix2Columns < $matrix1Columns) || ($matrix2Rows < $matrix1Rows)) {
            if ($matrix2Columns < $matrix1Columns) {
                for ($i = 0; $i < $matrix2Rows; ++$i) {
                    $x = $matrix2[$i][$matrix2Columns - 1];
                    for ($j = $matrix2Columns; $j < $matrix1Columns; ++$j) {
                        $matrix2[$i][$j] = $x;
                    }
                }
            }
            if ($matrix2Rows < $matrix1Rows) {
                $x = $matrix2[$matrix2Rows - 1];
                for ($i = 0; $i < $matrix1Rows; ++$i) {
                    $matrix2[$i] = $x;
                }
            }
        }

        if (($matrix1Columns < $matrix2Columns) || ($matrix1Rows < $matrix2Rows)) {
            if ($matrix1Columns < $matrix2Columns) {
                for ($i = 0; $i < $matrix1Rows; ++$i) {
                    $x = $matrix1[$i][$matrix1Columns - 1];
                    for ($j = $matrix1Columns; $j < $matrix2Columns; ++$j) {
                        $matrix1[$i][$j] = $x;
                    }
                }
            }
            if ($matrix1Rows < $matrix2Rows) {
                $x = $matrix1[$matrix1Rows - 1];
                for ($i = 0; $i < $matrix2Rows; ++$i) {
                    $matrix1[$i] = $x;
                }
            }
        }
    }

    /**
     * Format details of an operand for display in the log (based on operand type).
     *
     * @param mixed $value First matrix operand
     */
    private function showValue(mixed $value): mixed
    {
        if ($this->debugLog->getWriteDebugLog()) {
            $testArray = Functions::flattenArray($value);
            if (count($testArray) == 1) {
                $value = array_pop($testArray);
            }

            if (is_array($value)) {
                $returnMatrix = [];
                $pad = $rpad = ', ';
                foreach ($value as $row) {
                    if (is_array($row)) {
                        $returnMatrix[] = implode($pad, array_map([$this, 'showValue'], $row));
                        $rpad = '; ';
                    } else {
                        $returnMatrix[] = $this->showValue($row);
                    }
                }

                return '{ ' . implode($rpad, $returnMatrix) . ' }';
            } elseif (is_string($value) && (trim($value, self::FORMULA_STRING_QUOTE) == $value)) {
                return self::FORMULA_STRING_QUOTE . $value . self::FORMULA_STRING_QUOTE;
            } elseif (is_bool($value)) {
                return ($value) ? self::$localeBoolean['TRUE'] : self::$localeBoolean['FALSE'];
            } elseif ($value === null) {
                return self::$localeBoolean['NULL'];
            }
        }

        return Functions::flattenSingleValue($value);
    }

    /**
     * Format type and details of an operand for display in the log (based on operand type).
     *
     * @param mixed $value First matrix operand
     */
    private function showTypeDetails(mixed $value): ?string
    {
        if ($this->debugLog->getWriteDebugLog()) {
            $testArray = Functions::flattenArray($value);
            if (count($testArray) == 1) {
                $value = array_pop($testArray);
            }

            if ($value === null) {
                return 'a NULL value';
            } elseif (is_float($value)) {
                $typeString = 'a floating point number';
            } elseif (is_int($value)) {
                $typeString = 'an integer number';
            } elseif (is_bool($value)) {
                $typeString = 'a boolean';
            } elseif (is_array($value)) {
                $typeString = 'a matrix';
            } else {
                if ($value == '') {
                    return 'an empty string';
                } elseif ($value[0] == '#') {
                    return 'a ' . $value . ' error';
                }
                $typeString = 'a string';
            }

            return $typeString . ' with a value of ' . $this->showValue($value);
        }

        return null;
    }

    /**
     * @return false|string False indicates an error
     */
    private function convertMatrixReferences(string $formula): false|string
    {
        static $matrixReplaceFrom = [self::FORMULA_OPEN_MATRIX_BRACE, ';', self::FORMULA_CLOSE_MATRIX_BRACE];
        static $matrixReplaceTo = ['MKMATRIX(MKMATRIX(', '),MKMATRIX(', '))'];

        //    Convert any Excel matrix references to the MKMATRIX() function
        if (str_contains($formula, self::FORMULA_OPEN_MATRIX_BRACE)) {
            //    If there is the possibility of braces within a quoted string, then we don't treat those as matrix indicators
            if (str_contains($formula, self::FORMULA_STRING_QUOTE)) {
                //    So instead we skip replacing in any quoted strings by only replacing in every other array element after we've exploded
                //        the formula
                $temp = explode(self::FORMULA_STRING_QUOTE, $formula);
                //    Open and Closed counts used for trapping mismatched braces in the formula
                $openCount = $closeCount = 0;
                $notWithinQuotes = false;
                foreach ($temp as &$value) {
                    //    Only count/replace in alternating array entries
                    $notWithinQuotes = $notWithinQuotes === false;
                    if ($notWithinQuotes === true) {
                        $openCount += substr_count($value, self::FORMULA_OPEN_MATRIX_BRACE);
                        $closeCount += substr_count($value, self::FORMULA_CLOSE_MATRIX_BRACE);
                        $value = str_replace($matrixReplaceFrom, $matrixReplaceTo, $value);
                    }
                }
                unset($value);
                //    Then rebuild the formula string
                $formula = implode(self::FORMULA_STRING_QUOTE, $temp);
            } else {
                //    If there's no quoted strings, then we do a simple count/replace
                $openCount = substr_count($formula, self::FORMULA_OPEN_MATRIX_BRACE);
                $closeCount = substr_count($formula, self::FORMULA_CLOSE_MATRIX_BRACE);
                $formula = str_replace($matrixReplaceFrom, $matrixReplaceTo, $formula);
            }
            //    Trap for mismatched braces and trigger an appropriate error
            if ($openCount < $closeCount) {
                if ($openCount > 0) {
                    return $this->raiseFormulaError("Formula Error: Mismatched matrix braces '}'");
                }

                return $this->raiseFormulaError("Formula Error: Unexpected '}' encountered");
            } elseif ($openCount > $closeCount) {
                if ($closeCount > 0) {
                    return $this->raiseFormulaError("Formula Error: Mismatched matrix braces '{'");
                }

                return $this->raiseFormulaError("Formula Error: Unexpected '{' encountered");
            }
        }

        return $formula;
    }

    /**
     *    Binary Operators.
     *    These operators always work on two values.
     *    Array key is the operator, the value indicates whether this is a left or right associative operator.
     */
    private static array $operatorAssociativity = [
        '^' => 0, //    Exponentiation
        '*' => 0, '/' => 0, //    Multiplication and Division
        '+' => 0, '-' => 0, //    Addition and Subtraction
        '&' => 0, //    Concatenation
        '∪' => 0, '∩' => 0, ':' => 0, //    Union, Intersect and Range
        '>' => 0, '<' => 0, '=' => 0, '>=' => 0, '<=' => 0, '<>' => 0, //    Comparison
    ];

    /**
     *    Comparison (Boolean) Operators.
     *    These operators work on two values, but always return a boolean result.
     */
    private static array $comparisonOperators = ['>' => true, '<' => true, '=' => true, '>=' => true, '<=' => true, '<>' => true];

    /**
     *    Operator Precedence.
     *    This list includes all valid operators, whether binary (including boolean) or unary (such as %).
     *    Array key is the operator, the value is its precedence.
     */
    private static array $operatorPrecedence = [
        ':' => 9, //    Range
        '∩' => 8, //    Intersect
        '∪' => 7, //    Union
        '~' => 6, //    Negation
        '%' => 5, //    Percentage
        '^' => 4, //    Exponentiation
        '*' => 3, '/' => 3, //    Multiplication and Division
        '+' => 2, '-' => 2, //    Addition and Subtraction
        '&' => 1, //    Concatenation
        '>' => 0, '<' => 0, '=' => 0, '>=' => 0, '<=' => 0, '<>' => 0, //    Comparison
    ];

    // Convert infix to postfix notation

    /**
     * @return array<int, mixed>|false
     */
    private function internalParseFormula(string $formula, ?Cell $cell = null): bool|array
    {
        if (($formula = $this->convertMatrixReferences(trim($formula))) === false) {
            return false;
        }

        //    If we're using cell caching, then $pCell may well be flushed back to the cache (which detaches the parent worksheet),
        //        so we store the parent worksheet so that we can re-attach it when necessary
        $pCellParent = ($cell !== null) ? $cell->getWorksheet() : null;

        $regexpMatchString = '/^((?<string>' . self::CALCULATION_REGEXP_STRING
                                . ')|(?<function>' . self::CALCULATION_REGEXP_FUNCTION
                                . ')|(?<cellRef>' . self::CALCULATION_REGEXP_CELLREF
                                . ')|(?<colRange>' . self::CALCULATION_REGEXP_COLUMN_RANGE
                                . ')|(?<rowRange>' . self::CALCULATION_REGEXP_ROW_RANGE
                                . ')|(?<number>' . self::CALCULATION_REGEXP_NUMBER
                                . ')|(?<openBrace>' . self::CALCULATION_REGEXP_OPENBRACE
                                . ')|(?<structuredReference>' . self::CALCULATION_REGEXP_STRUCTURED_REFERENCE
                                . ')|(?<definedName>' . self::CALCULATION_REGEXP_DEFINEDNAME
                                . ')|(?<error>' . self::CALCULATION_REGEXP_ERROR
                                . '))/sui';

        //    Start with initialisation
        $index = 0;
        $stack = new Stack($this->branchPruner);
        $output = [];
        $expectingOperator = false; //    We use this test in syntax-checking the expression to determine when a
        //        - is a negation or + is a positive operator rather than an operation
        $expectingOperand = false; //    We use this test in syntax-checking the expression to determine whether an operand
        //        should be null in a function call

        //    The guts of the lexical parser
        //    Loop through the formula extracting each operator and operand in turn
        while (true) {
            // Branch pruning: we adapt the output item to the context (it will
            // be used to limit its computation)
            $this->branchPruner->initialiseForLoop();

            $opCharacter = $formula[$index]; //    Get the first character of the value at the current index position

            // Check for two-character operators (e.g. >=, <=, <>)
            if ((isset(self::$comparisonOperators[$opCharacter])) && (strlen($formula) > $index) && isset($formula[$index + 1], self::$comparisonOperators[$formula[$index + 1]])) {
                $opCharacter .= $formula[++$index];
            }
            //    Find out if we're currently at the beginning of a number, variable, cell/row/column reference,
            //         function, defined name, structured reference, parenthesis, error or operand
            $isOperandOrFunction = (bool) preg_match($regexpMatchString, substr($formula, $index), $match);

            $expectingOperatorCopy = $expectingOperator;
            if ($opCharacter === '-' && !$expectingOperator) {                //    Is it a negation instead of a minus?
                //    Put a negation on the stack
                $stack->push('Unary Operator', '~');
                ++$index; //        and drop the negation symbol
            } elseif ($opCharacter === '%' && $expectingOperator) {
                //    Put a percentage on the stack
                $stack->push('Unary Operator', '%');
                ++$index;
            } elseif ($opCharacter === '+' && !$expectingOperator) {            //    Positive (unary plus rather than binary operator plus) can be discarded?
                ++$index; //    Drop the redundant plus symbol
            } elseif ((($opCharacter === '~') || ($opCharacter === '∩') || ($opCharacter === '∪')) && (!$isOperandOrFunction)) {
                //    We have to explicitly deny a tilde, union or intersect because they are legal
                return $this->raiseFormulaError("Formula Error: Illegal character '~'"); //        on the stack but not in the input expression
            } elseif ((isset(self::CALCULATION_OPERATORS[$opCharacter]) || $isOperandOrFunction) && $expectingOperator) {    //    Are we putting an operator on the stack?
                while (
                    $stack->count() > 0
                    && ($o2 = $stack->last())
                    && isset(self::CALCULATION_OPERATORS[$o2['value']])
                    && @(self::$operatorAssociativity[$opCharacter] ? self::$operatorPrecedence[$opCharacter] < self::$operatorPrecedence[$o2['value']] : self::$operatorPrecedence[$opCharacter] <= self::$operatorPrecedence[$o2['value']])
                ) {
                    $output[] = $stack->pop(); //    Swap operands and higher precedence operators from the stack to the output
                }

                //    Finally put our current operator onto the stack
                $stack->push('Binary Operator', $opCharacter);

                ++$index;
                $expectingOperator = false;
            } elseif ($opCharacter === ')' && $expectingOperator) { //    Are we expecting to close a parenthesis?
                $expectingOperand = false;
                while (($o2 = $stack->pop()) && $o2['value'] !== '(') { //    Pop off the stack back to the last (
                    $output[] = $o2;
                }
                $d = $stack->last(2);

                // Branch pruning we decrease the depth whether is it a function
                // call or a parenthesis
                $this->branchPruner->decrementDepth();

                if (is_array($d) && preg_match('/^' . self::CALCULATION_REGEXP_FUNCTION . '$/miu', $d['value'], $matches)) {
                    //    Did this parenthesis just close a function?
                    try {
                        $this->branchPruner->closingBrace($d['value']);
                    } catch (Exception $e) {
                        return $this->raiseFormulaError($e->getMessage(), $e->getCode(), $e);
                    }

                    $functionName = $matches[1]; //    Get the function name
                    $d = $stack->pop();
                    $argumentCount = $d['value'] ?? 0; //    See how many arguments there were (argument count is the next value stored on the stack)
                    $output[] = $d; //    Dump the argument count on the output
                    $output[] = $stack->pop(); //    Pop the function and push onto the output
                    if (isset(self::$controlFunctions[$functionName])) {
                        $expectedArgumentCount = self::$controlFunctions[$functionName]['argumentCount'];
                    } elseif (isset(self::$phpSpreadsheetFunctions[$functionName])) {
                        $expectedArgumentCount = self::$phpSpreadsheetFunctions[$functionName]['argumentCount'];
                    } else {    // did we somehow push a non-function on the stack? this should never happen
                        return $this->raiseFormulaError('Formula Error: Internal error, non-function on stack');
                    }
                    //    Check the argument count
                    $argumentCountError = false;
                    $expectedArgumentCountString = null;
                    if (is_numeric($expectedArgumentCount)) {
                        if ($expectedArgumentCount < 0) {
                            if ($argumentCount > abs($expectedArgumentCount + 0)) {
                                $argumentCountError = true;
                                $expectedArgumentCountString = 'no more than ' . abs($expectedArgumentCount + 0);
                            }
                        } else {
                            if ($argumentCount != $expectedArgumentCount) {
                                $argumentCountError = true;
                                $expectedArgumentCountString = $expectedArgumentCount;
                            }
                        }
                    } elseif ($expectedArgumentCount != '*') {
                        if (1 !== preg_match('/(\d*)([-+,])(\d*)/', $expectedArgumentCount, $argMatch)) {
                            $argMatch = ['', '', '', ''];
                        }
                        switch ($argMatch[2]) {
                            case '+':
                                if ($argumentCount < $argMatch[1]) {
                                    $argumentCountError = true;
                                    $expectedArgumentCountString = $argMatch[1] . ' or more ';
                                }

                                break;
                            case '-':
                                if (($argumentCount < $argMatch[1]) || ($argumentCount > $argMatch[3])) {
                                    $argumentCountError = true;
                                    $expectedArgumentCountString = 'between ' . $argMatch[1] . ' and ' . $argMatch[3];
                                }

                                break;
                            case ',':
                                if (($argumentCount != $argMatch[1]) && ($argumentCount != $argMatch[3])) {
                                    $argumentCountError = true;
                                    $expectedArgumentCountString = 'either ' . $argMatch[1] . ' or ' . $argMatch[3];
                                }

                                break;
                        }
                    }
                    if ($argumentCountError) {
                        return $this->raiseFormulaError("Formula Error: Wrong number of arguments for $functionName() function: $argumentCount given, " . $expectedArgumentCountString . ' expected');
                    }
                }
                ++$index;
            } elseif ($opCharacter === ',') { // Is this the separator for function arguments?
                try {
                    $this->branchPruner->argumentSeparator();
                } catch (Exception $e) {
                    return $this->raiseFormulaError($e->getMessage(), $e->getCode(), $e);
                }

                while (($o2 = $stack->pop()) && $o2['value'] !== '(') {        //    Pop off the stack back to the last (
                    $output[] = $o2; // pop the argument expression stuff and push onto the output
                }
                //    If we've a comma when we're expecting an operand, then what we actually have is a null operand;
                //        so push a null onto the stack
                if (($expectingOperand) || (!$expectingOperator)) {
                    $output[] = $stack->getStackItem('Empty Argument', null, 'NULL');
                }
                // make sure there was a function
                $d = $stack->last(2);
                if (!preg_match('/^' . self::CALCULATION_REGEXP_FUNCTION . '$/miu', $d['value'] ?? '', $matches)) {
                    // Can we inject a dummy function at this point so that the braces at least have some context
                    //     because at least the braces are paired up (at this stage in the formula)
                    // MS Excel allows this if the content is cell references; but doesn't allow actual values,
                    //    but at this point, we can't differentiate (so allow both)
                    return $this->raiseFormulaError('Formula Error: Unexpected ,');
                }

                /** @var array $d */
                $d = $stack->pop();
                ++$d['value']; // increment the argument count

                $stack->pushStackItem($d);
                $stack->push('Brace', '('); // put the ( back on, we'll need to pop back to it again

                $expectingOperator = false;
                $expectingOperand = true;
                ++$index;
            } elseif ($opCharacter === '(' && !$expectingOperator) {
                // Branch pruning: we go deeper
                $this->branchPruner->incrementDepth();
                $stack->push('Brace', '(', null);
                ++$index;
            } elseif ($isOperandOrFunction && !$expectingOperatorCopy) {
                // do we now have a function/variable/number?
                $expectingOperator = true;
                $expectingOperand = false;
                $val = $match[1] ?? ''; //* @phpstan-ignore-line
                $length = strlen($val);

                if (preg_match('/^' . self::CALCULATION_REGEXP_FUNCTION . '$/miu', $val, $matches)) {
                    $val = (string) preg_replace('/\s/u', '', $val);
                    if (isset(self::$phpSpreadsheetFunctions[strtoupper($matches[1])]) || isset(self::$controlFunctions[strtoupper($matches[1])])) {    // it's a function
                        $valToUpper = strtoupper($val);
                    } else {
                        $valToUpper = 'NAME.ERROR(';
                    }
                    // here $matches[1] will contain values like "IF"
                    // and $val "IF("

                    $this->branchPruner->functionCall($valToUpper);

                    $stack->push('Function', $valToUpper);
                    // tests if the function is closed right after opening
                    $ax = preg_match('/^\s*\)/u', substr($formula, $index + $length));
                    if ($ax) {
                        $stack->push('Operand Count for Function ' . $valToUpper . ')', 0);
                        $expectingOperator = true;
                    } else {
                        $stack->push('Operand Count for Function ' . $valToUpper . ')', 1);
                        $expectingOperator = false;
                    }
                    $stack->push('Brace', '(');
                } elseif (preg_match('/^' . self::CALCULATION_REGEXP_CELLREF . '$/miu', $val, $matches)) {
                    //    Watch for this case-change when modifying to allow cell references in different worksheets...
                    //    Should only be applied to the actual cell column, not the worksheet name
                    //    If the last entry on the stack was a : operator, then we have a cell range reference
                    $testPrevOp = $stack->last(1);
                    if ($testPrevOp !== null && $testPrevOp['value'] === ':') {
                        //    If we have a worksheet reference, then we're playing with a 3D reference
                        if ($matches[2] === '') {
                            //    Otherwise, we 'inherit' the worksheet reference from the start cell reference
                            //    The start of the cell range reference should be the last entry in $output
                            $rangeStartCellRef = $output[count($output) - 1]['value'] ?? '';
                            if ($rangeStartCellRef === ':') {
                                // Do we have chained range operators?
                                $rangeStartCellRef = $output[count($output) - 2]['value'] ?? '';
                            }
                            preg_match('/^' . self::CALCULATION_REGEXP_CELLREF . '$/miu', $rangeStartCellRef, $rangeStartMatches);
                            if (array_key_exists(2, $rangeStartMatches)) {
                                if ($rangeStartMatches[2] > '') {
                                    $val = $rangeStartMatches[2] . '!' . $val;
                                }
                            } else {
                                $val = ExcelError::REF();
                            }
                        } else {
                            $rangeStartCellRef = $output[count($output) - 1]['value'] ?? '';
                            if ($rangeStartCellRef === ':') {
                                // Do we have chained range operators?
                                $rangeStartCellRef = $output[count($output) - 2]['value'] ?? '';
                            }
                            preg_match('/^' . self::CALCULATION_REGEXP_CELLREF . '$/miu', $rangeStartCellRef, $rangeStartMatches);
                            if (isset($rangeStartMatches[2]) && $rangeStartMatches[2] !== $matches[2]) {
                                return $this->raiseFormulaError('3D Range references are not yet supported');
                            }
                        }
                    } elseif (!str_contains($val, '!') && $pCellParent !== null) {
                        $worksheet = $pCellParent->getTitle();
                        $val = "'{$worksheet}'!{$val}";
                    }
                    // unescape any apostrophes or double quotes in worksheet name
                    $val = str_replace(["''", '""'], ["'", '"'], $val);
                    $outputItem = $stack->getStackItem('Cell Reference', $val, $val);

                    $output[] = $outputItem;
                } elseif (preg_match('/^' . self::CALCULATION_REGEXP_STRUCTURED_REFERENCE . '$/miu', $val, $matches)) {
                    try {
                        $structuredReference = Operands\StructuredReference::fromParser($formula, $index, $matches);
                    } catch (Exception $e) {
                        return $this->raiseFormulaError($e->getMessage(), $e->getCode(), $e);
                    }

                    $val = $structuredReference->value();
                    $length = strlen($val);
                    $outputItem = $stack->getStackItem(Operands\StructuredReference::NAME, $structuredReference, null);

                    $output[] = $outputItem;
                    $expectingOperator = true;
                } else {
                    // it's a variable, constant, string, number or boolean
                    $localeConstant = false;
                    $stackItemType = 'Value';
                    $stackItemReference = null;

                    //    If the last entry on the stack was a : operator, then we may have a row or column range reference
                    $testPrevOp = $stack->last(1);
                    if ($testPrevOp !== null && $testPrevOp['value'] === ':') {
                        $stackItemType = 'Cell Reference';

                        if (
                            !is_numeric($val)
                            && ((ctype_alpha($val) === false || strlen($val) > 3))
                            && (preg_match('/^' . self::CALCULATION_REGEXP_DEFINEDNAME . '$/mui', $val) !== false)
                            && ($this->spreadsheet === null || $this->spreadsheet->getNamedRange($val) !== null)
                        ) {
                            $namedRange = ($this->spreadsheet === null) ? null : $this->spreadsheet->getNamedRange($val);
                            if ($namedRange !== null) {
                                $stackItemType = 'Defined Name';
                                $address = str_replace('$', '', $namedRange->getValue());
                                $stackItemReference = $val;
                                if (str_contains($address, ':')) {
                                    // We'll need to manipulate the stack for an actual named range rather than a named cell
                                    $fromTo = explode(':', $address);
                                    $to = array_pop($fromTo);
                                    foreach ($fromTo as $from) {
                                        $output[] = $stack->getStackItem($stackItemType, $from, $stackItemReference);
                                        $output[] = $stack->getStackItem('Binary Operator', ':');
                                    }
                                    $address = $to;
                                }
                                $val = $address;
                            }
                        } elseif ($val === ExcelError::REF()) {
                            $stackItemReference = $val;
                        } else {
                            /** @var non-empty-string $startRowColRef */
                            $startRowColRef = $output[count($output) - 1]['value'] ?? '';
                            [$rangeWS1, $startRowColRef] = Worksheet::extractSheetTitle($startRowColRef, true);
                            $rangeSheetRef = $rangeWS1;
                            if ($rangeWS1 !== '') {
                                $rangeWS1 .= '!';
                            }
                            $rangeSheetRef = trim($rangeSheetRef, "'");
                            [$rangeWS2, $val] = Worksheet::extractSheetTitle($val, true);
                            if ($rangeWS2 !== '') {
                                $rangeWS2 .= '!';
                            } else {
                                $rangeWS2 = $rangeWS1;
                            }

                            $refSheet = $pCellParent;
                            if ($pCellParent !== null && $rangeSheetRef !== '' && $rangeSheetRef !== $pCellParent->getTitle()) {
                                $refSheet = $pCellParent->getParentOrThrow()->getSheetByName($rangeSheetRef);
                            }

                            if (ctype_digit($val) && $val <= 1048576) {
                                //    Row range
                                $stackItemType = 'Row Reference';
                                /** @var int $valx */
                                $valx = $val;
                                $endRowColRef = ($refSheet !== null) ? $refSheet->getHighestDataColumn($valx) : AddressRange::MAX_COLUMN; //    Max 16,384 columns for Excel2007
                                $val = "{$rangeWS2}{$endRowColRef}{$val}";
                            } elseif (ctype_alpha($val) && is_string($val) && strlen($val) <= 3) {
                                //    Column range
                                $stackItemType = 'Column Reference';
                                $endRowColRef = ($refSheet !== null) ? $refSheet->getHighestDataRow($val) : AddressRange::MAX_ROW; //    Max 1,048,576 rows for Excel2007
                                $val = "{$rangeWS2}{$val}{$endRowColRef}";
                            }
                            $stackItemReference = $val;
                        }
                    } elseif ($opCharacter === self::FORMULA_STRING_QUOTE) {
                        //    UnEscape any quotes within the string
                        $val = self::wrapResult(str_replace('""', self::FORMULA_STRING_QUOTE, self::unwrapResult($val)));
                    } elseif (isset(self::$excelConstants[trim(strtoupper($val))])) {
                        $stackItemType = 'Constant';
                        $excelConstant = trim(strtoupper($val));
                        $val = self::$excelConstants[$excelConstant];
                        $stackItemReference = $excelConstant;
                    } elseif (($localeConstant = array_search(trim(strtoupper($val)), self::$localeBoolean)) !== false) {
                        $stackItemType = 'Constant';
                        $val = self::$excelConstants[$localeConstant];
                        $stackItemReference = $localeConstant;
                    } elseif (
                        preg_match('/^' . self::CALCULATION_REGEXP_ROW_RANGE . '/miu', substr($formula, $index), $rowRangeReference)
                    ) {
                        $val = $rowRangeReference[1];
                        $length = strlen($rowRangeReference[1]);
                        $stackItemType = 'Row Reference';
                        // unescape any apostrophes or double quotes in worksheet name
                        $val = str_replace(["''", '""'], ["'", '"'], $val);
                        $column = 'A';
                        if (($testPrevOp !== null && $testPrevOp['value'] === ':') && $pCellParent !== null) {
                            $column = $pCellParent->getHighestDataColumn($val);
                        }
                        $val = "{$rowRangeReference[2]}{$column}{$rowRangeReference[7]}";
                        $stackItemReference = $val;
                    } elseif (
                        preg_match('/^' . self::CALCULATION_REGEXP_COLUMN_RANGE . '/miu', substr($formula, $index), $columnRangeReference)
                    ) {
                        $val = $columnRangeReference[1];
                        $length = strlen($val);
                        $stackItemType = 'Column Reference';
                        // unescape any apostrophes or double quotes in worksheet name
                        $val = str_replace(["''", '""'], ["'", '"'], $val);
                        $row = '1';
                        if (($testPrevOp !== null && $testPrevOp['value'] === ':') && $pCellParent !== null) {
                            $row = $pCellParent->getHighestDataRow($val);
                        }
                        $val = "{$val}{$row}";
                        $stackItemReference = $val;
                    } elseif (preg_match('/^' . self::CALCULATION_REGEXP_DEFINEDNAME . '.*/miu', $val, $match)) {
                        $stackItemType = 'Defined Name';
                        $stackItemReference = $val;
                    } elseif (is_numeric($val)) {
                        if ((str_contains((string) $val, '.')) || (stripos((string) $val, 'e') !== false) || ($val > PHP_INT_MAX) || ($val < -PHP_INT_MAX)) {
                            $val = (float) $val;
                        } else {
                            $val = (int) $val;
                        }
                    }

                    $details = $stack->getStackItem($stackItemType, $val, $stackItemReference);
                    if ($localeConstant) {
                        $details['localeValue'] = $localeConstant;
                    }
                    $output[] = $details;
                }
                $index += $length;
            } elseif ($opCharacter === '$') { // absolute row or column range
                ++$index;
            } elseif ($opCharacter === ')') { // miscellaneous error checking
                if ($expectingOperand) {
                    $output[] = $stack->getStackItem('Empty Argument', null, 'NULL');
                    $expectingOperand = false;
                    $expectingOperator = true;
                } else {
                    return $this->raiseFormulaError("Formula Error: Unexpected ')'");
                }
            } elseif (isset(self::CALCULATION_OPERATORS[$opCharacter]) && !$expectingOperator) {
                return $this->raiseFormulaError("Formula Error: Unexpected operator '$opCharacter'");
            } else {    // I don't even want to know what you did to get here
                return $this->raiseFormulaError('Formula Error: An unexpected error occurred');
            }
            //    Test for end of formula string
            if ($index == strlen($formula)) {
                //    Did we end with an operator?.
                //    Only valid for the % unary operator
                if ((isset(self::CALCULATION_OPERATORS[$opCharacter])) && ($opCharacter != '%')) {
                    return $this->raiseFormulaError("Formula Error: Operator '$opCharacter' has no operands");
                }

                break;
            }
            //    Ignore white space
            while (($formula[$index] === "\n") || ($formula[$index] === "\r")) {
                ++$index;
            }

            if ($formula[$index] === ' ') {
                while ($formula[$index] === ' ') {
                    ++$index;
                }

                //    If we're expecting an operator, but only have a space between the previous and next operands (and both are
                //        Cell References, Defined Names or Structured References) then we have an INTERSECTION operator
                $countOutputMinus1 = count($output) - 1;
                if (
                    ($expectingOperator)
                    && array_key_exists($countOutputMinus1, $output)
                    && is_array($output[$countOutputMinus1])
                    && array_key_exists('type', $output[$countOutputMinus1])
                    && (
                        (preg_match('/^' . self::CALCULATION_REGEXP_CELLREF . '.*/miu', substr($formula, $index), $match))
                            && ($output[$countOutputMinus1]['type'] === 'Cell Reference')
                        || (preg_match('/^' . self::CALCULATION_REGEXP_DEFINEDNAME . '.*/miu', substr($formula, $index), $match))
                            && ($output[$countOutputMinus1]['type'] === 'Defined Name' || $output[$countOutputMinus1]['type'] === 'Value')
                        || (preg_match('/^' . self::CALCULATION_REGEXP_STRUCTURED_REFERENCE . '.*/miu', substr($formula, $index), $match))
                            && ($output[$countOutputMinus1]['type'] === Operands\StructuredReference::NAME || $output[$countOutputMinus1]['type'] === 'Value')
                    )
                ) {
                    while (
                        $stack->count() > 0
                        && ($o2 = $stack->last())
                        && isset(self::CALCULATION_OPERATORS[$o2['value']])
                        && @(self::$operatorAssociativity[$opCharacter] ? self::$operatorPrecedence[$opCharacter] < self::$operatorPrecedence[$o2['value']] : self::$operatorPrecedence[$opCharacter] <= self::$operatorPrecedence[$o2['value']])
                    ) {
                        $output[] = $stack->pop(); //    Swap operands and higher precedence operators from the stack to the output
                    }
                    $stack->push('Binary Operator', '∩'); //    Put an Intersect Operator on the stack
                    $expectingOperator = false;
                }
            }
        }

        while (($op = $stack->pop()) !== null) {
            // pop everything off the stack and push onto output
            if ((is_array($op) && $op['value'] == '(')) {
                return $this->raiseFormulaError("Formula Error: Expecting ')'"); // if there are any opening braces on the stack, then braces were unbalanced
            }
            $output[] = $op;
        }

        return $output;
    }

    private static function dataTestReference(array &$operandData): mixed
    {
        $operand = $operandData['value'];
        if (($operandData['reference'] === null) && (is_array($operand))) {
            $rKeys = array_keys($operand);
            $rowKey = array_shift($rKeys);
            if (is_array($operand[$rowKey]) === false) {
                $operandData['value'] = $operand[$rowKey];

                return $operand[$rowKey];
            }

            $cKeys = array_keys(array_keys($operand[$rowKey]));
            $colKey = array_shift($cKeys);
            if (ctype_upper("$colKey")) {
                $operandData['reference'] = $colKey . $rowKey;
            }
        }

        return $operand;
    }

    private static int $matchIndex8 = 8;

    private static int $matchIndex9 = 9;

    private static int $matchIndex10 = 10;

    /**
     * @return array<int, mixed>|false
     */
    private function processTokenStack(mixed $tokens, ?string $cellID = null, ?Cell $cell = null)
    {
        if ($tokens === false) {
            return false;
        }

        //    If we're using cell caching, then $pCell may well be flushed back to the cache (which detaches the parent cell collection),
        //        so we store the parent cell collection so that we can re-attach it when necessary
        $pCellWorksheet = ($cell !== null) ? $cell->getWorksheet() : null;
        $originalCoordinate = $cell?->getCoordinate();
        $pCellParent = ($cell !== null) ? $cell->getParent() : null;
        $stack = new Stack($this->branchPruner);

        // Stores branches that have been pruned
        $fakedForBranchPruning = [];
        // help us to know when pruning ['branchTestId' => true/false]
        $branchStore = [];
        //    Loop through each token in turn
        foreach ($tokens as $tokenIdx => $tokenData) {
            $this->processingAnchorArray = false;
            if ($tokenData['type'] === 'Cell Reference' && isset($tokens[$tokenIdx + 1]) && $tokens[$tokenIdx + 1]['type'] === 'Operand Count for Function ANCHORARRAY()') {
                $this->processingAnchorArray = true;
            }
            $token = $tokenData['value'];
            // Branch pruning: skip useless resolutions
            $storeKey = $tokenData['storeKey'] ?? null;
            if ($this->branchPruningEnabled && isset($tokenData['onlyIf'])) {
                $onlyIfStoreKey = $tokenData['onlyIf'];
                $storeValue = $branchStore[$onlyIfStoreKey] ?? null;
                $storeValueAsBool = ($storeValue === null)
                    ? true : (bool) Functions::flattenSingleValue($storeValue);
                if (is_array($storeValue)) {
                    $wrappedItem = end($storeValue);
                    $storeValue = is_array($wrappedItem) ? end($wrappedItem) : $wrappedItem;
                }

                if (
                    (isset($storeValue) || $tokenData['reference'] === 'NULL')
                    && (!$storeValueAsBool || Information\ErrorValue::isError($storeValue) || ($storeValue === 'Pruned branch'))
                ) {
                    // If branching value is not true, we don't need to compute
                    if (!isset($fakedForBranchPruning['onlyIf-' . $onlyIfStoreKey])) {
                        $stack->push('Value', 'Pruned branch (only if ' . $onlyIfStoreKey . ') ' . $token);
                        $fakedForBranchPruning['onlyIf-' . $onlyIfStoreKey] = true;
                    }

                    if (isset($storeKey)) {
                        // We are processing an if condition
                        // We cascade the pruning to the depending branches
                        $branchStore[$storeKey] = 'Pruned branch';
                        $fakedForBranchPruning['onlyIfNot-' . $storeKey] = true;
                        $fakedForBranchPruning['onlyIf-' . $storeKey] = true;
                    }

                    continue;
                }
            }

            if ($this->branchPruningEnabled && isset($tokenData['onlyIfNot'])) {
                $onlyIfNotStoreKey = $tokenData['onlyIfNot'];
                $storeValue = $branchStore[$onlyIfNotStoreKey] ?? null;
                $storeValueAsBool = ($storeValue === null)
                    ? true : (bool) Functions::flattenSingleValue($storeValue);
                if (is_array($storeValue)) {
                    $wrappedItem = end($storeValue);
                    $storeValue = is_array($wrappedItem) ? end($wrappedItem) : $wrappedItem;
                }

                if (
                    (isset($storeValue) || $tokenData['reference'] === 'NULL')
                    && ($storeValueAsBool || Information\ErrorValue::isError($storeValue) || ($storeValue === 'Pruned branch'))
                ) {
                    // If branching value is true, we don't need to compute
                    if (!isset($fakedForBranchPruning['onlyIfNot-' . $onlyIfNotStoreKey])) {
                        $stack->push('Value', 'Pruned branch (only if not ' . $onlyIfNotStoreKey . ') ' . $token);
                        $fakedForBranchPruning['onlyIfNot-' . $onlyIfNotStoreKey] = true;
                    }

                    if (isset($storeKey)) {
                        // We are processing an if condition
                        // We cascade the pruning to the depending branches
                        $branchStore[$storeKey] = 'Pruned branch';
                        $fakedForBranchPruning['onlyIfNot-' . $storeKey] = true;
                        $fakedForBranchPruning['onlyIf-' . $storeKey] = true;
                    }

                    continue;
                }
            }

            if ($token instanceof Operands\StructuredReference) {
                if ($cell === null) {
                    return $this->raiseFormulaError('Structured References must exist in a Cell context');
                }

                try {
                    $cellRange = $token->parse($cell);
                    if (str_contains($cellRange, ':')) {
                        $this->debugLog->writeDebugLog('Evaluating Structured Reference %s as Cell Range %s', $token->value(), $cellRange);
                        $rangeValue = self::getInstance($cell->getWorksheet()->getParent())->_calculateFormulaValue("={$cellRange}", $cellRange, $cell);
                        $stack->push('Value', $rangeValue);
                        $this->debugLog->writeDebugLog('Evaluated Structured Reference %s as value %s', $token->value(), $this->showValue($rangeValue));
                    } else {
                        $this->debugLog->writeDebugLog('Evaluating Structured Reference %s as Cell %s', $token->value(), $cellRange);
                        $cellValue = $cell->getWorksheet()->getCell($cellRange)->getCalculatedValue(false);
                        $stack->push('Cell Reference', $cellValue, $cellRange);
                        $this->debugLog->writeDebugLog('Evaluated Structured Reference %s as value %s', $token->value(), $this->showValue($cellValue));
                    }
                } catch (Exception $e) {
                    if ($e->getCode() === Exception::CALCULATION_ENGINE_PUSH_TO_STACK) {
                        $stack->push('Error', ExcelError::REF(), null);
                        $this->debugLog->writeDebugLog('Evaluated Structured Reference %s as error value %s', $token->value(), ExcelError::REF());
                    } else {
                        return $this->raiseFormulaError($e->getMessage(), $e->getCode(), $e);
                    }
                }
            } elseif (!is_numeric($token) && !is_object($token) && isset(self::BINARY_OPERATORS[$token])) {
                // if the token is a binary operator, pop the top two values off the stack, do the operation, and push the result back on the stack
                //    We must have two operands, error if we don't
                $operand2Data = $stack->pop();
                if ($operand2Data === null) {
                    return $this->raiseFormulaError('Internal error - Operand value missing from stack');
                }
                $operand1Data = $stack->pop();
                if ($operand1Data === null) {
                    return $this->raiseFormulaError('Internal error - Operand value missing from stack');
                }

                $operand1 = self::dataTestReference($operand1Data);
                $operand2 = self::dataTestReference($operand2Data);

                //    Log what we're doing
                if ($token == ':') {
                    $this->debugLog->writeDebugLog('Evaluating Range %s %s %s', $this->showValue($operand1Data['reference']), $token, $this->showValue($operand2Data['reference']));
                } else {
                    $this->debugLog->writeDebugLog('Evaluating %s %s %s', $this->showValue($operand1), $token, $this->showValue($operand2));
                }

                //    Process the operation in the appropriate manner
                switch ($token) {
                    // Comparison (Boolean) Operators
                    case '>': // Greater than
                    case '<': // Less than
                    case '>=': // Greater than or Equal to
                    case '<=': // Less than or Equal to
                    case '=': // Equality
                    case '<>': // Inequality
                        $result = $this->executeBinaryComparisonOperation($operand1, $operand2, (string) $token, $stack);
                        if (isset($storeKey)) {
                            $branchStore[$storeKey] = $result;
                        }

                        break;
                    // Binary Operators
                    case ':': // Range
                        if ($operand1Data['type'] === 'Defined Name') {
                            if (preg_match('/$' . self::CALCULATION_REGEXP_DEFINEDNAME . '^/mui', $operand1Data['reference']) !== false && $this->spreadsheet !== null) {
                                $definedName = $this->spreadsheet->getNamedRange($operand1Data['reference']);
                                if ($definedName !== null) {
                                    $operand1Data['reference'] = $operand1Data['value'] = str_replace('$', '', $definedName->getValue());
                                }
                            }
                        }
                        if (str_contains($operand1Data['reference'] ?? '', '!')) {
                            [$sheet1, $operand1Data['reference']] = Worksheet::extractSheetTitle($operand1Data['reference'], true);
                        } else {
                            $sheet1 = ($pCellWorksheet !== null) ? $pCellWorksheet->getTitle() : '';
                        }
                        $sheet1 ??= '';

                        [$sheet2, $operand2Data['reference']] = Worksheet::extractSheetTitle($operand2Data['reference'], true);
                        if (empty($sheet2)) {
                            $sheet2 = $sheet1;
                        }

                        if (trim($sheet1, "'") === trim($sheet2, "'")) {
                            if ($operand1Data['reference'] === null && $cell !== null) {
                                if (is_array($operand1Data['value'])) {
                                    $operand1Data['reference'] = $cell->getCoordinate();
                                } elseif ((trim($operand1Data['value']) != '') && (is_numeric($operand1Data['value']))) {
                                    $operand1Data['reference'] = $cell->getColumn() . $operand1Data['value'];
                                } elseif (trim($operand1Data['value']) == '') {
                                    $operand1Data['reference'] = $cell->getCoordinate();
                                } else {
                                    $operand1Data['reference'] = $operand1Data['value'] . $cell->getRow();
                                }
                            }
                            if ($operand2Data['reference'] === null && $cell !== null) {
                                if (is_array($operand2Data['value'])) {
                                    $operand2Data['reference'] = $cell->getCoordinate();
                                } elseif ((trim($operand2Data['value']) != '') && (is_numeric($operand2Data['value']))) {
                                    $operand2Data['reference'] = $cell->getColumn() . $operand2Data['value'];
                                } elseif (trim($operand2Data['value']) == '') {
                                    $operand2Data['reference'] = $cell->getCoordinate();
                                } else {
                                    $operand2Data['reference'] = $operand2Data['value'] . $cell->getRow();
                                }
                            }

                            $oData = array_merge(explode(':', $operand1Data['reference'] ?? ''), explode(':', $operand2Data['reference'] ?? ''));
                            $oCol = $oRow = [];
                            $breakNeeded = false;
                            foreach ($oData as $oDatum) {
                                try {
                                    $oCR = Coordinate::coordinateFromString($oDatum);
                                    $oCol[] = Coordinate::columnIndexFromString($oCR[0]) - 1;
                                    $oRow[] = $oCR[1];
                                } catch (\Exception) {
                                    $stack->push('Error', ExcelError::REF(), null);
                                    $breakNeeded = true;

                                    break;
                                }
                            }
                            if ($breakNeeded) {
                                break;
                            }
                            $cellRef = Coordinate::stringFromColumnIndex(min($oCol) + 1) . min($oRow) . ':' . Coordinate::stringFromColumnIndex(max($oCol) + 1) . max($oRow);
                            if ($pCellParent !== null && $this->spreadsheet !== null) {
                                $cellValue = $this->extractCellRange($cellRef, $this->spreadsheet->getSheetByName($sheet1), false);
                            } else {
                                return $this->raiseFormulaError('Unable to access Cell Reference');
                            }

                            $this->debugLog->writeDebugLog('Evaluation Result is %s', $this->showTypeDetails($cellValue));
                            $stack->push('Cell Reference', $cellValue, $cellRef);
                        } else {
                            $this->debugLog->writeDebugLog('Evaluation Result is a #REF! Error');
                            $stack->push('Error', ExcelError::REF(), null);
                        }

                        break;
                    case '+':            //    Addition
                    case '-':            //    Subtraction
                    case '*':            //    Multiplication
                    case '/':            //    Division
                    case '^':            //    Exponential
                        $result = $this->executeNumericBinaryOperation($operand1, $operand2, $token, $stack);
                        if (isset($storeKey)) {
                            $branchStore[$storeKey] = $result;
                        }

                        break;
                    case '&':            //    Concatenation
                        //    If either of the operands is a matrix, we need to treat them both as matrices
                        //        (converting the other operand to a matrix if need be); then perform the required
                        //        matrix operation
                        $operand1 = self::boolToString($operand1);
                        $operand2 = self::boolToString($operand2);
                        if (is_array($operand1) || is_array($operand2)) {
                            if (is_string($operand1)) {
                                $operand1 = self::unwrapResult($operand1);
                            }
                            if (is_string($operand2)) {
                                $operand2 = self::unwrapResult($operand2);
                            }
                            //    Ensure that both operands are arrays/matrices
                            [$rows, $columns] = self::checkMatrixOperands($operand1, $operand2, 2);

                            for ($row = 0; $row < $rows; ++$row) {
                                for ($column = 0; $column < $columns; ++$column) {
                                    $op1x = self::boolToString($operand1[$row][$column]);
                                    $op2x = self::boolToString($operand2[$row][$column]);
                                    if (Information\ErrorValue::isError($op1x)) {
                                        // no need to do anything
                                    } elseif (Information\ErrorValue::isError($op2x)) {
                                        $operand1[$row][$column] = $op2x;
                                    } else {
                                        $operand1[$row][$column]
                                            = Shared\StringHelper::substring(
                                                $op1x . $op2x,
                                                0,
                                                DataType::MAX_STRING_LENGTH
                                            );
                                    }
                                }
                            }
                            $result = $operand1;
                        } else {
                            // In theory, we should truncate here.
                            // But I can't figure out a formula
                            // using the concatenation operator
                            // with literals that fits in 32K,
                            // so I don't think we can overflow here.
                            if (Information\ErrorValue::isError($operand1)) {
                                $result = $operand1;
                            } elseif (Information\ErrorValue::isError($operand2)) {
                                $result = $operand2;
                            } else {
                                $result = self::FORMULA_STRING_QUOTE . str_replace('""', self::FORMULA_STRING_QUOTE, self::unwrapResult($operand1) . self::unwrapResult($operand2)) . self::FORMULA_STRING_QUOTE;
                            }
                        }
                        $this->debugLog->writeDebugLog('Evaluation Result is %s', $this->showTypeDetails($result));
                        $stack->push('Value', $result);

                        if (isset($storeKey)) {
                            $branchStore[$storeKey] = $result;
                        }

                        break;
                    case '∩':            //    Intersect
                        $rowIntersect = array_intersect_key($operand1, $operand2);
                        $cellIntersect = $oCol = $oRow = [];
                        foreach (array_keys($rowIntersect) as $row) {
                            $oRow[] = $row;
                            foreach ($rowIntersect[$row] as $col => $data) {
                                $oCol[] = Coordinate::columnIndexFromString($col) - 1;
                                $cellIntersect[$row] = array_intersect_key($operand1[$row], $operand2[$row]);
                            }
                        }
                        if (count(Functions::flattenArray($cellIntersect)) === 0) {
                            $this->debugLog->writeDebugLog('Evaluation Result is %s', $this->showTypeDetails($cellIntersect));
                            $stack->push('Error', ExcelError::null(), null);
                        } else {
                            $cellRef = Coordinate::stringFromColumnIndex(min($oCol) + 1) . min($oRow) . ':'
                                . Coordinate::stringFromColumnIndex(max($oCol) + 1) . max($oRow);
                            $this->debugLog->writeDebugLog('Evaluation Result is %s', $this->showTypeDetails($cellIntersect));
                            $stack->push('Value', $cellIntersect, $cellRef);
                        }

                        break;
                }
            } elseif (($token === '~') || ($token === '%')) {
                // if the token is a unary operator, pop one value off the stack, do the operation, and push it back on
                if (($arg = $stack->pop()) === null) {
                    return $this->raiseFormulaError('Internal error - Operand value missing from stack');
                }
                $arg = $arg['value'];
                if ($token === '~') {
                    $this->debugLog->writeDebugLog('Evaluating Negation of %s', $this->showValue($arg));
                    $multiplier = -1;
                } else {
                    $this->debugLog->writeDebugLog('Evaluating Percentile of %s', $this->showValue($arg));
                    $multiplier = 0.01;
                }
                if (is_array($arg)) {
                    $operand2 = $multiplier;
                    $result = $arg;
                    [$rows, $columns] = self::checkMatrixOperands($result, $operand2, 0);
                    for ($row = 0; $row < $rows; ++$row) {
                        for ($column = 0; $column < $columns; ++$column) {
                            if (self::isNumericOrBool($result[$row][$column])) {
                                $result[$row][$column] *= $multiplier;
                            } else {
                                $result[$row][$column] = self::makeError($result[$row][$column]);
                            }
                        }
                    }

                    $this->debugLog->writeDebugLog('Evaluation Result is %s', $this->showTypeDetails($result));
                    $stack->push('Value', $result);
                    if (isset($storeKey)) {
                        $branchStore[$storeKey] = $result;
                    }
                } else {
                    $this->executeNumericBinaryOperation($multiplier, $arg, '*', $stack);
                }
            } elseif (preg_match('/^' . self::CALCULATION_REGEXP_CELLREF . '$/i', $token ?? '', $matches)) {
                $cellRef = null;

                /* Phpstan says matches[8/9/10] is never set,
                   and code coverage report seems to confirm.
                   Appease PhpStan for now;
                   probably delete this block later.
                */
                if (isset($matches[self::$matchIndex8])) {
                    if ($cell === null) {
                        // We can't access the range, so return a REF error
                        $cellValue = ExcelError::REF();
                    } else {
                        $cellRef = $matches[6] . $matches[7] . ':' . $matches[self::$matchIndex9] . $matches[self::$matchIndex10];
                        if ($matches[2] > '') {
                            $matches[2] = trim($matches[2], "\"'");
                            if ((str_contains($matches[2], '[')) || (str_contains($matches[2], ']'))) {
                                //    It's a Reference to an external spreadsheet (not currently supported)
                                return $this->raiseFormulaError('Unable to access External Workbook');
                            }
                            $matches[2] = trim($matches[2], "\"'");
                            $this->debugLog->writeDebugLog('Evaluating Cell Range %s in worksheet %s', $cellRef, $matches[2]);
                            if ($pCellParent !== null && $this->spreadsheet !== null) {
                                $cellValue = $this->extractCellRange($cellRef, $this->spreadsheet->getSheetByName($matches[2]), false);
                            } else {
                                return $this->raiseFormulaError('Unable to access Cell Reference');
                            }
                            $this->debugLog->writeDebugLog('Evaluation Result for cells %s in worksheet %s is %s', $cellRef, $matches[2], $this->showTypeDetails($cellValue));
                        } else {
                            $this->debugLog->writeDebugLog('Evaluating Cell Range %s in current worksheet', $cellRef);
                            if ($pCellParent !== null) {
                                $cellValue = $this->extractCellRange($cellRef, $pCellWorksheet, false);
                            } else {
                                return $this->raiseFormulaError('Unable to access Cell Reference');
                            }
                            $this->debugLog->writeDebugLog('Evaluation Result for cells %s is %s', $cellRef, $this->showTypeDetails($cellValue));
                        }
                    }
                } else {
                    if ($cell === null) {
                        // We can't access the cell, so return a REF error
                        $cellValue = ExcelError::REF();
                    } else {
                        $cellRef = $matches[6] . $matches[7];
                        if ($matches[2] > '') {
                            $matches[2] = trim($matches[2], "\"'");
                            if ((str_contains($matches[2], '[')) || (str_contains($matches[2], ']'))) {
                                //    It's a Reference to an external spreadsheet (not currently supported)
                                return $this->raiseFormulaError('Unable to access External Workbook');
                            }
                            $this->debugLog->writeDebugLog('Evaluating Cell %s in worksheet %s', $cellRef, $matches[2]);
                            if ($pCellParent !== null && $this->spreadsheet !== null) {
                                $cellSheet = $this->spreadsheet->getSheetByName($matches[2]);
                                if ($cellSheet && $cellSheet->cellExists($cellRef)) {
                                    $cellValue = $this->extractCellRange($cellRef, $this->spreadsheet->getSheetByName($matches[2]), false);
                                    $cell->attach($pCellParent);
                                } else {
                                    $cellRef = ($cellSheet !== null) ? "'{$matches[2]}'!{$cellRef}" : $cellRef;
                                    $cellValue = ($cellSheet !== null) ? null : ExcelError::REF();
                                }
                            } else {
                                return $this->raiseFormulaError('Unable to access Cell Reference');
                            }
                            $this->debugLog->writeDebugLog('Evaluation Result for cell %s in worksheet %s is %s', $cellRef, $matches[2], $this->showTypeDetails($cellValue));
                        } else {
                            $this->debugLog->writeDebugLog('Evaluating Cell %s in current worksheet', $cellRef);
                            if ($pCellParent !== null && $pCellParent->has($cellRef)) {
                                $cellValue = $this->extractCellRange($cellRef, $pCellWorksheet, false);
                                $cell->attach($pCellParent);
                            } else {
                                $cellValue = null;
                            }
                            $this->debugLog->writeDebugLog('Evaluation Result for cell %s is %s', $cellRef, $this->showTypeDetails($cellValue));
                        }
                    }
                }

                if ($this->getInstanceArrayReturnType() === self::RETURN_ARRAY_AS_ARRAY && !$this->processingAnchorArray && is_array($cellValue)) {
                    while (is_array($cellValue)) {
                        $cellValue = array_shift($cellValue);
                    }
                    $this->debugLog->writeDebugLog('Scalar Result for cell %s is %s', $cellRef, $this->showTypeDetails($cellValue));
                }
                $this->processingAnchorArray = false;
                $stack->push('Cell Value', $cellValue, $cellRef);
                if (isset($storeKey)) {
                    $branchStore[$storeKey] = $cellValue;
                }
            } elseif (preg_match('/^' . self::CALCULATION_REGEXP_FUNCTION . '$/miu', $token ?? '', $matches)) {
                // if the token is a function, pop arguments off the stack, hand them to the function, and push the result back on
                if ($cell !== null && $pCellParent !== null) {
                    $cell->attach($pCellParent);
                }

                $functionName = $matches[1];
                $argCount = $stack->pop();
                $argCount = $argCount['value'];
                if ($functionName !== 'MKMATRIX') {
                    $this->debugLog->writeDebugLog('Evaluating Function %s() with %s argument%s', self::localeFunc($functionName), (($argCount == 0) ? 'no' : $argCount), (($argCount == 1) ? '' : 's'));
                }
                if ((isset(self::$phpSpreadsheetFunctions[$functionName])) || (isset(self::$controlFunctions[$functionName]))) {    // function
                    $passByReference = false;
                    $passCellReference = false;
                    $functionCall = null;
                    if (isset(self::$phpSpreadsheetFunctions[$functionName])) {
                        $functionCall = self::$phpSpreadsheetFunctions[$functionName]['functionCall'];
                        $passByReference = isset(self::$phpSpreadsheetFunctions[$functionName]['passByReference']);
                        $passCellReference = isset(self::$phpSpreadsheetFunctions[$functionName]['passCellReference']);
                    } elseif (isset(self::$controlFunctions[$functionName])) {
                        $functionCall = self::$controlFunctions[$functionName]['functionCall'];
                        $passByReference = isset(self::$controlFunctions[$functionName]['passByReference']);
                        $passCellReference = isset(self::$controlFunctions[$functionName]['passCellReference']);
                    }

                    // get the arguments for this function
                    $args = $argArrayVals = [];
                    $emptyArguments = [];
                    for ($i = 0; $i < $argCount; ++$i) {
                        $arg = $stack->pop();
                        $a = $argCount - $i - 1;
                        if (
                            ($passByReference)
                            && (isset(self::$phpSpreadsheetFunctions[$functionName]['passByReference'][$a]))
                            && (self::$phpSpreadsheetFunctions[$functionName]['passByReference'][$a])
                        ) {
                            if ($arg['reference'] === null) {
                                $nextArg = $cellID;
                                if ($functionName === 'ISREF' && is_array($arg) && ($arg['type'] ?? '') === 'Value') {
                                    if (array_key_exists('value', $arg)) {
                                        $argValue = $arg['value'];
                                        if (is_scalar($argValue)) {
                                            $nextArg = $argValue;
                                        } elseif (empty($argValue)) {
                                            $nextArg = '';
                                        }
                                    }
                                }
                                $args[] = $nextArg;
                                if ($functionName !== 'MKMATRIX') {
                                    $argArrayVals[] = $this->showValue($cellID);
                                }
                            } else {
                                $args[] = $arg['reference'];
                                if ($functionName !== 'MKMATRIX') {
                                    $argArrayVals[] = $this->showValue($arg['reference']);
                                }
                            }
                        } else {
                            if ($arg['type'] === 'Empty Argument' && in_array($functionName, ['MIN', 'MINA', 'MAX', 'MAXA', 'IF'], true)) {
                                $emptyArguments[] = false;
                                $args[] = $arg['value'] = 0;
                                $this->debugLog->writeDebugLog('Empty Argument reevaluated as 0');
                            } else {
                                $emptyArguments[] = $arg['type'] === 'Empty Argument';
                                $args[] = self::unwrapResult($arg['value']);
                            }
                            if ($functionName !== 'MKMATRIX') {
                                $argArrayVals[] = $this->showValue($arg['value']);
                            }
                        }
                    }

                    //    Reverse the order of the arguments
                    krsort($args);
                    krsort($emptyArguments);

                    if ($argCount > 0 && is_array($functionCall)) {
                        $args = $this->addDefaultArgumentValues($functionCall, $args, $emptyArguments);
                    }

                    if (($passByReference) && ($argCount == 0)) {
                        $args[] = $cellID;
                        $argArrayVals[] = $this->showValue($cellID);
                    }

                    if ($functionName !== 'MKMATRIX') {
                        if ($this->debugLog->getWriteDebugLog()) {
                            krsort($argArrayVals);
                            $this->debugLog->writeDebugLog('Evaluating %s ( %s )', self::localeFunc($functionName), implode(self::$localeArgumentSeparator . ' ', Functions::flattenArray($argArrayVals)));
                        }
                    }

                    //    Process the argument with the appropriate function call
                    if ($pCellWorksheet !== null && $originalCoordinate !== null) {
                        $pCellWorksheet->getCell($originalCoordinate);
                    }
                    $args = $this->addCellReference($args, $passCellReference, $functionCall, $cell);

                    if (!is_array($functionCall)) {
                        foreach ($args as &$arg) {
                            $arg = Functions::flattenSingleValue($arg);
                        }
                        unset($arg);
                    }

                    $result = call_user_func_array($functionCall, $args);

                    if ($functionName !== 'MKMATRIX') {
                        $this->debugLog->writeDebugLog('Evaluation Result for %s() function call is %s', self::localeFunc($functionName), $this->showTypeDetails($result));
                    }
                    $stack->push('Value', self::wrapResult($result));
                    if (isset($storeKey)) {
                        $branchStore[$storeKey] = $result;
                    }
                }
            } else {
                // if the token is a number, boolean, string or an Excel error, push it onto the stack
                if (isset(self::$excelConstants[strtoupper($token ?? '')])) {
                    $excelConstant = strtoupper($token);
                    $stack->push('Constant Value', self::$excelConstants[$excelConstant]);
                    if (isset($storeKey)) {
                        $branchStore[$storeKey] = self::$excelConstants[$excelConstant];
                    }
                    $this->debugLog->writeDebugLog('Evaluating Constant %s as %s', $excelConstant, $this->showTypeDetails(self::$excelConstants[$excelConstant]));
                } elseif ((is_numeric($token)) || ($token === null) || (is_bool($token)) || ($token == '') || ($token[0] == self::FORMULA_STRING_QUOTE) || ($token[0] == '#')) {
                    $stack->push($tokenData['type'], $token, $tokenData['reference']);
                    if (isset($storeKey)) {
                        $branchStore[$storeKey] = $token;
                    }
                } elseif (preg_match('/^' . self::CALCULATION_REGEXP_DEFINEDNAME . '$/miu', $token, $matches)) {
                    // if the token is a named range or formula, evaluate it and push the result onto the stack
                    $definedName = $matches[6];
                    if ($cell === null || $pCellWorksheet === null) {
                        return $this->raiseFormulaError("undefined name '$token'");
                    }
                    $specifiedWorksheet = trim($matches[2], "'");

                    $this->debugLog->writeDebugLog('Evaluating Defined Name %s', $definedName);
                    $namedRange = DefinedName::resolveName($definedName, $pCellWorksheet, $specifiedWorksheet);
                    // If not Defined Name, try as Table.
                    if ($namedRange === null && $this->spreadsheet !== null) {
                        $table = $this->spreadsheet->getTableByName($definedName);
                        if ($table !== null) {
                            $tableRange = Coordinate::getRangeBoundaries($table->getRange());
                            if ($table->getShowHeaderRow()) {
                                ++$tableRange[0][1];
                            }
                            if ($table->getShowTotalsRow()) {
                                --$tableRange[1][1];
                            }
                            $tableRangeString
                                = '$' . $tableRange[0][0]
                                . '$' . $tableRange[0][1]
                                . ':'
                                . '$' . $tableRange[1][0]
                                . '$' . $tableRange[1][1];
                            $namedRange = new NamedRange($definedName, $table->getWorksheet(), $tableRangeString);
                        }
                    }
                    if ($namedRange === null) {
                        return $this->raiseFormulaError("undefined name '$definedName'");
                    }

                    $result = $this->evaluateDefinedName($cell, $namedRange, $pCellWorksheet, $stack, $specifiedWorksheet !== '');
                    if (isset($storeKey)) {
                        $branchStore[$storeKey] = $result;
                    }
                } else {
                    return $this->raiseFormulaError("undefined name '$token'");
                }
            }
        }
        // when we're out of tokens, the stack should have a single element, the final result
        if ($stack->count() != 1) {
            return $this->raiseFormulaError('internal error');
        }
        $output = $stack->pop();
        $output = $output['value'];

        return $output;
    }

    private function validateBinaryOperand(mixed &$operand, mixed &$stack): bool
    {
        if (is_array($operand)) {
            if ((count($operand, COUNT_RECURSIVE) - count($operand)) == 1) {
                do {
                    $operand = array_pop($operand);
                } while (is_array($operand));
            }
        }
        //    Numbers, matrices and booleans can pass straight through, as they're already valid
        if (is_string($operand)) {
            //    We only need special validations for the operand if it is a string
            //    Start by stripping off the quotation marks we use to identify true excel string values internally
            if ($operand > '' && $operand[0] == self::FORMULA_STRING_QUOTE) {
                $operand = self::unwrapResult($operand);
            }
            //    If the string is a numeric value, we treat it as a numeric, so no further testing
            if (!is_numeric($operand)) {
                //    If not a numeric, test to see if the value is an Excel error, and so can't be used in normal binary operations
                if ($operand > '' && $operand[0] == '#') {
                    $stack->push('Value', $operand);
                    $this->debugLog->writeDebugLog('Evaluation Result is %s', $this->showTypeDetails($operand));

                    return false;
                } elseif (Engine\FormattedNumber::convertToNumberIfFormatted($operand) === false) {
                    //    If not a numeric, a fraction or a percentage, then it's a text string, and so can't be used in mathematical binary operations
                    $stack->push('Error', '#VALUE!');
                    $this->debugLog->writeDebugLog('Evaluation Result is a %s', $this->showTypeDetails('#VALUE!'));

                    return false;
                }
            }
        }

        //    return a true if the value of the operand is one that we can use in normal binary mathematical operations
        return true;
    }

    private function executeArrayComparison(mixed $operand1, mixed $operand2, string $operation, Stack &$stack, bool $recursingArrays): array
    {
        $result = [];
        if (!is_array($operand2)) {
            // Operand 1 is an array, Operand 2 is a scalar
            foreach ($operand1 as $x => $operandData) {
                $this->debugLog->writeDebugLog('Evaluating Comparison %s %s %s', $this->showValue($operandData), $operation, $this->showValue($operand2));
                $this->executeBinaryComparisonOperation($operandData, $operand2, $operation, $stack);
                $r = $stack->pop();
                $result[$x] = $r['value'];
            }
        } elseif (!is_array($operand1)) {
            // Operand 1 is a scalar, Operand 2 is an array
            foreach ($operand2 as $x => $operandData) {
                $this->debugLog->writeDebugLog('Evaluating Comparison %s %s %s', $this->showValue($operand1), $operation, $this->showValue($operandData));
                $this->executeBinaryComparisonOperation($operand1, $operandData, $operation, $stack);
                $r = $stack->pop();
                $result[$x] = $r['value'];
            }
        } else {
            // Operand 1 and Operand 2 are both arrays
            if (!$recursingArrays) {
                self::checkMatrixOperands($operand1, $operand2, 2);
            }
            foreach ($operand1 as $x => $operandData) {
                $this->debugLog->writeDebugLog('Evaluating Comparison %s %s %s', $this->showValue($operandData), $operation, $this->showValue($operand2[$x]));
                $this->executeBinaryComparisonOperation($operandData, $operand2[$x], $operation, $stack, true);
                $r = $stack->pop();
                $result[$x] = $r['value'];
            }
        }
        //    Log the result details
        $this->debugLog->writeDebugLog('Comparison Evaluation Result is %s', $this->showTypeDetails($result));
        //    And push the result onto the stack
        $stack->push('Array', $result);

        return $result;
    }

    private function executeBinaryComparisonOperation(mixed $operand1, mixed $operand2, string $operation, Stack &$stack, bool $recursingArrays = false): array|bool
    {
        //    If we're dealing with matrix operations, we want a matrix result
        if ((is_array($operand1)) || (is_array($operand2))) {
            return $this->executeArrayComparison($operand1, $operand2, $operation, $stack, $recursingArrays);
        }

        $result = BinaryComparison::compare($operand1, $operand2, $operation);

        //    Log the result details
        $this->debugLog->writeDebugLog('Evaluation Result is %s', $this->showTypeDetails($result));
        //    And push the result onto the stack
        $stack->push('Value', $result);

        return $result;
    }

    private function executeNumericBinaryOperation(mixed $operand1, mixed $operand2, string $operation, Stack &$stack): mixed
    {
        //    Validate the two operands
        if (
            ($this->validateBinaryOperand($operand1, $stack) === false)
            || ($this->validateBinaryOperand($operand2, $stack) === false)
        ) {
            return false;
        }

        if (
            (Functions::getCompatibilityMode() != Functions::COMPATIBILITY_OPENOFFICE)
            && ((is_string($operand1) && !is_numeric($operand1) && $operand1 !== '')
                || (is_string($operand2) && !is_numeric($operand2) && $operand2 !== ''))
        ) {
            $result = ExcelError::VALUE();
        } elseif (is_array($operand1) || is_array($operand2)) {
            //    Ensure that both operands are arrays/matrices
            if (is_array($operand1)) {
                foreach ($operand1 as $key => $value) {
                    $operand1[$key] = Functions::flattenArray($value);
                }
            }
            if (is_array($operand2)) {
                foreach ($operand2 as $key => $value) {
                    $operand2[$key] = Functions::flattenArray($value);
                }
            }
            [$rows, $columns] = self::checkMatrixOperands($operand1, $operand2, 3);

            for ($row = 0; $row < $rows; ++$row) {
                for ($column = 0; $column < $columns; ++$column) {
                    if ($operand1[$row][$column] === null) {
                        $operand1[$row][$column] = 0;
                    } elseif (!self::isNumericOrBool($operand1[$row][$column])) {
                        $operand1[$row][$column] = self::makeError($operand1[$row][$column]);

                        continue;
                    }
                    if ($operand2[$row][$column] === null) {
                        $operand2[$row][$column] = 0;
                    } elseif (!self::isNumericOrBool($operand2[$row][$column])) {
                        $operand1[$row][$column] = self::makeError($operand2[$row][$column]);

                        continue;
                    }
                    switch ($operation) {
                        case '+':
                            $operand1[$row][$column] += $operand2[$row][$column];

                            break;
                        case '-':
                            $operand1[$row][$column] -= $operand2[$row][$column];

                            break;
                        case '*':
                            $operand1[$row][$column] *= $operand2[$row][$column];

                            break;
                        case '/':
                            if ($operand2[$row][$column] == 0) {
                                $operand1[$row][$column] = ExcelError::DIV0();
                            } else {
                                $operand1[$row][$column] /= $operand2[$row][$column];
                            }

                            break;
                        case '^':
                            $operand1[$row][$column] = $operand1[$row][$column] ** $operand2[$row][$column];

                            break;

                        default:
                            throw new Exception('Unsupported numeric binary operation');
                    }
                }
            }
            $result = $operand1;
        } else {
            //    If we're dealing with non-matrix operations, execute the necessary operation
            switch ($operation) {
                //    Addition
                case '+':
                    $result = $operand1 + $operand2;

                    break;
                //    Subtraction
                case '-':
                    $result = $operand1 - $operand2;

                    break;
                //    Multiplication
                case '*':
                    $result = $operand1 * $operand2;

                    break;
                //    Division
                case '/':
                    if ($operand2 == 0) {
                        //    Trap for Divide by Zero error
                        $stack->push('Error', ExcelError::DIV0());
                        $this->debugLog->writeDebugLog('Evaluation Result is %s', $this->showTypeDetails(ExcelError::DIV0()));

                        return false;
                    }
                    $result = $operand1 / $operand2;

                    break;
                //    Power
                case '^':
                    $result = $operand1 ** $operand2;

                    break;

                default:
                    throw new Exception('Unsupported numeric binary operation');
            }
        }

        //    Log the result details
        $this->debugLog->writeDebugLog('Evaluation Result is %s', $this->showTypeDetails($result));
        //    And push the result onto the stack
        $stack->push('Value', $result);

        return $result;
    }

    /**
     * Trigger an error, but nicely, if need be.
     *
     * @return false
     */
    protected function raiseFormulaError(string $errorMessage, int $code = 0, ?Throwable $exception = null): bool
    {
        $this->formulaError = $errorMessage;
        $this->cyclicReferenceStack->clear();
        $suppress = $this->suppressFormulaErrors;
        if (!$suppress) {
            throw new Exception($errorMessage, $code, $exception);
        }

        return false;
    }

    /**
     * Extract range values.
     *
     * @param string $range String based range representation
     * @param ?Worksheet $worksheet Worksheet
     * @param bool $resetLog Flag indicating whether calculation log should be reset or not
     *
     * @return array Array of values in range if range contains more than one element. Otherwise, a single value is returned.
     */
    public function extractCellRange(string &$range = 'A1', ?Worksheet $worksheet = null, bool $resetLog = true): array
    {
        // Return value
        $returnValue = [];

        if ($worksheet !== null) {
            $worksheetName = $worksheet->getTitle();

            if (str_contains($range, '!')) {
                [$worksheetName, $range] = Worksheet::extractSheetTitle($range, true);
                $worksheet = ($this->spreadsheet === null) ? null : $this->spreadsheet->getSheetByName($worksheetName);
            }

            // Extract range
            $aReferences = Coordinate::extractAllCellReferencesInRange($range);
            $range = "'" . $worksheetName . "'" . '!' . $range;
            $currentCol = '';
            $currentRow = 0;
            if (!isset($aReferences[1])) {
                //    Single cell in range
                sscanf($aReferences[0], '%[A-Z]%d', $currentCol, $currentRow);
                if ($worksheet !== null && $worksheet->cellExists($aReferences[0])) {
                    $temp = $worksheet->getCell($aReferences[0])->getCalculatedValue($resetLog);
                    if ($this->getInstanceArrayReturnType() === self::RETURN_ARRAY_AS_ARRAY) {
                        while (is_array($temp)) {
                            $temp = array_shift($temp);
                        }
                    }
                    $returnValue[$currentRow][$currentCol] = $temp;
                } else {
                    $returnValue[$currentRow][$currentCol] = null;
                }
            } else {
                // Extract cell data for all cells in the range
                foreach ($aReferences as $reference) {
                    // Extract range
                    sscanf($reference, '%[A-Z]%d', $currentCol, $currentRow);
                    if ($worksheet !== null && $worksheet->cellExists($reference)) {
                        $temp = $worksheet->getCell($reference)->getCalculatedValue($resetLog);
                        if ($this->getInstanceArrayReturnType() === self::RETURN_ARRAY_AS_ARRAY) {
                            while (is_array($temp)) {
                                $temp = array_shift($temp);
                            }
                        }
                        $returnValue[$currentRow][$currentCol] = $temp;
                    } else {
                        $returnValue[$currentRow][$currentCol] = null;
                    }
                }
            }
        }

        return $returnValue;
    }

    /**
     * Extract range values.
     *
     * @param string $range String based range representation
     * @param null|Worksheet $worksheet Worksheet
     * @param bool $resetLog Flag indicating whether calculation log should be reset or not
     *
     * @return array|string Array of values in range if range contains more than one element. Otherwise, a single value is returned.
     */
    public function extractNamedRange(string &$range = 'A1', ?Worksheet $worksheet = null, bool $resetLog = true): string|array
    {
        // Return value
        $returnValue = [];

        if ($worksheet !== null) {
            if (str_contains($range, '!')) {
                [$worksheetName, $range] = Worksheet::extractSheetTitle($range, true);
                $worksheet = ($this->spreadsheet === null) ? null : $this->spreadsheet->getSheetByName($worksheetName);
            }

            // Named range?
            $namedRange = ($worksheet === null) ? null : DefinedName::resolveName($range, $worksheet);
            if ($namedRange === null) {
                return ExcelError::REF();
            }

            $worksheet = $namedRange->getWorksheet();
            $range = $namedRange->getValue();
            $splitRange = Coordinate::splitRange($range);
            //    Convert row and column references
            if ($worksheet !== null && ctype_alpha($splitRange[0][0])) {
                $range = $splitRange[0][0] . '1:' . $splitRange[0][1] . $worksheet->getHighestRow();
            } elseif ($worksheet !== null && ctype_digit($splitRange[0][0])) {
                $range = 'A' . $splitRange[0][0] . ':' . $worksheet->getHighestColumn() . $splitRange[0][1];
            }

            // Extract range
            $aReferences = Coordinate::extractAllCellReferencesInRange($range);
            if (!isset($aReferences[1])) {
                //    Single cell (or single column or row) in range
                [$currentCol, $currentRow] = Coordinate::coordinateFromString($aReferences[0]);
                if ($worksheet !== null && $worksheet->cellExists($aReferences[0])) {
                    $returnValue[$currentRow][$currentCol] = $worksheet->getCell($aReferences[0])->getCalculatedValue($resetLog);
                } else {
                    $returnValue[$currentRow][$currentCol] = null;
                }
            } else {
                // Extract cell data for all cells in the range
                foreach ($aReferences as $reference) {
                    // Extract range
                    [$currentCol, $currentRow] = Coordinate::coordinateFromString($reference);
                    if ($worksheet !== null && $worksheet->cellExists($reference)) {
                        $returnValue[$currentRow][$currentCol] = $worksheet->getCell($reference)->getCalculatedValue($resetLog);
                    } else {
                        $returnValue[$currentRow][$currentCol] = null;
                    }
                }
            }
        }

        return $returnValue;
    }

    /**
     * Is a specific function implemented?
     *
     * @param string $function Function Name
     */
    public function isImplemented(string $function): bool
    {
        $function = strtoupper($function);
        $notImplemented = !isset(self::$phpSpreadsheetFunctions[$function]) || (is_array(self::$phpSpreadsheetFunctions[$function]['functionCall']) && self::$phpSpreadsheetFunctions[$function]['functionCall'][1] === 'DUMMY');

        return !$notImplemented;
    }

    /**
     * Get a list of all implemented functions as an array of function objects.
     */
    public static function getFunctions(): array
    {
        return self::$phpSpreadsheetFunctions;
    }

    /**
     * Get a list of implemented Excel function names.
     */
    public function getImplementedFunctionNames(): array
    {
        $returnValue = [];
        foreach (self::$phpSpreadsheetFunctions as $functionName => $function) {
            if ($this->isImplemented($functionName)) {
                $returnValue[] = $functionName;
            }
        }

        return $returnValue;
    }

    private function addDefaultArgumentValues(array $functionCall, array $args, array $emptyArguments): array
    {
        $reflector = new ReflectionMethod($functionCall[0], $functionCall[1]);
        $methodArguments = $reflector->getParameters();

        if (count($methodArguments) > 0) {
            // Apply any defaults for empty argument values
            foreach ($emptyArguments as $argumentId => $isArgumentEmpty) {
                if ($isArgumentEmpty === true) {
                    $reflectedArgumentId = count($args) - (int) $argumentId - 1;
                    if (
                        !array_key_exists($reflectedArgumentId, $methodArguments)
                        || $methodArguments[$reflectedArgumentId]->isVariadic()
                    ) {
                        break;
                    }

                    $args[$argumentId] = $this->getArgumentDefaultValue($methodArguments[$reflectedArgumentId]);
                }
            }
        }

        return $args;
    }

    private function getArgumentDefaultValue(ReflectionParameter $methodArgument): mixed
    {
        $defaultValue = null;

        if ($methodArgument->isDefaultValueAvailable()) {
            $defaultValue = $methodArgument->getDefaultValue();
            if ($methodArgument->isDefaultValueConstant()) {
                $constantName = $methodArgument->getDefaultValueConstantName() ?? '';
                // read constant value
                if (str_contains($constantName, '::')) {
                    [$className, $constantName] = explode('::', $constantName);
                    $constantReflector = new ReflectionClassConstant($className, $constantName);

                    return $constantReflector->getValue();
                }

                return constant($constantName);
            }
        }

        return $defaultValue;
    }

    /**
     * Add cell reference if needed while making sure that it is the last argument.
     */
    private function addCellReference(array $args, bool $passCellReference, array|string $functionCall, ?Cell $cell = null): array
    {
        if ($passCellReference) {
            if (is_array($functionCall)) {
                $className = $functionCall[0];
                $methodName = $functionCall[1];

                $reflectionMethod = new ReflectionMethod($className, $methodName);
                $argumentCount = count($reflectionMethod->getParameters());
                while (count($args) < $argumentCount - 1) {
                    $args[] = null;
                }
            }

            $args[] = $cell;
        }

        return $args;
    }

    private function evaluateDefinedName(Cell $cell, DefinedName $namedRange, Worksheet $cellWorksheet, Stack $stack, bool $ignoreScope = false): mixed
    {
        $definedNameScope = $namedRange->getScope();
        if ($definedNameScope !== null && $definedNameScope !== $cellWorksheet && !$ignoreScope) {
            // The defined name isn't in our current scope, so #REF
            $result = ExcelError::REF();
            $stack->push('Error', $result, $namedRange->getName());

            return $result;
        }

        $definedNameValue = $namedRange->getValue();
        $definedNameType = $namedRange->isFormula() ? 'Formula' : 'Range';
        $definedNameWorksheet = $namedRange->getWorksheet();

        if ($definedNameValue[0] !== '=') {
            $definedNameValue = '=' . $definedNameValue;
        }

        $this->debugLog->writeDebugLog('Defined Name is a %s with a value of %s', $definedNameType, $definedNameValue);

        $originalCoordinate = $cell->getCoordinate();
        $recursiveCalculationCell = ($definedNameType !== 'Formula' && $definedNameWorksheet !== null && $definedNameWorksheet !== $cellWorksheet)
            ? $definedNameWorksheet->getCell('A1')
            : $cell;
        $recursiveCalculationCellAddress = $recursiveCalculationCell->getCoordinate();

        // Adjust relative references in ranges and formulae so that we execute the calculation for the correct rows and columns
        $definedNameValue = self::$referenceHelper->updateFormulaReferencesAnyWorksheet(
            $definedNameValue,
            Coordinate::columnIndexFromString($cell->getColumn()) - 1,
            $cell->getRow() - 1
        );

        $this->debugLog->writeDebugLog('Value adjusted for relative references is %s', $definedNameValue);

        $recursiveCalculator = new self($this->spreadsheet);
        $recursiveCalculator->getDebugLog()->setWriteDebugLog($this->getDebugLog()->getWriteDebugLog());
        $recursiveCalculator->getDebugLog()->setEchoDebugLog($this->getDebugLog()->getEchoDebugLog());
        $result = $recursiveCalculator->_calculateFormulaValue($definedNameValue, $recursiveCalculationCellAddress, $recursiveCalculationCell, true);
        $cellWorksheet->getCell($originalCoordinate);

        if ($this->getDebugLog()->getWriteDebugLog()) {
            $this->debugLog->mergeDebugLog(array_slice($recursiveCalculator->getDebugLog()->getLog(), 3));
            $this->debugLog->writeDebugLog('Evaluation Result for Named %s %s is %s', $definedNameType, $namedRange->getName(), $this->showTypeDetails($result));
        }

        $stack->push('Defined Name', $result, $namedRange->getName());

        return $result;
    }

    public function setSuppressFormulaErrors(bool $suppressFormulaErrors): void
    {
        $this->suppressFormulaErrors = $suppressFormulaErrors;
    }

    public function getSuppressFormulaErrors(): bool
    {
        return $this->suppressFormulaErrors;
    }

    public static function boolToString(mixed $operand1): mixed
    {
        if (is_bool($operand1)) {
            $operand1 = ($operand1) ? self::$localeBoolean['TRUE'] : self::$localeBoolean['FALSE'];
        } elseif ($operand1 === null) {
            $operand1 = '';
        }

        return $operand1;
    }

    private static function isNumericOrBool(mixed $operand): bool
    {
        return is_numeric($operand) || is_bool($operand);
    }

    private static function makeError(mixed $operand = ''): string
    {
        return Information\ErrorValue::isError($operand) ? $operand : ExcelError::VALUE();
    }
}
