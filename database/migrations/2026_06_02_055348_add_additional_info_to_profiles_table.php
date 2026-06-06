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
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('fakultas')->nullable();
            $table->string('jurusan')->nullable();
            $table->string('angkatan')->nullable();
            $table->string('gender')->nullable();
            $table->text('alamat')->nullable();
            $table->date('tanggal_lahir')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn([
                'fakultas',
                'jurusan',
                'angkatan',
                'gender',
                'alamat',
                'tanggal_lahir'
            ]);
        });
    }
};
