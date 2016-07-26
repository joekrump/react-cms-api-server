<?php

namespace App\Helpers;

class UnitConversionHelper
{
    public static function dollarsToCents($dollars)
    {
        if(is_string($dollars)){
          $dollars = floatval($dollars);
        }
        // amount sent to Stripe must be sent in cents. Value entered will initially be in Dollars to multiply by 100
        return $dollars * 100;
    }
}