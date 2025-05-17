<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\PersonalInformation;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\Region;
use App\Models\Province;

class MasterLists extends Controller
{
    //
    public function fetchLoans(){
        $data = [
            "SECRET" => "COOPAPP_22221123123",
            "API_KEY" => "aTvYImp99i"
        ];

        $response = Http::asForm()->post('https://bsu-mpcm.com/api/v1/get_unsynced_loans', $data);

        if ($response->successful()) {
            $responseData = $response->json(); 
           dd($responseData);
        } else {
            return response()->json(['error' => 'Request failed'], 500);
        }
    }
    public function fetchMember(){
        $data = [
            "SECRET" => "COOPAPP_22221123123",
            "API_KEY" => "aTvYImp99i"
        ];

        $response = Http::asForm()->post('https://bsu-mpcm.com/api/v1/get_unsynced_members', $data);

        if ($response->successful()) {
            $responseData = $response->json(); 
            return $responseData;
        } else {
            return response()->json(['error' => 'Request failed'], 500);
        }
    }

    public function debugMember(){
        $dd = $this->fetchMember();
       dd($dd);
    }
    function showMember(){
        $members = PersonalInformation::with('member')->get();
        $regions = Region::all();
        $provinces = Province::all();
        return view('master-lists.members-information',compact('members','regions','provinces'));
    }
function syncProfile()
{
    $data = $this->fetchMember(); // from API

    try {
        DB::transaction(function () use ($data) {
            $memberIdMap = [];

            foreach ($data as $member) {
                // Check if member already exists based on unique email or member_id
                $existing = DB::table('members')->where('email_address', $member['email_address'])->first();

                if ($existing) {
                    throw new \Exception('Member has already been synchronized.');
                }

                $internalId = DB::table('members')->insertGetId([
                    'member_id'         => $member['member_id'],
                    'status'            => $member['status'],
                    'email_address'     => $member['email_address'],
                    'member_since_date' => $member['member_since_date'],
                    'is_synced'         => $member['is_synced'],
                ]);

                $memberIdMap[$member['id']] = $internalId;
            }

            // Insert profile_informations
            $profileRows = array_map(function ($member) use ($memberIdMap) {
                $externalId = $member['id'];
                $internalId = $memberIdMap[$externalId];
                $profile = $member['profile'];

                return [
                    'member_id'            => $internalId,
                    'external_member_id'   => $externalId,
                    'first_name'           => $profile['first_name'],
                    'middle_name'          => $profile['middle_name'] ?? null,
                    'last_name'            => $profile['last_name'],
                    'gender'               => $profile['gender'],
                    'birthday'             => $profile['birthday'],
                    'civil_status'         => $profile['civil_status'],
                    'house_status'         => $profile['house_status'],
                    'name_on_check'        => $profile['name_on_check'],
                    'employment_date'      => $profile['employment_date'],
                    'contributions_percentage' => $profile['contributions_percentage'],
                    'tin_number'           => $profile['tin_number'],
                    'phone_number_1'       => $profile['phone_number_1'],
                    'phone_number_2'       => $profile['phone_number_2'],
                    'address_1'            => $profile['address_1'],
                    'regions_id'           => $profile['regions_id'],
                    'provinces_id'         => $profile['provinces_id'],
                    'municipalities_id'    => $profile['cities_id'],
                    'barangays_id'         => $profile['barangays_id'],
                    'countries_id'         => $profile['countries_id'],
                    'employee_number'      => $profile['employee_number'],
                    'employee_status'      => $profile['employee_status'],
                    'college_or_department'=> $profile['college_or_department'],
                    'photo'                => $profile['photo'],
                    'signature'            => $profile['signature'],
                ];
            }, $data);

            DB::table('personal_informations')->insert($profileRows);

            $subRows = array_map(function ($member) use ($memberIdMap) {
                $externalId = $member['id'];
                $internalId = $memberIdMap[$externalId];

                return array_map(function ($sub) use ($internalId) {
                    return [
                        'members_id'        => $internalId,
                        'information_type'  => $sub['information_type'],
                        'sub_information'   => json_encode($sub['sub_information'], JSON_UNESCAPED_UNICODE),
                    ];
                }, $member['sub_information']);
            }, $data);

            $subRows = array_merge(...$subRows);
            DB::table('sub_informations')->insert($subRows);
        });

        return redirect()->route('members-information')->with('success', 'Synchronization successful.');
    } catch (\Exception $e) {
        return redirect()->route('members-information')->with('error', 'Member has already been synchronized.');
    }
}

     public function getPersonInfo($person)
    {
        // Search members by name (adjust columns as needed)
        $results = PersonalInformation::join('members', 'personal_informations.member_id', '=', 'members.id')
        ->where('name_on_check', 'LIKE', "%{$person}%")
        ->where('members.status', 'approved')
        ->select('personal_informations.id', 'personal_informations.name_on_check as name')
        ->limit(10)
        ->get();

        return response()->json($results);
    }
    
    function getPerson($personId) {
        $person = PersonalInformation::with(['region','province', 'city', 'barangay'])->findOrFail($personId);
        $pp = [
            'id'=>$person->id,
            'first_name'=>$person->first_name,
            'middle_name'=>$person->middle_name,
            'last_name'=>$person->last_name,
            'gender'=>$person->gender,
            'birthday'=>$person->birthday,
            'civil_status'=>$person->civil_status,
            'house_status'=>$person->house_status,
            'name_on_check'=>$person->name_on_check,
            'employment_date'=>$person->employment_date,
            'contributions_percentage'=>$person->contributions_percentage,
            'tin_number'=>$person->tin_number,
            'phone_number_1'=>$person->phone_number_1,
            'phone_number_2'=>$person->phone_number_2,
            'address_1'=>$person->address_1,
            'regions_id'=>$person->region->reg_desc,
            'provinces_id'=>$person->province->prov_desc,
            'municipalities_id'=>$person->city->city_mun_desc,
            'barangays_id'=>$person->barangay->brgy_desc,
            'countries_id'=>$person->country->en_short_name,
            'employee_number'=>$person->employee_number,
            'employee_status'=>$person->employee_status,
            'college_or_department'=>$person->college_or_department,
            'photo'=>$person->photo,
            'signature'=>$person->signature,
            'region_entity_id' => $person->regions_id,
            'province_entity_id' => $person->provinces_id,
            'municipality_entity_id' => $person->municipalities_id,
            'barangay_entity_id' => $person->barangays_id
        ];
  
        return response()->json($pp);
    }
    public function updatePerson(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female,other',
            'birthday' => 'required|date',
            'civil_status' => 'nullable|string|max:100',
            'house_status' => 'nullable|string|max:100',
            'name_on_check' => 'nullable|string|max:255',
            'employment_date' => 'nullable|date',
            'contributions_percentage' => 'nullable|numeric|min:0|max:100',
            'tin_number' => 'nullable|string|max:20',
            'phone_number_1' => 'nullable|string|max:20',
            'phone_number_2' => 'nullable|string|max:20',
            'address_1' => 'nullable|string|max:255',
            'regions_id' => 'nullable|integer',
            'provinces_id' => 'nullable|integer',
            'municipalities_id' => 'nullable|integer',
            'barangays_id' => 'nullable|integer',
            'countries_id' => 'nullable|integer',
            'employee_number' => 'nullable|string|max:50',
            'employee_status' => 'nullable|string|max:100',
            'college_or_department' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'signature' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
            DB::beginTransaction();
        try {
            $person = PersonalInformation::find($request->id);

            if (!$person) {
                return redirect()->back()->withErrors(['error' => 'Personal information not found.']);
            }
          
            $person->fill($validated); // This only works if fillable is defined properly in the model
    
            // Optional: handle photo upload
            if ($request->hasFile('photo')) {
                $filename = 'photo_' . uniqid() . '.' . $request->photo->extension();
                $path = $request->photo->storeAs('photo', $filename, 'public');
                $person->photo = $path;
            }
    
            // Optional: handle signature upload
            if ($request->hasFile('signature')) {
                $filename = 'signature_' . uniqid() . '.' . $request->signature->extension();
                $path = $request->signature->storeAs('signature', $filename, 'public');
                $person->signature = $path;
            }
    
            $person->save();
    
            DB::commit();
    
            return redirect()->back()->with('success', 'Personal information updated successfully.');
    
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update failed: ' . $e->getMessage());
    
            return redirect()->back()->withErrors(['error' => 'Failed to update personal information.']);
        }
    }

    
}
