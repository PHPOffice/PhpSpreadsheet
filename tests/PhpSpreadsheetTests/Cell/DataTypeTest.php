<?php

namespace PhpSpreadsheetTests\Cell;

use PhpSpreadsheet\Cell\DataType;

class DataTypeTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!defined('PHPSPREADSHEET_ROOT')) {
            define('PHPSPREADSHEET_ROOT', APPLICATION_PATH . '/');
        }
        require_once PHPSPREADSHEET_ROOT . '/Bootstrap.php';
    }

    public function testGetErrorCodes()
    {
        $result = call_user_func([DataType::class, 'getErrorCodes']);
        $this->assertInternalType('array', $result);
        $this->assertGreaterThan(0, count($result));
        $this->assertArrayHasKey('#NULL!', $result);
    }
}
