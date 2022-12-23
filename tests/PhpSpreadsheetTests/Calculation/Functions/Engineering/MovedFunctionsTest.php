<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheetTests\Custom\ComplexAssert;
use PHPUnit\Framework\TestCase;

// Sanity tests for functions which have been moved out of Engineering
// to their own classes. A deprecated version remains in Engineering;
// this class contains cursory tests to ensure that those work properly.
// If Scrutinizer fails the PR because of these deprecations, I will
// remove this class from the PR.

class MovedFunctionsTest extends TestCase
{
    public function testMovedFunctions(): void
    {
        self::assertEquals(178, /** @scrutinizer ignore-deprecated */ Engineering::BINTODEC(10110010));
        self::assertEquals('B2', /** @scrutinizer ignore-deprecated */ Engineering::BINTOHEX(10110010));
        self::assertEquals(144, /** @scrutinizer ignore-deprecated */ Engineering::BINTOOCT(1100100));
        self::assertEquals(101100101, /** @scrutinizer ignore-deprecated */ Engineering::DECTOBIN(357));
        self::assertEquals(165, /** @scrutinizer ignore-deprecated */ Engineering::DECTOHEX(357));
        self::assertEquals(545, /** @scrutinizer ignore-deprecated */ Engineering::DECTOOCT(357));
        self::assertEquals(1100100, /** @scrutinizer ignore-deprecated */ Engineering::HEXTOBIN(64));
        self::assertEquals(357, /** @scrutinizer ignore-deprecated */ Engineering::HEXTODEC(165));
        self::assertEquals(653, /** @scrutinizer ignore-deprecated */ Engineering::HEXTOOCT('01AB'));
        self::assertEquals(1100100, /** @scrutinizer ignore-deprecated */ Engineering::OCTTOBIN(144));
        self::assertEquals(357, /** @scrutinizer ignore-deprecated */ Engineering::OCTTODEC(545));
        self::assertEquals('1AB', /** @scrutinizer ignore-deprecated */ Engineering::OCTTOHEX(653));
    }

    public function testOthers(): void
    {
        self::assertEqualsWithDelta(30596.33413506702, /** @scrutinizer ignore-deprecated */ Engineering::BESSELI(-12.5, 0), 1E-8);
        self::assertEqualsWithDelta(0.146884054700421, /** @scrutinizer ignore-deprecated */ Engineering::BESSELJ(-12.5, 0), 1E-8);
        self::assertEqualsWithDelta(2.20786908479938, /** @scrutinizer ignore-deprecated */ Engineering::BESSELK(0.125, 0), 1E-8);
        self::assertEqualsWithDelta(-1.38968063456627, /** @scrutinizer ignore-deprecated */ Engineering::BESSELY(0.125, 0), 1E-8);
        self::assertEqualsWithDelta(0.0, /** @scrutinizer ignore-deprecated */ Engineering::DELTA(-0.75, -1.5), 1E-8);
        self::assertEqualsWithDelta(0.0112834155558496, /** @scrutinizer ignore-deprecated */ Engineering::ERF(0.01), 1E-8);
        self::assertEqualsWithDelta(0.98871658444415, /** @scrutinizer ignore-deprecated */ Engineering::ERFC(0.01), 1E-8);
        self::assertEqualsWithDelta(0.0112834155558496, /** @scrutinizer ignore-deprecated */ Engineering::ERFPRECISE(0.01), 1E-8);
        self::assertEqualsWithDelta(1.0, /** @scrutinizer ignore-deprecated */ Engineering::GESTEP(-0.75, -1.5), 1E-8);
    }

    public function testConversions(): void
    {
        self::assertEqualsWithDelta(1.942559385723E-03, /** @scrutinizer ignore-deprecated */ Engineering::CONVERTUOM(1.0, 'ozm', 'sg'), 1E-8);
        self::assertContains('Temperature', /** @scrutinizer ignore-deprecated */ Engineering::getConversionGroups());
        self::assertArrayHasKey('Weight and Mass', /** @scrutinizer ignore-deprecated */ Engineering::getConversionGroupUnits());
        self::assertEquals('Degrees Celsius', /** @scrutinizer ignore-deprecated */ Engineering::getConversionGroupUnitDetails('Temperature')['Temperature'][0]['description']);
        self::assertEquals('yotta', /** @scrutinizer ignore-deprecated */ Engineering::getConversionMultipliers()['Y']['name']);
        self::assertEquals(1024, /** @scrutinizer ignore-deprecated */ Engineering::getBinaryConversionMultipliers()['ki']['multiplier']);
    }

    public function testImaginary(): void
    {
        $complexAssert = new ComplexAssert();
        $complexAssert->setDelta(1.0E-8);
        self::assertSame('3+4i', /** @scrutinizer ignore-deprecated */ Engineering::COMPLEX(3, 4));
        self::assertEqualsWithDelta(5.67, /** @scrutinizer ignore-deprecated */ Engineering::IMAGINARY('12.34+5.67j'), 1E-8);
        self::assertEqualsWithDelta(12.34, /** @scrutinizer ignore-deprecated */ Engineering::IMREAL('12.34+5.67j'), 1E-8);
        self::assertEqualsWithDelta(13.58029822942, /** @scrutinizer ignore-deprecated */ Engineering::IMABS('12.34+5.67j'), 1E-8);
        self::assertEqualsWithDelta(0.43071059555, /** @scrutinizer ignore-deprecated */ Engineering::IMARGUMENT('12.34+5.67j'), 1E-8);
        $complexAssert->runAssertComplexEquals('12.34-5.67j', /** @scrutinizer ignore-deprecated */ Engineering::IMCONJUGATE('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('141.319179436356+32.547610312508j', /** @scrutinizer ignore-deprecated */ Engineering::IMCOS('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('93502.0563713182121-65794.6618967782119j', /** @scrutinizer ignore-deprecated */ Engineering::IMCOSH('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('93502.0563713182121-65794.6618967782119j', /** @scrutinizer ignore-deprecated */ Engineering::IMCOSH('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('-0.0000104004141424230319-1.00002138037057154j', /** @scrutinizer ignore-deprecated */ Engineering::IMCOT('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('-0.00154774455592154432-0.00671986631601416928j', /** @scrutinizer ignore-deprecated */ Engineering::IMCSC('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('7.15308425027293823E-6+5.03341614148979354E-6j', /** @scrutinizer ignore-deprecated */ Engineering::IMCSCH('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('0.0961415519586104-0.00694248653276682j', /** @scrutinizer ignore-deprecated */ Engineering::IMDIV('12.34+5.67j', '123.45+67.89j'));
        $complexAssert->runAssertComplexEquals('187004.11273906-131589.323796073j', /** @scrutinizer ignore-deprecated */ Engineering::IMEXP('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('2.60862008281875+0.430710595550204j', /** @scrutinizer ignore-deprecated */ Engineering::IMLN('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('3.76344325733562+0.621384040306436j', /** @scrutinizer ignore-deprecated */ Engineering::IMLOG2('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('1.13290930735019+0.187055234944717j', /** @scrutinizer ignore-deprecated */ Engineering::IMLOG10('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('120.1267+139.9356j', /** @scrutinizer ignore-deprecated */ Engineering::IMPOWER('12.34+5.67j', 2));
        $complexAssert->runAssertComplexEquals('6454.936089+8718.895647i', /** @scrutinizer ignore-deprecated */ Engineering::IMPRODUCT('12.34+5.67i', '123.45+67.89i', '5.67'));
        $complexAssert->runAssertComplexEquals('0.00671973874162309199-0.00154764157870523791j', /** @scrutinizer ignore-deprecated */ Engineering::IMSEC('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('7.15308425036177674E-6+5.03341614116724074E-6j', /** @scrutinizer ignore-deprecated */ Engineering::IMSECH('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('-32.5483841590412+141.315819535092j', /** @scrutinizer ignore-deprecated */ Engineering::IMSIN('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('93502.0563677416700-65794.6618992949199j', /** @scrutinizer ignore-deprecated */ Engineering::IMSINH('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('3.60002071031685+0.787495469644252j', /** @scrutinizer ignore-deprecated */ Engineering::IMSQRT('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('-111.11-62.22j', /** @scrutinizer ignore-deprecated */ Engineering::IMSUB('12.34+5.67j', '123.45+67.89j'));
        $complexAssert->runAssertComplexEquals('135.79+73.56j', /** @scrutinizer ignore-deprecated */ Engineering::IMSUM('12.34+5.67j', '123.45+67.89j'));
        $complexAssert->runAssertComplexEquals('-0.0000103999694261435177+0.999978619978377253j', /** @scrutinizer ignore-deprecated */ Engineering::IMTAN('12.34+5.67j'));
    }
}
