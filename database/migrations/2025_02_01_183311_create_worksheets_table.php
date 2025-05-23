<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('worksheets', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("customer_id")->nullable();
            $table->foreign("customer_id")->references("id")->on("customers")->onDelete("cascade");

            $table->string("sheet_number")->unique();
            $table->enum("sheet_type", ["maintanance", "paid", "warranty"]);
            $table->date("print_date")->nullable();
            $table->date("declaration_time");
            $table->enum("declaration_mode", ["email", "phone", "personal", "onsite"]);
            $table->string("error_description");
            $table->string("comment")->nullable();
            $table->boolean("final")->default(false);

            $table->unsignedBigInteger("liable_id")->nullable();
            $table->foreign("liable_id")->references("id")->on("users")->onDelete("cascade");
            $table->unsignedBigInteger("coworker_id")->nullable();
            $table->foreign("coworker_id")->references("id")->on("users")->onDelete("cascade");


            $table->date("work_start");
            $table->date("work_end")->nullable();
            $table->integer("work_time")->nullable();
            $table->text("work_description")->nullable();

            $table->unsignedBigInteger("outsourcing_id")->nullable();
            $table->foreign("outsourcing_id")->references("id")->on("outsourcings")->onDelete("cascade");

            $table->enum("current_step", array_keys(Config::get('worksheet_steps')));

            $table->integer("liable_slot_number")->nullable();
            $table->integer("coworker_slot_number")->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worksheets');
    }
};
