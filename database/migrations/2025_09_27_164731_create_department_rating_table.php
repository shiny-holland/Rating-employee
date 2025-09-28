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
        Schema::create('department_rating', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dept_id');
            $table->string('department_head_name');
            $table->decimal('average_rating', 3, 2); // e.g., 4.25
            $table->timestamp('rating_date');
            $table->string('cby')->nullable();
            $table->string('cip')->nullable();
            $table->timestamp('cdate')->nullable();
            $table->string('mby')->nullable();
            $table->string('mip')->nullable();
            $table->timestamp('mdate')->nullable();
            $table->tinyInteger('is_delete')->default(0);
            $table->timestamps();
            
            // Foreign key constraint removed for now
            // $table->foreign('dept_id')->references('id')->on('department');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department_rating');
    }
};
