# Function list by category

## CATEGORY_CUBE

Excel Function           | PhpSpreadsheet Function
-------------------------|--------------------------------------
CUBEKPIMEMBER            | **Not yet Implemented**
CUBEMEMBER               | **Not yet Implemented**
CUBEMEMBERPROPERTY       | **Not yet Implemented**
CUBERANKEDMEMBER         | **Not yet Implemented**
CUBESET                  | **Not yet Implemented**
CUBESETCOUNT             | **Not yet Implemented**
CUBEVALUE                | **Not yet Implemented**

## CATEGORY_DATABASE

Excel Function           | PhpSpreadsheet Function
-------------------------|--------------------------------------
DAVERAGE                 | \PhpOffice\PhpSpreadsheet\Calculation\Database\DAverage::evaluate
DCOUNT                   | \PhpOffice\PhpSpreadsheet\Calculation\Database\DCount::evaluate
DCOUNTA                  | \PhpOffice\PhpSpreadsheet\Calculation\Database\DCountA::evaluate
DGET                     | \PhpOffice\PhpSpreadsheet\Calculation\Database\DGet::evaluate
DMAX                     | \PhpOffice\PhpSpreadsheet\Calculation\Database\DMax::evaluate
DMIN                     | \PhpOffice\PhpSpreadsheet\Calculation\Database\DMin::evaluate
DPRODUCT                 | \PhpOffice\PhpSpreadsheet\Calculation\Database\DProduct::evaluate
DSTDEV                   | \PhpOffice\PhpSpreadsheet\Calculation\Database\DStDev::evaluate
DSTDEVP                  | \PhpOffice\PhpSpreadsheet\Calculation\Database\DStDevP::evaluate
DSUM                     | \PhpOffice\PhpSpreadsheet\Calculation\Database\DSum::evaluate
DVAR                     | \PhpOffice\PhpSpreadsheet\Calculation\Database\DVar::evaluate
DVARP                    | \PhpOffice\PhpSpreadsheet\Calculation\Database\DVarP::evaluate

## CATEGORY_DATE_AND_TIME

Excel Function           | PhpSpreadsheet Function
-------------------------|--------------------------------------
DATE                     | \PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Date::fromYMD
DATEDIF                  | \PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Difference::interval
DATEVALUE                | \PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\DateValue::fromString
DAY                      | \PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\DateParts::day
DAYS                     | \PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Days::between
DAYS360                  | \PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Days360::between
EDATE                    | \PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Month::adjust
EOMONTH                  | \PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Month::lastDay
HOUR                     | \PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\TimeParts::hour
ISOWEEKNUM               | \PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Week::isoWeekNumber
MINUTE                   | \PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\TimeParts::minute
MONTH                    | \PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\DateParts::month
NETWORKDAYS              | \PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\NetworkDays::count
NETWORKDAYS.INTL         | **Not yet Implemented**
NOW                      | \PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Current::now
SECOND                   | \PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\TimeParts::second
TIME                     | \PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Time::fromHMS
TIMEVALUE                | \PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\TimeValue::fromString
TODAY                    | \PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Current::today
WEEKDAY                  | \PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Week::day
WEEKNUM                  | \PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Week::number
WORKDAY                  | \PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\WorkDay::date
WORKDAY.INTL             | **Not yet Implemented**
YEAR                     | \PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\DateParts::year
YEARFRAC                 | \PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\YearFrac::fraction

## CATEGORY_ENGINEERING

Excel Function           | PhpSpreadsheet Function
-------------------------|--------------------------------------
BESSELI                  | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\BesselI::BESSELI
BESSELJ                  | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\BesselJ::BESSELJ
BESSELK                  | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\BesselK::BESSELK
BESSELY                  | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\BesselY::BESSELY
BIN2DEC                  | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertBinary::toDecimal
BIN2HEX                  | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertBinary::toHex
BIN2OCT                  | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertBinary::toOctal
BITAND                   | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\BitWise::BITAND
BITLSHIFT                | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\BitWise::BITLSHIFT
BITOR                    | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\BitWise::BITOR
BITRSHIFT                | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\BitWise::BITRSHIFT
BITXOR                   | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\BitWise::BITXOR
COMPLEX                  | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\Complex::COMPLEX
CONVERT                  | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertUOM::CONVERT
DEC2BIN                  | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertDecimal::toBinary
DEC2HEX                  | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertDecimal::toHex
DEC2OCT                  | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertDecimal::toOctal
DELTA                    | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\Compare::DELTA
ERF                      | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\Erf::ERF
ERF.PRECISE              | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\Erf::ERFPRECISE
ERFC                     | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ErfC::ERFC
ERFC.PRECISE             | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ErfC::ERFC
GESTEP                   | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\Compare::GESTEP
HEX2BIN                  | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertHex::toBinary
HEX2DEC                  | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertHex::toDecimal
HEX2OCT                  | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertHex::toOctal
IMABS                    | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMABS
IMAGINARY                | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\Complex::IMAGINARY
IMARGUMENT               | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMARGUMENT
IMCONJUGATE              | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMCONJUGATE
IMCOS                    | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMCOS
IMCOSH                   | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMCOSH
IMCOT                    | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMCOT
IMCSC                    | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMCSC
IMCSCH                   | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMCSCH
IMDIV                    | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexOperations::IMDIV
IMEXP                    | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMEXP
IMLN                     | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMLN
IMLOG10                  | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMLOG10
IMLOG2                   | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMLOG2
IMPOWER                  | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMPOWER
IMPRODUCT                | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexOperations::IMPRODUCT
IMREAL                   | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\Complex::IMREAL
IMSEC                    | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMSEC
IMSECH                   | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMSECH
IMSIN                    | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMSIN
IMSINH                   | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMSINH
IMSQRT                   | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMSQRT
IMSUB                    | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexOperations::IMSUB
IMSUM                    | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexOperations::IMSUM
IMTAN                    | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMTAN
OCT2BIN                  | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertOctal::toBinary
OCT2DEC                  | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertOctal::toDecimal
OCT2HEX                  | \PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertOctal::toHex

## CATEGORY_FINANCIAL

Excel Function           | PhpSpreadsheet Function
-------------------------|--------------------------------------
ACCRINT                  | \PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\AccruedInterest::periodic
ACCRINTM                 | \PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\AccruedInterest::atMaturity
AMORDEGRC                | \PhpOffice\PhpSpreadsheet\Calculation\Financial\Amortization::AMORDEGRC
AMORLINC                 | \PhpOffice\PhpSpreadsheet\Calculation\Financial\Amortization::AMORLINC
COUPDAYBS                | \PhpOffice\PhpSpreadsheet\Calculation\Financial\Coupons::COUPDAYBS
COUPDAYS                 | \PhpOffice\PhpSpreadsheet\Calculation\Financial\Coupons::COUPDAYS
COUPDAYSNC               | \PhpOffice\PhpSpreadsheet\Calculation\Financial\Coupons::COUPDAYSNC
COUPNCD                  | \PhpOffice\PhpSpreadsheet\Calculation\Financial\Coupons::COUPNCD
COUPNUM                  | \PhpOffice\PhpSpreadsheet\Calculation\Financial\Coupons::COUPNUM
COUPPCD                  | \PhpOffice\PhpSpreadsheet\Calculation\Financial\Coupons::COUPPCD
CUMIPMT                  | \PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic\Cumulative::interest
CUMPRINC                 | \PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic\Cumulative::principal
DB                       | \PhpOffice\PhpSpreadsheet\Calculation\Financial\Depreciation::DB
DDB                      | \PhpOffice\PhpSpreadsheet\Calculation\Financial\Depreciation::DDB
DISC                     | \PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\Rates::discount
DOLLARDE                 | \PhpOffice\PhpSpreadsheet\Calculation\Financial\Dollar::decimal
DOLLARFR                 | \PhpOffice\PhpSpreadsheet\Calculation\Financial\Dollar::fractional
DURATION                 | **Not yet Implemented**
EFFECT                   | \PhpOffice\PhpSpreadsheet\Calculation\Financial\InterestRate::effective
FV                       | \PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic::futureValue
FVSCHEDULE               | \PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Single::futureValue
INTRATE                  | \PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\Rates::interest
IPMT                     | \PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic\Interest::payment
IRR                      | \PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Variable\Periodic::rate
ISPMT                    | \PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic\Interest::schedulePayment
MDURATION                | **Not yet Implemented**
MIRR                     | \PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Variable\Periodic::modifiedRate
NOMINAL                  | \PhpOffice\PhpSpreadsheet\Calculation\Financial\InterestRate::nominal
NPER                     | \PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic::periods
NPV                      | \PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Variable\Periodic::presentValue
ODDFPRICE                | **Not yet Implemented**
ODDFYIELD                | **Not yet Implemented**
ODDLPRICE                | **Not yet Implemented**
ODDLYIELD                | **Not yet Implemented**
PDURATION                | \PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Single::periods
PMT                      | \PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic\Payments::annuity
PPMT                     | \PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic\Payments::interestPayment
PRICE                    | \PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\Price::price
PRICEDISC                | \PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\Price::priceDiscounted
PRICEMAT                 | \PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\Price::priceAtMaturity
PV                       | \PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic::presentValue
RATE                     | \PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic\Interest::rate
RECEIVED                 | \PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\Price::received
RRI                      | \PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Single::interestRate
SLN                      | \PhpOffice\PhpSpreadsheet\Calculation\Financial\Depreciation::SLN
SYD                      | \PhpOffice\PhpSpreadsheet\Calculation\Financial\Depreciation::SYD
TBILLEQ                  | \PhpOffice\PhpSpreadsheet\Calculation\Financial\TreasuryBill::bondEquivalentYield
TBILLPRICE               | \PhpOffice\PhpSpreadsheet\Calculation\Financial\TreasuryBill::price
TBILLYIELD               | \PhpOffice\PhpSpreadsheet\Calculation\Financial\TreasuryBill::yield
USDOLLAR                 | \PhpOffice\PhpSpreadsheet\Calculation\Financial\Dollar::format
VDB                      | **Not yet Implemented**
XIRR                     | \PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Variable\NonPeriodic::rate
XNPV                     | \PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Variable\NonPeriodic::presentValue
YIELD                    | **Not yet Implemented**
YIELDDISC                | \PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\Yields::yieldDiscounted
YIELDMAT                 | \PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\Yields::yieldAtMaturity

## CATEGORY_INFORMATION

Excel Function           | PhpSpreadsheet Function
-------------------------|--------------------------------------
CELL                     | **Not yet Implemented**
ERROR.TYPE               | \PhpOffice\PhpSpreadsheet\Calculation\Functions::errorType
INFO                     | **Not yet Implemented**
ISBLANK                  | \PhpOffice\PhpSpreadsheet\Calculation\Functions::isBlank
ISERR                    | \PhpOffice\PhpSpreadsheet\Calculation\Functions::isErr
ISERROR                  | \PhpOffice\PhpSpreadsheet\Calculation\Functions::isError
ISEVEN                   | \PhpOffice\PhpSpreadsheet\Calculation\Functions::isEven
ISFORMULA                | \PhpOffice\PhpSpreadsheet\Calculation\Functions::isFormula
ISLOGICAL                | \PhpOffice\PhpSpreadsheet\Calculation\Functions::isLogical
ISNA                     | \PhpOffice\PhpSpreadsheet\Calculation\Functions::isNa
ISNONTEXT                | \PhpOffice\PhpSpreadsheet\Calculation\Functions::isNonText
ISNUMBER                 | \PhpOffice\PhpSpreadsheet\Calculation\Functions::isNumber
ISODD                    | \PhpOffice\PhpSpreadsheet\Calculation\Functions::isOdd
ISREF                    | **Not yet Implemented**
ISTEXT                   | \PhpOffice\PhpSpreadsheet\Calculation\Functions::isText
N                        | \PhpOffice\PhpSpreadsheet\Calculation\Functions::n
NA                       | \PhpOffice\PhpSpreadsheet\Calculation\Functions::NA
SHEET                    | **Not yet Implemented**
SHEETS                   | **Not yet Implemented**
TYPE                     | \PhpOffice\PhpSpreadsheet\Calculation\Functions::TYPE

## CATEGORY_LOGICAL

Excel Function           | PhpSpreadsheet Function
-------------------------|--------------------------------------
AND                      | \PhpOffice\PhpSpreadsheet\Calculation\Logical\Operations::logicalAnd
FALSE                    | \PhpOffice\PhpSpreadsheet\Calculation\Logical\Boolean::FALSE
IF                       | \PhpOffice\PhpSpreadsheet\Calculation\Logical\Conditional::statementIf
IFERROR                  | \PhpOffice\PhpSpreadsheet\Calculation\Logical\Conditional::IFERROR
IFNA                     | \PhpOffice\PhpSpreadsheet\Calculation\Logical\Conditional::IFNA
IFS                      | \PhpOffice\PhpSpreadsheet\Calculation\Logical\Conditional::IFS
NOT                      | \PhpOffice\PhpSpreadsheet\Calculation\Logical\Operations::NOT
OR                       | \PhpOffice\PhpSpreadsheet\Calculation\Logical\Operations::logicalOr
SWITCH                   | \PhpOffice\PhpSpreadsheet\Calculation\Logical\Conditional::statementSwitch
TRUE                     | \PhpOffice\PhpSpreadsheet\Calculation\Logical\Boolean::TRUE
XOR                      | \PhpOffice\PhpSpreadsheet\Calculation\Logical\Operations::logicalXor

## CATEGORY_LOOKUP_AND_REFERENCE

Excel Function           | PhpSpreadsheet Function
-------------------------|--------------------------------------
ADDRESS                  | \PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Address::cell
AREAS                    | **Not yet Implemented**
CHOOSE                   | \PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Selection::CHOOSE
COLUMN                   | \PhpOffice\PhpSpreadsheet\Calculation\LookupRef\RowColumnInformation::COLUMN
COLUMNS                  | \PhpOffice\PhpSpreadsheet\Calculation\LookupRef\RowColumnInformation::COLUMNS
FILTER                   | **Not yet Implemented**
FORMULATEXT              | \PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Formula::text
GETPIVOTDATA             | **Not yet Implemented**
HLOOKUP                  | \PhpOffice\PhpSpreadsheet\Calculation\LookupRef\HLookup::lookup
HYPERLINK                | \PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Hyperlink::set
INDEX                    | \PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Matrix::index
INDIRECT                 | \PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Indirect::INDIRECT
LOOKUP                   | \PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Lookup::lookup
MATCH                    | \PhpOffice\PhpSpreadsheet\Calculation\LookupRef\ExcelMatch::MATCH
OFFSET                   | \PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Offset::OFFSET
ROW                      | \PhpOffice\PhpSpreadsheet\Calculation\LookupRef\RowColumnInformation::ROW
ROWS                     | \PhpOffice\PhpSpreadsheet\Calculation\LookupRef\RowColumnInformation::ROWS
RTD                      | **Not yet Implemented**
SORT                     | **Not yet Implemented**
SORTBY                   | **Not yet Implemented**
TRANSPOSE                | \PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Matrix::transpose
UNIQUE                   | **Not yet Implemented**
VLOOKUP                  | \PhpOffice\PhpSpreadsheet\Calculation\LookupRef\VLookup::lookup
XLOOKUP                  | **Not yet Implemented**
XMATCH                   | **Not yet Implemented**

## CATEGORY_MATH_AND_TRIG

Excel Function           | PhpSpreadsheet Function
-------------------------|--------------------------------------
ABS                      | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Absolute::evaluate
ACOS                     | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Cosine::acos
ACOSH                    | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Cosine::acosh
ACOT                     | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Cotangent::acot
ACOTH                    | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Cotangent::acoth
AGGREGATE                | **Not yet Implemented**
ARABIC                   | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Arabic::evaluate
ASIN                     | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Sine::asin
ASINH                    | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Sine::asinh
ATAN                     | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Tangent::atan
ATAN2                    | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Tangent::atan2
ATANH                    | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Tangent::atanh
BASE                     | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Base::evaluate
CEILING                  | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Ceiling::ceiling
CEILING.MATH             | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Ceiling::math
CEILING.PRECISE          | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Ceiling::precise
COMBIN                   | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Combinations::withoutRepetition
COMBINA                  | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Combinations::withRepetition
COS                      | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Cosine::cos
COSH                     | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Cosine::cosh
COT                      | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Cotangent::cot
COTH                     | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Cotangent::coth
CSC                      | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Cosecant::csc
CSCH                     | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Cosecant::csch
DECIMAL                  | **Not yet Implemented**
DEGREES                  | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Angle::toDegrees
ECMA.CEILING             | **Not yet Implemented**
EVEN                     | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Round::even
EXP                      | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Exp::evaluate
FACT                     | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Factorial::fact
FACTDOUBLE               | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Factorial::factDouble
FLOOR                    | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Floor::floor
FLOOR.MATH               | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Floor::math
FLOOR.PRECISE            | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Floor::precise
GCD                      | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Gcd::evaluate
INT                      | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\IntClass::evaluate
ISO.CEILING              | **Not yet Implemented**
LCM                      | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Lcm::evaluate
LN                       | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Logarithms::natural
LOG                      | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Logarithms::withBase
LOG10                    | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Logarithms::base10
MDETERM                  | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\MatrixFunctions::determinant
MINVERSE                 | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\MatrixFunctions::inverse
MMULT                    | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\MatrixFunctions::multiply
MOD                      | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Operations::mod
MROUND                   | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Round::multiple
MULTINOMIAL              | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Factorial::multinomial
MUNIT                    | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\MatrixFunctions::identity
ODD                      | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Round::odd
PI                       | pi
POWER                    | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Operations::power
PRODUCT                  | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Operations::product
QUOTIENT                 | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Operations::quotient
RADIANS                  | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Angle::toRadians
RAND                     | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Random::rand
RANDARRAY                | **Not yet Implemented**
RANDBETWEEN              | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Random::randBetween
ROMAN                    | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Roman::evaluate
ROUND                    | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Round::round
ROUNDDOWN                | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Round::down
ROUNDUP                  | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Round::up
SEC                      | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Secant::sec
SECH                     | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Secant::sech
SEQUENCE                 | **Not yet Implemented**
SERIESSUM                | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\SeriesSum::evaluate
SIGN                     | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sign::evaluate
SIN                      | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Sine::sin
SINH                     | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Sine::sinh
SQRT                     | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sqrt::sqrt
SQRTPI                   | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sqrt::pi
SUBTOTAL                 | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Subtotal::evaluate
SUM                      | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sum::sumErroringStrings
SUMIF                    | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Conditional::SUMIF
SUMIFS                   | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Conditional::SUMIFS
SUMPRODUCT               | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sum::product
SUMSQ                    | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\SumSquares::sumSquare
SUMX2MY2                 | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\SumSquares::sumXSquaredMinusYSquared
SUMX2PY2                 | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\SumSquares::sumXSquaredPlusYSquared
SUMXMY2                  | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\SumSquares::sumXMinusYSquared
TAN                      | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Tangent::tan
TANH                     | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Tangent::tanh
TRUNC                    | \PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trunc::evaluate

## CATEGORY_STATISTICAL

Excel Function           | PhpSpreadsheet Function
-------------------------|--------------------------------------
AVEDEV                   | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages::averageDeviations
AVERAGE                  | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages::average
AVERAGEA                 | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages::averageA
AVERAGEIF                | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Conditional::AVERAGEIF
AVERAGEIFS               | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Conditional::AVERAGEIFS
BETA.DIST                | **Not yet Implemented**
BETA.INV                 | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Beta::inverse
BETADIST                 | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Beta::distribution
BETAINV                  | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Beta::inverse
BINOM.DIST               | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Binomial::distribution
BINOM.DIST.RANGE         | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Binomial::range
BINOM.INV                | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Binomial::inverse
BINOMDIST                | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Binomial::distribution
CHIDIST                  | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\ChiSquared::distributionRightTail
CHIINV                   | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\ChiSquared::inverseRightTail
CHISQ.DIST               | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\ChiSquared::distributionLeftTail
CHISQ.DIST.RT            | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\ChiSquared::distributionRightTail
CHISQ.INV                | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\ChiSquared::inverseLeftTail
CHISQ.INV.RT             | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\ChiSquared::inverseRightTail
CHISQ.TEST               | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\ChiSquared::test
CHITEST                  | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\ChiSquared::test
CONFIDENCE               | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Confidence::CONFIDENCE
CONFIDENCE.NORM          | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Confidence::CONFIDENCE
CONFIDENCE.T             | **Not yet Implemented**
CORREL                   | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends::CORREL
COUNT                    | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Counts::COUNT
COUNTA                   | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Counts::COUNTA
COUNTBLANK               | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Counts::COUNTBLANK
COUNTIF                  | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Conditional::COUNTIF
COUNTIFS                 | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Conditional::COUNTIFS
COVAR                    | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends::COVAR
COVARIANCE.P             | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends::COVAR
COVARIANCE.S             | **Not yet Implemented**
CRITBINOM                | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Binomial::inverse
DEVSQ                    | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Deviations::sumSquares
EXPON.DIST               | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Exponential::distribution
EXPONDIST                | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Exponential::distribution
F.DIST                   | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F::distribution
F.DIST.RT                | **Not yet Implemented**
F.INV                    | **Not yet Implemented**
F.INV.RT                 | **Not yet Implemented**
F.TEST                   | **Not yet Implemented**
FDIST                    | **Not yet Implemented**
FINV                     | **Not yet Implemented**
FISHER                   | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Fisher::distribution
FISHERINV                | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Fisher::inverse
FORECAST                 | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends::FORECAST
FORECAST.ETS             | **Not yet Implemented**
FORECAST.ETS.CONFINT     | **Not yet Implemented**
FORECAST.ETS.SEASONALITY | **Not yet Implemented**
FORECAST.ETS.STAT        | **Not yet Implemented**
FORECAST.LINEAR          | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends::FORECAST
FREQUENCY                | **Not yet Implemented**
FTEST                    | **Not yet Implemented**
GAMMA                    | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Gamma::gamma
GAMMA.DIST               | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Gamma::distribution
GAMMA.INV                | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Gamma::inverse
GAMMADIST                | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Gamma::distribution
GAMMAINV                 | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Gamma::inverse
GAMMALN                  | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Gamma::ln
GAMMALN.PRECISE          | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Gamma::ln
GAUSS                    | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\StandardNormal::gauss
GEOMEAN                  | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages\Mean::geometric
GROWTH                   | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends::GROWTH
HARMEAN                  | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages\Mean::harmonic
HYPGEOM.DIST             | **Not yet Implemented**
HYPGEOMDIST              | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\HyperGeometric::distribution
INTERCEPT                | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends::INTERCEPT
KURT                     | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Deviations::kurtosis
LARGE                    | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Size::large
LINEST                   | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends::LINEST
LOGEST                   | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends::LOGEST
LOGINV                   | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\LogNormal::inverse
LOGNORM.DIST             | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\LogNormal::distribution
LOGNORM.INV              | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\LogNormal::inverse
LOGNORMDIST              | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\LogNormal::cumulative
MAX                      | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Maximum::max
MAXA                     | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Maximum::maxA
MAXIFS                   | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Conditional::MAXIFS
MEDIAN                   | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages::median
MEDIANIF                 | **Not yet Implemented**
MIN                      | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Minimum::min
MINA                     | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Minimum::minA
MINIFS                   | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Conditional::MINIFS
MODE                     | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages::mode
MODE.MULT                | **Not yet Implemented**
MODE.SNGL                | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages::mode
NEGBINOM.DIST            | **Not yet Implemented**
NEGBINOMDIST             | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Binomial::negative
NORM.DIST                | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Normal::distribution
NORM.INV                 | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Normal::inverse
NORM.S.DIST              | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\StandardNormal::distribution
NORM.S.INV               | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\StandardNormal::inverse
NORMDIST                 | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Normal::distribution
NORMINV                  | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Normal::inverse
NORMSDIST                | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\StandardNormal::cumulative
NORMSINV                 | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\StandardNormal::inverse
PEARSON                  | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends::CORREL
PERCENTILE               | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Percentiles::PERCENTILE
PERCENTILE.EXC           | **Not yet Implemented**
PERCENTILE.INC           | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Percentiles::PERCENTILE
PERCENTRANK              | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Percentiles::PERCENTRANK
PERCENTRANK.EXC          | **Not yet Implemented**
PERCENTRANK.INC          | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Percentiles::PERCENTRANK
PERMUT                   | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Permutations::PERMUT
PERMUTATIONA             | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Permutations::PERMUTATIONA
PHI                      | **Not yet Implemented**
POISSON                  | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Poisson::distribution
POISSON.DIST             | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Poisson::distribution
PROB                     | **Not yet Implemented**
QUARTILE                 | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Percentiles::QUARTILE
QUARTILE.EXC             | **Not yet Implemented**
QUARTILE.INC             | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Percentiles::QUARTILE
RANK                     | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Percentiles::RANK
RANK.AVG                 | **Not yet Implemented**
RANK.EQ                  | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Percentiles::RANK
RSQ                      | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends::RSQ
SKEW                     | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Deviations::skew
SKEW.P                   | **Not yet Implemented**
SLOPE                    | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends::SLOPE
SMALL                    | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Size::small
STANDARDIZE              | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Standardize::execute
STDEV                    | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\StandardDeviations::STDEV
STDEV.P                  | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\StandardDeviations::STDEVP
STDEV.S                  | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\StandardDeviations::STDEV
STDEVA                   | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\StandardDeviations::STDEVA
STDEVP                   | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\StandardDeviations::STDEVP
STDEVPA                  | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\StandardDeviations::STDEVPA
STEYX                    | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends::STEYX
T.DIST                   | **Not yet Implemented**
T.DIST.2T                | **Not yet Implemented**
T.DIST.RT                | **Not yet Implemented**
T.INV                    | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\StudentT::inverse
T.INV.2T                 | **Not yet Implemented**
T.TEST                   | **Not yet Implemented**
TDIST                    | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\StudentT::distribution
TINV                     | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\StudentT::inverse
TREND                    | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends::TREND
TRIMMEAN                 | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages\Mean::trim
TTEST                    | **Not yet Implemented**
VAR                      | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Variances::VAR
VAR.P                    | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Variances::VARP
VAR.S                    | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Variances::VAR
VARA                     | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Variances::VARA
VARP                     | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Variances::VARP
VARPA                    | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Variances::VARPA
WEIBULL                  | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Weibull::distribution
WEIBULL.DIST             | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Weibull::distribution
Z.TEST                   | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\StandardNormal::zTest
ZTEST                    | \PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\StandardNormal::zTest

## CATEGORY_TEXT_AND_DATA

Excel Function           | PhpSpreadsheet Function
-------------------------|--------------------------------------
ASC                      | **Not yet Implemented**
BAHTTEXT                 | **Not yet Implemented**
CHAR                     | \PhpOffice\PhpSpreadsheet\Calculation\TextData\CharacterConvert::character
CLEAN                    | \PhpOffice\PhpSpreadsheet\Calculation\TextData\Trim::nonPrintable
CODE                     | \PhpOffice\PhpSpreadsheet\Calculation\TextData\CharacterConvert::code
CONCAT                   | \PhpOffice\PhpSpreadsheet\Calculation\TextData\Concatenate::CONCATENATE
CONCATENATE              | \PhpOffice\PhpSpreadsheet\Calculation\TextData\Concatenate::CONCATENATE
DBCS                     | **Not yet Implemented**
DOLLAR                   | \PhpOffice\PhpSpreadsheet\Calculation\TextData\Format::DOLLAR
EXACT                    | \PhpOffice\PhpSpreadsheet\Calculation\TextData\Text::exact
FIND                     | \PhpOffice\PhpSpreadsheet\Calculation\TextData\Search::sensitive
FINDB                    | \PhpOffice\PhpSpreadsheet\Calculation\TextData\Search::sensitive
FIXED                    | \PhpOffice\PhpSpreadsheet\Calculation\TextData\Format::FIXEDFORMAT
JIS                      | **Not yet Implemented**
LEFT                     | \PhpOffice\PhpSpreadsheet\Calculation\TextData\Extract::left
LEFTB                    | \PhpOffice\PhpSpreadsheet\Calculation\TextData\Extract::left
LEN                      | \PhpOffice\PhpSpreadsheet\Calculation\TextData\Text::length
LENB                     | \PhpOffice\PhpSpreadsheet\Calculation\TextData\Text::length
LOWER                    | \PhpOffice\PhpSpreadsheet\Calculation\TextData\CaseConvert::lower
MID                      | \PhpOffice\PhpSpreadsheet\Calculation\TextData\Extract::mid
MIDB                     | \PhpOffice\PhpSpreadsheet\Calculation\TextData\Extract::mid
NUMBERVALUE              | \PhpOffice\PhpSpreadsheet\Calculation\TextData\Format::NUMBERVALUE
PHONETIC                 | **Not yet Implemented**
PROPER                   | \PhpOffice\PhpSpreadsheet\Calculation\TextData\CaseConvert::proper
REPLACE                  | \PhpOffice\PhpSpreadsheet\Calculation\TextData\Replace::replace
REPLACEB                 | \PhpOffice\PhpSpreadsheet\Calculation\TextData\Replace::replace
REPT                     | \PhpOffice\PhpSpreadsheet\Calculation\TextData\Concatenate::builtinREPT
RIGHT                    | \PhpOffice\PhpSpreadsheet\Calculation\TextData\Extract::right
RIGHTB                   | \PhpOffice\PhpSpreadsheet\Calculation\TextData\Extract::right
SEARCH                   | \PhpOffice\PhpSpreadsheet\Calculation\TextData\Search::insensitive
SEARCHB                  | \PhpOffice\PhpSpreadsheet\Calculation\TextData\Search::insensitive
SUBSTITUTE               | \PhpOffice\PhpSpreadsheet\Calculation\TextData\Replace::substitute
T                        | \PhpOffice\PhpSpreadsheet\Calculation\TextData\Text::test
TEXT                     | \PhpOffice\PhpSpreadsheet\Calculation\TextData\Format::TEXTFORMAT
TEXTJOIN                 | \PhpOffice\PhpSpreadsheet\Calculation\TextData\Concatenate::TEXTJOIN
TRIM                     | \PhpOffice\PhpSpreadsheet\Calculation\TextData\Trim::spaces
UNICHAR                  | \PhpOffice\PhpSpreadsheet\Calculation\TextData\CharacterConvert::character
UNICODE                  | \PhpOffice\PhpSpreadsheet\Calculation\TextData\CharacterConvert::code
UPPER                    | \PhpOffice\PhpSpreadsheet\Calculation\TextData\CaseConvert::upper
VALUE                    | \PhpOffice\PhpSpreadsheet\Calculation\TextData\Format::VALUE

## CATEGORY_WEB

Excel Function           | PhpSpreadsheet Function
-------------------------|--------------------------------------
ENCODEURL                | \PhpOffice\PhpSpreadsheet\Calculation\Web\Service::urlEncode
FILTERXML                | **Not yet Implemented**
WEBSERVICE               | \PhpOffice\PhpSpreadsheet\Calculation\Web\Service::webService
