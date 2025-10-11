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
        Schema::create('payment_request_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_request_id')->constrained('payment_requests')->onDelete('cascade');
            $table->text('description'); // Nội dung
            $table->decimal('amount_before_tax', 15, 2); // Số tiền chưa thuế
            $table->decimal('tax_amount', 15, 2)->default(0); // Thuế GTGT
            $table->decimal('total_amount', 15, 2); // Tổng tiền
            $table->string('invoice_number')->nullable(); // Số hóa đơn
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_request_details');
    }
};
