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
        Schema::create('computer_worksheet', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("computer_id")->nullable();
            $table->foreign("computer_id")->references("id")->on("computers")->onDelete("cascade");
            $table->unsignedBigInteger("worksheet_id")->nullable();
            $table->foreign("worksheet_id")->references("id")->on("worksheets")->onDelete("cascade");

            $table->string("password")->nullable();
            $table->string("condition")->nullable();
            $table->string("imagename")->nullable();
            $table->string("imagename_hash")->nullable();
            $table->string("addons")->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('computer_worksheet');
    }
};
