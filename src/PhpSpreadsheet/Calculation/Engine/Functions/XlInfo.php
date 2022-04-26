<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

/**
 * @inheritDoc
 */
class XlInfo extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'INFO';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_INFORMATION;

    /**
     * @var callable
     */
    protected $functionCall = [Functions::class, 'DUMMY'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
