<?php

namespace PhpSpreadsheetTests\Shared;

use PhpSpreadsheet\Shared\PasswordHasher;

class PasswordHasherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerHashPassword
     * @group fail19
     */
    public function testHashPassword()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([PasswordHasher::class, 'hashPassword'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerHashPassword()
    {
        return require 'data/Shared/PasswordHashes.php';
    }
}
