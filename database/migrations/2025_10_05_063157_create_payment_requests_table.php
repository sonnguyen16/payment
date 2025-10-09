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
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['advance', 'payment_proposal', 'other_expense']);
            $table->decimal('amount', 15, 2);
            $table->text('description');
            $table->text('reason');
            $table->date('expected_date');
            $table->enum('priority', ['urgent', 'normal'])->default('normal');
            $table->enum('status', [
                'draft', 'pending_department_head', 'pending_accountant', 
                'pending_ceo', 'pending_payment', 'paid', 'rejected', 'cancelled', 'deleted'
            ])->default('draft');
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('current_approver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->string('payment_code', 50)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('user_id');
            $table->index('status');
            $table->index('priority');
            $table->index('project_id');
            $table->index('current_approver_id');
            $table->index(['status', 'priority']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_requests');
    }
};
