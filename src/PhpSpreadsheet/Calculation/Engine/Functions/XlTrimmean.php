<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages\Mean;

/**
 * @inheritDoc
 */
class XlTrimmean extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'TRIMMEAN';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Mean::class, 'trim'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
