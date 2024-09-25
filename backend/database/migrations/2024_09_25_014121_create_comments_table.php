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
        Schema::create('comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 50)->nullable();
            $table->integer('influencer_rating')->nullable()->comment('Rating for the influencer');
            $table->string('comment', 500)->nullable();
            $table->integer('comment_rating')->nullable()->comment('Likes of the comment');
            $table->boolean('is_approved')->default(false);
            $table->unsignedBigInteger('influencer_id')->index('comments_influencer_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
