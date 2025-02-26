<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Helper;

use PhpOffice\PhpSpreadsheet\Helper\Handler;
use PHPUnit\Framework\TestCase;
use Throwable;

class HandlerTest extends TestCase
{
    public function testSuppressed(): void
    {
        self::assertTrue(Handler::suppressed());
    }

    public function testDeprecated(): void
    {
        try {
            Handler::deprecated();
            self::fail('Expected error/exception did not happen');
        } catch (Throwable $e) {
            self::assertStringContainsString('Invalid characters', $e->getMessage());
        }
    }

    public function testNotice(): void
    {
        try {
            Handler::notice('invalidtz');
            self::fail('Expected error/exception did not happen');
        } catch (Throwable $e) {
            self::assertStringContainsString('Timezone', $e->getMessage());
        }
    }

    public function testWarning(): void
    {
        try {
            Handler::warning();
            self::fail('Expected error/exception did not happen');
        } catch (Throwable $e) {
            self::assertStringContainsString('ailed to open stream', $e->getMessage());
        }
    }

    public function testUserDeprecated(): void
    {
        try {
            Handler::userDeprecated();
            self::fail('Expected error/exception did not happen');
        } catch (Throwable $e) {
            self::assertStringContainsString('hello', $e->getMessage());
        }
    }

    public function testUserNotice(): void
    {
        try {
            Handler::userNotice();
            self::fail('Expected error/exception did not happen');
        } catch (Throwable $e) {
            self::assertStringContainsString('userNotice', $e->getMessage());
        }
    }

    public function testUserWarning(): void
    {
        try {
            Handler::userWarning();
            self::fail('Expected error/exception did not happen');
        } catch (Throwable $e) {
            self::assertStringContainsString('userWarning', $e->getMessage());
        }
    }
}
