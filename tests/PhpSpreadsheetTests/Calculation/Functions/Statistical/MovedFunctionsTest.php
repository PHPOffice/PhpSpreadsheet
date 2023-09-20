<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

// Sanity tests for functions which have been moved out of Statistical
// to their own classes. A deprecated version remains in Statistical;
// this class contains cursory tests to ensure that those work properly.

class MovedFunctionsTest extends TestCase
{
    public function testMovedFunctions(): void
    {
        self::assertEqualsWithDelta(1.020408163265, Statistical::AVEDEV([4, 5, 6, 7, 5, 4, 3]), 1E-8);
        self::assertEqualsWithDelta(11, Statistical::AVERAGE([10, 7, 9, 27, 2]), 1E-8);
        self::assertEqualsWithDelta(7.0, Statistical::AVERAGEA([10, 7, 9, 2]), 1E-8);
        self::assertEqualsWithDelta(14000, Statistical::AVERAGEIF([1 => 7000, 14000, 21000, 28000], '<23000'), 1E-8);
        self::assertEqualsWithDelta(0.4059136, Statistical::BETADIST(0.4, 4, 5), 1E-8);
        self::assertEqualsWithDelta(1.862243320728, Statistical::BETAINV(0.52, 3, 4, 1, 3), 1E-8);
        self::assertEqualsWithDelta(0.706399436132, Statistical::BINOMDIST(3, 8, 0.35, true), 1E-8);
        self::assertEqualsWithDelta(0.964294972685, Statistical::CHIDIST(3, 9), 1E-8);
        self::assertEqualsWithDelta(8.383430828608, Statistical::CHIINV(0.3, 7), 1E-8);
        self::assertEqualsWithDelta(0.692951912734, Statistical::CONFIDENCE(0.05, 2.5, 50), 1E-8);
        self::assertEqualsWithDelta(0.997054485502, Statistical::CORREL([3, 2, 4, 5, 6], [9, 7, 12, 15, 17]), 1E-8);
        self::assertEqualsWithDelta(4, Statistical::COUNT(['0.1.A' => 0, '0.2.A' => 2, '0.3.A' => '', '0.4.A' => null, '0.5.A' => 5, '0.6.A' => 6.1]), 1E-8);
        self::assertEqualsWithDelta(5, Statistical::COUNTA(['0.1.A' => 0, '0.2.A' => 2, '0.3.A' => '', '0.4.A' => null, '0.5.A' => 5, '0.6.A' => 6.1]), 1E-8);
        self::assertEqualsWithDelta(2, Statistical::COUNTBLANK(['0.1.A' => 0, '0.2.A' => 2, '0.3.A' => '', '0.4.A' => null, '0.5.A' => 5, '0.6.A' => 6.1]), 1E-8);
        self::assertEqualsWithDelta(2, Statistical::COUNTIF(['apples', 'oranges', 'peaches', 'apples'], 'apples'), 1E-8);
        self::assertEqualsWithDelta(2, Statistical::COUNTIFS(['Y', 'Y', 'N'], '=Y'), 1E-8);
        self::assertEqualsWithDelta(5.2, Statistical::COVAR([3, 2, 4, 5, 6], [9, 7, 12, 15, 17]), 1E-8);
        self::assertEqualsWithDelta(32, Statistical::CRITBINOM(100, 0.3, 0.7), 1E-8);
        self::assertEqualsWithDelta(48, Statistical::DEVSQ(4, 5, 8, 7, 11, 4, 3), 1E-8);
        self::assertEqualsWithDelta(1.353352832366, Statistical::EXPONDIST(0.2, 10, false), 1E-8);
        self::assertEqualsWithDelta(0.001223791709, Statistical::FDIST2(15.2069, 6, 4, false), 1E-8);
        self::assertEqualsWithDelta(-1.472219489583, Statistical::FISHER(-0.9), 1E-8);
        self::assertEqualsWithDelta(-0.197375320225, Statistical::FISHERINV(-0.2), 1E-8);
        self::assertEqualsWithDelta(10.607253086419, Statistical::FORECAST(30, [6, 7, 9, 15, 21], [20, 28, 31, 38, 40]), 1E-8);
        self::assertEqualsWithDelta(1.329340388179, Statistical::GAMMAFunction(2.5), 1E-8);
        self::assertEqualsWithDelta(0.03263913041829, Statistical::GAMMADIST(10.00001131, 9, 2, false), 1E-8);
        self::assertEqualsWithDelta(10.0000111914377, Statistical::GAMMAINV(0.068094, 9, 2), 1E-8);
        self::assertEqualsWithDelta(2.453736570842, Statistical::GAMMALN(4.5), 1E-8);
        self::assertEqualsWithDelta(0.4772498680518, Statistical::GAUSS(2), 1E-8);
        self::assertEqualsWithDelta(5.47698696965696, Statistical::GEOMEAN(4, 5, 8, 7, 11, 4, 3), 1E-8);
        self::assertEqualsWithDelta([[[32618.203773539713], [47729.42261474774], [69841.30085621739], [102197.07337883231], [149542.48674004572], [218821.87621459525]]], Statistical::GROWTH([33100, 47300, 69000, 102000, 150000, 220000], [11, 12, 13, 14, 15, 16]), 1E-8);
        self::assertEqualsWithDelta(5.028375962062, Statistical::HARMEAN(4, 5, 8, 7, 11, 4, 3), 1E-8);
        self::assertEqualsWithDelta(0.3632610939112, Statistical::HYPGEOMDIST(1, 4, 8, 20), 1E-8);
        self::assertEqualsWithDelta(25.0, Statistical::INTERCEPT([5, 10, 15, 20], [12, 9, 6, 3]), 1E-8);
        self::assertEqualsWithDelta(-0.1517996372084, Statistical::KURT([3, 4, 5, 2, 3, 4, 5, 6, 4, 7]), 1E-8);
        self::assertEqualsWithDelta(5, Statistical::LARGE([3, 4, 5, 2, 3, 4, 5, 6, 4, 7], 3), 1E-8);
        self::assertEqualsWithDelta([1.0, 0.0], Statistical::LINEST([1, 2, 3, 4, 5], [1, 2, 3, 4, 5], false, false), 1E-8);
        self::assertEqualsWithDelta([1.000174230092, 1.0], Statistical::LOGEST([1, 2, 3, 4, 5], [1, 10, 100, 1000, 10000], false, false), 1E-8);
        self::assertEqualsWithDelta(4.000025209777, Statistical::LOGINV(0.039084, 3.5, 1.2), 1E-8);
        self::assertEqualsWithDelta(0.0390835557068, Statistical::LOGNORMDIST(4, 3.5, 1.2), 1E-8);
        self::assertEqualsWithDelta(0.0390835557068, Statistical::LOGNORMDIST2(4, 3.5, 1.2, true), 1E-8);
        self::assertEqualsWithDelta(27, Statistical::MAX(10, 7, 9, 27, 2), 1E-8);
        self::assertEqualsWithDelta(10, Statistical::MAXA(10, 7, 9, '17', 2), 1E-8);
        self::assertEqualsWithDelta(2, Statistical::MAXIFS([1, 2, 3], ['Y', 'Y', 'N'], '=Y', ['H', 'H', 'H'], '=H'), 1E-8);
        self::assertEqualsWithDelta(8.0, Statistical::MEDIAN(1, 4.5, 7, 8, 9, 13, 14), 1E-8);
        self::assertEqualsWithDelta(2, Statistical::MIN(10, 7, 9, 27, 2), 1E-8);
        self::assertEqualsWithDelta(-7, Statistical::MINA(10, '-9', -7, '17', 2), 1E-8);
        self::assertEqualsWithDelta(1, Statistical::MINIFS([1, 2, 3], ['Y', 'Y', 'N'], '=Y', ['H', 'H', 'H'], '=H'), 1E-8);
        self::assertEqualsWithDelta(4.1, Statistical::MODE(5.6, 4.1, 4.1, 3, 2, 4.1), 1E-8);
        self::assertEqualsWithDelta(0.05504866037517786, Statistical::NEGBINOMDIST(10, 5, 0.25), 1E-8);
        self::assertEqualsWithDelta(0.05504866037517786, Statistical::NEGBINOMDIST(10, 5, 0.25), 1E-8);
        self::assertEqualsWithDelta(0.9087887802741, Statistical::NORMDIST(42, 40, 1.5, true), 1E-8);
        self::assertEqualsWithDelta(42.000002008416, Statistical::NORMINV(0.908789, 40, 1.5), 1E-8);
        self::assertEqualsWithDelta(0.908788780274, Statistical::NORMSDIST(1.333333333333), 1E-8);
        self::assertEqualsWithDelta(0.164010074676, Statistical::NORMSDIST2(1.333333333333, false), 1E-8);
        self::assertEqualsWithDelta(1.9, Statistical::PERCENTILE([1, 2, 3, 4], 0.3), 1E-8);
        self::assertEqualsWithDelta(0.667, Statistical::PERCENTRANK([1, 2, 3, 4], 3), 1E-8);
        self::assertEqualsWithDelta(20, Statistical::PERMUT(5, 2), 1E-8);
        self::assertEqualsWithDelta(0.12465201948308113, Statistical::POISSON(2, 5, true), 1E-8);
        self::assertEqualsWithDelta(3.5, Statistical::QUARTILE([1, 2, 4, 7, 8, 9, 10, 12], 1), 1E-8);
        self::assertEqualsWithDelta(2, Statistical::RANK(3.5, [7, 3.5, 3.5, 2, 1]), 1E-8);
        self::assertEqualsWithDelta(0.057950191571, Statistical::RSQ([2, 3, 9, 1, 8, 7, 5], [6, 5, 11, 7, 5, 4, 4]), 1E-8);
        self::assertEqualsWithDelta(0.359543071407, Statistical::SKEW([3, 4, 5, 2, 3, 4, 5, 6, 4, 7]), 1E-8);
        self::assertEqualsWithDelta(0.6, Statistical::SLOPE([3, 6, 9, 12], [5, 10, 15, 20]), 1E-8);
        self::assertEqualsWithDelta(3, Statistical::SMALL([1, 4, 8, 3, 7, 12, 54, 8, 23], 2), 1E-8);
        self::assertEqualsWithDelta(1.333333333333, Statistical::STANDARDIZE(42, 40, 1.5), 1E-8);
        self::assertEqualsWithDelta(27.463915719843, Statistical::STDEV([1345, 1301, 1368, 1322, 1310, 1370, 1318, 1350, 1303, 1299]), 1E-8);
        self::assertEqualsWithDelta(0.577350269190, Statistical::STDEVA([true, false, 1]), 1E-8);
        self::assertEqualsWithDelta(26.0545581424825, Statistical::STDEVP([1345, 1301, 1368, 1322, 1310, 1370, 1318, 1350, 1303, 1299]), 1E-8);
        self::assertEqualsWithDelta(0.471404520791, Statistical::STDEVPA([true, false, 1]), 1E-8);
        self::assertEqualsWithDelta(3.305718950210, Statistical::STEYX([2, 3, 9, 1, 8, 7, 5], [6, 5, 11, 7, 5, 4, 4]), 1E-8);
        self::assertEqualsWithDelta(0.027322464988, Statistical::TDIST(1.959999998, 60, 1), 1E-8);
        self::assertEqualsWithDelta(1.960041187127, Statistical::TINV(0.05464, 60), 1E-8);
        $trendExpected = [[
            [133953.33333333334],
            [134971.51515151517],
            [135989.69696969696],
            [137007.87878787878],
            [138026.0606060606],
            [139044.24242424243],
            [140062.42424242425],
            [141080.60606060608],
            [142098.78787878787],
            [143116.9696969697],
            [144135.15151515152],
            [145153.33333333334],
        ]];
        $trendArg1 = [133890, 135000, 135790, 137300, 138130, 139100, 139900, 141120, 141890, 143230, 144000, 145290];
        $trendArg2 = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        self::assertEqualsWithDelta($trendExpected, Statistical::TREND($trendArg1, $trendArg2), 1E-8);
        self::assertEqualsWithDelta(3.777777777778, Statistical::TRIMMEAN([4, 5, 6, 7, 2, 3, 4, 5, 1, 2, 3], 0.2), 1E-8);
        self::assertEqualsWithDelta(754.266666666667, Statistical::VARFunc([1345, 1301, 1368, 1322, 1310, 1370, 1318, 1350, 1303, 1299]), 1E-8);
        self::assertEqualsWithDelta(754.266666666667, Statistical::VARA([1345, 1301, 1368, 1322, 1310, 1370, 1318, 1350, 1303, 1299]), 1E-8);
        self::assertEqualsWithDelta(678.84, Statistical::VARP([1345, 1301, 1368, 1322, 1310, 1370, 1318, 1350, 1303, 1299]), 1E-8);
        self::assertEqualsWithDelta(0.222222222222, Statistical::VARPA([true, false, 1]), 1E-8);
        self::assertEqualsWithDelta(0.929581390070, Statistical::WEIBULL(105, 20, 100, true), 1E-8);
        self::assertEqualsWithDelta(0.090574196851, Statistical::ZTEST([3, 6, 7, 8, 6, 5, 4, 2, 1, 9], 4), 1E-8);
    }
}
