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
        Schema::create('outsourcings', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("company_id")->nullable();
            $table->foreign("company_id")->references("id")->on("companies")->onDelete("cascade");

            $table->date("entry_time");
            $table->string("outsourced_number")->unique();
            $table->double("outsourced_price");
            $table->double("our_price");
            $table->enum("finished", ["ongoing", "finished", "brought"]);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outsourcings');
    }
};
