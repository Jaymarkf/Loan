<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    //

    public function personalInformations()
    {
        return $this->hasMany(PersonalInformation::class, 'municipalities_id');
    }
}
