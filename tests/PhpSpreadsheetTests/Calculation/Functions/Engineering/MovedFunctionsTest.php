<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheetTests\Custom\ComplexAssert;
use PHPUnit\Framework\TestCase;

// Sanity tests for functions which have been moved out of Engineering
// to their own classes. A deprecated version remains in Engineering;
// this class contains cursory tests to ensure that those work properly.

class MovedFunctionsTest extends TestCase
{
    public function testMovedFunctions(): void
    {
        self::assertEquals(178, Engineering::BINTODEC(10110010));
        self::assertEquals('B2', Engineering::BINTOHEX(10110010));
        self::assertEquals(144, Engineering::BINTOOCT(1100100));
        self::assertEquals(101100101, Engineering::DECTOBIN(357));
        self::assertEquals(165, Engineering::DECTOHEX(357));
        self::assertEquals(545, Engineering::DECTOOCT(357));
        self::assertEquals(1100100, Engineering::HEXTOBIN(64));
        self::assertEquals(357, Engineering::HEXTODEC(165));
        self::assertEquals(653, Engineering::HEXTOOCT('01AB'));
        self::assertEquals(1100100, Engineering::OCTTOBIN(144));
        self::assertEquals(357, Engineering::OCTTODEC(545));
        self::assertEquals('1AB', Engineering::OCTTOHEX(653));
    }

    public function testOthers(): void
    {
        self::assertEqualsWithDelta(30596.33413506702, Engineering::BESSELI(-12.5, 0), 1E-8);
        self::assertEqualsWithDelta(0.146884054700421, Engineering::BESSELJ(-12.5, 0), 1E-8);
        self::assertEqualsWithDelta(2.20786908479938, Engineering::BESSELK(0.125, 0), 1E-8);
        self::assertEqualsWithDelta(-1.38968063456627, Engineering::BESSELY(0.125, 0), 1E-8);
        self::assertEqualsWithDelta(0.0, Engineering::DELTA(-0.75, -1.5), 1E-8);
        self::assertEqualsWithDelta(0.0112834155558496, Engineering::ERF(0.01), 1E-8);
        self::assertEqualsWithDelta(0.98871658444415, Engineering::ERFC(0.01), 1E-8);
        self::assertEqualsWithDelta(0.0112834155558496, Engineering::ERFPRECISE(0.01), 1E-8);
        self::assertEqualsWithDelta(1.0, Engineering::GESTEP(-0.75, -1.5), 1E-8);
    }

    public function testConversions(): void
    {
        self::assertEqualsWithDelta(1.942559385723E-03, Engineering::CONVERTUOM(1.0, 'ozm', 'sg'), 1E-8);
        self::assertContains('Temperature', Engineering::getConversionGroups());
        self::assertArrayHasKey('Weight and Mass', Engineering::getConversionGroupUnits());
        self::assertEquals('Degrees Celsius', Engineering::getConversionGroupUnitDetails('Temperature')['Temperature'][0]['description']);
        self::assertEquals('yotta', Engineering::getConversionMultipliers()['Y']['name']);
        self::assertEquals(1024, Engineering::getBinaryConversionMultipliers()['ki']['multiplier']);
    }

    public function testImaginary(): void
    {
        $complexAssert = new ComplexAssert();
        $complexAssert->setDelta(1.0E-8);
        self::assertSame('3+4i', Engineering::COMPLEX(3, 4));
        self::assertEqualsWithDelta(5.67, Engineering::IMAGINARY('12.34+5.67j'), 1E-8);
        self::assertEqualsWithDelta(12.34, Engineering::IMREAL('12.34+5.67j'), 1E-8);
        self::assertEqualsWithDelta(13.58029822942, Engineering::IMABS('12.34+5.67j'), 1E-8);
        self::assertEqualsWithDelta(0.43071059555, Engineering::IMARGUMENT('12.34+5.67j'), 1E-8);
        $complexAssert->runAssertComplexEquals('12.34-5.67j', Engineering::IMCONJUGATE('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('141.319179436356+32.547610312508j', Engineering::IMCOS('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('93502.0563713182121-65794.6618967782119j', Engineering::IMCOSH('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('93502.0563713182121-65794.6618967782119j', Engineering::IMCOSH('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('-0.0000104004141424230319-1.00002138037057154j', Engineering::IMCOT('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('-0.00154774455592154432-0.00671986631601416928j', Engineering::IMCSC('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('7.15308425027293823E-6+5.03341614148979354E-6j', Engineering::IMCSCH('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('0.0961415519586104-0.00694248653276682j', Engineering::IMDIV('12.34+5.67j', '123.45+67.89j'));
        $complexAssert->runAssertComplexEquals('187004.11273906-131589.323796073j', Engineering::IMEXP('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('2.60862008281875+0.430710595550204j', Engineering::IMLN('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('3.76344325733562+0.621384040306436j', Engineering::IMLOG2('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('1.13290930735019+0.187055234944717j', Engineering::IMLOG10('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('120.1267+139.9356j', Engineering::IMPOWER('12.34+5.67j', 2));
        $complexAssert->runAssertComplexEquals('6454.936089+8718.895647i', Engineering::IMPRODUCT('12.34+5.67i', '123.45+67.89i', '5.67'));
        $complexAssert->runAssertComplexEquals('0.00671973874162309199-0.00154764157870523791j', Engineering::IMSEC('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('7.15308425036177674E-6+5.03341614116724074E-6j', Engineering::IMSECH('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('-32.5483841590412+141.315819535092j', Engineering::IMSIN('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('93502.0563677416700-65794.6618992949199j', Engineering::IMSINH('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('3.60002071031685+0.787495469644252j', Engineering::IMSQRT('12.34+5.67j'));
        $complexAssert->runAssertComplexEquals('-111.11-62.22j', Engineering::IMSUB('12.34+5.67j', '123.45+67.89j'));
        $complexAssert->runAssertComplexEquals('135.79+73.56j', Engineering::IMSUM('12.34+5.67j', '123.45+67.89j'));
        $complexAssert->runAssertComplexEquals('-0.0000103999694261435177+0.999978619978377253j', Engineering::IMTAN('12.34+5.67j'));
    }
}
