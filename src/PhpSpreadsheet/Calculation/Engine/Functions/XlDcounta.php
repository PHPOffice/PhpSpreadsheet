<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Database\DCountA;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;

/**
 * @inheritDoc
 */
class XlDcounta extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'DCOUNTA';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_DATABASE;

    /**
     * @var callable
     */
    protected $functionCall = [DCountA::class, 'evaluate'];

    /**
     * @var string
     */
    protected $argumentCount = '3';
}
