<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Web\Service;

/**
 * @inheritDoc
 */
class XlEncodeurl extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'ENCODEURL';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_WEB;

    /**
     * @var callable
     */
    protected $functionCall = [Service::class, 'urlEncode'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
