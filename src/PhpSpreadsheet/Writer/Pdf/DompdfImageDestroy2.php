<?php

namespace Dompdf\Adapter;

if (!function_exists(__NAMESPACE__ . '\imagedestroy')) {
    function imagedestroy(): void
    {
    }
}
