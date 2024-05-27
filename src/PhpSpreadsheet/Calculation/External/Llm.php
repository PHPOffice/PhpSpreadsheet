<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\External;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;

class Llm
{
    use ArrayEnabled;

    
  
    public static function call(string $prompt,string $model="dolphinLama2.3"): string
    {
        return "falue";
    }
}
