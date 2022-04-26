<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Extract;

/**
 * @inheritDoc
 */
class XlMidb extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'MIDB';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_TEXT_AND_DATA;

    /**
     * @var callable
     */
    protected $functionCall = [Extract::class, 'mid'];

    /**
     * @var string
     */
    protected $argumentCount = '3';
}
