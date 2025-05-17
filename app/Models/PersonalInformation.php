<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalInformation extends Model
{
    use HasFactory;

    // Table name (Laravel automatically uses snake_case and plurala form)
    protected $table = 'personal_informations';

    // Enable timestamps (the 'created_at' and 'updated_at' columns)
    public $timestamps = true;

    // Define the fillable fields (to allow mass assignment)
    protected $fillable = [
        'member_id',
        'external_id',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'birthday',
        'civil_status',
        'house_status',
        'name_on_check',
        'employment_date',
        'contributions_percentage',
        'tin_number',
        'phone_number_1',
        'phone_number_2',
        'address_1',
        'regions_id',
        'provinces_id',
        'municipalities_id',
        'barangays_id',
        'countries_id',
        'employee_number',
        'employee_status',
        'college_or_department',
        'photo',
        'signature',
    ];


    public function member()
        {
            return $this->belongsTo(Member::class, 'member_id', 'id');
        }
    // Optional: Define relationships if you have foreign keys (e.g., municipality, barangay)
    public function city()
    {
        return $this->belongsTo(City::class, 'municipalities_id');
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'barangays_id');
    }

    // You can also define relationships for other tables if necessary, like regions, provinces, etc.
    // Example:
    public function region()
    {
        return $this->belongsTo(Region::class, 'regions_id');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'provinces_id');
    }
    

    public function country()
    {
        return $this->belongsTo(Country::class, 'countries_id');
    }
}
