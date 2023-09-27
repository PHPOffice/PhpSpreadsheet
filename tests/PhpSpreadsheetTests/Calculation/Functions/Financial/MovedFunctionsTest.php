<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

//use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

// Sanity tests for functions which have been moved out of Financial
// to their own classes. A deprecated version remains in Financial;
// this class contains cursory tests to ensure that those work properly.

class MovedFunctionsTest extends TestCase
{
    public function testMovedFunctions(): void
    {
        self::assertEqualsWithDelta(16.6666666666666, Financial::ACCRINT('2008-03-01', '2008-08-31', '2008-05-01', 0.10, 1000, 2, 0), 1E-8);
        self::assertEqualsWithDelta(20.547945205478999, Financial::ACCRINTM('2008-04-01', '2008-06-15', 0.10, 1000, 3), 1E-8);
        self::assertEqualsWithDelta(776, Financial::AMORDEGRC(2400, '2008-08-19', '2008-12-31', 300, 1, 0.15, 1), 1E-8);
        self::assertEqualsWithDelta(360, Financial::AMORLINC(2400, '2008-08-19', '2008-12-31', 300, 1, 0.15, 1), 1E-8);
        self::assertEqualsWithDelta(71, Financial::COUPDAYBS('25-Jan-2007', '15-Nov-2008', 2, 1), 1E-8);
        self::assertEqualsWithDelta(181, Financial::COUPDAYS('25-Jan-2007', '15-Nov-2008', 2, 1), 1E-8);
        self::assertEqualsWithDelta(110, Financial::COUPDAYSNC('25-Jan-2007', '15-Nov-2008', 2, 1), 1E-8);
        self::assertEqualsWithDelta(39217, Financial::COUPNCD('25-Jan-2007', '15-Nov-2008', 2, 1), 1E-8);
        self::assertEqualsWithDelta(4, Financial::COUPNUM('25-Jan-2007', '15-Nov-2008', 2, 1), 1E-8);
        self::assertEqualsWithDelta(39036, Financial::COUPPCD('25-Jan-2007', '15-Nov-2008', 2, 1), 1E-8);
        self::assertEqualsWithDelta(-11135.232130750999, Financial::CUMIPMT(0.0075, 360, 125000, 13, 24, 0), 1E-8);
        self::assertEqualsWithDelta(-934.10712342088004, Financial::CUMPRINC(0.0075, 360, 125000, 13, 24, 0), 1E-8);
        self::assertEqualsWithDelta(186083.3333333334, Financial::DB(1000000, 100000, 6, 1, 7), 1E-8);
        self::assertEqualsWithDelta(0.13150684931507001, Financial::DDB(2400, 300, 36500, 1), 1E-8);
        self::assertEqualsWithDelta(0.052420213, Financial::DISC('2007-01-25', '2007-06-15', 97.974999999999994, 100, 1), 1E-8);
        self::assertEqualsWithDelta(2.5, Financial::DOLLARDE(1.6, 4), 1E-8);
        self::assertEqualsWithDelta(1.24, Financial::DOLLARFR(1.6, 4), 1E-8);
        self::assertEqualsWithDelta(0.053542667370758003, Financial::EFFECT(0.052499999999999998, 4), 1E-8);
        self::assertEqualsWithDelta(2581.4033740600998, Financial::FV(0.005, 10, -200, -500, 1), 1E-8);
        self::assertEqualsWithDelta(1.3308899999999999, Financial::FVSCHEDULE(1, [0.089999999999999997, 0.11, 0.10000000000000001]), 1E-8);
        self::assertEqualsWithDelta(0.05768, Financial::INTRATE('2008-02-15', '2008-05-15', 1000000, 1014420, 2), 1E-8);
        self::assertEqualsWithDelta(-22.858787457480013, Financial::IPMT(0.0085, 3, 3, 8000), 1E-8);
        self::assertEqualsWithDelta(-0.02124484827341, Financial::IRR([-70000, 12000, 15000, 18000, 21000]), 1E-8);
        self::assertEqualsWithDelta(-66111.111111111, Financial::ISPMT(0.0085, 1, 36, 8000000), 1E-8);
        self::assertEqualsWithDelta(0.12609413036591, Financial::MIRR([-120000, [39000, 30000, 21000, 37000, 46000]], 0.10, 0.12), 1E-8);
        self::assertEqualsWithDelta(0.052500319868356002, Financial::NOMINAL(0.053543, 4), 1E-8);
        self::assertEqualsWithDelta(59.673865674295001, Financial::NPER(0.01, -100, -1000, 10000, 1), 1E-8);
        self::assertEqualsWithDelta(1188.4434123352, Financial::NPV(0.10, -10000, 3000, 4200, 6800), 1E-8);
        self::assertEqualsWithDelta(10.33803507, Financial::PDURATION(0.04, 10000, 15000), 1E-8);
        self::assertEqualsWithDelta(-1037.032089359164, Financial::PMT(0.08 / 12, 10, 10000), 1E-8);
        self::assertEqualsWithDelta(-75.623186008367, Financial::PPMT(0.10 / 12, 1, 2 * 12, 2000), 1E-8);
        self::assertEqualsWithDelta(94.6343616213221, Financial::PRICE('15-Feb-2008', '15-Nov-2017', 0.0575, 0.065, 100, 2, 0), 1E-8);
        self::assertEqualsWithDelta(90.0, Financial::PRICEDISC('01-Apr-2017', '31-Mar-2021', 0.025, 100), 1E-8);
        self::assertEqualsWithDelta(99.98449887555694, Financial::PRICEMAT('15-Feb-2008', '13-Apr-2008', '11-Nov-2007', 0.061, 0.061, 0), 1E-8);
        self::assertEqualsWithDelta(-52990.70632392715, Financial::PV(0.05 / 12, 60, 1000), 1E-8);
        self::assertEqualsWithDelta(0.0077014724882014003, Financial::RATE(48, -200, 8000), 1E-8);
        self::assertEqualsWithDelta(1014584.6544071021, Financial::RECEIVED('15-Feb-2008', '15-May-2008', 1000000, 0.0575, 2), 1E-8);
        self::assertEqualsWithDelta(0.04137974399241062, Financial::RRI(10, 10000, 15000), 1E-8);
        self::assertEqualsWithDelta(1800, Financial::SLN(10000, 1000, 5), 1E-8);
        self::assertEqualsWithDelta(3000, Financial::SYD(10000, 1000, 5, 1), 1E-8);
        self::assertEqualsWithDelta(0.094151494, Financial::TBILLEQ('31-Mar-2008', '1-Jun-2008', 0.0914), 1E-8);
        self::assertEqualsWithDelta(98.45, Financial::TBILLPRICE('31-Mar-2008', '1-Jun-2008', 0.09), 1E-8);
        self::assertEqualsWithDelta(0.09141696292534264, Financial::TBILLYIELD('31-Mar-2008', '1-Jun-2008', 98.45), 1E-8);
        self::assertEqualsWithDelta(0.77868869226873, Financial::XIRR([4000, -46000], ['2015-04-01', '2019-06-27'], 0.1), 1E-8);
        self::assertEqualsWithDelta(772830.7339573108, Financial::XNPV(0.10, [0, 120000, 120000, 120000, 120000, 120000, 120000, 120000, 120000, 120000, 120000], ['2018-06-30', '2018-12-31', '2019-12-31', '2020-12-31', '2021-12-31', '2022-12-31', '2023-12-31', '2024-12-31', '2025-12-31', '2026-12-31', '2027-12-31']), 1E-8);
        self::assertEqualsWithDelta(0.05282257198685834, Financial::YIELDDISC('16-Feb-2008', '1-Mar-2008', 99.795, 100, 2), 1E-8);
        self::assertEqualsWithDelta(0.06095433369153867, Financial::YIELDMAT('15-Mar-2008', '3-Nov-2008', '8-Nov-2007', 0.0625, 100.0123, 0), 1E-8);
    }
}
