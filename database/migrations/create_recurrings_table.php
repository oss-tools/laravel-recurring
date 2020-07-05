<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecurringsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recurrings', function (Blueprint $table) {
            $table->id();
            $table->dateTime('start_date');
            $table->date('end_date')->nullable();
            $table->string('recurring_type');
            $table->unsignedBigInteger('recurring_id');
            $table->unique(['start_date', 'end_date', 'recurring_type', 'recurring_id'], 'start_end_recurring_foreign');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recurrings');
    }
}
