<?php

namespace PhpSpreadsheet\Tests\Cell;

use PHPExcel\Cell\DataType;

class DataTypeTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        if (!defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH . '/');
        }
        require_once(PHPEXCEL_ROOT . '/Bootstrap.php');
    }

    public function testGetErrorCodes()
    {
        $result = call_user_func(array(DataType::class,'getErrorCodes'));
        $this->assertInternalType('array', $result);
        $this->assertGreaterThan(0, count($result));
        $this->assertArrayHasKey('#NULL!', $result);
    }
}
