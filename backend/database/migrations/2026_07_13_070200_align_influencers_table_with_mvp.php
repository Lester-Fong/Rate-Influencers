<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('influencers', function (Blueprint $table) {
            $table->text('bio')->nullable()->after('slug');
            $table->unsignedInteger('review_count')->default(0)->after('rating');
            $table->unique('slug');
        });

        DB::table('influencers')
            ->select('id')
            ->orderBy('id')
            ->each(function (object $influencer): void {
                $approvedReviews = DB::table('reviews')
                    ->where('influencer_id', $influencer->id)
                    ->where('status', 'approved');

                DB::table('influencers')
                    ->where('id', $influencer->id)
                    ->update([
                        'rating' => (float) ($approvedReviews->avg('rating') ?? 0),
                        'review_count' => $approvedReviews->count(),
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('influencers', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn(['bio', 'review_count']);
        });
    }
};
