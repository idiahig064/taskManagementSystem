<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('color_code', 7)->unique();
            $table->string('description');
            $table->integer('parent_id')->nullable();
            $table->integer('position');
            $table->timestamps();
            $table->softDeletes();
        });

//        $categories = \App\Models\Category::all();
//        foreach ($categories as $index => $category) {
//            $category->update(['position' => $index + 1]);
//        }
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
