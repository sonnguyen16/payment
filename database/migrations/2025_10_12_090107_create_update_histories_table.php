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
        Schema::create('update_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('reason'); // Lý do chỉnh sửa
            $table->text('changes')->nullable(); // Lưu các thay đổi (old_value, new_value)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('update_histories');
    }
};
