<?php

namespace PHPExcel\Shared;

require_once 'testDataFileIterator.php';

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
        $result = call_user_func_array(array('\PHPExcel\Shared\PasswordHasher','hashPassword'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerHashPassword()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Shared/PasswordHashes.data');
    }
}
