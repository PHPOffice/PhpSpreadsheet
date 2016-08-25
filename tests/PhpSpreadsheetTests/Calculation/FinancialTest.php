<?php

namespace PhpSpreadsheetTests\Calculation;

use PhpSpreadsheet\Calculation\Financial;
use PhpSpreadsheet\Calculation\Functions;

class FinancialTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerACCRINT
     * @group fail19
     */
    public function testACCRINT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'ACCRINT'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerACCRINT()
    {
        return require 'data/Calculation/Financial/ACCRINT.php';
    }

    /**
     * @dataProvider providerACCRINTM
     */
    public function testACCRINTM()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'ACCRINTM'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerACCRINTM()
    {
        return require 'data/Calculation/Financial/ACCRINTM.php';
    }

    /**
     * @dataProvider providerAMORDEGRC
     */
    public function testAMORDEGRC()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'AMORDEGRC'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerAMORDEGRC()
    {
        return require 'data/Calculation/Financial/AMORDEGRC.php';
    }

    /**
     * @dataProvider providerAMORLINC
     */
    public function testAMORLINC()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'AMORLINC'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerAMORLINC()
    {
        return require 'data/Calculation/Financial/AMORLINC.php';
    }

    /**
     * @dataProvider providerCOUPDAYBS
     */
    public function testCOUPDAYBS()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'COUPDAYBS'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerCOUPDAYBS()
    {
        return require 'data/Calculation/Financial/COUPDAYBS.php';
    }

    /**
     * @dataProvider providerCOUPDAYS
     */
    public function testCOUPDAYS()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'COUPDAYS'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerCOUPDAYS()
    {
        return require 'data/Calculation/Financial/COUPDAYS.php';
    }

    /**
     * @dataProvider providerCOUPDAYSNC
     */
    public function testCOUPDAYSNC()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'COUPDAYSNC'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerCOUPDAYSNC()
    {
        return require 'data/Calculation/Financial/COUPDAYSNC.php';
    }

    /**
     * @dataProvider providerCOUPNCD
     */
    public function testCOUPNCD()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'COUPNCD'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerCOUPNCD()
    {
        return require 'data/Calculation/Financial/COUPNCD.php';
    }

    /**
     * @dataProvider providerCOUPNUM
     */
    public function testCOUPNUM()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'COUPNUM'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerCOUPNUM()
    {
        return require 'data/Calculation/Financial/COUPNUM.php';
    }

    /**
     * @dataProvider providerCOUPPCD
     */
    public function testCOUPPCD()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'COUPPCD'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerCOUPPCD()
    {
        return require 'data/Calculation/Financial/COUPPCD.php';
    }

    /**
     * @dataProvider providerCUMIPMT
     */
    public function testCUMIPMT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'CUMIPMT'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerCUMIPMT()
    {
        return require 'data/Calculation/Financial/CUMIPMT.php';
    }

    /**
     * @dataProvider providerCUMPRINC
     */
    public function testCUMPRINC()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'CUMPRINC'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerCUMPRINC()
    {
        return require 'data/Calculation/Financial/CUMPRINC.php';
    }

    /**
     * @dataProvider providerDB
     */
    public function testDB()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'DB'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerDB()
    {
        return require 'data/Calculation/Financial/DB.php';
    }

    /**
     * @dataProvider providerDDB
     */
    public function testDDB()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'DDB'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerDDB()
    {
        return require 'data/Calculation/Financial/DDB.php';
    }

    /**
     * @dataProvider providerDISC
     */
    public function testDISC()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'DISC'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerDISC()
    {
        return require 'data/Calculation/Financial/DISC.php';
    }

    /**
     * @dataProvider providerDOLLARDE
     */
    public function testDOLLARDE()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'DOLLARDE'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerDOLLARDE()
    {
        return require 'data/Calculation/Financial/DOLLARDE.php';
    }

    /**
     * @dataProvider providerDOLLARFR
     */
    public function testDOLLARFR()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'DOLLARFR'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerDOLLARFR()
    {
        return require 'data/Calculation/Financial/DOLLARFR.php';
    }

    /**
     * @dataProvider providerEFFECT
     */
    public function testEFFECT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'EFFECT'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerEFFECT()
    {
        return require 'data/Calculation/Financial/EFFECT.php';
    }

    /**
     * @dataProvider providerFV
     */
    public function testFV()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'FV'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerFV()
    {
        return require 'data/Calculation/Financial/FV.php';
    }

    /**
     * @dataProvider providerFVSCHEDULE
     */
    public function testFVSCHEDULE()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'FVSCHEDULE'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerFVSCHEDULE()
    {
        return require 'data/Calculation/Financial/FVSCHEDULE.php';
    }

    /**
     * @dataProvider providerINTRATE
     */
    public function testINTRATE()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'INTRATE'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerINTRATE()
    {
        return require 'data/Calculation/Financial/INTRATE.php';
    }

    /**
     * @dataProvider providerIPMT
     */
    public function testIPMT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'IPMT'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIPMT()
    {
        return require 'data/Calculation/Financial/IPMT.php';
    }

    /**
     * @dataProvider providerIRR
     */
    public function testIRR()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'IRR'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerIRR()
    {
        return require 'data/Calculation/Financial/IRR.php';
    }

    /**
     * @dataProvider providerISPMT
     */
    public function testISPMT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'ISPMT'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerISPMT()
    {
        return require 'data/Calculation/Financial/ISPMT.php';
    }

    /**
     * @dataProvider providerMIRR
     */
    public function testMIRR()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'MIRR'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerMIRR()
    {
        return require 'data/Calculation/Financial/MIRR.php';
    }

    /**
     * @dataProvider providerNOMINAL
     */
    public function testNOMINAL()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'NOMINAL'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerNOMINAL()
    {
        return require 'data/Calculation/Financial/NOMINAL.php';
    }

    /**
     * @dataProvider providerNPER
     */
    public function testNPER()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'NPER'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerNPER()
    {
        return require 'data/Calculation/Financial/NPER.php';
    }

    /**
     * @dataProvider providerNPV
     */
    public function testNPV()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'NPV'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerNPV()
    {
        return require 'data/Calculation/Financial/NPV.php';
    }

    /**
     * @dataProvider providerPRICE
     * @group fail19
     */
    public function testPRICE()
    {
        $this->markTestIncomplete('TODO: This test should be fixed');

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'PRICE'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerPRICE()
    {
        return require 'data/Calculation/Financial/PRICE.php';
    }

    /**
     * @dataProvider providerRATE
     * @group fail19
     */
    public function testRATE()
    {
        $this->markTestIncomplete('TODO: This test should be fixed');

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'RATE'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerRATE()
    {
        return require 'data/Calculation/Financial/RATE.php';
    }

    /**
     * @dataProvider providerXIRR
     * @group fail19
     */
    public function testXIRR()
    {
        $this->markTestIncomplete('TODO: This test should be fixed');

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Financial::class, 'XIRR'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerXIRR()
    {
        return require 'data/Calculation/Financial/XIRR.php';
    }
}
