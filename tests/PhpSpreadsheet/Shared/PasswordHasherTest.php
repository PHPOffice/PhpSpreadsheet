<?php

namespace PhpSpreadsheet\Tests\Shared;

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
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Shared/PasswordHashes.data');
    }
}
