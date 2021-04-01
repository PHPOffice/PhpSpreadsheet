# Function list by category

## Cube

Excel Function           | PhpSpreadsheet Function
-------------------------|------------------------
CUBEKPIMEMBER            | **Not yet implemented**
CUBEMEMBER               | **Not yet implemented**
CUBEMEMBERPROPERTY       | **Not yet implemented**
CUBERANKEDMEMBER         | **Not yet implemented**
CUBESET                  | **Not yet implemented**
CUBESETCOUNT             | **Not yet implemented**
CUBEVALUE                | **Not yet implemented**

## Database

Excel Function           | PhpSpreadsheet Function
-------------------------|------------------------
DAVERAGE                 | PhpOffice\PhpSpreadsheet\Calculation\Database\DAverage::evaluate
DCOUNT                   | PhpOffice\PhpSpreadsheet\Calculation\Database\DCount::evaluate
DCOUNTA                  | PhpOffice\PhpSpreadsheet\Calculation\Database\DCountA::evaluate
DGET                     | PhpOffice\PhpSpreadsheet\Calculation\Database\DGet::evaluate
DMAX                     | PhpOffice\PhpSpreadsheet\Calculation\Database\DMax::evaluate
DMIN                     | PhpOffice\PhpSpreadsheet\Calculation\Database\DMin::evaluate
DPRODUCT                 | PhpOffice\PhpSpreadsheet\Calculation\Database\DProduct::evaluate
DSTDEV                   | PhpOffice\PhpSpreadsheet\Calculation\Database\DStDev::evaluate
DSTDEVP                  | PhpOffice\PhpSpreadsheet\Calculation\Database\DStDevP::evaluate
DSUM                     | PhpOffice\PhpSpreadsheet\Calculation\Database\DSum::evaluate
DVAR                     | PhpOffice\PhpSpreadsheet\Calculation\Database\DVar::evaluate
DVARP                    | PhpOffice\PhpSpreadsheet\Calculation\Database\DVarP::evaluate

## Date and Time

Excel Function           | PhpSpreadsheet Function
-------------------------|------------------------
DATE                     | PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Datefunc::funcDate
DATEDIF                  | PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\DateDif::funcDateDif
DATEVALUE                | PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\DateValue::funcDateValue
DAY                      | PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Day::funcDay
DAYS                     | PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Days::funcDays
DAYS360                  | PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Days360::funcDays360
EDATE                    | PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\EDate::funcEDate
EOMONTH                  | PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\EoMonth::funcEoMonth
HOUR                     | PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Hour::funcHour
ISOWEEKNUM               | PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\IsoWeekNum::funcIsoWeekNum
MINUTE                   | PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Minute::funcMinute
MONTH                    | PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Month::funcMonth
NETWORKDAYS              | PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\NetworkDays::funcNetworkDays
NETWORKDAYS.INTL         | **Not yet implemented**
NOW                      | PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Now::funcNow
SECOND                   | PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Second::funcSecond
TIME                     | PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Time::funcTime
TIMEVALUE                | PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\TimeValue::funcTimeValue
TODAY                    | PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Today::funcToday
WEEKDAY                  | PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\WeekDay::funcWeekDay
WEEKNUM                  | PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\WeekNum::funcWeekNum
WORKDAY                  | PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\WorkDay::funcWorkDay
WORKDAY.INTL             | **Not yet implemented**
YEAR                     | PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Year::funcYear
YEARFRAC                 | PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\YearFrac::funcYearFrac

## Engineering

Excel Function           | PhpSpreadsheet Function
-------------------------|------------------------
BESSELI                  | PhpOffice\PhpSpreadsheet\Calculation\Engineering\BesselI::BESSELI
BESSELJ                  | PhpOffice\PhpSpreadsheet\Calculation\Engineering\BesselJ::BESSELJ
BESSELK                  | PhpOffice\PhpSpreadsheet\Calculation\Engineering\BesselK::BESSELK
BESSELY                  | PhpOffice\PhpSpreadsheet\Calculation\Engineering\BesselY::BESSELY
BIN2DEC                  | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertBinary::toDecimal
BIN2HEX                  | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertBinary::toHex
BIN2OCT                  | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertBinary::toOctal
BITAND                   | PhpOffice\PhpSpreadsheet\Calculation\Engineering\BitWise::BITAND
BITLSHIFT                | PhpOffice\PhpSpreadsheet\Calculation\Engineering\BitWise::BITLSHIFT
BITOR                    | PhpOffice\PhpSpreadsheet\Calculation\Engineering\BitWise::BITOR
BITRSHIFT                | PhpOffice\PhpSpreadsheet\Calculation\Engineering\BitWise::BITRSHIFT
BITXOR                   | PhpOffice\PhpSpreadsheet\Calculation\Engineering\BitWise::BITXOR
COMPLEX                  | PhpOffice\PhpSpreadsheet\Calculation\Engineering\Complex::COMPLEX
CONVERT                  | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertUOM::CONVERT
DEC2BIN                  | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertDecimal::toBinary
DEC2HEX                  | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertDecimal::toHex
DEC2OCT                  | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertDecimal::toOctal
DELTA                    | PhpOffice\PhpSpreadsheet\Calculation\Engineering\Compare::DELTA
ERF                      | PhpOffice\PhpSpreadsheet\Calculation\Engineering\Erf::ERF
ERF.PRECISE              | PhpOffice\PhpSpreadsheet\Calculation\Engineering\Erf::ERFPRECISE
ERFC                     | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ErfC::ERFC
ERFC.PRECISE             | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ErfC::ERFC
GESTEP                   | PhpOffice\PhpSpreadsheet\Calculation\Engineering\Compare::GESTEP
HEX2BIN                  | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertHex::toBinary
HEX2DEC                  | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertHex::toDecimal
HEX2OCT                  | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertHex::toOctal
IMABS                    | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMABS
IMAGINARY                | PhpOffice\PhpSpreadsheet\Calculation\Engineering\Complex::IMAGINARY
IMARGUMENT               | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMARGUMENT
IMCONJUGATE              | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMCONJUGATE
IMCOS                    | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMCOS
IMCOSH                   | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMCOSH
IMCOT                    | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMCOT
IMCSC                    | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMCSC
IMCSCH                   | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMCSCH
IMDIV                    | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexOperations::IMDIV
IMEXP                    | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMEXP
IMLN                     | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMLN
IMLOG10                  | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMLOG10
IMLOG2                   | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMLOG2
IMPOWER                  | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMPOWER
IMPRODUCT                | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexOperations::IMPRODUCT
IMREAL                   | PhpOffice\PhpSpreadsheet\Calculation\Engineering\Complex::IMREAL
IMSEC                    | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMSEC
IMSECH                   | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMSECH
IMSIN                    | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMSIN
IMSINH                   | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMSINH
IMSQRT                   | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMSQRT
IMSUB                    | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexOperations::IMSUB
IMSUM                    | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexOperations::IMSUM
IMTAN                    | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions::IMTAN
OCT2BIN                  | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertOctal::toBinary
OCT2DEC                  | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertOctal::toDecimal
OCT2HEX                  | PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertOctal::toHex

## Financial

Excel Function           | PhpSpreadsheet Function
-------------------------|------------------------
ACCRINT                  | PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\AccruedInterest::periodic
ACCRINTM                 | PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\AccruedInterest::atMaturity
AMORDEGRC                | PhpOffice\PhpSpreadsheet\Calculation\Financial\Amortization::AMORDEGRC
AMORLINC                 | PhpOffice\PhpSpreadsheet\Calculation\Financial\Amortization::AMORLINC
COUPDAYBS                | PhpOffice\PhpSpreadsheet\Calculation\Financial\Coupons::COUPDAYBS
COUPDAYS                 | PhpOffice\PhpSpreadsheet\Calculation\Financial\Coupons::COUPDAYS
COUPDAYSNC               | PhpOffice\PhpSpreadsheet\Calculation\Financial\Coupons::COUPDAYSNC
COUPNCD                  | PhpOffice\PhpSpreadsheet\Calculation\Financial\Coupons::COUPNCD
COUPNUM                  | PhpOffice\PhpSpreadsheet\Calculation\Financial\Coupons::COUPNUM
COUPPCD                  | PhpOffice\PhpSpreadsheet\Calculation\Financial\Coupons::COUPPCD
CUMIPMT                  | PhpOffice\PhpSpreadsheet\Calculation\Financial::CUMIPMT
CUMPRINC                 | PhpOffice\PhpSpreadsheet\Calculation\Financial::CUMPRINC
DB                       | PhpOffice\PhpSpreadsheet\Calculation\Financial::DB
DDB                      | PhpOffice\PhpSpreadsheet\Calculation\Financial::DDB
DISC                     | PhpOffice\PhpSpreadsheet\Calculation\Financial::DISC
DOLLARDE                 | PhpOffice\PhpSpreadsheet\Calculation\Financial\Dollar::decimal
DOLLARFR                 | PhpOffice\PhpSpreadsheet\Calculation\Financial\Dollar::fractional
DURATION                 | **Not yet implemented**
EFFECT                   | PhpOffice\PhpSpreadsheet\Calculation\Financial\InterestRate::effective
FV                       | PhpOffice\PhpSpreadsheet\Calculation\Financial::FV
FVSCHEDULE               | PhpOffice\PhpSpreadsheet\Calculation\Financial::FVSCHEDULE
INTRATE                  | PhpOffice\PhpSpreadsheet\Calculation\Financial::INTRATE
IPMT                     | PhpOffice\PhpSpreadsheet\Calculation\Financial::IPMT
IRR                      | PhpOffice\PhpSpreadsheet\Calculation\Financial::IRR
ISPMT                    | PhpOffice\PhpSpreadsheet\Calculation\Financial::ISPMT
MDURATION                | **Not yet implemented**
MIRR                     | PhpOffice\PhpSpreadsheet\Calculation\Financial::MIRR
NOMINAL                  | PhpOffice\PhpSpreadsheet\Calculation\Financial\InterestRate::nominal
NPER                     | PhpOffice\PhpSpreadsheet\Calculation\Financial::NPER
NPV                      | PhpOffice\PhpSpreadsheet\Calculation\Financial::NPV
ODDFPRICE                | **Not yet implemented**
ODDFYIELD                | **Not yet implemented**
ODDLPRICE                | **Not yet implemented**
ODDLYIELD                | **Not yet implemented**
PDURATION                | PhpOffice\PhpSpreadsheet\Calculation\Financial::PDURATION
PMT                      | PhpOffice\PhpSpreadsheet\Calculation\Financial::PMT
PPMT                     | PhpOffice\PhpSpreadsheet\Calculation\Financial::PPMT
PRICE                    | PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\Price::price
PRICEDISC                | PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\Price::priceDiscounted
PRICEMAT                 | PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\Price::priceAtMaturity
PV                       | PhpOffice\PhpSpreadsheet\Calculation\Financial::PV
RATE                     | PhpOffice\PhpSpreadsheet\Calculation\Financial::RATE
RECEIVED                 | PhpOffice\PhpSpreadsheet\Calculation\Financial::RECEIVED
RRI                      | PhpOffice\PhpSpreadsheet\Calculation\Financial::RRI
SLN                      | PhpOffice\PhpSpreadsheet\Calculation\Financial\Depreciation::SLN
SYD                      | PhpOffice\PhpSpreadsheet\Calculation\Financial\Depreciation::SYD
TBILLEQ                  | PhpOffice\PhpSpreadsheet\Calculation\Financial\TreasuryBill::bondEquivalentYield
TBILLPRICE               | PhpOffice\PhpSpreadsheet\Calculation\Financial\TreasuryBill::price
TBILLYIELD               | PhpOffice\PhpSpreadsheet\Calculation\Financial\TreasuryBill::yield
USDOLLAR                 | **Not yet implemented**
VDB                      | **Not yet implemented**
XIRR                     | PhpOffice\PhpSpreadsheet\Calculation\Financial::XIRR
XNPV                     | PhpOffice\PhpSpreadsheet\Calculation\Financial::XNPV
YIELD                    | **Not yet implemented**
YIELDDISC                | PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\Yields::yieldDiscounted
YIELDMAT                 | PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\Yields::yieldAtMaturity

## Information

Excel Function           | PhpSpreadsheet Function
-------------------------|------------------------
CELL                     | **Not yet implemented**
ERROR.TYPE               | PhpOffice\PhpSpreadsheet\Calculation\Functions::errorType
INFO                     | **Not yet implemented**
ISBLANK                  | PhpOffice\PhpSpreadsheet\Calculation\Functions::isBlank
ISERR                    | PhpOffice\PhpSpreadsheet\Calculation\Functions::isErr
ISERROR                  | PhpOffice\PhpSpreadsheet\Calculation\Functions::isError
ISEVEN                   | PhpOffice\PhpSpreadsheet\Calculation\Functions::isEven
ISFORMULA                | PhpOffice\PhpSpreadsheet\Calculation\Functions::isFormula
ISLOGICAL                | PhpOffice\PhpSpreadsheet\Calculation\Functions::isLogical
ISNA                     | PhpOffice\PhpSpreadsheet\Calculation\Functions::isNa
ISNONTEXT                | PhpOffice\PhpSpreadsheet\Calculation\Functions::isNonText
ISNUMBER                 | PhpOffice\PhpSpreadsheet\Calculation\Functions::isNumber
ISODD                    | PhpOffice\PhpSpreadsheet\Calculation\Functions::isOdd
ISREF                    | **Not yet implemented**
ISTEXT                   | PhpOffice\PhpSpreadsheet\Calculation\Functions::isText
N                        | PhpOffice\PhpSpreadsheet\Calculation\Functions::n
NA                       | PhpOffice\PhpSpreadsheet\Calculation\Functions::NA
SHEET                    | **Not yet implemented**
SHEETS                   | **Not yet implemented**
TYPE                     | PhpOffice\PhpSpreadsheet\Calculation\Functions::TYPE

## Logical

Excel Function           | PhpSpreadsheet Function
-------------------------|------------------------
AND                      | PhpOffice\PhpSpreadsheet\Calculation\Logical\Operations::logicalAnd
FALSE                    | PhpOffice\PhpSpreadsheet\Calculation\Logical\Boolean::FALSE
IF                       | PhpOffice\PhpSpreadsheet\Calculation\Logical\Conditional::statementIf
IFERROR                  | PhpOffice\PhpSpreadsheet\Calculation\Logical\Conditional::IFERROR
IFNA                     | PhpOffice\PhpSpreadsheet\Calculation\Logical\Conditional::IFNA
IFS                      | PhpOffice\PhpSpreadsheet\Calculation\Logical\Conditional::IFS
NOT                      | PhpOffice\PhpSpreadsheet\Calculation\Logical\Operations::NOT
OR                       | PhpOffice\PhpSpreadsheet\Calculation\Logical\Operations::logicalOr
SWITCH                   | PhpOffice\PhpSpreadsheet\Calculation\Logical\Conditional::statementSwitch
TRUE                     | PhpOffice\PhpSpreadsheet\Calculation\Logical\Boolean::TRUE
XOR                      | PhpOffice\PhpSpreadsheet\Calculation\Logical\Operations::logicalXor

## Lookup and Reference

Excel Function           | PhpSpreadsheet Function
-------------------------|------------------------
ADDRESS                  | PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Address::cell
AREAS                    | **Not yet implemented**
CHOOSE                   | PhpOffice\PhpSpreadsheet\Calculation\LookupRef::CHOOSE
COLUMN                   | PhpOffice\PhpSpreadsheet\Calculation\LookupRef\RowColumnInformation::COLUMN
COLUMNS                  | PhpOffice\PhpSpreadsheet\Calculation\LookupRef\RowColumnInformation::COLUMNS
FILTER                   | **Not yet implemented**
FORMULATEXT              | PhpOffice\PhpSpreadsheet\Calculation\LookupRef::FORMULATEXT
GETPIVOTDATA             | **Not yet implemented**
HLOOKUP                  | PhpOffice\PhpSpreadsheet\Calculation\LookupRef\HLookup::lookup
HYPERLINK                | PhpOffice\PhpSpreadsheet\Calculation\LookupRef::HYPERLINK
INDEX                    | PhpOffice\PhpSpreadsheet\Calculation\LookupRef::INDEX
INDIRECT                 | PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Indirect::INDIRECT
LOOKUP                   | PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Lookup::lookup
MATCH                    | PhpOffice\PhpSpreadsheet\Calculation\LookupRef\ExcelMatch::MATCH
OFFSET                   | PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Offset::OFFSET
ROW                      | PhpOffice\PhpSpreadsheet\Calculation\LookupRef\RowColumnInformation::ROW
ROWS                     | PhpOffice\PhpSpreadsheet\Calculation\LookupRef\RowColumnInformation::ROWS
RTD                      | **Not yet implemented**
SORT                     | **Not yet implemented**
SORTBY                   | **Not yet implemented**
TRANSPOSE                | PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Matrix::transpose
UNIQUE                   | **Not yet implemented**
VLOOKUP                  | PhpOffice\PhpSpreadsheet\Calculation\LookupRef\VLookup::lookup
XLOOKUP                  | **Not yet implemented**
XMATCH                   | **Not yet implemented**

## Math and Trig

Excel Function           | PhpSpreadsheet Function
-------------------------|------------------------
ABS                      | PhpOffice\PhpSpreadsheet\Calculation\MathTrig::builtinABS
ACOS                     | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Acos::funcAcos
ACOSH                    | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Acosh::funcAcosh
ACOT                     | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Acot::funcAcot
ACOTH                    | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Acoth::funcAcoth
AGGREGATE                | **Not yet implemented**
ARABIC                   | PhpOffice\PhpSpreadsheet\Calculation\MathTrig::ARABIC
ASIN                     | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Asin::funcAsin
ASINH                    | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Asinh::funcAsinh
ATAN                     | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Atan::funcAtan
ATAN2                    | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Atan2::funcAtan2
ATANH                    | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Atanh::funcAtanh
BASE                     | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Base::funcBase
CEILING                  | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Ceiling::funcCeiling
CEILING.MATH             | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\CeilingMath::funcCeilingMath
CEILING.PRECISE          | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\CeilingPrecise::funcCeilingPrecise
COMBIN                   | PhpOffice\PhpSpreadsheet\Calculation\MathTrig::COMBIN
COMBINA                  | **Not yet implemented**
COS                      | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Cos::funcCos
COSH                     | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Cosh::funcCosh
COT                      | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Cot::funcCot
COTH                     | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Coth::funcCoth
CSC                      | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Csc::funcCsc
CSCH                     | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Csch::funcCsch
DECIMAL                  | **Not yet implemented**
DEGREES                  | PhpOffice\PhpSpreadsheet\Calculation\MathTrig::builtinDEGREES
EVEN                     | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Even::funcEven
EXP                      | PhpOffice\PhpSpreadsheet\Calculation\MathTrig::builtinEXP
FACT                     | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Fact::funcFact
FACTDOUBLE               | PhpOffice\PhpSpreadsheet\Calculation\MathTrig::FACTDOUBLE
FLOOR                    | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Floor::funcFloor
FLOOR.MATH               | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\FloorMath::funcFloorMath
FLOOR.PRECISE            | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\FloorPrecise::funcFloorPrecise
GCD                      | PhpOffice\PhpSpreadsheet\Calculation\MathTrig::GCD
INT                      | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\IntClass::funcInt
ISO.CEILING              | **Not yet implemented**
LCM                      | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Lcm::funcLcm
LN                       | PhpOffice\PhpSpreadsheet\Calculation\MathTrig::builtinLN
LOG                      | PhpOffice\PhpSpreadsheet\Calculation\MathTrig::logBase
LOG10                    | PhpOffice\PhpSpreadsheet\Calculation\MathTrig::builtinLOG10
MDETERM                  | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\MatrixFunctions::funcMDeterm
MINVERSE                 | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\MatrixFunctions::funcMinverse
MMULT                    | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\MatrixFunctions::funcMMult
MOD                      | PhpOffice\PhpSpreadsheet\Calculation\MathTrig::MOD
MROUND                   | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Mround::funcMround
MULTINOMIAL              | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Multinomial::funcMultinomial
MUNIT                    | **Not yet implemented**
ODD                      | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Odd::funcOdd
PI                       | p::i
POWER                    | PhpOffice\PhpSpreadsheet\Calculation\MathTrig::POWER
PRODUCT                  | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Product::funcProduct
QUOTIENT                 | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Quotient::funcQuotient
RADIANS                  | PhpOffice\PhpSpreadsheet\Calculation\MathTrig::builtinRADIANS
RAND                     | PhpOffice\PhpSpreadsheet\Calculation\MathTrig::RAND
RANDARRAY                | **Not yet implemented**
RANDBETWEEN              | PhpOffice\PhpSpreadsheet\Calculation\MathTrig::RAND
ROMAN                    | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Roman::funcRoman
ROUND                    | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Round::builtinROUND
ROUNDDOWN                | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\RoundDown::funcRoundDown
ROUNDUP                  | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\RoundUp::funcRoundUp
SEC                      | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sec::funcSec
SECH                     | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sech::funcSech
SEQUENCE                 | **Not yet implemented**
SERIESSUM                | PhpOffice\PhpSpreadsheet\Calculation\MathTrig::SERIESSUM
SIGN                     | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sign::funcSign
SIN                      | PhpOffice\PhpSpreadsheet\Calculation\MathTrig::builtinSIN
SINH                     | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sinh::funcSinh
SQRT                     | PhpOffice\PhpSpreadsheet\Calculation\MathTrig::builtinSQRT
SQRTPI                   | PhpOffice\PhpSpreadsheet\Calculation\MathTrig::SQRTPI
SUBTOTAL                 | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Subtotal::funcSubtotal
SUM                      | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sum::funcSumNoStrings
SUMIF                    | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Conditional::SUMIF
SUMIFS                   | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Conditional::SUMIFS
SUMPRODUCT               | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\SumProduct::funcSumProduct
SUMSQ                    | PhpOffice\PhpSpreadsheet\Calculation\MathTrig::SUMSQ
SUMX2MY2                 | PhpOffice\PhpSpreadsheet\Calculation\MathTrig::SUMX2MY2
SUMX2PY2                 | PhpOffice\PhpSpreadsheet\Calculation\MathTrig::SUMX2PY2
SUMXMY2                  | PhpOffice\PhpSpreadsheet\Calculation\MathTrig::SUMXMY2
TAN                      | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Tan::funcTan
TANH                     | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Tanh::funcTanh
TRUNC                    | PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trunc::funcTrunc

## Statistical

Excel Function           | PhpSpreadsheet Function
-------------------------|------------------------
AVEDEV                   | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages::averageDeviations
AVERAGE                  | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages::average
AVERAGEA                 | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages::averageA
AVERAGEIF                | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Conditional::AVERAGEIF
AVERAGEIFS               | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Conditional::AVERAGEIFS
BETA.DIST                | **Not yet implemented**
BETA.INV                 | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Beta::inverse
BETADIST                 | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Beta::distribution
BETAINV                  | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Beta::inverse
BINOM.DIST               | PhpOffice\PhpSpreadsheet\Calculation\Statistical::BINOMDIST
BINOM.DIST.RANGE         | **Not yet implemented**
BINOM.INV                | **Not yet implemented**
BINOMDIST                | PhpOffice\PhpSpreadsheet\Calculation\Statistical::BINOMDIST
CHIDIST                  | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\ChiSquared::distributionRightTail
CHIINV                   | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\ChiSquared::inverseRightTail
CHISQ.DIST               | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\ChiSquared::distributionLeftTail
CHISQ.DIST.RT            | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\ChiSquared::distributionRightTail
CHISQ.INV                | **Not yet implemented**
CHISQ.INV.RT             | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\ChiSquared::inverseRightTail
CHISQ.TEST               | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\ChiSquared::test
CHITEST                  | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\ChiSquared::test
CONFIDENCE               | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Confidence::CONFIDENCE
CONFIDENCE.NORM          | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Confidence::CONFIDENCE
CONFIDENCE.T             | **Not yet implemented**
CORREL                   | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends::CORREL
COUNT                    | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Counts::COUNT
COUNTA                   | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Counts::COUNTA
COUNTBLANK               | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Counts::COUNTBLANK
COUNTIF                  | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Conditional::COUNTIF
COUNTIFS                 | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Conditional::COUNTIFS
COVAR                    | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends::COVAR
COVARIANCE.P             | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends::COVAR
COVARIANCE.S             | **Not yet implemented**
CRITBINOM                | PhpOffice\PhpSpreadsheet\Calculation\Statistical::CRITBINOM
DEVSQ                    | PhpOffice\PhpSpreadsheet\Calculation\Statistical::DEVSQ
EXPON.DIST               | PhpOffice\PhpSpreadsheet\Calculation\Statistical::EXPONDIST
EXPONDIST                | PhpOffice\PhpSpreadsheet\Calculation\Statistical::EXPONDIST
F.DIST                   | PhpOffice\PhpSpreadsheet\Calculation\Statistical::FDIST2
F.DIST.RT                | **Not yet implemented**
F.INV                    | **Not yet implemented**
F.INV.RT                 | **Not yet implemented**
F.TEST                   | **Not yet implemented**
FDIST                    | **Not yet implemented**
FINV                     | **Not yet implemented**
FISHER                   | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Fisher::distribution
FISHERINV                | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Fisher::inverse
FORECAST                 | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends::FORECAST
FORECAST.ETS             | **Not yet implemented**
FORECAST.ETS.CONFINT     | **Not yet implemented**
FORECAST.ETS.SEASONALITY | **Not yet implemented**
FORECAST.ETS.STAT        | **Not yet implemented**
FORECAST.LINEAR          | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends::FORECAST
FREQUENCY                | **Not yet implemented**
FTEST                    | **Not yet implemented**
GAMMA                    | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Gamma::gamma
GAMMA.DIST               | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Gamma::distribution
GAMMA.INV                | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Gamma::inverse
GAMMADIST                | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Gamma::distribution
GAMMAINV                 | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Gamma::inverse
GAMMALN                  | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Gamma::ln
GAMMALN.PRECISE          | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Gamma::ln
GAUSS                    | PhpOffice\PhpSpreadsheet\Calculation\Statistical::GAUSS
GEOMEAN                  | PhpOffice\PhpSpreadsheet\Calculation\Statistical::GEOMEAN
GROWTH                   | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends::GROWTH
HARMEAN                  | PhpOffice\PhpSpreadsheet\Calculation\Statistical::HARMEAN
HYPGEOM.DIST             | **Not yet implemented**
HYPGEOMDIST              | PhpOffice\PhpSpreadsheet\Calculation\Statistical::HYPGEOMDIST
INTERCEPT                | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends::INTERCEPT
KURT                     | PhpOffice\PhpSpreadsheet\Calculation\Statistical::KURT
LARGE                    | PhpOffice\PhpSpreadsheet\Calculation\Statistical::LARGE
LINEST                   | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends::LINEST
LOGEST                   | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends::LOGEST
LOGINV                   | PhpOffice\PhpSpreadsheet\Calculation\Statistical::LOGINV
LOGNORM.DIST             | PhpOffice\PhpSpreadsheet\Calculation\Statistical::LOGNORMDIST2
LOGNORM.INV              | PhpOffice\PhpSpreadsheet\Calculation\Statistical::LOGINV
LOGNORMDIST              | PhpOffice\PhpSpreadsheet\Calculation\Statistical::LOGNORMDIST
MAX                      | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Maximum::MAX
MAXA                     | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Maximum::MAXA
MAXIFS                   | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Conditional::MAXIFS
MEDIAN                   | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages::median
MEDIANIF                 | **Not yet implemented**
MIN                      | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Minimum::MIN
MINA                     | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Minimum::MINA
MINIFS                   | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Conditional::MINIFS
MODE                     | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages::mode
MODE.MULT                | **Not yet implemented**
MODE.SNGL                | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages::mode
NEGBINOM.DIST            | **Not yet implemented**
NEGBINOMDIST             | PhpOffice\PhpSpreadsheet\Calculation\Statistical::NEGBINOMDIST
NORM.DIST                | PhpOffice\PhpSpreadsheet\Calculation\Statistical::NORMDIST
NORM.INV                 | PhpOffice\PhpSpreadsheet\Calculation\Statistical::NORMINV
NORM.S.DIST              | PhpOffice\PhpSpreadsheet\Calculation\Statistical::NORMSDIST2
NORM.S.INV               | PhpOffice\PhpSpreadsheet\Calculation\Statistical::NORMSINV
NORMDIST                 | PhpOffice\PhpSpreadsheet\Calculation\Statistical::NORMDIST
NORMINV                  | PhpOffice\PhpSpreadsheet\Calculation\Statistical::NORMINV
NORMSDIST                | PhpOffice\PhpSpreadsheet\Calculation\Statistical::NORMSDIST
NORMSINV                 | PhpOffice\PhpSpreadsheet\Calculation\Statistical::NORMSINV
PEARSON                  | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends::CORREL
PERCENTILE               | PhpOffice\PhpSpreadsheet\Calculation\Statistical::PERCENTILE
PERCENTILE.EXC           | **Not yet implemented**
PERCENTILE.INC           | PhpOffice\PhpSpreadsheet\Calculation\Statistical::PERCENTILE
PERCENTRANK              | PhpOffice\PhpSpreadsheet\Calculation\Statistical::PERCENTRANK
PERCENTRANK.EXC          | **Not yet implemented**
PERCENTRANK.INC          | PhpOffice\PhpSpreadsheet\Calculation\Statistical::PERCENTRANK
PERMUT                   | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Permutations::PERMUT
PERMUTATIONA             | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Permutations::PERMUTATIONA
PHI                      | **Not yet implemented**
POISSON                  | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Poisson::distribution
POISSON.DIST             | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Poisson::distribution
PROB                     | **Not yet implemented**
QUARTILE                 | PhpOffice\PhpSpreadsheet\Calculation\Statistical::QUARTILE
QUARTILE.EXC             | **Not yet implemented**
QUARTILE.INC             | PhpOffice\PhpSpreadsheet\Calculation\Statistical::QUARTILE
RANK                     | PhpOffice\PhpSpreadsheet\Calculation\Statistical::RANK
RANK.AVG                 | **Not yet implemented**
RANK.EQ                  | PhpOffice\PhpSpreadsheet\Calculation\Statistical::RANK
RSQ                      | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends::RSQ
SKEW                     | PhpOffice\PhpSpreadsheet\Calculation\Statistical::SKEW
SKEW.P                   | **Not yet implemented**
SLOPE                    | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends::SLOPE
SMALL                    | PhpOffice\PhpSpreadsheet\Calculation\Statistical::SMALL
STANDARDIZE              | PhpOffice\PhpSpreadsheet\Calculation\Statistical::STANDARDIZE
STDEV                    | PhpOffice\PhpSpreadsheet\Calculation\Statistical\StandardDeviations::STDEV
STDEV.P                  | PhpOffice\PhpSpreadsheet\Calculation\Statistical\StandardDeviations::STDEVP
STDEV.S                  | PhpOffice\PhpSpreadsheet\Calculation\Statistical\StandardDeviations::STDEV
STDEVA                   | PhpOffice\PhpSpreadsheet\Calculation\Statistical\StandardDeviations::STDEVA
STDEVP                   | PhpOffice\PhpSpreadsheet\Calculation\Statistical::STDEVP
STDEVPA                  | PhpOffice\PhpSpreadsheet\Calculation\Statistical::STDEVPA
STEYX                    | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends::STEYX
T.DIST                   | **Not yet implemented**
T.DIST.2T                | **Not yet implemented**
T.DIST.RT                | **Not yet implemented**
T.INV                    | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\StudentT::inverse
T.INV.2T                 | **Not yet implemented**
T.TEST                   | **Not yet implemented**
TDIST                    | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\StudentT::distribution
TINV                     | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\StudentT::inverse
TREND                    | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends::TREND
TRIMMEAN                 | PhpOffice\PhpSpreadsheet\Calculation\Statistical::TRIMMEAN
TTEST                    | **Not yet implemented**
VAR                      | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Variances::VAR
VAR.P                    | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Variances::VARP
VAR.S                    | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Variances::VAR
VARA                     | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Variances::VARA
VARP                     | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Variances::VARP
VARPA                    | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Variances::VARPA
WEIBULL                  | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Weibull::distribution
WEIBULL.DIST             | PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Weibull::distribution
Z.TEST                   | PhpOffice\PhpSpreadsheet\Calculation\Statistical::ZTEST
ZTEST                    | PhpOffice\PhpSpreadsheet\Calculation\Statistical::ZTEST

## Text and Data

Excel Function           | PhpSpreadsheet Function
-------------------------|------------------------
ASC                      | **Not yet implemented**
BAHTTEXT                 | **Not yet implemented**
CHAR                     | PhpOffice\PhpSpreadsheet\Calculation\TextData\CharacterConvert::character
CLEAN                    | PhpOffice\PhpSpreadsheet\Calculation\TextData\Trim::nonPrintable
CODE                     | PhpOffice\PhpSpreadsheet\Calculation\TextData\CharacterConvert::code
CONCAT                   | PhpOffice\PhpSpreadsheet\Calculation\TextData\Concatenate::CONCATENATE
CONCATENATE              | PhpOffice\PhpSpreadsheet\Calculation\TextData\Concatenate::CONCATENATE
DBCS                     | **Not yet implemented**
DOLLAR                   | PhpOffice\PhpSpreadsheet\Calculation\TextData\Format::DOLLAR
EXACT                    | PhpOffice\PhpSpreadsheet\Calculation\TextData\Text::exact
FIND                     | PhpOffice\PhpSpreadsheet\Calculation\TextData\Search::sensitive
FINDB                    | PhpOffice\PhpSpreadsheet\Calculation\TextData\Search::sensitive
FIXED                    | PhpOffice\PhpSpreadsheet\Calculation\TextData\Format::FIXEDFORMAT
JIS                      | **Not yet implemented**
LEFT                     | PhpOffice\PhpSpreadsheet\Calculation\TextData\Extract::left
LEFTB                    | PhpOffice\PhpSpreadsheet\Calculation\TextData\Extract::left
LEN                      | PhpOffice\PhpSpreadsheet\Calculation\TextData\Text::length
LENB                     | PhpOffice\PhpSpreadsheet\Calculation\TextData\Text::length
LOWER                    | PhpOffice\PhpSpreadsheet\Calculation\TextData\CaseConvert::lower
MID                      | PhpOffice\PhpSpreadsheet\Calculation\TextData\Extract::mid
MIDB                     | PhpOffice\PhpSpreadsheet\Calculation\TextData\Extract::mid
NUMBERVALUE              | PhpOffice\PhpSpreadsheet\Calculation\TextData\Format::NUMBERVALUE
PHONETIC                 | **Not yet implemented**
PROPER                   | PhpOffice\PhpSpreadsheet\Calculation\TextData\CaseConvert::proper
REPLACE                  | PhpOffice\PhpSpreadsheet\Calculation\TextData\Replace::replace
REPLACEB                 | PhpOffice\PhpSpreadsheet\Calculation\TextData\Replace::replace
REPT                     | PhpOffice\PhpSpreadsheet\Calculation\TextData\Concatenate::builtinREPT
RIGHT                    | PhpOffice\PhpSpreadsheet\Calculation\TextData\Extract::right
RIGHTB                   | PhpOffice\PhpSpreadsheet\Calculation\TextData\Extract::right
SEARCH                   | PhpOffice\PhpSpreadsheet\Calculation\TextData\Search::insensitive
SEARCHB                  | PhpOffice\PhpSpreadsheet\Calculation\TextData\Search::insensitive
SUBSTITUTE               | PhpOffice\PhpSpreadsheet\Calculation\TextData\Replace::substitute
T                        | PhpOffice\PhpSpreadsheet\Calculation\TextData\Text::test
TEXT                     | PhpOffice\PhpSpreadsheet\Calculation\TextData\Format::TEXTFORMAT
TEXTJOIN                 | PhpOffice\PhpSpreadsheet\Calculation\TextData::TEXTJOIN
TRIM                     | PhpOffice\PhpSpreadsheet\Calculation\TextData\Trim::spaces
UNICHAR                  | PhpOffice\PhpSpreadsheet\Calculation\TextData\CharacterConvert::character
UNICODE                  | PhpOffice\PhpSpreadsheet\Calculation\TextData\CharacterConvert::code
UPPER                    | PhpOffice\PhpSpreadsheet\Calculation\TextData\CaseConvert::upper
VALUE                    | PhpOffice\PhpSpreadsheet\Calculation\TextData\Format::VALUE

## Web

Excel Function           | PhpSpreadsheet Function
-------------------------|------------------------
ENCODEURL                | **Not yet implemented**
FILTERXML                | **Not yet implemented**
WEBSERVICE               | PhpOffice\PhpSpreadsheet\Calculation\Web::WEBSERVICE
