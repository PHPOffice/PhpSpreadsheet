<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine;

use ArrayAccess;
use Iterator;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;

/**
 * @implements ArrayAccess<string, XlFunctionAbstract>
 * @implements Iterator<string, XlFunctionAbstract>
 */
class ExcelFunctions implements ArrayAccess, Iterator
{
    /**
     * @var int
     */
    private $index;

    /**
     * @var array<string, bool|XlFunctionAbstract>
     */
    private static $excelFunctions = [
        'ABS' => true,
        'ACCRINT' => true,
        'ACCRINTM' => true,
        'ACOS' => true,
        'ACOSH' => true,
        'ACOT' => true,
        'ACOTH' => true,
        'ADDRESS' => true,
        'AGGREGATE' => true,
        'AMORDEGRC' => true,
        'AMORLINC' => true,
        'AND' => true,
        'ARABIC' => true,
        'AREAS' => true,
        'ARRAYTOTEXT' => true,
        'ASC' => true,
        'ASIN' => true,
        'ASINH' => true,
        'ATAN' => true,
        'ATAN2' => true,
        'ATANH' => true,
        'AVEDEV' => true,
        'AVERAGE' => true,
        'AVERAGEA' => true,
        'AVERAGEIF' => true,
        'AVERAGEIFS' => true,
        'BAHTTEXT' => true,
        'BASE' => true,
        'BESSELI' => true,
        'BESSELJ' => true,
        'BESSELK' => true,
        'BESSELY' => true,
        'BETADIST' => true,
        'BETA.DIST' => true,
        'BETAINV' => true,
        'BETA.INV' => true,
        'BIN2DEC' => true,
        'BIN2HEX' => true,
        'BIN2OCT' => true,
        'BINOMDIST' => true,
        'BINOM.DIST' => true,
        'BINOM.DIST.RANGE' => true,
        'BINOM.INV' => true,
        'BITAND' => true,
        'BITOR' => true,
        'BITXOR' => true,
        'BITLSHIFT' => true,
        'BITRSHIFT' => true,
        'CEILING' => true,
        'CEILING.MATH' => true,
        'CEILING.PRECISE' => true,
        'CELL' => true,
        'CHAR' => true,
        'CHIDIST' => true,
        'CHISQ.DIST' => true,
        'CHISQ.DIST.RT' => true,
        'CHIINV' => true,
        'CHISQ.INV' => true,
        'CHISQ.INV.RT' => true,
        'CHITEST' => true,
        'CHISQ.TEST' => true,
        'CHOOSE' => true,
        'CLEAN' => true,
        'CODE' => true,
        'COLUMN' => true,
        'COLUMNS' => true,
        'COMBIN' => true,
        'COMBINA' => true,
        'COMPLEX' => true,
        'CONCAT' => true,
        'CONCATENATE' => true,
        'CONFIDENCE' => true,
        'CONFIDENCE.NORM' => true,
        'CONFIDENCE.T' => true,
        'CONVERT' => true,
        'CORREL' => true,
        'COS' => true,
        'COSH' => true,
        'COT' => true,
        'COTH' => true,
        'COUNT' => true,
        'COUNTA' => true,
        'COUNTBLANK' => true,
        'COUNTIF' => true,
        'COUNTIFS' => true,
        'COUPDAYBS' => true,
        'COUPDAYS' => true,
        'COUPDAYSNC' => true,
        'COUPNCD' => true,
        'COUPNUM' => true,
        'COUPPCD' => true,
        'COVAR' => true,
        'COVARIANCE.P' => true,
        'COVARIANCE.S' => true,
        'CRITBINOM' => true,
        'CSC' => true,
        'CSCH' => true,
        'CUBEKPIMEMBER' => true,
        'CUBEMEMBER' => true,
        'CUBEMEMBERPROPERTY' => true,
        'CUBERANKEDMEMBER' => true,
        'CUBESET' => true,
        'CUBESETCOUNT' => true,
        'CUBEVALUE' => true,
        'CUMIPMT' => true,
        'CUMPRINC' => true,
        'DATE' => true,
        'DATEDIF' => true,
        'DATESTRING' => true,
        'DATEVALUE' => true,
        'DAVERAGE' => true,
        'DAY' => true,
        'DAYS' => true,
        'DAYS360' => true,
        'DB' => true,
        'DBCS' => true,
        'DCOUNT' => true,
        'DCOUNTA' => true,
        'DDB' => true,
        'DEC2BIN' => true,
        'DEC2HEX' => true,
        'DEC2OCT' => true,
        'DECIMAL' => true,
        'DEGREES' => true,
        'DELTA' => true,
        'DEVSQ' => true,
        'DGET' => true,
        'DISC' => true,
        'DMAX' => true,
        'DMIN' => true,
        'DOLLAR' => true,
        'DOLLARDE' => true,
        'DOLLARFR' => true,
        'DPRODUCT' => true,
        'DSTDEV' => true,
        'DSTDEVP' => true,
        'DSUM' => true,
        'DURATION' => true,
        'DVAR' => true,
        'DVARP' => true,
        'ECMA.CEILING' => true,
        'EDATE' => true,
        'EFFECT' => true,
        'ENCODEURL' => true,
        'EOMONTH' => true,
        'ERF' => true,
        'ERF.PRECISE' => true,
        'ERFC' => true,
        'ERFC.PRECISE' => true,
        'ERROR.TYPE' => true,
        'EVEN' => true,
        'EXACT' => true,
        'EXP' => true,
        'EXPONDIST' => true,
        'EXPON.DIST' => true,
        'FACT' => true,
        'FACTDOUBLE' => true,
        'FALSE' => true,
        'FDIST' => true,
        'F.DIST' => true,
        'F.DIST.RT' => true,
        'FILTER' => true,
        'FILTERXML' => true,
        'FIND' => true,
        'FINDB' => true,
        'FINV' => true,
        'F.INV' => true,
        'F.INV.RT' => true,
        'FISHER' => true,
        'FISHERINV' => true,
        'FIXED' => true,
        'FLOOR' => true,
        'FLOOR.MATH' => true,
        'FLOOR.PRECISE' => true,
        'FORECAST' => true,
        'FORECAST.ETS' => true,
        'FORECAST.ETS.CONFINT' => true,
        'FORECAST.ETS.SEASONALITY' => true,
        'FORECAST.ETS.STAT' => true,
        'FORECAST.LINEAR' => true,
        'FORMULATEXT' => true,
        'FREQUENCY' => true,
        'FTEST' => true,
        'F.TEST' => true,
        'FV' => true,
        'FVSCHEDULE' => true,
        'GAMMA' => true,
        'GAMMADIST' => true,
        'GAMMA.DIST' => true,
        'GAMMAINV' => true,
        'GAMMA.INV' => true,
        'GAMMALN' => true,
        'GAMMALN.PRECISE' => true,
        'GAUSS' => true,
        'GCD' => true,
        'GEOMEAN' => true,
        'GESTEP' => true,
        'GETPIVOTDATA' => true,
        'GROWTH' => true,
        'HARMEAN' => true,
        'HEX2BIN' => true,
        'HEX2DEC' => true,
        'HEX2OCT' => true,
        'HLOOKUP' => true,
        'HOUR' => true,
        'HYPERLINK' => true,
        'HYPGEOMDIST' => true,
        'HYPGEOM.DIST' => true,
        'IF' => true,
        'IFERROR' => true,
        'IFNA' => true,
        'IFS' => true,
        'IMABS' => true,
        'IMAGINARY' => true,
        'IMARGUMENT' => true,
        'IMCONJUGATE' => true,
        'IMCOS' => true,
        'IMCOSH' => true,
        'IMCOT' => true,
        'IMCSC' => true,
        'IMCSCH' => true,
        'IMDIV' => true,
        'IMEXP' => true,
        'IMLN' => true,
        'IMLOG10' => true,
        'IMLOG2' => true,
        'IMPOWER' => true,
        'IMPRODUCT' => true,
        'IMREAL' => true,
        'IMSEC' => true,
        'IMSECH' => true,
        'IMSIN' => true,
        'IMSINH' => true,
        'IMSQRT' => true,
        'IMSUB' => true,
        'IMSUM' => true,
        'IMTAN' => true,
        'INDEX' => true,
        'INDIRECT' => true,
        'INFO' => true,
        'INT' => true,
        'INTERCEPT' => true,
        'INTRATE' => true,
        'IPMT' => true,
        'IRR' => true,
        'ISBLANK' => true,
        'ISERR' => true,
        'ISERROR' => true,
        'ISEVEN' => true,
        'ISFORMULA' => true,
        'ISLOGICAL' => true,
        'ISNA' => true,
        'ISNONTEXT' => true,
        'ISNUMBER' => true,
        'ISO.CEILING' => true,
        'ISODD' => true,
        'ISOWEEKNUM' => true,
        'ISPMT' => true,
        'ISREF' => true,
        'ISTEXT' => true,
        'ISTHAIDIGIT' => true,
        'JIS' => true,
        'KURT' => true,
        'LARGE' => true,
        'LCM' => true,
        'LEFT' => true,
        'LEFTB' => true,
        'LEN' => true,
        'LENB' => true,
        'LINEST' => true,
        'LN' => true,
        'LOG' => true,
        'LOG10' => true,
        'LOGEST' => true,
        'LOGINV' => true,
        'LOGNORMDIST' => true,
        'LOGNORM.DIST' => true,
        'LOGNORM.INV' => true,
        'LOOKUP' => true,
        'LOWER' => true,
        'MATCH' => true,
        'MAX' => true,
        'MAXA' => true,
        'MAXIFS' => true,
        'MDETERM' => true,
        'MDURATION' => true,
        'MEDIAN' => true,
        'MEDIANIF' => true,
        'MID' => true,
        'MIDB' => true,
        'MIN' => true,
        'MINA' => true,
        'MINIFS' => true,
        'MINUTE' => true,
        'MINVERSE' => true,
        'MIRR' => true,
        'MMULT' => true,
        'MOD' => true,
        'MODE' => true,
        'MODE.MULT' => true,
        'MODE.SNGL' => true,
        'MONTH' => true,
        'MROUND' => true,
        'MULTINOMIAL' => true,
        'MUNIT' => true,
        'N' => true,
        'NA' => true,
        'NEGBINOMDIST' => true,
        'NEGBINOM.DIST' => true,
        'NETWORKDAYS' => true,
        'NETWORKDAYS.INTL' => true,
        'NOMINAL' => true,
        'NORMDIST' => true,
        'NORM.DIST' => true,
        'NORMINV' => true,
        'NORM.INV' => true,
        'NORMSDIST' => true,
        'NORM.S.DIST' => true,
        'NORMSINV' => true,
        'NORM.S.INV' => true,
        'NOT' => true,
        'NOW' => true,
        'NPER' => true,
        'NPV' => true,
        'NUMBERSTRING' => true,
        'NUMBERVALUE' => true,
        'OCT2BIN' => true,
        'OCT2DEC' => true,
        'OCT2HEX' => true,
        'ODD' => true,
        'ODDFPRICE' => true,
        'ODDFYIELD' => true,
        'ODDLPRICE' => true,
        'ODDLYIELD' => true,
        'OFFSET' => true,
        'OR' => true,
        'PDURATION' => true,
        'PEARSON' => true,
        'PERCENTILE' => true,
        'PERCENTILE.EXC' => true,
        'PERCENTILE.INC' => true,
        'PERCENTRANK' => true,
        'PERCENTRANK.EXC' => true,
        'PERCENTRANK.INC' => true,
        'PERMUT' => true,
        'PERMUTATIONA' => true,
        'PHONETIC' => true,
        'PHI' => true,
        'PI' => true,
        'PMT' => true,
        'POISSON' => true,
        'POISSON.DIST' => true,
        'POWER' => true,
        'PPMT' => true,
        'PRICE' => true,
        'PRICEDISC' => true,
        'PRICEMAT' => true,
        'PROB' => true,
        'PRODUCT' => true,
        'PROPER' => true,
        'PV' => true,
        'QUARTILE' => true,
        'QUARTILE.EXC' => true,
        'QUARTILE.INC' => true,
        'QUOTIENT' => true,
        'RADIANS' => true,
        'RAND' => true,
        'RANDARRAY' => true,
        'RANDBETWEEN' => true,
        'RANK' => true,
        'RANK.AVG' => true,
        'RANK.EQ' => true,
        'RATE' => true,
        'RECEIVED' => true,
        'REPLACE' => true,
        'REPLACEB' => true,
        'REPT' => true,
        'RIGHT' => true,
        'RIGHTB' => true,
        'ROMAN' => true,
        'ROUND' => true,
        'ROUNDBAHTDOWN' => true,
        'ROUNDBAHTUP' => true,
        'ROUNDDOWN' => true,
        'ROUNDUP' => true,
        'ROW' => true,
        'ROWS' => true,
        'RRI' => true,
        'RSQ' => true,
        'RTD' => true,
        'SEARCH' => true,
        'SEARCHB' => true,
        'SEC' => true,
        'SECH' => true,
        'SECOND' => true,
        'SEQUENCE' => true,
        'SERIESSUM' => true,
        'SHEET' => true,
        'SHEETS' => true,
        'SIGN' => true,
        'SIN' => true,
        'SINH' => true,
        'SKEW' => true,
        'SKEW.P' => true,
        'SLN' => true,
        'SLOPE' => true,
        'SMALL' => true,
        'SORT' => true,
        'SORTBY' => true,
        'SQRT' => true,
        'SQRTPI' => true,
        'STANDARDIZE' => true,
        'STDEV' => true,
        'STDEV.S' => true,
        'STDEV.P' => true,
        'STDEVA' => true,
        'STDEVP' => true,
        'STDEVPA' => true,
        'STEYX' => true,
        'SUBSTITUTE' => true,
        'SUBTOTAL' => true,
        'SUM' => true,
        'SUMIF' => true,
        'SUMIFS' => true,
        'SUMPRODUCT' => true,
        'SUMSQ' => true,
        'SUMX2MY2' => true,
        'SUMX2PY2' => true,
        'SUMXMY2' => true,
        'SWITCH' => true,
        'SYD' => true,
        'T' => true,
        'TAN' => true,
        'TANH' => true,
        'TBILLEQ' => true,
        'TBILLPRICE' => true,
        'TBILLYIELD' => true,
        'TDIST' => true,
        'T.DIST' => true,
        'T.DIST.2T' => true,
        'T.DIST.RT' => true,
        'TEXT' => true,
        'TEXTJOIN' => true,
        'THAIDAYOFWEEK' => true,
        'THAIDIGIT' => true,
        'THAIMONTHOFYEAR' => true,
        'THAINUMSOUND' => true,
        'THAINUMSTRING' => true,
        'THAISTRINGLENGTH' => true,
        'THAIYEAR' => true,
        'TIME' => true,
        'TIMEVALUE' => true,
        'TINV' => true,
        'T.INV' => true,
        'T.INV.2T' => true,
        'TODAY' => true,
        'TRANSPOSE' => true,
        'TREND' => true,
        'TRIM' => true,
        'TRIMMEAN' => true,
        'TRUE' => true,
        'TRUNC' => true,
        'TTEST' => true,
        'T.TEST' => true,
        'TYPE' => true,
        'UNICHAR' => true,
        'UNICODE' => true,
        'UNIQUE' => true,
        'UPPER' => true,
        'USDOLLAR' => true,
        'VALUE' => true,
        'VALUETOTEXT' => true,
        'VAR' => true,
        'VAR.P' => true,
        'VAR.S' => true,
        'VARA' => true,
        'VARP' => true,
        'VARPA' => true,
        'VDB' => true,
        'VLOOKUP' => true,
        'WEBSERVICE' => true,
        'WEEKDAY' => true,
        'WEEKNUM' => true,
        'WEIBULL' => true,
        'WEIBULL.DIST' => true,
        'WORKDAY' => true,
        'WORKDAY.INTL' => true,
        'XIRR' => true,
        'XLOOKUP' => true,
        'XNPV' => true,
        'XMATCH' => true,
        'XOR' => true,
        'YEAR' => true,
        'YEARFRAC' => true,
        'YIELD' => true,
        'YIELDDISC' => true,
        'YIELDMAT' => true,
        'ZTEST' => true,
        'Z.TEST' => true,
    ];

    /**
     * @var bool
     */
    private $loading = false;

    public function isRecognisedExcelFunction(string $functionName): bool
    {
        return isset(self::$excelFunctions[$functionName]);
    }

    private function loadFunctionDefinition(string $className): XlFunctionAbstract
    {
        $this->loading = true;
        /** @var XlFunctionAbstract $definition */
        $definition = new $className();
        $excelFunctionName = $definition->name;
        self::$excelFunctions[$excelFunctionName] = $definition;

        if (isset($definition->synonyms)) {
            /** @var string $synonym */
            foreach ($definition->synonyms as $synonym) {
                self::$excelFunctions[$synonym] = $definition;
            }
        }
        $this->loading = false;

        return $definition;
    }

    private function buildClassName(string $functionName): string
    {
        $classPath = 'PhpOffice\\PhpSpreadsheet\\Calculation\\Engine\\Functions\\Xl';

        return $classPath . ucfirst(strtolower(str_replace('.', '_', $functionName)));
    }

    private function functionDefinitionFactory(string $functionName): ?XlFunctionAbstract
    {
        if (isset(self::$excelFunctions[$functionName]) && self::$excelFunctions[$functionName] === true) {
            $functionDefinition = $this->loadFunctionDefinition($this->buildClassName($functionName));

            return $functionDefinition;
        }

        return null;
    }

    /**
     * @param string $functionName
     */
    public function offsetExists($functionName): bool
    {
        $functionName = strtoupper($functionName);

        return isset(self::$excelFunctions[$functionName]);
    }

    /**
     * @param string $functionName
     */
    public function offsetGet($functionName): ?XlFunctionAbstract
    {
        $functionName = strtoupper($functionName);

        return (isset(self::$excelFunctions[$functionName]) && self::$excelFunctions[$functionName] instanceof XlFunctionAbstract)
            ? self::$excelFunctions[$functionName]
            : $this->functionDefinitionFactory($functionName);
    }

    /**
     * @param string $functionName
     * @param mixed $value
     */
    public function offsetSet($functionName, $value): void
    {
        throw new Exception('Action not permitted');
    }

    /**
     * @param string $functionName
     */
    public function offsetUnset($functionName): void
    {
        throw new Exception('Action not permitted');
    }

    public function __isset(string $functionName): bool
    {
        $functionName = strtoupper($functionName);

        return isset(self::$excelFunctions[$functionName]);
    }

    public function __get(string $functionName): ?XlFunctionAbstract
    {
        $functionName = strtoupper($functionName);

        return (isset(self::$excelFunctions[$functionName]) && self::$excelFunctions[$functionName] instanceof XlFunctionAbstract)
            ? self::$excelFunctions[$functionName]
            : $this->functionDefinitionFactory($functionName);
    }

    /**
     * @param mixed $value
     */
    public function __set(string $functionName, $value): void
    {
        $functionName = strtoupper($functionName);
        if ($this->loading === false) {
            throw new Exception('Action not permitted');
        }

        self::$excelFunctions[$functionName] = $value;
    }

    public function __unset(string $functionName): void
    {
        throw new Exception('Action not permitted');
    }

    public function current(): ?XlFunctionAbstract
    {
        $functionName = array_keys(self::$excelFunctions)[$this->index];
        if (isset(self::$excelFunctions[$functionName]) && self::$excelFunctions[$functionName] instanceof XlFunctionAbstract) {
            return self::$excelFunctions[$functionName];
        }

        return $this->functionDefinitionFactory($functionName);
    }

    public function next(): void
    {
        ++$this->index;
    }

    public function key(): string
    {
        return array_keys(self::$excelFunctions)[$this->index];
    }

    public function valid(): bool
    {
        return $this->index < count(self::$excelFunctions);
    }

    public function rewind(): void
    {
        $this->index = 0;
    }
}
