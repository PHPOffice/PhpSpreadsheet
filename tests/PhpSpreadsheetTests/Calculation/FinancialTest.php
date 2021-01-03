<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class FinancialTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerAMORDEGRC
     *
     * @param mixed $expectedResult
     */
    public function testAMORDEGRC($expectedResult, ...$args): void
    {
        $result = Financial::AMORDEGRC(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerAMORDEGRC()
    {
        return require 'tests/data/Calculation/Financial/AMORDEGRC.php';
    }

    /**
     * @dataProvider providerAMORLINC
     *
     * @param mixed $expectedResult
     */
    public function testAMORLINC($expectedResult, ...$args): void
    {
        $result = Financial::AMORLINC(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerAMORLINC()
    {
        return require 'tests/data/Calculation/Financial/AMORLINC.php';
    }

    /**
     * @dataProvider providerCOUPDAYBS
     *
     * @param mixed $expectedResult
     */
    public function testCOUPDAYBS($expectedResult, ...$args): void
    {
        $result = Financial::COUPDAYBS(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerCOUPDAYBS()
    {
        return require 'tests/data/Calculation/Financial/COUPDAYBS.php';
    }

    /**
     * @dataProvider providerCOUPDAYS
     *
     * @param mixed $expectedResult
     */
    public function testCOUPDAYS($expectedResult, ...$args): void
    {
        $result = Financial::COUPDAYS(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerCOUPDAYS()
    {
        return require 'tests/data/Calculation/Financial/COUPDAYS.php';
    }

    /**
     * @dataProvider providerCOUPDAYSNC
     *
     * @param mixed $expectedResult
     */
    public function testCOUPDAYSNC($expectedResult, ...$args): void
    {
        $result = Financial::COUPDAYSNC(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerCOUPDAYSNC()
    {
        return require 'tests/data/Calculation/Financial/COUPDAYSNC.php';
    }

    /**
     * @dataProvider providerCOUPNCD
     *
     * @param mixed $expectedResult
     */
    public function testCOUPNCD($expectedResult, ...$args): void
    {
        $result = Financial::COUPNCD(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerCOUPNCD()
    {
        return require 'tests/data/Calculation/Financial/COUPNCD.php';
    }

    /**
     * @dataProvider providerCOUPNUM
     *
     * @param mixed $expectedResult
     */
    public function testCOUPNUM($expectedResult, ...$args): void
    {
        $result = Financial::COUPNUM(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerCOUPNUM()
    {
        return require 'tests/data/Calculation/Financial/COUPNUM.php';
    }

    /**
     * @dataProvider providerCOUPPCD
     *
     * @param mixed $expectedResult
     */
    public function testCOUPPCD($expectedResult, ...$args): void
    {
        $result = Financial::COUPPCD(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerCOUPPCD()
    {
        return require 'tests/data/Calculation/Financial/COUPPCD.php';
    }

    /**
     * @dataProvider providerCUMIPMT
     *
     * @param mixed $expectedResult
     */
    public function testCUMIPMT($expectedResult, ...$args): void
    {
        $result = Financial::CUMIPMT(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerCUMIPMT()
    {
        return require 'tests/data/Calculation/Financial/CUMIPMT.php';
    }

    /**
     * @dataProvider providerCUMPRINC
     *
     * @param mixed $expectedResult
     */
    public function testCUMPRINC($expectedResult, ...$args): void
    {
        $result = Financial::CUMPRINC(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerCUMPRINC()
    {
        return require 'tests/data/Calculation/Financial/CUMPRINC.php';
    }

    /**
     * @dataProvider providerDB
     *
     * @param mixed $expectedResult
     */
    public function testDB($expectedResult, ...$args): void
    {
        $result = Financial::DB(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerDB()
    {
        return require 'tests/data/Calculation/Financial/DB.php';
    }

    /**
     * @dataProvider providerDDB
     *
     * @param mixed $expectedResult
     */
    public function testDDB($expectedResult, ...$args): void
    {
        $result = Financial::DDB(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerDDB()
    {
        return require 'tests/data/Calculation/Financial/DDB.php';
    }

    /**
     * @dataProvider providerDISC
     *
     * @param mixed $expectedResult
     */
    public function testDISC($expectedResult, ...$args): void
    {
        $result = Financial::DISC(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerDISC()
    {
        return require 'tests/data/Calculation/Financial/DISC.php';
    }

    /**
     * @dataProvider providerDOLLARDE
     *
     * @param mixed $expectedResult
     */
    public function testDOLLARDE($expectedResult, ...$args): void
    {
        $result = Financial::DOLLARDE(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerDOLLARDE()
    {
        return require 'tests/data/Calculation/Financial/DOLLARDE.php';
    }

    /**
     * @dataProvider providerDOLLARFR
     *
     * @param mixed $expectedResult
     */
    public function testDOLLARFR($expectedResult, ...$args): void
    {
        $result = Financial::DOLLARFR(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerDOLLARFR()
    {
        return require 'tests/data/Calculation/Financial/DOLLARFR.php';
    }

    /**
     * @dataProvider providerEFFECT
     *
     * @param mixed $expectedResult
     */
    public function testEFFECT($expectedResult, ...$args): void
    {
        $result = Financial::EFFECT(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerEFFECT()
    {
        return require 'tests/data/Calculation/Financial/EFFECT.php';
    }

    /**
     * @dataProvider providerFV
     *
     * @param mixed $expectedResult
     */
    public function testFV($expectedResult, ...$args): void
    {
        $result = Financial::FV(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerFV()
    {
        return require 'tests/data/Calculation/Financial/FV.php';
    }

    /**
     * @dataProvider providerFVSCHEDULE
     *
     * @param mixed $expectedResult
     */
    public function testFVSCHEDULE($expectedResult, ...$args): void
    {
        $result = Financial::FVSCHEDULE(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerFVSCHEDULE()
    {
        return require 'tests/data/Calculation/Financial/FVSCHEDULE.php';
    }

    /**
     * @dataProvider providerINTRATE
     *
     * @param mixed $expectedResult
     */
    public function testINTRATE($expectedResult, ...$args): void
    {
        $result = Financial::INTRATE(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerINTRATE()
    {
        return require 'tests/data/Calculation/Financial/INTRATE.php';
    }

    /**
     * @dataProvider providerIPMT
     *
     * @param mixed $expectedResult
     */
    public function testIPMT($expectedResult, ...$args): void
    {
        $result = Financial::IPMT(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerIPMT()
    {
        return require 'tests/data/Calculation/Financial/IPMT.php';
    }

    /**
     * @dataProvider providerIRR
     *
     * @param mixed $expectedResult
     */
    public function testIRR($expectedResult, ...$args): void
    {
        $result = Financial::IRR(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerIRR()
    {
        return require 'tests/data/Calculation/Financial/IRR.php';
    }

    /**
     * @dataProvider providerISPMT
     *
     * @param mixed $expectedResult
     */
    public function testISPMT($expectedResult, ...$args): void
    {
        $result = Financial::ISPMT(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerISPMT()
    {
        return require 'tests/data/Calculation/Financial/ISPMT.php';
    }

    /**
     * @dataProvider providerMIRR
     *
     * @param mixed $expectedResult
     */
    public function testMIRR($expectedResult, ...$args): void
    {
        $result = Financial::MIRR(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerMIRR()
    {
        return require 'tests/data/Calculation/Financial/MIRR.php';
    }

    /**
     * @dataProvider providerNOMINAL
     *
     * @param mixed $expectedResult
     */
    public function testNOMINAL($expectedResult, ...$args): void
    {
        $result = Financial::NOMINAL(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerNOMINAL()
    {
        return require 'tests/data/Calculation/Financial/NOMINAL.php';
    }

    /**
     * @dataProvider providerNPER
     *
     * @param mixed $expectedResult
     */
    public function testNPER($expectedResult, ...$args): void
    {
        $result = Financial::NPER(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerNPER()
    {
        return require 'tests/data/Calculation/Financial/NPER.php';
    }

    /**
     * @dataProvider providerNPV
     *
     * @param mixed $expectedResult
     */
    public function testNPV($expectedResult, ...$args): void
    {
        $result = Financial::NPV(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerNPV()
    {
        return require 'tests/data/Calculation/Financial/NPV.php';
    }

    /**
     * @dataProvider providerPRICE
     *
     * @param mixed $expectedResult
     */
    public function testPRICE($expectedResult, ...$args): void
    {
        $result = Financial::PRICE(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-7);
    }

    public function providerPRICE()
    {
        return require 'tests/data/Calculation/Financial/PRICE.php';
    }

    /**
     * @dataProvider providerPRICE3
     *
     * @param mixed $expectedResult
     */
    public function testPRICE3($expectedResult, ...$args): void
    {
        // These results (PRICE function with basis codes 2 and 3)
        // agree with published algorithm, LibreOffice, and Gnumeric.
        // They do not agree with Excel.
        $result = Financial::PRICE(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-7);
    }

    public function providerPRICE3()
    {
        return require 'tests/data/Calculation/Financial/PRICE3.php';
    }

    /**
     * @dataProvider providerPRICEDISC
     *
     * @param mixed $expectedResult
     */
    public function testPRICEDISC($expectedResult, array $args): void
    {
        $result = Financial::PRICEDISC(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerPRICEDISC()
    {
        return require 'tests/data/Calculation/Financial/PRICEDISC.php';
    }

    /**
     * @dataProvider providerPV
     *
     * @param mixed $expectedResult
     */
    public function testPV($expectedResult, array $args): void
    {
        $result = Financial::PV(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerPV()
    {
        return require 'tests/data/Calculation/Financial/PV.php';
    }

    /**
     * @dataProvider providerRATE
     *
     * @param mixed $expectedResult
     */
    public function testRATE($expectedResult, ...$args): void
    {
        $result = Financial::RATE(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerRATE()
    {
        return require 'tests/data/Calculation/Financial/RATE.php';
    }

    /**
     * @dataProvider providerXIRR
     *
     * @param mixed $expectedResult
     * @param mixed $message
     */
    public function testXIRR($expectedResult, $message, ...$args): void
    {
        $result = Financial::XIRR(...$args);
        if (is_numeric($result) && is_numeric($expectedResult)) {
            if ($expectedResult != 0) {
                $frac = $result / $expectedResult;
                if ($frac > 0.999999 && $frac < 1.000001) {
                    $result = $expectedResult;
                }
            }
        }
        self::assertEquals($expectedResult, $result, $message);
    }

    public function providerXIRR()
    {
        return require 'tests/data/Calculation/Financial/XIRR.php';
    }

    /**
     * @dataProvider providerXNPV
     *
     * @param mixed $expectedResult
     * @param mixed $message
     */
    public function testXNPV($expectedResult, $message, ...$args): void
    {
        $result = Financial::XNPV(...$args);
        if (is_numeric($result) && is_numeric($expectedResult)) {
            if ($expectedResult != 0) {
                $frac = $result / $expectedResult;
                if ($frac > 0.999999 && $frac < 1.000001) {
                    $result = $expectedResult;
                }
            }
        }
        self::assertEquals($expectedResult, $result, $message);
    }

    public function providerXNPV()
    {
        return require 'tests/data/Calculation/Financial/XNPV.php';
    }

    /**
     * @dataProvider providerPDURATION
     *
     * @param mixed $expectedResult
     */
    public function testPDURATION($expectedResult, array $args): void
    {
        $result = Financial::PDURATION(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerPDURATION()
    {
        return require 'tests/data/Calculation/Financial/PDURATION.php';
    }

    /**
     * @dataProvider providerRRI
     *
     * @param mixed $expectedResult
     */
    public function testRRI($expectedResult, array $args): void
    {
        $result = Financial::RRI(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerRRI()
    {
        return require 'tests/data/Calculation/Financial/RRI.php';
    }

    /**
     * @dataProvider providerSLN
     *
     * @param mixed $expectedResult
     */
    public function testSLN($expectedResult, array $args): void
    {
        $result = Financial::SLN(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerSLN()
    {
        return require 'tests/data/Calculation/Financial/SLN.php';
    }

    /**
     * @dataProvider providerSYD
     *
     * @param mixed $expectedResult
     */
    public function testSYD($expectedResult, array $args): void
    {
        $result = Financial::SYD(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerSYD()
    {
        return require 'tests/data/Calculation/Financial/SYD.php';
    }
}
