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
        Schema::table('users', function (Blueprint $table) {
            $table->string('image_url')->nullable(); // 画像パス用
            $table->string('postcode')->nullable();  // 郵便番号
            $table->string('address')->nullable();   // 住所
            $table->string('building')->nullable();  // 建物名
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['image_url', 'postcode', 'address', 'building']);
        });
    }
};
