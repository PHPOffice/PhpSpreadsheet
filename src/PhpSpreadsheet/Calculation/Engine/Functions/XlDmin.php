<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Database\DMin;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;

/**
 * @inheritDoc
 */
class XlDmin extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'DMIN';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_DATABASE;

    /**
     * @var callable
     */
    protected $functionCall = [DMin::class, 'evaluate'];

    /**
     * @var string
     */
    protected $argumentCount = '3';
}
