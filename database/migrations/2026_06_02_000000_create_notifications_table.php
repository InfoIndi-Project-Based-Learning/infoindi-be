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
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('type', ['admin', 'new_post', 'new_follower', 'post_liked', 'post_commented']);
            $table->string('title');
            $table->text('message')->nullable();
            $table->foreignUuid('sender_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignUuid('post_id')->nullable()->constrained('posts')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('notification_user', function (Blueprint $table) {
            $table->foreignUuid('notification_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->primary(['notification_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_user');
        Schema::dropIfExists('notifications');
    }
};
