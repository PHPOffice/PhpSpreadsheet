<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertOctal;

/**
 * @inheritDoc
 */
class XlOct2bin extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'OCT2BIN';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_ENGINEERING;

    /**
     * @var callable
     */
    protected $functionCall = [ConvertOctal::class, 'toBinary'];

    /**
     * @var string
     */
    protected $argumentCount = '1,2';
}
