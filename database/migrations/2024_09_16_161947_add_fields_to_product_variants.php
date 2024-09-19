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
        Schema::table('product_variants', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('quantity');
            $table->decimal('discount', 5, 2)->nullable()->after('is_active');
            $table->string('image_url')->nullable()->after('quantity'); 
            
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('is_active');
            $table->dropColumn('discount');
            $table->dropColumn('image_url');

            $table->dropIndex(['product_id']);
        });
    }
};
