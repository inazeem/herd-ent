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
        Schema::create('billing_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('description');
            $table->enum('type', ['cpt', 'diagnostic', 'other']);
            $table->decimal('default_price', 10, 2)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_codes');
    }
};
