<?php

use App\Enums\ImportStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $statusEnum = [
            ImportStatus::PENDING,
            ImportStatus::COMPLETED,
            ImportStatus::FAILED
        ];

        Schema::create('imports', function (Blueprint $table) use ($statusEnum) {
            $table->uuid('id')->primary();
            $table->string('filename');
            $table->string('path');
            $table->integer('total_rows')->nullable();
            $table->enum('status', $statusEnum);
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
        Schema::dropIfExists('imports');
    }
}
