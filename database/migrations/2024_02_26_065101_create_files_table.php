<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
         * Я тут поправил, то что обсуждали ранее, добавил версию, чтобы не ломать голову
         * над номером файла с тем же названием
         * Убрал то что обсуждали ранее именно пользователя, все выносим в Access
         */
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->string('name');
            $table->integer('version')->default(0);
            $table->string('type');
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
        Schema::dropIfExists('files');
    }
};
