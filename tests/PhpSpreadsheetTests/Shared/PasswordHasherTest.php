<?php

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Shared\PasswordHasher;
use PHPUnit_Framework_TestCase;

class PasswordHasherTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerHashPassword
     * @group fail19
     *
     * @param mixed $expectedResult
     */
    public function testHashPassword($expectedResult, ...$args)
    {
        $result = PasswordHasher::hashPassword(...$args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerHashPassword()
    {
        return require 'data/Shared/PasswordHashes.php';
    }
}
