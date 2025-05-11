<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

class FunctionArray extends CalculationBase
{
    /**
     * Array of functions usable on Spreadsheet.
     *
     * @var array<string, array{category: string, functionCall: string|string[], argumentCount: string, passCellReference?: bool, passByReference?: bool[], custom?: bool}>
     */
    protected static array $phpSpreadsheetFunctions = [
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
        // pseudo-function to help with Ods
        'CEILING.ODS' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Ceiling::class, 'mathOds'],
            'argumentCount' => '1-3',
        ],
        'CEILING.PRECISE' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Ceiling::class, 'precise'],
            'argumentCount' => '1,2',
        ],
        // pseudo-function implemented in Ods
        'CEILING.XCL' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Ceiling::class, 'ceiling'],
            'argumentCount' => '2',
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
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef\ChooseRowsEtc::class, 'chooseCols'],
            'argumentCount' => '2+',
        ],
        'CHOOSEROWS' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef\ChooseRowsEtc::class, 'chooseRows'],
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
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef\ChooseRowsEtc::class, 'drop'],
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
            'functionCall' => [Information\ExcelError::class, 'type'],
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
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef\ChooseRowsEtc::class, 'expand'],
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
        // pseudo-function to help with Ods
        'FLOOR.ODS' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Floor::class, 'mathOds'],
            'argumentCount' => '1-3',
        ],
        'FLOOR.PRECISE' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Floor::class, 'precise'],
            'argumentCount' => '1-2',
        ],
        // pseudo-function implemented in Ods
        'FLOOR.XCL' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig\Floor::class, 'floor'],
            'argumentCount' => '2',
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
        'GROUPBY' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3-7',
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
            'functionCall' => [Information\ExcelError::class, 'NA'],
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
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef\ChooseRowsEtc::class, 'take'],
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
}
