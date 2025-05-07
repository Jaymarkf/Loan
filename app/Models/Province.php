<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    //
         public function personalInformations()
        {
            return $this->hasMany(PersonalInformation::class, 'provinces_id');
        }
}
