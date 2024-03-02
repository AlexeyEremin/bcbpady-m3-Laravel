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
        Schema::create('accesses', function (Blueprint $table) {
            $table->id();
            /*
             * конструкция foreignIdFor создает автоматически по названию твоего класса
             * колонку формата CLASS_id => user_id или file_id и заменяет 2 операции:
             * создание колонки через unsignedBigInteger и foreign
             */
            $table->foreignIdFor(\App\Models\User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\File::class)->constrained()->cascadeOnDelete();
            # Еще мы договорились что добавил поле автор
            $table->boolean('author')->default(false);
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
        Schema::dropIfExists('accesses');
    }
};
