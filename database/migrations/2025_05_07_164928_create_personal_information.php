<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('personal_informations', function (Blueprint $table) {
            $table->id();
            // Foreign key referencing 'id' in 'members' table
            $table->unsignedBigInteger('member_id');
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
            $table->string('external_member_id');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('birthday');
            $table->enum('civil_status', ['single', 'married', 'widowed', 'divorced'])->nullable();
            $table->enum('house_status', ['owned', 'rented', 'family', 'others'])->nullable();
            $table->string('name_on_check')->nullable();
            $table->date('employment_date')->nullable();
            $table->decimal('contributions_percentage', 5, 2)->default(0);
            $table->string('tin_number', 20)->nullable();
            $table->string('phone_number_1', 20)->nullable();
            $table->string('phone_number_2', 20)->nullable();
            $table->string('address_1')->nullable();
            $table->foreignId('regions_id')->constrained('regions');
            $table->unsignedBigInteger('provinces_id')->constrained('provinces');
            $table->foreignId('municipalities_id')->constrained('cities');
            $table->foreignId('barangays_id')->constrained('barangays');
            $table->unsignedBigInteger('countries_id')->constrained('countries');
            $table->string('employee_number');
            $table->enum('employee_status', ['regular', 'probationary', 'contractual', 'resigned'])->default('regular');
            $table->string('college_or_department')->nullable();
            $table->string('photo')->nullable();
            $table->string('signature')->nullable();
            $table->boolean('is_edited')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    

    public function down(): void
    {
        Schema::dropIfExists('personal_information');
    }
};
