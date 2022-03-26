<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Database\DGet;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;

/**
 * @inheritDoc
 */
class XlDget extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'DGET';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_DATABASE;

    /**
     * @var callable
     */
    protected $functionCall = [DGet::class, 'evaluate'];

    /**
     * @var string
     */
    protected $argumentCount = '3';
}
