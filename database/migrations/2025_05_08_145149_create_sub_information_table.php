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
        Schema::create('sub_informations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('members_id')->constrained()->onDelete('cascade'); // Assuming a foreign key relation
            $table->string('information_type'); // Type like "DEPENDENT"
            $table->json('sub_information'); // To store JSON data like the one in your example
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_information');
    }
};
