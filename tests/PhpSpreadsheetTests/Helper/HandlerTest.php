<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Helper;

use Exception;
use PhpOffice\PhpSpreadsheet\Helper\Handler;
use PHPUnit\Framework\TestCase;

class HandlerTest extends TestCase
{
    public function testSuppressed(): void
    {
        self::assertTrue(Handler::suppressed());
    }

    public function testDeprecated(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/Invalid characters/');

        Handler::deprecated();
    }

    public function testNotice(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/Timezone/');

        Handler::notice('invalidtz');
    }

    public function testWarning(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/ailed to open stream/');

        Handler::warning();
    }

    public function testUserDeprecated(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/hello/');

        Handler::userDeprecated();
    }

    public function testUserNotice(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/userNotice/');

        Handler::userNotice();
    }

    public function testUserWarning(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/userWarning/');

        Handler::userWarning();
    }
}
