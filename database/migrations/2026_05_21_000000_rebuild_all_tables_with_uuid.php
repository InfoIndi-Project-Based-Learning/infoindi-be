<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Rebuilds all tables with UUID primary keys and adds new tables (post_likes, reports).
     */
    public function up(): void
    {
        // Drop existing tables in reverse dependency order
        Schema::dropIfExists('user_follow_user');
        Schema::dropIfExists('profiles');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');

        // ========================
        // Re-create all tables with UUID
        // ========================

        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['user', 'admin'])->default('user');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('category_name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('category_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->string('post_name');
            $table->text('description');
            $table->text('banner_url')->nullable();
            $table->integer('view_count')->default(0);
            $table->timestamps();
        });

        Schema::create('profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->string('avatar')->nullable();
            $table->string('phone')->nullable();
            $table->string('website_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->text('bio')->nullable();
            $table->boolean('is_mahasiswa')->default(true);
            $table->timestamps();
        });

        Schema::create('user_follow_user', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('follower_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('followed_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // ========================
        // New tables
        // ========================

        Schema::create('post_likes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('post_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['post_id', 'user_id']);
        });

        Schema::create('reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('type', ['post'])->default('post');
            $table->uuid('target_id');
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->enum('reason', ['spam', 'inappropriate', 'harassment', 'misinformation', 'other']);
            $table->text('additional_info')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'resolved', 'dismissed'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->uuid('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->unique(['type', 'target_id', 'user_id']);
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
        Schema::dropIfExists('post_likes');
        Schema::dropIfExists('user_follow_user');
        Schema::dropIfExists('profiles');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
