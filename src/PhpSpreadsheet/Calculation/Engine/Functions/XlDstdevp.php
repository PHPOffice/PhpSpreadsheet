<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Database\DStDevP;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;

/**
 * @inheritDoc
 */
class XlDstdevp extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'DSTDEVP';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_DATABASE;

    /**
     * @var callable
     */
    protected $functionCall = [DStDevP::class, 'evaluate'];

    /**
     * @var string
     */
    protected $argumentCount = '3';
}
