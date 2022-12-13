<?php

namespace App\Helper;

class Common
{
    public static function priceFormat($price)
    {
        return number_format((float)$price, 2, '.', '');
    }

}
