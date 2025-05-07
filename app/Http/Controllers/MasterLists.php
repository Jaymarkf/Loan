<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\PersonalInformation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MasterLists extends Controller
{
    //
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
        $members = PersonalInformation::all();
        return view('master-lists.members-information',compact('members'));
    }
    public function syncProfile(){
            $members = $this->fetchMember();

            if (!$members || !is_array($members)) {
                return response()->json(['error' => 'No data found'], 404);
            }
            DB::beginTransaction();
            try {
                foreach ($members as $key => $member) {
                    try {
                        $value = $member['profile'];
                        $photoPath = null;
                        if (!empty($value['photo'])) {
                            try {
                                // Prepend your configured base URL
                                $photoUrl = rtrim(config('app.photo_api'), '/') . '/' . ltrim($value['photo'], '/');
                                $photoContents = file_get_contents($photoUrl);
                                if ($photoContents !== false) {
                                    $photoExtension = pathinfo(parse_url($photoUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                                    $photoName = 'photos/' . uniqid('photo_', true) . '.' . $photoExtension;
                                    Storage::disk('public')->put($photoName, $photoContents);
                                    $photoPath = $photoName;
                                }
                            } catch (\Exception $e) {
                                Log::warning('Failed to download photo.', [
                                    'photo_url' => $photoUrl,
                                    'error' => $e->getMessage(),
                                ]);
                            }
                        }
                        $signaturePhotoPath = null;
                        if (!empty($value['photo'])) {
                            try {
                                // Prepend your configured base URL
                                $photoUrl = rtrim(config('app.photo_api'), '/') . '/' . ltrim($value['photo'], '/');
                                $photoContents = file_get_contents($photoUrl);
                                if ($photoContents !== false) {
                                    $photoExtension = pathinfo(parse_url($photoUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                                    $photoName = 'photos/' . uniqid('photo_', true) . '.' . $photoExtension;
                                    Storage::disk('public')->put($photoName, $photoContents);
                                    $signaturePhotoPath = $photoName;
                                }
                            } catch (\Exception $e) {
                                Log::warning('Failed to download photo.', [
                                    'photo_url' => $photoUrl,
                                    'error' => $e->getMessage(),
                                ]);
                            }
                        }
                        $data = [
                            'first_name' => $value['first_name'],
                            'middle_name' => $value['middle_name'] ?? null,
                            'last_name' => $value['last_name'],
                            'gender' => $value['gender'] ?? null,
                            'birthday' => $value['birthday'],
                            'civil_status' => $value['civil_status'] ?? null,
                            'house_status' => $value['house_status'] ?? null,
                            'name_on_check' => $value['name_on_check'] ?? null,
                            'employment_date' => $value['employment_date'] ?? null,
                            'contributions_percentage' => $value['contributions_percentage'] ?? 0.00,
                            'tin_number' => $value['tin_number'] ?? null,
                            'phone_number_1' => $value['phone_number_1'] ?? null,
                            'phone_number_2' => $value['phone_number_2'] ?? null,
                            'address_1' => $value['address_1'] ?? null,
                            'regions_id' => $value['regions_id'],
                            'provinces_id' => $value['provinces_id'],
                            'municipalities_id' => (int) $value['cities_id'], // Make sure this is integer
                            'barangays_id' => $value['barangays_id'],
                            'countries_id' => $value['countries_id'],
                            'employee_number' => $value['employee_number'],
                            'employee_status' => $value['employee_status'] ?? 'regular',
                            'college_or_department' => $value['college_or_department'] ?? null,
                            'photo' =>  $photoPath ?? null,
                            'signature' => $signaturePhotoPath ?? null,
                        ];
                        PersonalInformation::create($data);
                    } catch (\Exception $e) {
                        Log::error('Failed to insert profile', [
                            'error' => $e->getMessage(),
                            'data' => $value,
                        ]);
                        // Continue to the next record instead of failing all
                        continue;
                    }
                }
                DB::commit();
                return redirect()->route('members-information')->with('success', 'Sync successful');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::critical('Sync profile transaction failed.', [
                    'error' => $e->getMessage(),
                ]);
                return response()->json(['error' => 'Sync failed'], 500);
            }
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
            'signature'=>$person->signature
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
