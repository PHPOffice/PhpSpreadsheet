<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

/**
 * @inheritDoc
 */
class XlCubememberproperty extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'CUBEMEMBERPROPERTY';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_CUBE;

    /**
     * @var callable
     */
    protected $functionCall = [Functions::class, 'DUMMY'];

    /**
     * @var string
     */
    protected $argumentCount = '?';
}
