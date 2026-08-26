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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('legal_name');
            $table->string('cnpj', 14)->unique();
            $table->string('street');
            $table->string('number', 50);
            $table->string('district');
            $table->string('city');
            $table->string('state', 2);
            $table->string('zip_code', 8);
            $table->string('complement')->nullable();
            $table->string('primary_contact_name');
            $table->string('primary_contact_email');
            $table->string('primary_contact_phone', 20);
            $table->timestamps();

            $table->index('legal_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
