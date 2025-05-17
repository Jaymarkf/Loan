@extends('master')
@section('third-party-plugin')
<script
  src="https://code.jquery.com/jquery-3.7.1.min.js"
  integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
  crossorigin="anonymous"></script>
<!-- <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script> -->
@endsection

@section('content')



<div class="w-full max-w-3xl mx-auto p-6 bg-green-50 rounded-lg shadow-md">
  <h2 class="text-2xl font-semibold text-green-800 mb-6 text-center">Co-Maker Information</h2>

  <form id="comaker-form" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf

    <!-- Member Autocomplete -->
 <div class="relative w-full md:col-span-2">
  <label for="member" class="block mb-2 font-medium text-green-700">Member</label>
  <input
    type="text"
    id="member_name"
    name="member"
    placeholder="Type to search member"
    autocomplete="off"
    class="w-full px-4 py-2 border border-green-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-600"
  />
  <!-- Hidden input to store selected member's ID -->
  <input type="hidden" id="member_id" name="member_id" />

  <!-- Eye icon for verification -->
  <button
    id="verifyMemberBtn"
    type="button"
    disabled
    title="Verify selected member"
    class="absolute right-2 top-10 text-green-600 hover:text-green-900 !text-[16px]"
    style="font-size: 1.3rem; cursor: pointer;"
  >
    👁️ see this member
  </button>
</div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Full Name -->
      <div>
        <label for="comaker_fullname" class="block mb-2 font-medium text-green-700">Co-Maker Full Name</label>
        <input
          type="text"
          id="comaker_fullname"
          name="comaker_fullname"
          required
          placeholder="First Middle Last"
          class="w-full px-4 py-2 border border-green-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-600"
        />
      </div>

      <!-- Relationship -->
      <div>
        <label for="relationship" class="block mb-2 font-medium text-green-700">Relationship to Member</label>
        <input
          type="text"
          id="relationship"
          name="relationship"
          required
          placeholder="e.g. Friend, Relative"
          class="w-full px-4 py-2 border border-green-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-600"
        />
      </div>

      <!-- Contact Number -->
      <div>
        <label for="contact_number" class="block mb-2 font-medium text-green-700">Contact Number</label>
        <input
          type="tel"
          id="contact_number"
          name="contact_number"
          required
          placeholder="09XXXXXXXXX"
          class="w-full px-4 py-2 border border-green-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-600"
        />
      </div>

      <!-- ID Type -->
      <div>
        <label for="id_type" class="block mb-2 font-medium text-green-700">ID Type</label>
        <select
          id="id_type"
          name="id_type"
          required
          class="w-full px-4 py-2 border border-green-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-600"
        >
          <option value="" disabled selected>Select ID Type</option>
          <option value="government_id">Government ID</option>
          <option value="company_id">Company ID</option>
          <option value="passport">Passport</option>
          <option value="others">Others</option>
        </select>
      </div>

      <!-- ID Number -->
      <div>
        <label for="id_number" class="block mb-2 font-medium text-green-700">ID Number</label>
        <input
          type="text"
          id="id_number"
          name="id_number"
          required
          placeholder="ID Number"
          class="w-full px-4 py-2 border border-green-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-600"
        />
      </div>

      <!-- Upload ID Photo -->
      <div>
        <label for="id_photo" class="block mb-2 font-medium text-green-700">Upload ID Photo</label>
        <input
          type="file"
          id="id_photo"
          name="id_photo"
          accept="image/*"
          required
          class="w-full text-green-700 focus:outline-none"
        />
      </div>

      <!-- Address (full width) -->
      <div class="md:col-span-2">
        <label for="address" class="block mb-2 font-medium text-green-700">Address</label>
        <textarea
          id="address"
          name="address"
          rows="3"
          required
          placeholder="Complete address"
          class="w-full px-4 py-2 border border-green-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-600 resize-none"
        ></textarea>
      </div>
    </div>

    <button
      type="submit"
      class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-md transition-colors duration-200"
    >
      Submit Co-Maker Info
    </button>
  </form>
</div>




<!-- Modal Overlay -->
<div id="memberModal" class="fixed inset-0 bg-black/80 hidden z-50 flex items-center justify-center">
  <div class="bg-white rounded-lg shadow-lg max-w-xl w-full p-6 relative">
    <button id="closeModalBtn" class="absolute top-2 right-2 text-gray-500 hover:text-red-500 text-xl font-bold">&times;</button>
    <h3 class="text-lg font-semibold text-green-700 mb-4">Member Details</h3>
    <div id="memberDetails" class="text-sm text-gray-700 max-h-[400px] overflow-auto border border-green-100 rounded p-4 bg-green-50">
      Loading...
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('member_name');
    const suggestions = document.createElement('ul');
    const memberIdInput = document.getElementById('member_id');
    const verifyBtn = document.getElementById('verifyMemberBtn');
    let debounceTimeout;

    // Setup suggestions container
    suggestions.id = 'suggestions';
    suggestions.className = 'absolute bg-white border border-green-300 w-full mt-1 rounded-md shadow-lg z-50';
    suggestions.style.display = 'none';
    input.parentElement.appendChild(suggestions);

    // Disable verify button by default
    verifyBtn.disabled = true;

    input.addEventListener('input', () => {
      clearTimeout(debounceTimeout);
      const query = input.value.trim();

      // Reset if input is too short
      if (query.length < 2) {
        suggestions.style.display = 'none';
        memberIdInput.value = '';
        verifyBtn.disabled = true;
        return;
      }

      debounceTimeout = setTimeout(() => {
        fetch(`/person-info/${encodeURIComponent(query)}`)
          .then(res => res.json())
          .then(data => {
            suggestions.innerHTML = '';
            if (!data.length) {
              suggestions.style.display = 'none';
              memberIdInput.value = '';
              verifyBtn.disabled = true;
              return;
            }

            data.forEach(person => {
              const li = document.createElement('li');
              li.textContent = person.name;
              li.className = 'px-4 py-2 cursor-pointer hover:bg-green-100';
              li.addEventListener('click', () => {
                input.value = person.name;
                memberIdInput.value = person.id;
                suggestions.style.display = 'none';
                verifyBtn.disabled = false; // ✅ Enable verify button
              });
              suggestions.appendChild(li);
            });

            suggestions.style.display = 'block';
          })
          .catch(() => {
            suggestions.style.display = 'none';
            memberIdInput.value = '';
            verifyBtn.disabled = true;
          });
      }, 300);
    });

    // Hide suggestions on outside click
    document.addEventListener('click', (e) => {
      if (!input.contains(e.target) && !suggestions.contains(e.target)) {
        suggestions.style.display = 'none';
      }
    });
  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('member_name');
    const memberIdInput = document.getElementById('member_id');
    const verifyBtn = document.getElementById('verifyMemberBtn');
    const modal = document.getElementById('memberModal');
    const modalContent = document.getElementById('memberDetails');
    const closeModalBtn = document.getElementById('closeModalBtn');

    // Disable button if input changes (resets state)
    input.addEventListener('input', () => {
      verifyBtn.disabled = true;
    });

    // Show modal on button click
    verifyBtn.addEventListener('click', () => {
      const memberId = memberIdInput.value;
      if (!memberId) return;

      modal.classList.remove('hidden');
      modalContent.textContent = 'Loading...';

      fetch(`/person/${memberId}`)
        .then(res => res.json())
        .then(data => {
          const formatDate = (isoDate) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(isoDate).toLocaleDateString(undefined, options);
};

modalContent.innerHTML = `
  <div class="text-left flex flex-col gap-2">
    <p><strong>Name:</strong> ${data.first_name} ${data.middle_name} ${data.last_name}</p>
    <p><strong>Gender:</strong> ${capitalize(data.gender)}</p>
    <p><strong>Birthday:</strong> ${formatDate(data.birthday)}</p>
    <p><strong>Civil Status:</strong> ${capitalize(data.civil_status)}</p>
    <p><strong>House Status:</strong> ${capitalize(data.house_status)}</p>
    <p><strong>Name on Check:</strong> ${data.name_on_check}</p>
    <p><strong>Employment Date:</strong> ${formatDate(data.employment_date)}</p>
    <p><strong>Contributions (%):</strong> ${data.contributions_percentage}</p>
    <p><strong>TIN Number:</strong> ${data.tin_number}</p>
    <p><strong>Phone Number 1:</strong> ${data.phone_number_1}</p>
    <p><strong>Phone Number 2:</strong> ${data.phone_number_2}</p>
    <p><strong>Address:</strong> ${data.address_1}</p>
    <p><strong>Region:</strong> ${data.regions_id}</p>
    <p><strong>Province:</strong> ${data.provinces_id}</p>
    <p><strong>City/Municipality:</strong> ${data.municipalities_id}</p>
    <p><strong>Barangay:</strong> ${data.barangays_id}</p>
    <p><strong>Country:</strong> ${data.countries_id}</p>
    <p><strong>Employee No.:</strong> ${data.employee_number}</p>
    <p><strong>Employee Status:</strong> ${capitalize(data.employee_status)}</p>
    <p><strong>Department:</strong> ${data.college_or_department}</p>
  </div>
`;

function capitalize(str) {
  return str.charAt(0).toUpperCase() + str.slice(1);
}

        })
        .catch(() => {
          modalContent.textContent = 'Failed to load member data.';
        });
    });

    // Close modal
    closeModalBtn.addEventListener('click', () => {
      modal.classList.add('hidden');
    });

    // Close when clicking outside modal box
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.classList.add('hidden');
      }
    });
  });
</script>






@endsection