<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

/**
 * @inheritDoc
 */
class XlThaidayofweek extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'THAIDAYOFWEEK';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_DATE_AND_TIME;

    /**
     * @var callable
     */
    protected $functionCall = [Functions::class, 'DUMMY'];

    /**
     * @var string
     */
    protected $argumentCount = '?';
}
