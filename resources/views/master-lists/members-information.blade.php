@extends('master')
@section('third-party-plugin')
<script
  src="https://code.jquery.com/jquery-3.7.1.min.js"
  integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
  crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
@endsection

@section('content')
<style>
    table.dataTable {
        width: 100%; /* Full width */
        border-collapse: collapse;
        margin: 20px 0;
        font-size: 14px;
    }
    table.dataTable th,
    table.dataTable td {
        padding: 10px; /* Adds spacing around table content */
        text-align: left; /* Aligns the content to the left */
        border: 1px solid #ddd; /* Light border for better readability */
    }
    table.dataTable th {
        background-color: #f4f4f4; /* Slightly lighter background for headers */
        color: #333; /* Darker color for text */
        font-weight: bold;
    }
    table.dataTable tr:nth-child(even) {
        background-color: #f9f9f9; /* Alternating row colors for better readability */
    }
    table.dataTable tr:hover {
        background-color: #f1f1f1; /* Highlight row on hover */
    }
</style>

<h1 class="my-5 text-2xl bold">Members Information </h1>
<a href="{{ route('sync-member') }}" class="bg-green-500 py-3 px-3 rounded-md bordered hover:bg-green-400 cursor-pointer mb-5 block max-w-max"><i class="fa fa-sync"></i> Sync online members</a>
@if (session('success'))
    <div class="mb-4 rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3 text-sm max-w-max" role="alert">
        ✅ {{ session('success') }}
    </div>
@endif
           
<table id="myTable" class="table table-bordered table-striped w-full">
    <thead>
        <tr>
            <th>Actions</th>
            <th>Member ID</th>
            <th>First Name</th>
            <th>Middle Name</th>
            <th>Last Name</th>
            <th>Gender</th>
            <th>Birthday</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($members) && count($members) > 0)
            @foreach($members as $member)
                <tr>
                    <td>
                        <!-- Example Actions: You can replace this with actual buttons for actions -->
                        <button onclick="openModal( {{$member->id}})" class="hover:bg-orange-600 btn btn-primary bordered bg-orange-500 text-white px-2 py-1 rounded-md mr-3 cursor-pointer"><i class="fa fa-eye"></i>/ <i class="fa fa-edit"></i> </button>
                        <button class="hover:bg-red-700 btn btn-danger bg-red-500 text-white px-2 py-1 rounded-md cursor-pointer"> <i class="fa fa-trash-can"></i> Delete</button>
                    </td>
                    <td>{{ $member->id }}</td>
                    <td>{{ $member->first_name }}</td>
                    <td>{{ $member->middle_name ?? 'N/A' }}</td> <!-- Handle nullable middle_name -->
                    <td>{{ $member->last_name }}</td>
                    <td>{{ $member->gender }}</td>
                    <td>{{ $member->birthday }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>

<div id="memberModal" class="fixed inset-0 z-50 flex hidden items-center justify-center bg-[#00000063] mx-auto">
    <div class="bg-white p-6 pt-0 rounded-lg w-full max-w-4xl shadow-xl max-h-[90vh] min-h-[90vh] overflow-y-auto animate-fade-in relative">
        <div class="flex justify-between items-center mb-4 sticky inset-0 h-6 bg-white pt-3">
            <h2 class="text-xl font-semibold">Member Information</h2>
            <button onclick="closeModal()" class="text-gray-600 hover:text-red-600 text-2xl leading-none">&times;</button>
        </div>
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{route('update-person')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" id="id" value="">
            <div id="loader" class="absolute max-h-[90vh] inset-0  flex items-center justify-center bg-white z-50 top-[10%]">
                <div class="w-16 h-16 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 hide_me_first">
    <!-- First Name -->
                <div class="mb-4">
                    <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                    <input type="text" name="first_name" id="first_name" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Middle Name -->
                <div class="mb-4">
                    <label for="middle_name" class="block text-sm font-medium text-gray-700">Middle Name</label>
                    <input type="text" name="middle_name" id="middle_name" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Last Name -->
                <div class="mb-4">
                    <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                    <input type="text" name="last_name" id="last_name" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Gender -->
                <div class="mb-4">
                    <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                    <input type="text" name="gender" id="gender" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Birthday -->
                <div class="mb-4">
                    <label for="birthday" class="block text-sm font-medium text-gray-700">Birthday</label>
                    <input type="date" name="birthday" id="birthday" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Civil Status -->
                <div class="mb-4">
                    <label for="civil_status" class="block text-sm font-medium text-gray-700">Civil Status</label>
                    <input type="text" name="civil_status" id="civil_status" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- House Status -->
                <div class="mb-4">
                    <label for="house_status" class="block text-sm font-medium text-gray-700">House Status</label>
                    <input type="text" name="house_status" id="house_status" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Name on Check -->
                <div class="mb-4">
                    <label for="name_on_check" class="block text-sm font-medium text-gray-700">Name on Check</label>
                    <input type="text" name="name_on_check" id="name_on_check" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Employment Date -->
                <div class="mb-4">
                    <label for="employment_date" class="block text-sm font-medium text-gray-700">Employment Date</label>
                    <input type="date" name="employment_date" id="employment_date" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Contributions Percentage -->
                <div class="mb-4">
                    <label for="contributions_percentage" class="block text-sm font-medium text-gray-700">Contributions %</label>
                    <input type="number" name="contributions_percentage" id="contributions_percentage" step="0.01" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- TIN Number -->
                <div class="mb-4">
                    <label for="tin_number" class="block text-sm font-medium text-gray-700">TIN Number</label>
                    <input type="text" name="tin_number" id="tin_number" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Phone 1 -->
                <div class="mb-4">
                    <label for="phone_number_1" class="block text-sm font-medium text-gray-700">Phone 1</label>
                    <input type="text" name="phone_number_1" id="phone_number_1" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Phone 2 -->
                <div class="mb-4">
                    <label for="phone_number_2" class="block text-sm font-medium text-gray-700">Phone 2</label>
                    <input type="text" name="phone_number_2" id="phone_number_2" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Address -->
                <div class="mb-4">
                    <label for="address_1" class="block text-sm font-medium text-gray-700">Address</label>
                    <input type="text" name="address_1" id="address_1" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Region ID -->
                <div class="mb-4">
                    <label for="regions_id" class="block text-sm font-medium text-gray-700">Region ID</label>
                    <input type="text" name="regions_id" id="regions_id" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Province ID -->
                <div class="mb-4">
                    <label for="provinces_id" class="block text-sm font-medium text-gray-700">Province ID</label>
                    <input type="text" name="provinces_id" id="provinces_id" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Municipality ID -->
                <div class="mb-4">
                    <label for="municipalities_id" class="block text-sm font-medium text-gray-700">Municipality ID</label>
                    <input type="text" name="municipalities_id" id="municipalities_id" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Barangay ID -->
                <div class="mb-4">
                    <label for="barangays_id" class="block text-sm font-medium text-gray-700">Barangay ID</label>
                    <input type="text" name="barangays_id" id="barangays_id" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Country ID -->
                <div class="mb-4">
                    <label for="countries_id" class="block text-sm font-medium text-gray-700">Country ID</label>
                    <input type="text" name="countries_id" id="countries_id" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Employee Number -->
                <div class="mb-4">
                    <label for="employee_number" class="block text-sm font-medium text-gray-700">Employee Number</label>
                    <input type="text" name="employee_number" id="employee_number" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Employee Status -->
                <div class="mb-4">
                    <label for="employee_status" class="block text-sm font-medium text-gray-700">Employee Status</label>
                    <input type="text" name="employee_status" id="employee_status" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- College / Department -->
                <div class="mb-4">
                    <label for="college_or_department" class="block text-sm font-medium text-gray-700">College / Department</label>
                    <input type="text" name="college_or_department" id="college_or_department" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <!-- Photo Section -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Photo</label>
                    <div class="relative w-40 h-40 group">
                        <img id="photo_preview"
                            src="https://placehold.co/200x200/EEE/31343C?font=montserrat&text=No+Photo"
                            alt="Photo"
                            class="w-full h-full rounded-md border border-gray-300 object-cover">
                            <input type="file" id="photo" name="photo" accept="image/*" class="hidden" onchange="handlePhotoChange(event)">
                        <div class="absolute inset-0 flex items-center justify-center gap-3 opacity-0 group-hover:opacity-100 bg-black/40 rounded-md transition">
                            <button type="button"
                                    onclick="editPhoto()"
                                    class="bg-white p-2 rounded-md shadow hover:bg-blue-100 cursor-pointer"
                                    title="Edit Photo">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button"
                                    onclick="deletePhoto()"
                                    class="bg-white p-2 rounded-md shadow hover:bg-red-100 cursor-pointer"
                                    title="Delete Photo">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Signature Section -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Signature</label>
                    <div class="relative w-40 h-40 group">
                        <img id="signature_preview"
                            src="https://placehold.co/200x200/EEE/31343C?font=montserrat&text=No+Signature"
                            alt="Signature"
                            class="w-full h-full rounded-md border border-gray-300 object-cover">
                            <input type="file" id="signature" name="signature" accept="image/*" class="hidden" onchange="handleSignatureChange(event)">
                        <div class="absolute inset-0 flex items-center justify-center gap-3 opacity-0 group-hover:opacity-100 bg-black/40 rounded-md transition">
                            <button type="button"
                                    onclick="editSignature()"
                                    class="bg-white p-2 rounded-md shadow hover:bg-blue-100 cursor-pointer"
                                    title="Edit Signature">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button"
                                    onclick="deleteSignature()"
                                    class="bg-white p-2 rounded-md shadow hover:bg-red-100 cursor-pointer"
                                    title="Delete Signature">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-6 text-right hide_me_first">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Save Member
                </button>
            </div>
        </form>
    </div>
</div>



<!-- Display error message if set -->
@if(isset($error))
    <div class="alert alert-danger">
        {{ $error }}
    </div>
@endif
<script>
    $(document).ready(function() {
        $('#myTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "responsive": true
        });


    });
    function closeModal() {
        document.getElementById('memberModal').classList.add('hidden');
        }

        function openModal(id) {
            document.getElementById('memberModal').classList.remove('hidden');
            var members_id = id;
            var loader = document.getElementById('loader');
            loader.classList.remove('hidden');
            fetchPerson(members_id);
            
        }


        async function fetchPerson(personId) {
        try {
            const response = await fetch(`/person/${personId}`);

            if (!response.ok) {
                throw new Error(`Error ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            const PHOTO_API_BASE = "{{ config('app.photo_api') }}";
            Object.entries(data).forEach(([key, value]) => {
                const input = document.getElementById(key);
                
                // Skip setting value on file inputs
                if (input && input.type !== 'file') {
                    input.value = value;
                }

                if (key === 'photo') {
                    const img = document.getElementById('photo_preview');
                    if (img) img.src = `${PHOTO_API_BASE}/${value}`;
                }

                if (key === 'signature') {
                    const img = document.getElementById('signature_preview');
                    if (img) img.src = `${PHOTO_API_BASE}/${value}`;
                }
            });
            var hider = document.querySelectorAll('.hide_me_first');
            hider.forEach(function(element) {
                element.classList.remove('hide_me_first');
            });
            var loader = document.getElementById('loader');
            if (loader) {
                loader.classList.add('hidden');
            }

            // You can now use this data to populate form fields, show in UI, etc.
        } catch (error) {
            console.error('Failed to fetch person:', error);
        }
    }
</script>
<script>
const defaultPhotoURL = "https://placehold.co/200x200/EEE/31343C?font=montserrat&text=No+Photo";
const defaultSignatureURL = "https://placehold.co/200x200/EEE/31343C?font=montserrat&text=No+Signature";

// Photo functions
function editPhoto() {
    document.getElementById('photo').click();
}

function handlePhotoChange(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('photo_preview');
    if (file) {
        preview.src = URL.createObjectURL(file);
    }
}

function deletePhoto() {
    document.getElementById('photo').value = '';
    document.getElementById('photo_preview').src = defaultPhotoURL;
}

// Signature functions
function editSignature() {
    document.getElementById('signature').click();
}

function handleSignatureChange(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('signature_preview');
    if (file) {
        preview.src = URL.createObjectURL(file);
    }
}

function deleteSignature() {
    document.getElementById('signature').value = '';
    document.getElementById('signature_preview').src = defaultSignatureURL;
}
</script>

@endsection