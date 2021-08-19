<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('contract_id')->unique()->index();
            $table->string('announcement')->nullable();
            $table->string('contract_type')->nullable();
            $table->string('procedure_type')->nullable();
            $table->text('contract_object')->nullable();
            $table->text('adjudicators')->nullable();
            $table->text('contractors')->nullable();
            $table->date('publication_date')->nullable();
            $table->date('celebration_date')->nullable();
            $table->decimal('contract_price', 11, 2)->nullable();
            $table->text('cpv')->nullable();
            $table->integer('execution_term')->nullable();
            $table->text('execution_location')->nullable();
            $table->text('reasoning')->nullable();
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contracts');
    }
}
