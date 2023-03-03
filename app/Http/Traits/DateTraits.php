<?php

namespace App\Http\Traits;

trait DateTraits
{


    /**
     * @param  string|null  $month
     * @param  bool  $endYear
     * @return int
     */
    public function validMonth(?string $month , bool $endYear = false) : int
    {
        if (is_null($month)) {
            return $endYear === true ? 12 : 1;
        }

        return $month >= 1 && $month <= 12 ? (int) $month : 1;
    }


}
