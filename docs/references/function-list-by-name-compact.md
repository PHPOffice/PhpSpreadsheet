# Function list by name compact

Category should be prefixed by `CATEGORY_` to match the values in \PhpOffice\PhpSpreadsheet\Calculation\Category

Function should be prefixed by `PhpOffice\PhpSpreadsheet\Calculation\`

A less compact list can be found [here](./function-list-by-name.md)


## A

Excel Function           | Category              | PhpSpreadsheet Function
-------------------------|-----------------------|--------------------------------------
ABS                      | MATH_AND_TRIG         | MathTrig\Absolute::evaluate
ACCRINT                  | FINANCIAL             | Financial\Securities\AccruedInterest::periodic
ACCRINTM                 | FINANCIAL             | Financial\Securities\AccruedInterest::atMaturity
ACOS                     | MATH_AND_TRIG         | MathTrig\Trig\Cosine::acos
ACOSH                    | MATH_AND_TRIG         | MathTrig\Trig\Cosine::acosh
ACOT                     | MATH_AND_TRIG         | MathTrig\Trig\Cotangent::acot
ACOTH                    | MATH_AND_TRIG         | MathTrig\Trig\Cotangent::acoth
ADDRESS                  | LOOKUP_AND_REFERENCE  | LookupRef\Address::cell
AGGREGATE                | MATH_AND_TRIG         | **Not yet Implemented**
AMORDEGRC                | FINANCIAL             | Financial\Amortization::AMORDEGRC
AMORLINC                 | FINANCIAL             | Financial\Amortization::AMORLINC
ANCHORARRAY              | MICROSOFT_INTERNAL    | Internal\ExcelArrayPseudoFunctions::anchorArray
AND                      | LOGICAL               | Logical\Operations::logicalAnd
ARABIC                   | MATH_AND_TRIG         | MathTrig\Arabic::evaluate
AREAS                    | LOOKUP_AND_REFERENCE  | **Not yet Implemented**
ARRAYTOTEXT              | TEXT_AND_DATA         | TextData\Text::fromArray
ASC                      | TEXT_AND_DATA         | **Not yet Implemented**
ASIN                     | MATH_AND_TRIG         | MathTrig\Trig\Sine::asin
ASINH                    | MATH_AND_TRIG         | MathTrig\Trig\Sine::asinh
ATAN                     | MATH_AND_TRIG         | MathTrig\Trig\Tangent::atan
ATAN2                    | MATH_AND_TRIG         | MathTrig\Trig\Tangent::atan2
ATANH                    | MATH_AND_TRIG         | MathTrig\Trig\Tangent::atanh
AVEDEV                   | STATISTICAL           | Statistical\Averages::averageDeviations
AVERAGE                  | STATISTICAL           | Statistical\Averages::average
AVERAGEA                 | STATISTICAL           | Statistical\Averages::averageA
AVERAGEIF                | STATISTICAL           | Statistical\Conditional::AVERAGEIF
AVERAGEIFS               | STATISTICAL           | Statistical\Conditional::AVERAGEIFS

## B

Excel Function           | Category              | PhpSpreadsheet Function
-------------------------|-----------------------|--------------------------------------
BAHTTEXT                 | TEXT_AND_DATA         | **Not yet Implemented**
BASE                     | MATH_AND_TRIG         | MathTrig\Base::evaluate
BESSELI                  | ENGINEERING           | Engineering\BesselI::BESSELI
BESSELJ                  | ENGINEERING           | Engineering\BesselJ::BESSELJ
BESSELK                  | ENGINEERING           | Engineering\BesselK::BESSELK
BESSELY                  | ENGINEERING           | Engineering\BesselY::BESSELY
BETA.DIST                | STATISTICAL           | **Not yet Implemented**
BETA.INV                 | STATISTICAL           | Statistical\Distributions\Beta::inverse
BETADIST                 | STATISTICAL           | Statistical\Distributions\Beta::distribution
BETAINV                  | STATISTICAL           | Statistical\Distributions\Beta::inverse
BIN2DEC                  | ENGINEERING           | Engineering\ConvertBinary::toDecimal
BIN2HEX                  | ENGINEERING           | Engineering\ConvertBinary::toHex
BIN2OCT                  | ENGINEERING           | Engineering\ConvertBinary::toOctal
BINOM.DIST               | STATISTICAL           | Statistical\Distributions\Binomial::distribution
BINOM.DIST.RANGE         | STATISTICAL           | Statistical\Distributions\Binomial::range
BINOM.INV                | STATISTICAL           | Statistical\Distributions\Binomial::inverse
BINOMDIST                | STATISTICAL           | Statistical\Distributions\Binomial::distribution
BITAND                   | ENGINEERING           | Engineering\BitWise::BITAND
BITLSHIFT                | ENGINEERING           | Engineering\BitWise::BITLSHIFT
BITOR                    | ENGINEERING           | Engineering\BitWise::BITOR
BITRSHIFT                | ENGINEERING           | Engineering\BitWise::BITRSHIFT
BITXOR                   | ENGINEERING           | Engineering\BitWise::BITXOR
BYCOL                    | LOGICAL               | **Not yet Implemented**
BYROW                    | LOGICAL               | **Not yet Implemented**

## C

Excel Function           | Category              | PhpSpreadsheet Function
-------------------------|-----------------------|--------------------------------------
CEILING                  | MATH_AND_TRIG         | MathTrig\Ceiling::ceiling
CEILING.MATH             | MATH_AND_TRIG         | MathTrig\Ceiling::math
CEILING.PRECISE          | MATH_AND_TRIG         | MathTrig\Ceiling::precise
CELL                     | INFORMATION           | **Not yet Implemented**
CHAR                     | TEXT_AND_DATA         | TextData\CharacterConvert::character
CHIDIST                  | STATISTICAL           | Statistical\Distributions\ChiSquared::distributionRightTail
CHIINV                   | STATISTICAL           | Statistical\Distributions\ChiSquared::inverseRightTail
CHISQ.DIST               | STATISTICAL           | Statistical\Distributions\ChiSquared::distributionLeftTail
CHISQ.DIST.RT            | STATISTICAL           | Statistical\Distributions\ChiSquared::distributionRightTail
CHISQ.INV                | STATISTICAL           | Statistical\Distributions\ChiSquared::inverseLeftTail
CHISQ.INV.RT             | STATISTICAL           | Statistical\Distributions\ChiSquared::inverseRightTail
CHISQ.TEST               | STATISTICAL           | Statistical\Distributions\ChiSquared::test
CHITEST                  | STATISTICAL           | Statistical\Distributions\ChiSquared::test
CHOOSE                   | LOOKUP_AND_REFERENCE  | LookupRef\Selection::CHOOSE
CHOOSECOLS               | LOOKUP_AND_REFERENCE  | LookupRef\ChooseRowsEtc::chooseCols
CHOOSEROWS               | LOOKUP_AND_REFERENCE  | LookupRef\ChooseRowsEtc::chooseRows
CLEAN                    | TEXT_AND_DATA         | TextData\Trim::nonPrintable
CODE                     | TEXT_AND_DATA         | TextData\CharacterConvert::code
COLUMN                   | LOOKUP_AND_REFERENCE  | LookupRef\RowColumnInformation::COLUMN
COLUMNS                  | LOOKUP_AND_REFERENCE  | LookupRef\RowColumnInformation::COLUMNS
COMBIN                   | MATH_AND_TRIG         | MathTrig\Combinations::withoutRepetition
COMBINA                  | MATH_AND_TRIG         | MathTrig\Combinations::withRepetition
COMPLEX                  | ENGINEERING           | Engineering\Complex::COMPLEX
CONCAT                   | TEXT_AND_DATA         | TextData\Concatenate::CONCATENATE
CONCATENATE              | TEXT_AND_DATA         | TextData\Concatenate::actualCONCATENATE
CONFIDENCE               | STATISTICAL           | Statistical\Confidence::CONFIDENCE
CONFIDENCE.NORM          | STATISTICAL           | Statistical\Confidence::CONFIDENCE
CONFIDENCE.T             | STATISTICAL           | **Not yet Implemented**
CONVERT                  | ENGINEERING           | Engineering\ConvertUOM::CONVERT
CORREL                   | STATISTICAL           | Statistical\Trends::CORREL
COS                      | MATH_AND_TRIG         | MathTrig\Trig\Cosine::cos
COSH                     | MATH_AND_TRIG         | MathTrig\Trig\Cosine::cosh
COT                      | MATH_AND_TRIG         | MathTrig\Trig\Cotangent::cot
COTH                     | MATH_AND_TRIG         | MathTrig\Trig\Cotangent::coth
COUNT                    | STATISTICAL           | Statistical\Counts::COUNT
COUNTA                   | STATISTICAL           | Statistical\Counts::COUNTA
COUNTBLANK               | STATISTICAL           | Statistical\Counts::COUNTBLANK
COUNTIF                  | STATISTICAL           | Statistical\Conditional::COUNTIF
COUNTIFS                 | STATISTICAL           | Statistical\Conditional::COUNTIFS
COUPDAYBS                | FINANCIAL             | Financial\Coupons::COUPDAYBS
COUPDAYS                 | FINANCIAL             | Financial\Coupons::COUPDAYS
COUPDAYSNC               | FINANCIAL             | Financial\Coupons::COUPDAYSNC
COUPNCD                  | FINANCIAL             | Financial\Coupons::COUPNCD
COUPNUM                  | FINANCIAL             | Financial\Coupons::COUPNUM
COUPPCD                  | FINANCIAL             | Financial\Coupons::COUPPCD
COVAR                    | STATISTICAL           | Statistical\Trends::COVAR
COVARIANCE.P             | STATISTICAL           | Statistical\Trends::COVAR
COVARIANCE.S             | STATISTICAL           | **Not yet Implemented**
CRITBINOM                | STATISTICAL           | Statistical\Distributions\Binomial::inverse
CSC                      | MATH_AND_TRIG         | MathTrig\Trig\Cosecant::csc
CSCH                     | MATH_AND_TRIG         | MathTrig\Trig\Cosecant::csch
CUBEKPIMEMBER            | CUBE                  | **Not yet Implemented**
CUBEMEMBER               | CUBE                  | **Not yet Implemented**
CUBEMEMBERPROPERTY       | CUBE                  | **Not yet Implemented**
CUBERANKEDMEMBER         | CUBE                  | **Not yet Implemented**
CUBESET                  | CUBE                  | **Not yet Implemented**
CUBESETCOUNT             | CUBE                  | **Not yet Implemented**
CUBEVALUE                | CUBE                  | **Not yet Implemented**
CUMIPMT                  | FINANCIAL             | Financial\CashFlow\Constant\Periodic\Cumulative::interest
CUMPRINC                 | FINANCIAL             | Financial\CashFlow\Constant\Periodic\Cumulative::principal

## D

Excel Function           | Category              | PhpSpreadsheet Function
-------------------------|-----------------------|--------------------------------------
DATE                     | DATE_AND_TIME         | DateTimeExcel\Date::fromYMD
DATEDIF                  | DATE_AND_TIME         | DateTimeExcel\Difference::interval
DATESTRING               | DATE_AND_TIME         | **Not yet Implemented**
DATEVALUE                | DATE_AND_TIME         | DateTimeExcel\DateValue::fromString
DAVERAGE                 | DATABASE              | Database\DAverage::evaluate
DAY                      | DATE_AND_TIME         | DateTimeExcel\DateParts::day
DAYS                     | DATE_AND_TIME         | DateTimeExcel\Days::between
DAYS360                  | DATE_AND_TIME         | DateTimeExcel\Days360::between
DB                       | FINANCIAL             | Financial\Depreciation::DB
DBCS                     | TEXT_AND_DATA         | **Not yet Implemented**
DCOUNT                   | DATABASE              | Database\DCount::evaluate
DCOUNTA                  | DATABASE              | Database\DCountA::evaluate
DDB                      | FINANCIAL             | Financial\Depreciation::DDB
DEC2BIN                  | ENGINEERING           | Engineering\ConvertDecimal::toBinary
DEC2HEX                  | ENGINEERING           | Engineering\ConvertDecimal::toHex
DEC2OCT                  | ENGINEERING           | Engineering\ConvertDecimal::toOctal
DECIMAL                  | MATH_AND_TRIG         | **Not yet Implemented**
DEGREES                  | MATH_AND_TRIG         | MathTrig\Angle::toDegrees
DELTA                    | ENGINEERING           | Engineering\Compare::DELTA
DEVSQ                    | STATISTICAL           | Statistical\Deviations::sumSquares
DGET                     | DATABASE              | Database\DGet::evaluate
DISC                     | FINANCIAL             | Financial\Securities\Rates::discount
DMAX                     | DATABASE              | Database\DMax::evaluate
DMIN                     | DATABASE              | Database\DMin::evaluate
DOLLAR                   | TEXT_AND_DATA         | TextData\Format::DOLLAR
DOLLARDE                 | FINANCIAL             | Financial\Dollar::decimal
DOLLARFR                 | FINANCIAL             | Financial\Dollar::fractional
DPRODUCT                 | DATABASE              | Database\DProduct::evaluate
DROP                     | LOOKUP_AND_REFERENCE  | LookupRef\ChooseRowsEtc::drop
DSTDEV                   | DATABASE              | Database\DStDev::evaluate
DSTDEVP                  | DATABASE              | Database\DStDevP::evaluate
DSUM                     | DATABASE              | Database\DSum::evaluate
DURATION                 | FINANCIAL             | **Not yet Implemented**
DVAR                     | DATABASE              | Database\DVar::evaluate
DVARP                    | DATABASE              | Database\DVarP::evaluate

## E

Excel Function           | Category              | PhpSpreadsheet Function
-------------------------|-----------------------|--------------------------------------
ECMA.CEILING             | MATH_AND_TRIG         | **Not yet Implemented**
EDATE                    | DATE_AND_TIME         | DateTimeExcel\Month::adjust
EFFECT                   | FINANCIAL             | Financial\InterestRate::effective
ENCODEURL                | WEB                   | Web\Service::urlEncode
EOMONTH                  | DATE_AND_TIME         | DateTimeExcel\Month::lastDay
ERF                      | ENGINEERING           | Engineering\Erf::ERF
ERF.PRECISE              | ENGINEERING           | Engineering\Erf::ERFPRECISE
ERFC                     | ENGINEERING           | Engineering\ErfC::ERFC
ERFC.PRECISE             | ENGINEERING           | Engineering\ErfC::ERFC
ERROR.TYPE               | INFORMATION           | Information\ExcelError::type
EVEN                     | MATH_AND_TRIG         | MathTrig\Round::even
EXACT                    | TEXT_AND_DATA         | TextData\Text::exact
EXP                      | MATH_AND_TRIG         | MathTrig\Exp::evaluate
EXPAND                   | LOOKUP_AND_REFERENCE  | LookupRef\ChooseRowsEtc::expand
EXPON.DIST               | STATISTICAL           | Statistical\Distributions\Exponential::distribution
EXPONDIST                | STATISTICAL           | Statistical\Distributions\Exponential::distribution

## F

Excel Function           | Category              | PhpSpreadsheet Function
-------------------------|-----------------------|--------------------------------------
F.DIST                   | STATISTICAL           | Statistical\Distributions\F::distribution
F.DIST.RT                | STATISTICAL           | **Not yet Implemented**
F.INV                    | STATISTICAL           | **Not yet Implemented**
F.INV.RT                 | STATISTICAL           | **Not yet Implemented**
F.TEST                   | STATISTICAL           | **Not yet Implemented**
FACT                     | MATH_AND_TRIG         | MathTrig\Factorial::fact
FACTDOUBLE               | MATH_AND_TRIG         | MathTrig\Factorial::factDouble
FALSE                    | LOGICAL               | Logical\Boolean::FALSE
FDIST                    | STATISTICAL           | **Not yet Implemented**
FILTER                   | LOOKUP_AND_REFERENCE  | LookupRef\Filter::filter
FILTERXML                | WEB                   | **Not yet Implemented**
FIND                     | TEXT_AND_DATA         | TextData\Search::sensitive
FINDB                    | TEXT_AND_DATA         | TextData\Search::sensitive
FINV                     | STATISTICAL           | **Not yet Implemented**
FISHER                   | STATISTICAL           | Statistical\Distributions\Fisher::distribution
FISHERINV                | STATISTICAL           | Statistical\Distributions\Fisher::inverse
FIXED                    | TEXT_AND_DATA         | TextData\Format::FIXEDFORMAT
FLOOR                    | MATH_AND_TRIG         | MathTrig\Floor::floor
FLOOR.MATH               | MATH_AND_TRIG         | MathTrig\Floor::math
FLOOR.PRECISE            | MATH_AND_TRIG         | MathTrig\Floor::precise
FORECAST                 | STATISTICAL           | Statistical\Trends::FORECAST
FORECAST.ETS             | STATISTICAL           | **Not yet Implemented**
FORECAST.ETS.CONFINT     | STATISTICAL           | **Not yet Implemented**
FORECAST.ETS.SEASONALITY | STATISTICAL           | **Not yet Implemented**
FORECAST.ETS.STAT        | STATISTICAL           | **Not yet Implemented**
FORECAST.LINEAR          | STATISTICAL           | Statistical\Trends::FORECAST
FORMULATEXT              | LOOKUP_AND_REFERENCE  | LookupRef\Formula::text
FREQUENCY                | STATISTICAL           | **Not yet Implemented**
FTEST                    | STATISTICAL           | **Not yet Implemented**
FV                       | FINANCIAL             | Financial\CashFlow\Constant\Periodic::futureValue
FVSCHEDULE               | FINANCIAL             | Financial\CashFlow\Single::futureValue

## G

Excel Function           | Category              | PhpSpreadsheet Function
-------------------------|-----------------------|--------------------------------------
GAMMA                    | STATISTICAL           | Statistical\Distributions\Gamma::gamma
GAMMA.DIST               | STATISTICAL           | Statistical\Distributions\Gamma::distribution
GAMMA.INV                | STATISTICAL           | Statistical\Distributions\Gamma::inverse
GAMMADIST                | STATISTICAL           | Statistical\Distributions\Gamma::distribution
GAMMAINV                 | STATISTICAL           | Statistical\Distributions\Gamma::inverse
GAMMALN                  | STATISTICAL           | Statistical\Distributions\Gamma::ln
GAMMALN.PRECISE          | STATISTICAL           | Statistical\Distributions\Gamma::ln
GAUSS                    | STATISTICAL           | Statistical\Distributions\StandardNormal::gauss
GCD                      | MATH_AND_TRIG         | MathTrig\Gcd::evaluate
GEOMEAN                  | STATISTICAL           | Statistical\Averages\Mean::geometric
GESTEP                   | ENGINEERING           | Engineering\Compare::GESTEP
GETPIVOTDATA             | LOOKUP_AND_REFERENCE  | **Not yet Implemented**
GROUPBY                  | LOOKUP_AND_REFERENCE  | **Not yet Implemented**
GROWTH                   | STATISTICAL           | Statistical\Trends::GROWTH

## H

Excel Function           | Category              | PhpSpreadsheet Function
-------------------------|-----------------------|--------------------------------------
HARMEAN                  | STATISTICAL           | Statistical\Averages\Mean::harmonic
HEX2BIN                  | ENGINEERING           | Engineering\ConvertHex::toBinary
HEX2DEC                  | ENGINEERING           | Engineering\ConvertHex::toDecimal
HEX2OCT                  | ENGINEERING           | Engineering\ConvertHex::toOctal
HLOOKUP                  | LOOKUP_AND_REFERENCE  | LookupRef\HLookup::lookup
HOUR                     | DATE_AND_TIME         | DateTimeExcel\TimeParts::hour
HSTACK                   | LOOKUP_AND_REFERENCE  | LookupRef\Hstack::hstack
HYPERLINK                | LOOKUP_AND_REFERENCE  | LookupRef\Hyperlink::set
HYPGEOM.DIST             | STATISTICAL           | **Not yet Implemented**
HYPGEOMDIST              | STATISTICAL           | Statistical\Distributions\HyperGeometric::distribution

## I

Excel Function           | Category              | PhpSpreadsheet Function
-------------------------|-----------------------|--------------------------------------
IF                       | LOGICAL               | Logical\Conditional::statementIf
IFERROR                  | LOGICAL               | Logical\Conditional::IFERROR
IFNA                     | LOGICAL               | Logical\Conditional::IFNA
IFS                      | LOGICAL               | Logical\Conditional::IFS
IMABS                    | ENGINEERING           | Engineering\ComplexFunctions::IMABS
IMAGINARY                | ENGINEERING           | Engineering\Complex::IMAGINARY
IMARGUMENT               | ENGINEERING           | Engineering\ComplexFunctions::IMARGUMENT
IMCONJUGATE              | ENGINEERING           | Engineering\ComplexFunctions::IMCONJUGATE
IMCOS                    | ENGINEERING           | Engineering\ComplexFunctions::IMCOS
IMCOSH                   | ENGINEERING           | Engineering\ComplexFunctions::IMCOSH
IMCOT                    | ENGINEERING           | Engineering\ComplexFunctions::IMCOT
IMCSC                    | ENGINEERING           | Engineering\ComplexFunctions::IMCSC
IMCSCH                   | ENGINEERING           | Engineering\ComplexFunctions::IMCSCH
IMDIV                    | ENGINEERING           | Engineering\ComplexOperations::IMDIV
IMEXP                    | ENGINEERING           | Engineering\ComplexFunctions::IMEXP
IMLN                     | ENGINEERING           | Engineering\ComplexFunctions::IMLN
IMLOG10                  | ENGINEERING           | Engineering\ComplexFunctions::IMLOG10
IMLOG2                   | ENGINEERING           | Engineering\ComplexFunctions::IMLOG2
IMPOWER                  | ENGINEERING           | Engineering\ComplexFunctions::IMPOWER
IMPRODUCT                | ENGINEERING           | Engineering\ComplexOperations::IMPRODUCT
IMREAL                   | ENGINEERING           | Engineering\Complex::IMREAL
IMSEC                    | ENGINEERING           | Engineering\ComplexFunctions::IMSEC
IMSECH                   | ENGINEERING           | Engineering\ComplexFunctions::IMSECH
IMSIN                    | ENGINEERING           | Engineering\ComplexFunctions::IMSIN
IMSINH                   | ENGINEERING           | Engineering\ComplexFunctions::IMSINH
IMSQRT                   | ENGINEERING           | Engineering\ComplexFunctions::IMSQRT
IMSUB                    | ENGINEERING           | Engineering\ComplexOperations::IMSUB
IMSUM                    | ENGINEERING           | Engineering\ComplexOperations::IMSUM
IMTAN                    | ENGINEERING           | Engineering\ComplexFunctions::IMTAN
INDEX                    | LOOKUP_AND_REFERENCE  | LookupRef\Matrix::index
INDIRECT                 | LOOKUP_AND_REFERENCE  | LookupRef\Indirect::INDIRECT
INFO                     | INFORMATION           | **Not yet Implemented**
INT                      | MATH_AND_TRIG         | MathTrig\IntClass::evaluate
INTERCEPT                | STATISTICAL           | Statistical\Trends::INTERCEPT
INTRATE                  | FINANCIAL             | Financial\Securities\Rates::interest
IPMT                     | FINANCIAL             | Financial\CashFlow\Constant\Periodic\Interest::payment
IRR                      | FINANCIAL             | Financial\CashFlow\Variable\Periodic::rate
ISBLANK                  | INFORMATION           | Information\Value::isBlank
ISERR                    | INFORMATION           | Information\ErrorValue::isErr
ISERROR                  | INFORMATION           | Information\ErrorValue::isError
ISEVEN                   | INFORMATION           | Information\Value::isEven
ISFORMULA                | INFORMATION           | Information\Value::isFormula
ISLOGICAL                | INFORMATION           | Information\Value::isLogical
ISNA                     | INFORMATION           | Information\ErrorValue::isNa
ISNONTEXT                | INFORMATION           | Information\Value::isNonText
ISNUMBER                 | INFORMATION           | Information\Value::isNumber
ISO.CEILING              | MATH_AND_TRIG         | **Not yet Implemented**
ISODD                    | INFORMATION           | Information\Value::isOdd
ISOMITTED                | INFORMATION           | **Not yet Implemented**
ISOWEEKNUM               | DATE_AND_TIME         | DateTimeExcel\Week::isoWeekNumber
ISPMT                    | FINANCIAL             | Financial\CashFlow\Constant\Periodic\Interest::schedulePayment
ISREF                    | INFORMATION           | Information\Value::isRef
ISTEXT                   | INFORMATION           | Information\Value::isText
ISTHAIDIGIT              | TEXT_AND_DATA         | **Not yet Implemented**

## J

Excel Function           | Category              | PhpSpreadsheet Function
-------------------------|-----------------------|--------------------------------------
JIS                      | TEXT_AND_DATA         | **Not yet Implemented**

## K

Excel Function           | Category              | PhpSpreadsheet Function
-------------------------|-----------------------|--------------------------------------
KURT                     | STATISTICAL           | Statistical\Deviations::kurtosis

## L

Excel Function           | Category              | PhpSpreadsheet Function
-------------------------|-----------------------|--------------------------------------
LAMBDA                   | LOGICAL               | **Not yet Implemented**
LARGE                    | STATISTICAL           | Statistical\Size::large
LCM                      | MATH_AND_TRIG         | MathTrig\Lcm::evaluate
LEFT                     | TEXT_AND_DATA         | TextData\Extract::left
LEFTB                    | TEXT_AND_DATA         | TextData\Extract::left
LEN                      | TEXT_AND_DATA         | TextData\Text::length
LENB                     | TEXT_AND_DATA         | TextData\Text::length
LET                      | LOGICAL               | **Not yet Implemented**
LINEST                   | STATISTICAL           | Statistical\Trends::LINEST
LN                       | MATH_AND_TRIG         | MathTrig\Logarithms::natural
LOG                      | MATH_AND_TRIG         | MathTrig\Logarithms::withBase
LOG10                    | MATH_AND_TRIG         | MathTrig\Logarithms::base10
LOGEST                   | STATISTICAL           | Statistical\Trends::LOGEST
LOGINV                   | STATISTICAL           | Statistical\Distributions\LogNormal::inverse
LOGNORM.DIST             | STATISTICAL           | Statistical\Distributions\LogNormal::distribution
LOGNORM.INV              | STATISTICAL           | Statistical\Distributions\LogNormal::inverse
LOGNORMDIST              | STATISTICAL           | Statistical\Distributions\LogNormal::cumulative
LOOKUP                   | LOOKUP_AND_REFERENCE  | LookupRef\Lookup::lookup
LOWER                    | TEXT_AND_DATA         | TextData\CaseConvert::lower

## M

Excel Function           | Category              | PhpSpreadsheet Function
-------------------------|-----------------------|--------------------------------------
MAKEARRAY                | LOGICAL               | **Not yet Implemented**
MAP                      | LOGICAL               | **Not yet Implemented**
MATCH                    | LOOKUP_AND_REFERENCE  | LookupRef\ExcelMatch::MATCH
MAX                      | STATISTICAL           | Statistical\Maximum::max
MAXA                     | STATISTICAL           | Statistical\Maximum::maxA
MAXIFS                   | STATISTICAL           | Statistical\Conditional::MAXIFS
MDETERM                  | MATH_AND_TRIG         | MathTrig\MatrixFunctions::determinant
MDURATION                | FINANCIAL             | **Not yet Implemented**
MEDIAN                   | STATISTICAL           | Statistical\Averages::median
MEDIANIF                 | STATISTICAL           | **Not yet Implemented**
MID                      | TEXT_AND_DATA         | TextData\Extract::mid
MIDB                     | TEXT_AND_DATA         | TextData\Extract::mid
MIN                      | STATISTICAL           | Statistical\Minimum::min
MINA                     | STATISTICAL           | Statistical\Minimum::minA
MINIFS                   | STATISTICAL           | Statistical\Conditional::MINIFS
MINUTE                   | DATE_AND_TIME         | DateTimeExcel\TimeParts::minute
MINVERSE                 | MATH_AND_TRIG         | MathTrig\MatrixFunctions::inverse
MIRR                     | FINANCIAL             | Financial\CashFlow\Variable\Periodic::modifiedRate
MMULT                    | MATH_AND_TRIG         | MathTrig\MatrixFunctions::multiply
MOD                      | MATH_AND_TRIG         | MathTrig\Operations::mod
MODE                     | STATISTICAL           | Statistical\Averages::mode
MODE.MULT                | STATISTICAL           | **Not yet Implemented**
MODE.SNGL                | STATISTICAL           | Statistical\Averages::mode
MONTH                    | DATE_AND_TIME         | DateTimeExcel\DateParts::month
MROUND                   | MATH_AND_TRIG         | MathTrig\Round::multiple
MULTINOMIAL              | MATH_AND_TRIG         | MathTrig\Factorial::multinomial
MUNIT                    | MATH_AND_TRIG         | MathTrig\MatrixFunctions::identity

## N

Excel Function           | Category              | PhpSpreadsheet Function
-------------------------|-----------------------|--------------------------------------
N                        | INFORMATION           | Information\Value::asNumber
NA                       | INFORMATION           | Information\ExcelError::NA
NEGBINOM.DIST            | STATISTICAL           | **Not yet Implemented**
NEGBINOMDIST             | STATISTICAL           | Statistical\Distributions\Binomial::negative
NETWORKDAYS              | DATE_AND_TIME         | DateTimeExcel\NetworkDays::count
NETWORKDAYS.INTL         | DATE_AND_TIME         | **Not yet Implemented**
NOMINAL                  | FINANCIAL             | Financial\InterestRate::nominal
NORM.DIST                | STATISTICAL           | Statistical\Distributions\Normal::distribution
NORM.INV                 | STATISTICAL           | Statistical\Distributions\Normal::inverse
NORM.S.DIST              | STATISTICAL           | Statistical\Distributions\StandardNormal::distribution
NORM.S.INV               | STATISTICAL           | Statistical\Distributions\StandardNormal::inverse
NORMDIST                 | STATISTICAL           | Statistical\Distributions\Normal::distribution
NORMINV                  | STATISTICAL           | Statistical\Distributions\Normal::inverse
NORMSDIST                | STATISTICAL           | Statistical\Distributions\StandardNormal::cumulative
NORMSINV                 | STATISTICAL           | Statistical\Distributions\StandardNormal::inverse
NOT                      | LOGICAL               | Logical\Operations::NOT
NOW                      | DATE_AND_TIME         | DateTimeExcel\Current::now
NPER                     | FINANCIAL             | Financial\CashFlow\Constant\Periodic::periods
NPV                      | FINANCIAL             | Financial\CashFlow\Variable\Periodic::presentValue
NUMBERSTRING             | TEXT_AND_DATA         | **Not yet Implemented**
NUMBERVALUE              | TEXT_AND_DATA         | TextData\Format::NUMBERVALUE

## O

Excel Function           | Category              | PhpSpreadsheet Function
-------------------------|-----------------------|--------------------------------------
OCT2BIN                  | ENGINEERING           | Engineering\ConvertOctal::toBinary
OCT2DEC                  | ENGINEERING           | Engineering\ConvertOctal::toDecimal
OCT2HEX                  | ENGINEERING           | Engineering\ConvertOctal::toHex
ODD                      | MATH_AND_TRIG         | MathTrig\Round::odd
ODDFPRICE                | FINANCIAL             | **Not yet Implemented**
ODDFYIELD                | FINANCIAL             | **Not yet Implemented**
ODDLPRICE                | FINANCIAL             | **Not yet Implemented**
ODDLYIELD                | FINANCIAL             | **Not yet Implemented**
OFFSET                   | LOOKUP_AND_REFERENCE  | LookupRef\Offset::OFFSET
OR                       | LOGICAL               | Logical\Operations::logicalOr

## P

Excel Function           | Category              | PhpSpreadsheet Function
-------------------------|-----------------------|--------------------------------------
PDURATION                | FINANCIAL             | Financial\CashFlow\Single::periods
PEARSON                  | STATISTICAL           | Statistical\Trends::CORREL
PERCENTILE               | STATISTICAL           | Statistical\Percentiles::PERCENTILE
PERCENTILE.EXC           | STATISTICAL           | **Not yet Implemented**
PERCENTILE.INC           | STATISTICAL           | Statistical\Percentiles::PERCENTILE
PERCENTRANK              | STATISTICAL           | Statistical\Percentiles::PERCENTRANK
PERCENTRANK.EXC          | STATISTICAL           | **Not yet Implemented**
PERCENTRANK.INC          | STATISTICAL           | Statistical\Percentiles::PERCENTRANK
PERMUT                   | STATISTICAL           | Statistical\Permutations::PERMUT
PERMUTATIONA             | STATISTICAL           | Statistical\Permutations::PERMUTATIONA
PHI                      | STATISTICAL           | **Not yet Implemented**
PHONETIC                 | TEXT_AND_DATA         | **Not yet Implemented**
PI                       | MATH_AND_TRIG         | pi
PMT                      | FINANCIAL             | Financial\CashFlow\Constant\Periodic\Payments::annuity
POISSON                  | STATISTICAL           | Statistical\Distributions\Poisson::distribution
POISSON.DIST             | STATISTICAL           | Statistical\Distributions\Poisson::distribution
POWER                    | MATH_AND_TRIG         | MathTrig\Operations::power
PPMT                     | FINANCIAL             | Financial\CashFlow\Constant\Periodic\Payments::interestPayment
PRICE                    | FINANCIAL             | Financial\Securities\Price::price
PRICEDISC                | FINANCIAL             | Financial\Securities\Price::priceDiscounted
PRICEMAT                 | FINANCIAL             | Financial\Securities\Price::priceAtMaturity
PROB                     | STATISTICAL           | **Not yet Implemented**
PRODUCT                  | MATH_AND_TRIG         | MathTrig\Operations::product
PROPER                   | TEXT_AND_DATA         | TextData\CaseConvert::proper
PV                       | FINANCIAL             | Financial\CashFlow\Constant\Periodic::presentValue

## Q

Excel Function           | Category              | PhpSpreadsheet Function
-------------------------|-----------------------|--------------------------------------
QUARTILE                 | STATISTICAL           | Statistical\Percentiles::QUARTILE
QUARTILE.EXC             | STATISTICAL           | **Not yet Implemented**
QUARTILE.INC             | STATISTICAL           | Statistical\Percentiles::QUARTILE
QUOTIENT                 | MATH_AND_TRIG         | MathTrig\Operations::quotient

## R

Excel Function           | Category              | PhpSpreadsheet Function
-------------------------|-----------------------|--------------------------------------
RADIANS                  | MATH_AND_TRIG         | MathTrig\Angle::toRadians
RAND                     | MATH_AND_TRIG         | MathTrig\Random::rand
RANDARRAY                | MATH_AND_TRIG         | MathTrig\Random::randArray
RANDBETWEEN              | MATH_AND_TRIG         | MathTrig\Random::randBetween
RANK                     | STATISTICAL           | Statistical\Percentiles::RANK
RANK.AVG                 | STATISTICAL           | **Not yet Implemented**
RANK.EQ                  | STATISTICAL           | Statistical\Percentiles::RANK
RATE                     | FINANCIAL             | Financial\CashFlow\Constant\Periodic\Interest::rate
RECEIVED                 | FINANCIAL             | Financial\Securities\Price::received
REDUCE                   | LOGICAL               | **Not yet Implemented**
REPLACE                  | TEXT_AND_DATA         | TextData\Replace::replace
REPLACEB                 | TEXT_AND_DATA         | TextData\Replace::replace
REPT                     | TEXT_AND_DATA         | TextData\Concatenate::builtinREPT
RIGHT                    | TEXT_AND_DATA         | TextData\Extract::right
RIGHTB                   | TEXT_AND_DATA         | TextData\Extract::right
ROMAN                    | MATH_AND_TRIG         | MathTrig\Roman::evaluate
ROUND                    | MATH_AND_TRIG         | MathTrig\Round::round
ROUNDBAHTDOWN            | MATH_AND_TRIG         | **Not yet Implemented**
ROUNDBAHTUP              | MATH_AND_TRIG         | **Not yet Implemented**
ROUNDDOWN                | MATH_AND_TRIG         | MathTrig\Round::down
ROUNDUP                  | MATH_AND_TRIG         | MathTrig\Round::up
ROW                      | LOOKUP_AND_REFERENCE  | LookupRef\RowColumnInformation::ROW
ROWS                     | LOOKUP_AND_REFERENCE  | LookupRef\RowColumnInformation::ROWS
RRI                      | FINANCIAL             | Financial\CashFlow\Single::interestRate
RSQ                      | STATISTICAL           | Statistical\Trends::RSQ
RTD                      | LOOKUP_AND_REFERENCE  | **Not yet Implemented**

## S

Excel Function           | Category              | PhpSpreadsheet Function
-------------------------|-----------------------|--------------------------------------
SCAN                     | LOGICAL               | **Not yet Implemented**
SEARCH                   | TEXT_AND_DATA         | TextData\Search::insensitive
SEARCHB                  | TEXT_AND_DATA         | TextData\Search::insensitive
SEC                      | MATH_AND_TRIG         | MathTrig\Trig\Secant::sec
SECH                     | MATH_AND_TRIG         | MathTrig\Trig\Secant::sech
SECOND                   | DATE_AND_TIME         | DateTimeExcel\TimeParts::second
SEQUENCE                 | MATH_AND_TRIG         | MathTrig\MatrixFunctions::sequence
SERIESSUM                | MATH_AND_TRIG         | MathTrig\SeriesSum::evaluate
SHEET                    | INFORMATION           | **Not yet Implemented**
SHEETS                   | INFORMATION           | **Not yet Implemented**
SIGN                     | MATH_AND_TRIG         | MathTrig\Sign::evaluate
SIN                      | MATH_AND_TRIG         | MathTrig\Trig\Sine::sin
SINGLE                   | MICROSOFT_INTERNAL    | Internal\ExcelArrayPseudoFunctions::single
SINH                     | MATH_AND_TRIG         | MathTrig\Trig\Sine::sinh
SKEW                     | STATISTICAL           | Statistical\Deviations::skew
SKEW.P                   | STATISTICAL           | **Not yet Implemented**
SLN                      | FINANCIAL             | Financial\Depreciation::SLN
SLOPE                    | STATISTICAL           | Statistical\Trends::SLOPE
SMALL                    | STATISTICAL           | Statistical\Size::small
SORT                     | LOOKUP_AND_REFERENCE  | LookupRef\Sort::sort
SORTBY                   | LOOKUP_AND_REFERENCE  | LookupRef\Sort::sortBy
SQRT                     | MATH_AND_TRIG         | MathTrig\Sqrt::sqrt
SQRTPI                   | MATH_AND_TRIG         | MathTrig\Sqrt::pi
STANDARDIZE              | STATISTICAL           | Statistical\Standardize::execute
STDEV                    | STATISTICAL           | Statistical\StandardDeviations::STDEV
STDEV.P                  | STATISTICAL           | Statistical\StandardDeviations::STDEVP
STDEV.S                  | STATISTICAL           | Statistical\StandardDeviations::STDEV
STDEVA                   | STATISTICAL           | Statistical\StandardDeviations::STDEVA
STDEVP                   | STATISTICAL           | Statistical\StandardDeviations::STDEVP
STDEVPA                  | STATISTICAL           | Statistical\StandardDeviations::STDEVPA
STEYX                    | STATISTICAL           | Statistical\Trends::STEYX
SUBSTITUTE               | TEXT_AND_DATA         | TextData\Replace::substitute
SUBTOTAL                 | MATH_AND_TRIG         | MathTrig\Subtotal::evaluate
SUM                      | MATH_AND_TRIG         | MathTrig\Sum::sumErroringStrings
SUMIF                    | MATH_AND_TRIG         | Statistical\Conditional::SUMIF
SUMIFS                   | MATH_AND_TRIG         | Statistical\Conditional::SUMIFS
SUMPRODUCT               | MATH_AND_TRIG         | MathTrig\Sum::product
SUMSQ                    | MATH_AND_TRIG         | MathTrig\SumSquares::sumSquare
SUMX2MY2                 | MATH_AND_TRIG         | MathTrig\SumSquares::sumXSquaredMinusYSquared
SUMX2PY2                 | MATH_AND_TRIG         | MathTrig\SumSquares::sumXSquaredPlusYSquared
SUMXMY2                  | MATH_AND_TRIG         | MathTrig\SumSquares::sumXMinusYSquared
SWITCH                   | LOGICAL               | Logical\Conditional::statementSwitch
SYD                      | FINANCIAL             | Financial\Depreciation::SYD

## T

Excel Function           | Category              | PhpSpreadsheet Function
-------------------------|-----------------------|--------------------------------------
T                        | TEXT_AND_DATA         | TextData\Text::test
T.DIST                   | STATISTICAL           | **Not yet Implemented**
T.DIST.2T                | STATISTICAL           | **Not yet Implemented**
T.DIST.RT                | STATISTICAL           | **Not yet Implemented**
T.INV                    | STATISTICAL           | Statistical\Distributions\StudentT::inverse
T.INV.2T                 | STATISTICAL           | **Not yet Implemented**
T.TEST                   | STATISTICAL           | **Not yet Implemented**
TAKE                     | LOOKUP_AND_REFERENCE  | LookupRef\ChooseRowsEtc::take
TAN                      | MATH_AND_TRIG         | MathTrig\Trig\Tangent::tan
TANH                     | MATH_AND_TRIG         | MathTrig\Trig\Tangent::tanh
TBILLEQ                  | FINANCIAL             | Financial\TreasuryBill::bondEquivalentYield
TBILLPRICE               | FINANCIAL             | Financial\TreasuryBill::price
TBILLYIELD               | FINANCIAL             | Financial\TreasuryBill::yield
TDIST                    | STATISTICAL           | Statistical\Distributions\StudentT::distribution
TEXT                     | TEXT_AND_DATA         | TextData\Format::TEXTFORMAT
TEXTAFTER                | TEXT_AND_DATA         | TextData\Extract::after
TEXTBEFORE               | TEXT_AND_DATA         | TextData\Extract::before
TEXTJOIN                 | TEXT_AND_DATA         | TextData\Concatenate::TEXTJOIN
TEXTSPLIT                | TEXT_AND_DATA         | TextData\Text::split
THAIDAYOFWEEK            | DATE_AND_TIME         | **Not yet Implemented**
THAIDIGIT                | TEXT_AND_DATA         | **Not yet Implemented**
THAIMONTHOFYEAR          | DATE_AND_TIME         | **Not yet Implemented**
THAINUMSOUND             | TEXT_AND_DATA         | **Not yet Implemented**
THAINUMSTRING            | TEXT_AND_DATA         | **Not yet Implemented**
THAISTRINGLENGTH         | TEXT_AND_DATA         | **Not yet Implemented**
THAIYEAR                 | DATE_AND_TIME         | **Not yet Implemented**
TIME                     | DATE_AND_TIME         | DateTimeExcel\Time::fromHMS
TIMEVALUE                | DATE_AND_TIME         | DateTimeExcel\TimeValue::fromString
TINV                     | STATISTICAL           | Statistical\Distributions\StudentT::inverse
TOCOL                    | LOOKUP_AND_REFERENCE  | LookupRef\TorowTocol::tocol
TODAY                    | DATE_AND_TIME         | DateTimeExcel\Current::today
TOROW                    | LOOKUP_AND_REFERENCE  | LookupRef\TorowTocol::torow
TRANSPOSE                | LOOKUP_AND_REFERENCE  | LookupRef\Matrix::transpose
TREND                    | STATISTICAL           | Statistical\Trends::TREND
TRIM                     | TEXT_AND_DATA         | TextData\Trim::spaces
TRIMMEAN                 | STATISTICAL           | Statistical\Averages\Mean::trim
TRUE                     | LOGICAL               | Logical\Boolean::TRUE
TRUNC                    | MATH_AND_TRIG         | MathTrig\Trunc::evaluate
TTEST                    | STATISTICAL           | **Not yet Implemented**
TYPE                     | INFORMATION           | Information\Value::type

## U

Excel Function           | Category              | PhpSpreadsheet Function
-------------------------|-----------------------|--------------------------------------
UNICHAR                  | TEXT_AND_DATA         | TextData\CharacterConvert::character
UNICODE                  | TEXT_AND_DATA         | TextData\CharacterConvert::code
UNIQUE                   | LOOKUP_AND_REFERENCE  | LookupRef\Unique::unique
UPPER                    | TEXT_AND_DATA         | TextData\CaseConvert::upper
USDOLLAR                 | FINANCIAL             | Financial\Dollar::format

## V

Excel Function           | Category              | PhpSpreadsheet Function
-------------------------|-----------------------|--------------------------------------
VALUE                    | TEXT_AND_DATA         | TextData\Format::VALUE
VALUETOTEXT              | TEXT_AND_DATA         | TextData\Format::valueToText
VAR                      | STATISTICAL           | Statistical\Variances::VAR
VAR.P                    | STATISTICAL           | Statistical\Variances::VARP
VAR.S                    | STATISTICAL           | Statistical\Variances::VAR
VARA                     | STATISTICAL           | Statistical\Variances::VARA
VARP                     | STATISTICAL           | Statistical\Variances::VARP
VARPA                    | STATISTICAL           | Statistical\Variances::VARPA
VDB                      | FINANCIAL             | **Not yet Implemented**
VLOOKUP                  | LOOKUP_AND_REFERENCE  | LookupRef\VLookup::lookup
VSTACK                   | LOOKUP_AND_REFERENCE  | LookupRef\Vstack::vstack

## W

Excel Function           | Category              | PhpSpreadsheet Function
-------------------------|-----------------------|--------------------------------------
WEBSERVICE               | WEB                   | Web\Service::webService
WEEKDAY                  | DATE_AND_TIME         | DateTimeExcel\Week::day
WEEKNUM                  | DATE_AND_TIME         | DateTimeExcel\Week::number
WEIBULL                  | STATISTICAL           | Statistical\Distributions\Weibull::distribution
WEIBULL.DIST             | STATISTICAL           | Statistical\Distributions\Weibull::distribution
WORKDAY                  | DATE_AND_TIME         | DateTimeExcel\WorkDay::date
WORKDAY.INTL             | DATE_AND_TIME         | **Not yet Implemented**
WRAPCOLS                 | MATH_AND_TRIG         | **Not yet Implemented**
WRAPROWS                 | MATH_AND_TRIG         | **Not yet Implemented**

## X

Excel Function           | Category              | PhpSpreadsheet Function
-------------------------|-----------------------|--------------------------------------
XIRR                     | FINANCIAL             | Financial\CashFlow\Variable\NonPeriodic::rate
XLOOKUP                  | LOOKUP_AND_REFERENCE  | **Not yet Implemented**
XMATCH                   | LOOKUP_AND_REFERENCE  | **Not yet Implemented**
XNPV                     | FINANCIAL             | Financial\CashFlow\Variable\NonPeriodic::presentValue
XOR                      | LOGICAL               | Logical\Operations::logicalXor

## Y

Excel Function           | Category              | PhpSpreadsheet Function
-------------------------|-----------------------|--------------------------------------
YEAR                     | DATE_AND_TIME         | DateTimeExcel\DateParts::year
YEARFRAC                 | DATE_AND_TIME         | DateTimeExcel\YearFrac::fraction
YIELD                    | FINANCIAL             | **Not yet Implemented**
YIELDDISC                | FINANCIAL             | Financial\Securities\Yields::yieldDiscounted
YIELDMAT                 | FINANCIAL             | Financial\Securities\Yields::yieldAtMaturity

## Z

Excel Function           | Category              | PhpSpreadsheet Function
-------------------------|-----------------------|--------------------------------------
Z.TEST                   | STATISTICAL           | Statistical\Distributions\StandardNormal::zTest
ZTEST                    | STATISTICAL           | Statistical\Distributions\StandardNormal::zTest
