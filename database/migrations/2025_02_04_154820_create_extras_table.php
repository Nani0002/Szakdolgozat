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
        Schema::create('extras', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("worksheet_id")->nullable();
            $table->foreign("worksheet_id")->references("id")->on("worksheets")->onDelete("cascade");

            $table->string("manufacturer");
            $table->string("type");
            $table->string("serial_number")->unique();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extras');
    }
};
