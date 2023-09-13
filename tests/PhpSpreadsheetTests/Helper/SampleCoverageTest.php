<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Helper;

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \PhpOffice\PhpSpreadsheet\Helper\Sample
 */
class SampleCoverageTest extends TestCase
{
    public function testSample(): void
    {
        $helper = new Sample();
        $samples = $helper->getSamples();
        self::assertArrayHasKey('Basic', $samples);
        $basic = $samples['Basic'];
        self::assertArrayHasKey('02 Types', $basic);
        self::assertSame('Basic/02_Types.php', $basic['02 Types']);
        self::assertSame('phpunit', $helper->getPageTitle());
        self::assertSame('<h1>phpunit</h1>', $helper->getPageHeading());
    }

    public function testDirectoryFail(): void
    {
        $this->expectException(RuntimeException::class);

        $helper = $this->getMockBuilder(Sample::class)
            ->onlyMethods(['isDirOrMkdir'])
            ->getMock();
        $helper->expects(self::once())
            ->method('isDirOrMkdir')
            ->with(self::isType('string'))
            ->willReturn(false);
        self::assertSame('', $helper->getFilename('a.xlsx'));
    }
}
