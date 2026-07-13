<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('comments', 'reviews');

        Schema::table('reviews', function (Blueprint $table) {
            $table->renameColumn('name', 'reviewer_name');
            $table->renameColumn('influencer_rating', 'rating');
            $table->renameColumn('comment', 'review');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->after('review');
        });

        DB::table('reviews')
            ->where('is_approved', true)
            ->update(['status' => 'approved']);

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['comment_rating', 'is_approved']);
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->integer('comment_rating')->nullable()->after('review');
            $table->boolean('is_approved')->default(false)->after('comment_rating');
        });

        DB::table('reviews')
            ->where('status', 'approved')
            ->update(['is_approved' => true]);

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->renameColumn('reviewer_name', 'name');
            $table->renameColumn('rating', 'influencer_rating');
            $table->renameColumn('review', 'comment');
        });

        Schema::rename('reviews', 'comments');
    }
};
