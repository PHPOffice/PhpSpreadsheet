<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Database\DVarP;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;

/**
 * @inheritDoc
 */
class XlDvarp extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'DVARP';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_DATABASE;

    /**
     * @var callable
     */
    protected $functionCall = [DVarP::class, 'evaluate'];

    /**
     * @var string
     */
    protected $argumentCount = '3';
}
