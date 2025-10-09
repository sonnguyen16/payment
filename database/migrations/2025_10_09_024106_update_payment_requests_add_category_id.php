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
        Schema::table('payment_requests', function (Blueprint $table) {
            // Thêm category_id
            $table->foreignId('category_id')->nullable()->after('type')->constrained('categories')->onDelete('set null');
            
            // Xóa cột type sau khi đã migrate data
            // $table->dropColumn('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_requests', function (Blueprint $table) {
            // Khôi phục cột type
            // $table->enum('type', ['advance', 'payment_proposal', 'other'])->after('category_id');
            
            // Xóa category_id
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
