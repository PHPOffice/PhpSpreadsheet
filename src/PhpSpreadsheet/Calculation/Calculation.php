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
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use ReflectionClassConstant;
use ReflectionMethod;
use ReflectionParameter;
use Throwable;
use TypeError;

class Calculation extends CalculationLocale
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
    const CALCULATION_REGEXP_STRUCTURED_REFERENCE = '([\p{L}_\\\][\p{L}\p{N}\._]+)?(\[(?:[^\d\]+-])?)';
    //    Error
    const CALCULATION_REGEXP_ERROR = '\#[A-Z][A-Z0_\/]*[!\?]?';

    /** constants */
    const RETURN_ARRAY_AS_ERROR = 'error';
    const RETURN_ARRAY_AS_VALUE = 'value';
    const RETURN_ARRAY_AS_ARRAY = 'array';

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
     *
     * @var mixed[]
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
     * An array of the nested cell references accessed by the calculation engine, used for the debug log.
     */
    private CyclicReferenceStack $cyclicReferenceStack;

    /** @var mixed[] */
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
     * Excel constant string translations to their PHP equivalents
     * Constant conversion from text name/value to actual (datatyped) value.
     */
    private const EXCEL_CONSTANTS = [
        'TRUE' => true,
        'FALSE' => false,
        'NULL' => null,
    ];

    public static function keyInExcelConstants(string $key): bool
    {
        return array_key_exists($key, self::EXCEL_CONSTANTS);
    }

    public static function getExcelConstants(string $key): bool|null
    {
        return self::EXCEL_CONSTANTS[$key];
    }

    /**
     *    Internal functions used for special control purposes.
     *
     * @var array<string, array<string, array<string>|string>>
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
    public function setCalculationCacheEnabled(bool $calculationCacheEnabled): self
    {
        $this->calculationCacheEnabled = $calculationCacheEnabled;
        $this->clearCalculationCache();

        return $this;
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

    public function getBranchPruningEnabled(): bool
    {
        return $this->branchPruningEnabled;
    }

    public function setBranchPruningEnabled(mixed $enabled): self
    {
        $this->branchPruningEnabled = (bool) $enabled;
        $this->branchPruner = new BranchPruner($this->branchPruningEnabled);

        return $this;
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
            if (is_string($value) && $cell->getDataType() === DataType::TYPE_FORMULA) {
                $value = preg_replace_callback(
                    self::CALCULATION_REGEXP_CELLREF_SPILL,
                    fn (array $matches) => 'ANCHORARRAY(' . substr($matches[0], 0, -1) . ')',
                    $value
                );
            }
            $result = self::unwrapResult($this->_calculateFormulaValue($value, $cell->getCoordinate(), $cell)); //* @phpstan-ignore-line
            if ($this->spreadsheet === null) {
                throw new Exception('null spreadsheet in calculateCellValue');
            }
            $cellAddressAttempted = true;
            $cellAddress = array_pop($this->cellStack);
            if ($cellAddress === null) {
                throw new Exception('null cellAddress in calculateCellValue');
            }
            /** @var array{sheet: string, cell: string} $cellAddress */
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
                $sheetName = $cellAddress['sheet'] ?? null;
                $testSheet = is_string($sheetName) ? $this->spreadsheet->getSheetByName($sheetName) : null;
                if ($testSheet !== null && array_key_exists('cell', $cellAddress)) {
                    /** @var array{cell: string} $cellAddress */
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
     *
     * @return array<mixed>|bool
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
     *
     * @param-out mixed[] $operand1
     *
     * @param mixed $operand2 Second matrix operand
     *
     * @param-out mixed[] $operand2
     *
     * @param int $resize Flag indicating whether the matrices should be resized to match
     *                                        and (if so), whether the smaller dimension should grow or the
     *                                        larger should shrink.
     *                                            0 = no resize
     *                                            1 = shrink to fit
     *                                            2 = extend to fit
     *
     * @return mixed[]
     */
    public static function checkMatrixOperands(mixed &$operand1, mixed &$operand2, int $resize = 1): array
    {
        //    Examine each of the two operands, and turn them into an array if they aren't one already
        //    Note that this function should only be called if one or both of the operand is already an array
        if (!is_array($operand1)) {
            if (is_array($operand2)) {
                [$matrixRows, $matrixColumns] = self::getMatrixDimensions($operand2);
                $operand1 = array_fill(0, $matrixRows, array_fill(0, $matrixColumns, $operand1));
                $resize = 0;
            } else {
                $operand1 = [$operand1];
                $operand2 = [$operand2];
            }
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
            /** @var mixed[][] $operand1 */
            /** @var mixed[][] $operand2 */
            self::resizeMatricesShrink($operand1, $operand2, $matrix1Rows, $matrix1Columns, $matrix2Rows, $matrix2Columns);
        }
        [$matrix1Rows, $matrix1Columns] = self::getMatrixDimensions($operand1);
        [$matrix2Rows, $matrix2Columns] = self::getMatrixDimensions($operand2);

        return [$matrix1Rows, $matrix1Columns, $matrix2Rows, $matrix2Columns];
    }

    /**
     * Read the dimensions of a matrix, and re-index it with straight numeric keys starting from row 0, column 0.
     *
     * @param mixed[] $matrix matrix operand
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
     * @param mixed[][] $matrix1 First matrix operand
     * @param mixed[][] $matrix2 Second matrix operand
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
     * @param mixed[] $matrix1 First matrix operand
     * @param mixed[] $matrix2 Second matrix operand
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
                    /** @var mixed[][] $matrix2 */
                    $x = ($matrix2Columns === 1) ? $matrix2[$i][0] : null;
                    for ($j = $matrix2Columns; $j < $matrix1Columns; ++$j) {
                        $matrix2[$i][$j] = $x;
                    }
                }
            }
            if ($matrix2Rows < $matrix1Rows) {
                $x = ($matrix2Rows === 1) ? $matrix2[0] : array_fill(0, $matrix2Columns, null);
                for ($i = $matrix2Rows; $i < $matrix1Rows; ++$i) {
                    $matrix2[$i] = $x;
                }
            }
        }

        if (($matrix1Columns < $matrix2Columns) || ($matrix1Rows < $matrix2Rows)) {
            if ($matrix1Columns < $matrix2Columns) {
                for ($i = 0; $i < $matrix1Rows; ++$i) {
                    /** @var mixed[][] $matrix1 */
                    $x = ($matrix1Columns === 1) ? $matrix1[$i][0] : null;
                    for ($j = $matrix1Columns; $j < $matrix2Columns; ++$j) {
                        $matrix1[$i][$j] = $x;
                    }
                }
            }
            if ($matrix1Rows < $matrix2Rows) {
                $x = ($matrix1Rows === 1) ? $matrix1[0] : array_fill(0, $matrix1Columns, null);
                for ($i = $matrix1Rows; $i < $matrix2Rows; ++$i) {
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
                /** @var string $value */
                if ($value == '') {
                    return 'an empty string';
                } elseif ($value[0] == '#') {
                    return 'a ' . $value . ' error';
                }
                $typeString = 'a string';
            }

            return $typeString . ' with a value of ' . StringHelper::convertToString($this->showValue($value));
        }

        return null;
    }

    private const MATRIX_REPLACE_FROM = [self::FORMULA_OPEN_MATRIX_BRACE, ';', self::FORMULA_CLOSE_MATRIX_BRACE];
    private const MATRIX_REPLACE_TO = ['MKMATRIX(MKMATRIX(', '),MKMATRIX(', '))'];

    /**
     * @return false|string False indicates an error
     */
    private function convertMatrixReferences(string $formula): false|string
    {
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
                        $value = str_replace(self::MATRIX_REPLACE_FROM, self::MATRIX_REPLACE_TO, $value);
                    }
                }
                unset($value);
                //    Then rebuild the formula string
                $formula = implode(self::FORMULA_STRING_QUOTE, $temp);
            } else {
                //    If there's no quoted strings, then we do a simple count/replace
                $openCount = substr_count($formula, self::FORMULA_OPEN_MATRIX_BRACE);
                $closeCount = substr_count($formula, self::FORMULA_CLOSE_MATRIX_BRACE);
                $formula = str_replace(self::MATRIX_REPLACE_FROM, self::MATRIX_REPLACE_TO, $formula);
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
     *    Comparison (Boolean) Operators.
     *    These operators work on two values, but always return a boolean result.
     */
    private const COMPARISON_OPERATORS = ['>' => true, '<' => true, '=' => true, '>=' => true, '<=' => true, '<>' => true];

    /**
     *    Operator Precedence.
     *    This list includes all valid operators, whether binary (including boolean) or unary (such as %).
     *    Array key is the operator, the value is its precedence.
     */
    private const OPERATOR_PRECEDENCE = [
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

    /**
     * @return array<int, mixed>|false
     */
    private function internalParseFormula(string $formula, ?Cell $cell = null): bool|array
    {
        if (($formula = $this->convertMatrixReferences(trim($formula))) === false) {
            return false;
        }
        $phpSpreadsheetFunctions = &self::getFunctionsAddress();

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
            if ((isset(self::COMPARISON_OPERATORS[$opCharacter])) && (strlen($formula) > $index) && isset($formula[$index + 1], self::COMPARISON_OPERATORS[$formula[$index + 1]])) {
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
                while (self::swapOperands($stack, $opCharacter)) {
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

                if (is_array($d) && preg_match('/^' . self::CALCULATION_REGEXP_FUNCTION . '$/miu', StringHelper::convertToString($d['value']), $matches)) {
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
                    } elseif (isset($phpSpreadsheetFunctions[$functionName])) {
                        $expectedArgumentCount = $phpSpreadsheetFunctions[$functionName]['argumentCount'];
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
                    } elseif (is_string($expectedArgumentCount) && $expectedArgumentCount !== '*') {
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
                        /** @var int $argumentCount */
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
                /** @var string */
                $temp = $d['value'] ?? '';
                if (!preg_match('/^' . self::CALCULATION_REGEXP_FUNCTION . '$/miu', $temp, $matches)) {
                    // Can we inject a dummy function at this point so that the braces at least have some context
                    //     because at least the braces are paired up (at this stage in the formula)
                    // MS Excel allows this if the content is cell references; but doesn't allow actual values,
                    //    but at this point, we can't differentiate (so allow both)
                    return $this->raiseFormulaError('Formula Error: Unexpected ,');
                }

                /** @var array<string, int> $d */
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
                    if (isset($phpSpreadsheetFunctions[strtoupper($matches[1])]) || isset(self::$controlFunctions[strtoupper($matches[1])])) {    // it's a function
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
                            /** @var string $rangeStartCellRef */
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
                            /** @var string $rangeStartCellRef */
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
                            if (str_starts_with($rangeSheetRef, "'")) {
                                $rangeSheetRef = Worksheet::unApostrophizeTitle($rangeSheetRef);
                            }
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
                                $valx = $val;
                                $endRowColRef = ($refSheet !== null) ? $refSheet->getHighestDataColumn($valx) : AddressRange::MAX_COLUMN; //    Max 16,384 columns for Excel2007
                                $val = "{$rangeWS2}{$endRowColRef}{$val}";
                            } elseif (ctype_alpha($val) && strlen($val) <= 3) {
                                //    Column range
                                $stackItemType = 'Column Reference';
                                $endRowColRef = ($refSheet !== null) ? $refSheet->getHighestDataRow($val) : AddressRange::MAX_ROW; //    Max 1,048,576 rows for Excel2007
                                $val = "{$rangeWS2}{$val}{$endRowColRef}";
                            }
                            $stackItemReference = $val;
                        }
                    } elseif ($opCharacter === self::FORMULA_STRING_QUOTE) {
                        //    UnEscape any quotes within the string
                        $val = self::wrapResult(str_replace('""', self::FORMULA_STRING_QUOTE, StringHelper::convertToString(self::unwrapResult($val))));
                    } elseif (isset(self::EXCEL_CONSTANTS[trim(strtoupper($val))])) {
                        $stackItemType = 'Constant';
                        $excelConstant = trim(strtoupper($val));
                        $val = self::EXCEL_CONSTANTS[$excelConstant];
                        $stackItemReference = $excelConstant;
                    } elseif (($localeConstant = array_search(trim(strtoupper($val)), self::$localeBoolean)) !== false) {
                        $stackItemType = 'Constant';
                        $val = self::EXCEL_CONSTANTS[$localeConstant];
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
                    while (self::swapOperands($stack, $opCharacter)) {
                        $output[] = $stack->pop(); //    Swap operands and higher precedence operators from the stack to the output
                    }
                    $stack->push('Binary Operator', '∩'); //    Put an Intersect Operator on the stack
                    $expectingOperator = false;
                }
            }
        }

        while (($op = $stack->pop()) !== null) {
            // pop everything off the stack and push onto output
            if ($op['value'] == '(') {
                return $this->raiseFormulaError("Formula Error: Expecting ')'"); // if there are any opening braces on the stack, then braces were unbalanced
            }
            $output[] = $op;
        }

        return $output;
    }

    /** @param mixed[] $operandData */
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
     * @param array<mixed>|false $tokens
     *
     * @return array<int, mixed>|false|string
     */
    private function processTokenStack(false|array $tokens, ?string $cellID = null, ?Cell $cell = null)
    {
        if ($tokens === false) {
            return false;
        }
        $phpSpreadsheetFunctions = &self::getFunctionsAddress();

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
            /** @var mixed[] $tokenData */
            $this->processingAnchorArray = false;
            if ($tokenData['type'] === 'Cell Reference' && isset($tokens[$tokenIdx + 1]) && $tokens[$tokenIdx + 1]['type'] === 'Operand Count for Function ANCHORARRAY()') { //* @phpstan-ignore-line
                $this->processingAnchorArray = true;
            }
            $token = $tokenData['value'];
            // Branch pruning: skip useless resolutions
            /** @var ?string */
            $storeKey = $tokenData['storeKey'] ?? null;
            if ($this->branchPruningEnabled && isset($tokenData['onlyIf'])) {
                /** @var string */
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
                    /** @var string $onlyIfStoreKey */
                    if (!isset($fakedForBranchPruning['onlyIf-' . $onlyIfStoreKey])) {
                        /** @var string $token */
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
                /** @var string */
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
                        /** @var string $token */
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
                            /** @var array{reference: string} $operand1Data */
                            if (preg_match('/$' . self::CALCULATION_REGEXP_DEFINEDNAME . '^/mui', $operand1Data['reference']) !== false && $this->spreadsheet !== null) {
                                /** @var string[] $operand1Data */
                                $definedName = $this->spreadsheet->getNamedRange($operand1Data['reference']);
                                if ($definedName !== null) {
                                    $operand1Data['reference'] = $operand1Data['value'] = str_replace('$', '', $definedName->getValue());
                                }
                            }
                        }
                        /** @var array{reference?: ?string} $operand1Data */
                        if (str_contains($operand1Data['reference'] ?? '', '!')) {
                            [$sheet1, $operand1Data['reference']] = Worksheet::extractSheetTitle($operand1Data['reference'], true, true);
                        } else {
                            $sheet1 = ($pCellWorksheet !== null) ? $pCellWorksheet->getTitle() : '';
                        }
                        //$sheet1 ??= ''; // phpstan level 10 says this is unneeded

                        /** @var string */
                        $op2ref = $operand2Data['reference'];
                        [$sheet2, $operand2Data['reference']] = Worksheet::extractSheetTitle($op2ref, true, true);
                        if (empty($sheet2)) {
                            $sheet2 = $sheet1;
                        }

                        if ($sheet1 === $sheet2) {
                            /** @var array{reference: ?string, value: string|string[]} $operand1Data */
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
                            /** @var array{reference: ?string, value: string|string[]} $operand2Data */
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
                            $cellRef = Coordinate::stringFromColumnIndex(min($oCol) + 1) . min($oRow) . ':' . Coordinate::stringFromColumnIndex(max($oCol) + 1) . max($oRow); // @phpstan-ignore-line
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
                                    /** @var mixed[][] $operand1 */
                                    $op1x = self::boolToString($operand1[$row][$column]);
                                    /** @var mixed[][] $operand2 */
                                    $op2x = self::boolToString($operand2[$row][$column]);
                                    if (Information\ErrorValue::isError($op1x)) {
                                        // no need to do anything
                                    } elseif (Information\ErrorValue::isError($op2x)) {
                                        $operand1[$row][$column] = $op2x;
                                    } else {
                                        /** @var string $op1x */
                                        /** @var string $op2x */
                                        $operand1[$row][$column]
                                            = StringHelper::substring(
                                                $op1x . $op2x,
                                                0,
                                                DataType::MAX_STRING_LENGTH
                                            );
                                    }
                                }
                            }
                            $result = $operand1;
                        } else {
                            if (Information\ErrorValue::isError($operand1)) {
                                $result = $operand1;
                            } elseif (Information\ErrorValue::isError($operand2)) {
                                $result = $operand2;
                            } else {
                                $result = str_replace('""', self::FORMULA_STRING_QUOTE, self::unwrapResult($operand1) . self::unwrapResult($operand2)); //* @phpstan-ignore-line
                                $result = StringHelper::substring(
                                    $result,
                                    0,
                                    DataType::MAX_STRING_LENGTH
                                );
                                $result = self::FORMULA_STRING_QUOTE . $result . self::FORMULA_STRING_QUOTE;
                            }
                        }
                        $this->debugLog->writeDebugLog('Evaluation Result is %s', $this->showTypeDetails($result));
                        $stack->push('Value', $result);

                        if (isset($storeKey)) {
                            $branchStore[$storeKey] = $result;
                        }

                        break;
                    case '∩':            //    Intersect
                        /** @var mixed[][] $operand1 */
                        /** @var mixed[][] $operand2 */
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
                            $cellRef = Coordinate::stringFromColumnIndex(min($oCol) + 1) . min($oRow) . ':' // @phpstan-ignore-line
                                . Coordinate::stringFromColumnIndex(max($oCol) + 1) . max($oRow); // @phpstan-ignore-line
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
                            /** @var mixed[][] $result */
                            if (self::isNumericOrBool($result[$row][$column])) {
                                /** @var float|int|numeric-string */
                                $temp = $result[$row][$column];
                                $result[$row][$column] = $temp * $multiplier;
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
            } elseif (preg_match('/^' . self::CALCULATION_REGEXP_CELLREF . '$/i', StringHelper::convertToString($token ?? ''), $matches)) {
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
                    if (is_string($cellValue)) {
                        $cellValue = preg_replace('/"/', '""', $cellValue);
                    }
                    $this->debugLog->writeDebugLog('Scalar Result for cell %s is %s', $cellRef, $this->showTypeDetails($cellValue));
                }
                $this->processingAnchorArray = false;
                $stack->push('Cell Value', $cellValue, $cellRef);
                if (isset($storeKey)) {
                    $branchStore[$storeKey] = $cellValue;
                }
            } elseif (preg_match('/^' . self::CALCULATION_REGEXP_FUNCTION . '$/miu', StringHelper::convertToString($token ?? ''), $matches)) {
                // if the token is a function, pop arguments off the stack, hand them to the function, and push the result back on
                if ($cell !== null && $pCellParent !== null) {
                    $cell->attach($pCellParent);
                }

                $functionName = $matches[1];
                /** @var array<string, int> $argCount */
                $argCount = $stack->pop();
                $argCount = $argCount['value'];
                if ($functionName !== 'MKMATRIX') {
                    $this->debugLog->writeDebugLog('Evaluating Function %s() with %s argument%s', self::localeFunc($functionName), (($argCount == 0) ? 'no' : $argCount), (($argCount == 1) ? '' : 's'));
                }
                if ((isset($phpSpreadsheetFunctions[$functionName])) || (isset(self::$controlFunctions[$functionName]))) {    // function
                    $passByReference = false;
                    $passCellReference = false;
                    $functionCall = null;
                    if (isset($phpSpreadsheetFunctions[$functionName])) {
                        $functionCall = $phpSpreadsheetFunctions[$functionName]['functionCall'];
                        $passByReference = isset($phpSpreadsheetFunctions[$functionName]['passByReference']);
                        $passCellReference = isset($phpSpreadsheetFunctions[$functionName]['passCellReference']);
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
                            && (isset($phpSpreadsheetFunctions[$functionName]['passByReference'][$a])) //* @phpstan-ignore-line
                            && ($phpSpreadsheetFunctions[$functionName]['passByReference'][$a])
                        ) {
                            /** @var mixed[] $arg */
                            if ($arg['reference'] === null) {
                                $nextArg = $cellID;
                                if ($functionName === 'ISREF' && ($arg['type'] ?? '') === 'Value') {
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
                            /** @var mixed[] $arg */
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
                        /** @var string[] */
                        $functionCallCopy = $functionCall;
                        $args = $this->addDefaultArgumentValues($functionCallCopy, $args, $emptyArguments);
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
                    /** @var array<string>|string $functionCall */
                    $args = $this->addCellReference($args, $passCellReference, $functionCall, $cell);

                    if (!is_array($functionCall)) {
                        foreach ($args as &$arg) {
                            $arg = Functions::flattenSingleValue($arg);
                        }
                        unset($arg);
                    }

                    /** @var callable $functionCall */
                    try {
                        $result = call_user_func_array($functionCall, $args);
                    } catch (TypeError $e) {
                        if (!$this->suppressFormulaErrors) {
                            throw $e;
                        }
                        $result = false;
                    }
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
                /** @var ?string $token */
                if (isset(self::EXCEL_CONSTANTS[strtoupper($token ?? '')])) {
                    $excelConstant = strtoupper("$token");
                    $stack->push('Constant Value', self::EXCEL_CONSTANTS[$excelConstant]);
                    if (isset($storeKey)) {
                        $branchStore[$storeKey] = self::EXCEL_CONSTANTS[$excelConstant];
                    }
                    $this->debugLog->writeDebugLog('Evaluating Constant %s as %s', $excelConstant, $this->showTypeDetails(self::EXCEL_CONSTANTS[$excelConstant]));
                } elseif ((is_numeric($token)) || ($token === null) || (is_bool($token)) || ($token == '') || ($token[0] == self::FORMULA_STRING_QUOTE) || ($token[0] == '#')) { //* @phpstan-ignore-line
                    /** @var array{type: string, reference: ?string} $tokenData */
                    $stack->push($tokenData['type'], $token, $tokenData['reference']);
                    if (isset($storeKey)) {
                        $branchStore[$storeKey] = $token;
                    }
                } elseif (preg_match('/^' . self::CALCULATION_REGEXP_DEFINEDNAME . '$/miu', $token, $matches)) {
                    // if the token is a named range or formula, evaluate it and push the result onto the stack
                    $definedName = $matches[6];
                    if (str_starts_with($definedName, '_xleta')) {
                        return Functions::NOT_YET_IMPLEMENTED;
                    }
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
        /** @var array<string, array<int, mixed>|false|string> */
        $output = $stack->pop();
        $output = $output['value'];

        return $output;
    }

    private function validateBinaryOperand(mixed &$operand, Stack &$stack): bool
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
                $operand = StringHelper::convertToString(self::unwrapResult($operand));
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

    /** @return mixed[] */
    private function executeArrayComparison(mixed $operand1, mixed $operand2, string $operation, Stack &$stack, bool $recursingArrays): array
    {
        $result = [];
        if (!is_array($operand2) && is_array($operand1)) {
            // Operand 1 is an array, Operand 2 is a scalar
            foreach ($operand1 as $x => $operandData) {
                $this->debugLog->writeDebugLog('Evaluating Comparison %s %s %s', $this->showValue($operandData), $operation, $this->showValue($operand2));
                $this->executeBinaryComparisonOperation($operandData, $operand2, $operation, $stack);
                /** @var array<string, mixed> $r */
                $r = $stack->pop();
                $result[$x] = $r['value'];
            }
        } elseif (is_array($operand2) && !is_array($operand1)) {
            // Operand 1 is a scalar, Operand 2 is an array
            foreach ($operand2 as $x => $operandData) {
                $this->debugLog->writeDebugLog('Evaluating Comparison %s %s %s', $this->showValue($operand1), $operation, $this->showValue($operandData));
                $this->executeBinaryComparisonOperation($operand1, $operandData, $operation, $stack);
                /** @var array<string, mixed> $r */
                $r = $stack->pop();
                $result[$x] = $r['value'];
            }
        } elseif (is_array($operand2) && is_array($operand1)) {
            // Operand 1 and Operand 2 are both arrays
            if (!$recursingArrays) {
                self::checkMatrixOperands($operand1, $operand2, 2);
            }
            foreach ($operand1 as $x => $operandData) {
                $this->debugLog->writeDebugLog('Evaluating Comparison %s %s %s', $this->showValue($operandData), $operation, $this->showValue($operand2[$x]));
                $this->executeBinaryComparisonOperation($operandData, $operand2[$x], $operation, $stack, true);
                /** @var array<string, mixed> $r */
                $r = $stack->pop();
                $result[$x] = $r['value'];
            }
        } else {
            throw new Exception('Neither operand is an arra');
        }
        //    Log the result details
        $this->debugLog->writeDebugLog('Comparison Evaluation Result is %s', $this->showTypeDetails($result));
        //    And push the result onto the stack
        $stack->push('Array', $result);

        return $result;
    }

    /** @return bool|mixed[] */
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
                    /** @var mixed[][] $operand1 */
                    if (($operand1[$row][$column] ?? null) === null) {
                        $operand1[$row][$column] = 0;
                    } elseif (!self::isNumericOrBool($operand1[$row][$column])) {
                        $operand1[$row][$column] = self::makeError($operand1[$row][$column]);

                        continue;
                    }
                    /** @var mixed[][] $operand2 */
                    if (($operand2[$row][$column] ?? null) === null) {
                        $operand2[$row][$column] = 0;
                    } elseif (!self::isNumericOrBool($operand2[$row][$column])) {
                        $operand1[$row][$column] = self::makeError($operand2[$row][$column]);

                        continue;
                    }
                    /** @var float|int */
                    $operand1Val = $operand1[$row][$column];
                    /** @var float|int */
                    $operand2Val = $operand2[$row][$column];
                    switch ($operation) {
                        case '+':
                            $operand1[$row][$column] = $operand1Val + $operand2Val;

                            break;
                        case '-':
                            $operand1[$row][$column] = $operand1Val - $operand2Val;

                            break;
                        case '*':
                            $operand1[$row][$column] = $operand1Val * $operand2Val;

                            break;
                        case '/':
                            if ($operand2Val == 0) {
                                $operand1[$row][$column] = ExcelError::DIV0();
                            } else {
                                $operand1[$row][$column] = $operand1Val / $operand2Val;
                            }

                            break;
                        case '^':
                            $operand1[$row][$column] = $operand1Val ** $operand2Val;

                            break;

                        default:
                            throw new Exception('Unsupported numeric binary operation');
                    }
                }
            }
            $result = $operand1;
        } else {
            //    If we're dealing with non-matrix operations, execute the necessary operation
            /** @var float|int $operand1 */
            /** @var float|int $operand2 */
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
     * @return mixed[] Array of values in range if range contains more than one element. Otherwise, a single value is returned.
     */
    public function extractCellRange(string &$range = 'A1', ?Worksheet $worksheet = null, bool $resetLog = true): array
    {
        // Return value
        /** @var mixed[][] */
        $returnValue = [];

        if ($worksheet !== null) {
            $worksheetName = $worksheet->getTitle();

            if (str_contains($range, '!')) {
                [$worksheetName, $range] = Worksheet::extractSheetTitle($range, true, true);
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
     * @return mixed[]|string Array of values in range if range contains more than one element. Otherwise, a single value is returned.
     */
    public function extractNamedRange(string &$range = 'A1', ?Worksheet $worksheet = null, bool $resetLog = true): string|array
    {
        // Return value
        $returnValue = [];

        if ($worksheet !== null) {
            if (str_contains($range, '!')) {
                [$worksheetName, $range] = Worksheet::extractSheetTitle($range, true, true);
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
                /** @var mixed[][] $returnValue */
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
        $phpSpreadsheetFunctions = &self::getFunctionsAddress();
        $notImplemented = !isset($phpSpreadsheetFunctions[$function]) || (is_array($phpSpreadsheetFunctions[$function]['functionCall']) && $phpSpreadsheetFunctions[$function]['functionCall'][1] === 'DUMMY');

        return !$notImplemented;
    }

    /**
     * Get a list of implemented Excel function names.
     *
     * @return string[]
     */
    public function getImplementedFunctionNames(): array
    {
        $returnValue = [];
        $phpSpreadsheetFunctions = &self::getFunctionsAddress();
        foreach ($phpSpreadsheetFunctions as $functionName => $function) {
            if ($this->isImplemented($functionName)) {
                $returnValue[] = $functionName;
            }
        }

        return $returnValue;
    }

    /**
     * @param string[] $functionCall
     * @param mixed[] $args
     * @param mixed[] $emptyArguments
     *
     * @return mixed[]
     */
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
     *
     * @param mixed[] $args
     * @param string|string[] $functionCall
     *
     * @return mixed[]
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
        $definedNameValue = ReferenceHelper::getInstance()
            ->updateFormulaReferencesAnyWorksheet(
                $definedNameValue,
                Coordinate::columnIndexFromString(
                    $cell->getColumn()
                ) - 1,
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

    public function setSuppressFormulaErrors(bool $suppressFormulaErrors): self
    {
        $this->suppressFormulaErrors = $suppressFormulaErrors;

        return $this;
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
        return (is_string($operand) && Information\ErrorValue::isError($operand)) ? $operand : ExcelError::VALUE();
    }

    private static function swapOperands(Stack $stack, string $opCharacter): bool
    {
        $retVal = false;
        if ($stack->count() > 0) {
            $o2 = $stack->last();
            if ($o2) {
                if (isset(self::CALCULATION_OPERATORS[$o2['value']])) {
                    $retVal = (self::OPERATOR_PRECEDENCE[$opCharacter] ?? 0) <= self::OPERATOR_PRECEDENCE[$o2['value']];
                }
            }
        }

        return $retVal;
    }

    public function getSpreadsheet(): ?Spreadsheet
    {
        return $this->spreadsheet;
    }
}
