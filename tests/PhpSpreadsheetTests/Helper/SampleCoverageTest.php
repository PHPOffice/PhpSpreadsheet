<?php

namespace PhpOffice\PhpSpreadsheetTests\Helper;

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class SampleCoverageTest extends TestCase
{
    public function testSample(): void
    {
        $helper = new Sample();
        $samples = $helper->getSamples();
        self::assertArrayHasKey('Basic', $samples);
        $basic = $samples['Basic'];
        self::assertArrayHasKey('02 Types', $basic);
        self::assertEquals('Basic/02_Types.php', $basic['02 Types']);
        self::assertEquals('phpunit', $helper->getPageTitle());
        self::assertEquals('<h1>phpunit</h1>', $helper->getPageHeading());
    }

    public function testDirectoryFail(): void
    {
        $this->expectException(RuntimeException::class);

        $helper = $this->getMockBuilder(Sample::class)
            ->setMethods(['isDirOrMkdir'])
            ->getMock();
        $helper->expects(self::atMost(1))->method('isDirOrMkdir')->willReturn(false);
        self::assertEquals('', $helper->getFilename('a.xlsx'));
    }
}
