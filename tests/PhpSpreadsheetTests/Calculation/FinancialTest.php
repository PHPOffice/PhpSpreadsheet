<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class FinancialTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerACCRINT
     *
     * @param mixed $expectedResult
     */
    public function testACCRINT($expectedResult, ...$args)
    {
        $result = Financial::ACCRINT(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerACCRINT()
    {
        return require 'data/Calculation/Financial/ACCRINT.php';
    }

    /**
     * @dataProvider providerACCRINTM
     *
     * @param mixed $expectedResult
     */
    public function testACCRINTM($expectedResult, ...$args)
    {
        $result = Financial::ACCRINTM(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerACCRINTM()
    {
        return require 'data/Calculation/Financial/ACCRINTM.php';
    }

    /**
     * @dataProvider providerAMORDEGRC
     *
     * @param mixed $expectedResult
     */
    public function testAMORDEGRC($expectedResult, ...$args)
    {
        $result = Financial::AMORDEGRC(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerAMORDEGRC()
    {
        return require 'data/Calculation/Financial/AMORDEGRC.php';
    }

    /**
     * @dataProvider providerAMORLINC
     *
     * @param mixed $expectedResult
     */
    public function testAMORLINC($expectedResult, ...$args)
    {
        $result = Financial::AMORLINC(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerAMORLINC()
    {
        return require 'data/Calculation/Financial/AMORLINC.php';
    }

    /**
     * @dataProvider providerCOUPDAYBS
     *
     * @param mixed $expectedResult
     */
    public function testCOUPDAYBS($expectedResult, ...$args)
    {
        $result = Financial::COUPDAYBS(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerCOUPDAYBS()
    {
        return require 'data/Calculation/Financial/COUPDAYBS.php';
    }

    /**
     * @dataProvider providerCOUPDAYS
     *
     * @param mixed $expectedResult
     */
    public function testCOUPDAYS($expectedResult, ...$args)
    {
        $result = Financial::COUPDAYS(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerCOUPDAYS()
    {
        return require 'data/Calculation/Financial/COUPDAYS.php';
    }

    /**
     * @dataProvider providerCOUPDAYSNC
     *
     * @param mixed $expectedResult
     */
    public function testCOUPDAYSNC($expectedResult, ...$args)
    {
        $result = Financial::COUPDAYSNC(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerCOUPDAYSNC()
    {
        return require 'data/Calculation/Financial/COUPDAYSNC.php';
    }

    /**
     * @dataProvider providerCOUPNCD
     *
     * @param mixed $expectedResult
     */
    public function testCOUPNCD($expectedResult, ...$args)
    {
        $result = Financial::COUPNCD(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerCOUPNCD()
    {
        return require 'data/Calculation/Financial/COUPNCD.php';
    }

    /**
     * @dataProvider providerCOUPNUM
     *
     * @param mixed $expectedResult
     */
    public function testCOUPNUM($expectedResult, ...$args)
    {
        $result = Financial::COUPNUM(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerCOUPNUM()
    {
        return require 'data/Calculation/Financial/COUPNUM.php';
    }

    /**
     * @dataProvider providerCOUPPCD
     *
     * @param mixed $expectedResult
     */
    public function testCOUPPCD($expectedResult, ...$args)
    {
        $result = Financial::COUPPCD(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerCOUPPCD()
    {
        return require 'data/Calculation/Financial/COUPPCD.php';
    }

    /**
     * @dataProvider providerCUMIPMT
     *
     * @param mixed $expectedResult
     */
    public function testCUMIPMT($expectedResult, ...$args)
    {
        $result = Financial::CUMIPMT(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerCUMIPMT()
    {
        return require 'data/Calculation/Financial/CUMIPMT.php';
    }

    /**
     * @dataProvider providerCUMPRINC
     *
     * @param mixed $expectedResult
     */
    public function testCUMPRINC($expectedResult, ...$args)
    {
        $result = Financial::CUMPRINC(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerCUMPRINC()
    {
        return require 'data/Calculation/Financial/CUMPRINC.php';
    }

    /**
     * @dataProvider providerDB
     *
     * @param mixed $expectedResult
     */
    public function testDB($expectedResult, ...$args)
    {
        $result = Financial::DB(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerDB()
    {
        return require 'data/Calculation/Financial/DB.php';
    }

    /**
     * @dataProvider providerDDB
     *
     * @param mixed $expectedResult
     */
    public function testDDB($expectedResult, ...$args)
    {
        $result = Financial::DDB(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerDDB()
    {
        return require 'data/Calculation/Financial/DDB.php';
    }

    /**
     * @dataProvider providerDISC
     *
     * @param mixed $expectedResult
     */
    public function testDISC($expectedResult, ...$args)
    {
        $result = Financial::DISC(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerDISC()
    {
        return require 'data/Calculation/Financial/DISC.php';
    }

    /**
     * @dataProvider providerDOLLARDE
     *
     * @param mixed $expectedResult
     */
    public function testDOLLARDE($expectedResult, ...$args)
    {
        $result = Financial::DOLLARDE(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerDOLLARDE()
    {
        return require 'data/Calculation/Financial/DOLLARDE.php';
    }

    /**
     * @dataProvider providerDOLLARFR
     *
     * @param mixed $expectedResult
     */
    public function testDOLLARFR($expectedResult, ...$args)
    {
        $result = Financial::DOLLARFR(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerDOLLARFR()
    {
        return require 'data/Calculation/Financial/DOLLARFR.php';
    }

    /**
     * @dataProvider providerEFFECT
     *
     * @param mixed $expectedResult
     */
    public function testEFFECT($expectedResult, ...$args)
    {
        $result = Financial::EFFECT(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerEFFECT()
    {
        return require 'data/Calculation/Financial/EFFECT.php';
    }

    /**
     * @dataProvider providerFV
     *
     * @param mixed $expectedResult
     */
    public function testFV($expectedResult, ...$args)
    {
        $result = Financial::FV(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerFV()
    {
        return require 'data/Calculation/Financial/FV.php';
    }

    /**
     * @dataProvider providerFVSCHEDULE
     *
     * @param mixed $expectedResult
     */
    public function testFVSCHEDULE($expectedResult, ...$args)
    {
        $result = Financial::FVSCHEDULE(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerFVSCHEDULE()
    {
        return require 'data/Calculation/Financial/FVSCHEDULE.php';
    }

    /**
     * @dataProvider providerINTRATE
     *
     * @param mixed $expectedResult
     */
    public function testINTRATE($expectedResult, ...$args)
    {
        $result = Financial::INTRATE(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerINTRATE()
    {
        return require 'data/Calculation/Financial/INTRATE.php';
    }

    /**
     * @dataProvider providerIPMT
     *
     * @param mixed $expectedResult
     */
    public function testIPMT($expectedResult, ...$args)
    {
        $result = Financial::IPMT(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIPMT()
    {
        return require 'data/Calculation/Financial/IPMT.php';
    }

    /**
     * @dataProvider providerIRR
     *
     * @param mixed $expectedResult
     */
    public function testIRR($expectedResult, ...$args)
    {
        $result = Financial::IRR(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIRR()
    {
        return require 'data/Calculation/Financial/IRR.php';
    }

    /**
     * @dataProvider providerISPMT
     *
     * @param mixed $expectedResult
     */
    public function testISPMT($expectedResult, ...$args)
    {
        $result = Financial::ISPMT(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerISPMT()
    {
        return require 'data/Calculation/Financial/ISPMT.php';
    }

    /**
     * @dataProvider providerMIRR
     *
     * @param mixed $expectedResult
     */
    public function testMIRR($expectedResult, ...$args)
    {
        $result = Financial::MIRR(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerMIRR()
    {
        return require 'data/Calculation/Financial/MIRR.php';
    }

    /**
     * @dataProvider providerNOMINAL
     *
     * @param mixed $expectedResult
     */
    public function testNOMINAL($expectedResult, ...$args)
    {
        $result = Financial::NOMINAL(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerNOMINAL()
    {
        return require 'data/Calculation/Financial/NOMINAL.php';
    }

    /**
     * @dataProvider providerNPER
     *
     * @param mixed $expectedResult
     */
    public function testNPER($expectedResult, ...$args)
    {
        $result = Financial::NPER(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerNPER()
    {
        return require 'data/Calculation/Financial/NPER.php';
    }

    /**
     * @dataProvider providerNPV
     *
     * @param mixed $expectedResult
     */
    public function testNPV($expectedResult, ...$args)
    {
        $result = Financial::NPV(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerNPV()
    {
        return require 'data/Calculation/Financial/NPV.php';
    }

    /**
     * @dataProvider providerPRICE
     *
     * @param mixed $expectedResult
     */
    public function testPRICE($expectedResult, ...$args)
    {
        $this->markTestIncomplete('TODO: This test should be fixed');

        $result = Financial::PRICE(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerPRICE()
    {
        return require 'data/Calculation/Financial/PRICE.php';
    }

    /**
     * @dataProvider providerRATE
     *
     * @param mixed $expectedResult
     */
    public function testRATE($expectedResult, ...$args)
    {
        $this->markTestIncomplete('TODO: This test should be fixed');

        $result = Financial::RATE(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerRATE()
    {
        return require 'data/Calculation/Financial/RATE.php';
    }

    /**
     * @dataProvider providerXIRR
     *
     * @param mixed $expectedResult
     */
    public function testXIRR($expectedResult, ...$args)
    {
        $this->markTestIncomplete('TODO: This test should be fixed');

        $result = Financial::XIRR(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerXIRR()
    {
        return require 'data/Calculation/Financial/XIRR.php';
    }
}
