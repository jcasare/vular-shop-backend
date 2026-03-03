<?php

use App\Models\Category;
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
        Schema::table('products', function (Blueprint $table) {
            $table->foreignIdFor(Category::class)->nullable()->constrained()->nullOnDelete()->after('slug');
            $table->decimal('rating', 2, 1)->default(0)->after('quantity');
            $table->unsignedInteger('reviews_count')->default(0)->after('rating');
            $table->boolean('featured')->default(false)->after('reviews_count');
            $table->json('images')->nullable()->after('image_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(Category::class);
            $table->dropColumn(['rating', 'reviews_count', 'featured', 'images']);
        });
    }
};
