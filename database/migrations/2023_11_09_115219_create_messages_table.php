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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id');
            $table->foreign("sender_id")->references('id')->on('users');
            $table->foreign("receiver_id")->references('id')->on('users');
            $table->string("type");
            $table->string("text")->nullable();
            $table->string("media")->nullable();
            $table->string("video")->nullable();
            $table->string("document")->nullable();
            $table->foreignId('conversation_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('is_sender_delete')->default(false);
            $table->boolean('is_receiver_delete')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
