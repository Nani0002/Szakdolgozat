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
        Schema::create('worksheets', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("customer_id")->nullable();
            $table->foreign("customer_id")->references("id")->on("customers")->onDelete("cascade");

            $table->string("sheet_number");
            $table->enum("sheet_type", ["maintanance", "paid", "warranty"]);
            $table->date("print_date")->nullable();
            $table->date("declaration_time");
            $table->enum("declaration_mode", ["email", "phone", "personal", "onsite"]);
            $table->string("error_description");
            $table->string("comment");
            $table->boolean("final")->default(false);

            $table->unsignedBigInteger("coworker_id")->nullable();
            $table->foreign("coworker_id")->references("id")->on("users")->onDelete("cascade");
            $table->unsignedBigInteger("liable_id")->nullable();
            $table->foreign("liable_id")->references("id")->on("users")->onDelete("cascade");

            $table->date("work_start");
            $table->date("work_end")->nullable();
            $table->integer("worktme");
            $table->text("work_description");

            $table->unsignedBigInteger("outsourcing_id")->nullable();
            $table->foreign("outsourcing_id")->references("id")->on("outsourcings")->onDelete("cascade");

            $table->enum("current_step", ["open", "started", "ongoing", "price_offered", "waiting", "to_invoice", "closed"]);

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
