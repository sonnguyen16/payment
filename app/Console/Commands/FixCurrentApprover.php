<?php

namespace App\Console\Commands;

use App\Models\PaymentRequest;
use App\Models\User;
use App\Enums\PaymentRequestStatus;
use Illuminate\Console\Command;

class FixCurrentApprover extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:fix-approver {--dry-run : Show what would be fixed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix missing current_approver_id for payment requests';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
        }
        
        // Find requests with null current_approver_id that should have one
        $requests = PaymentRequest::whereNull('current_approver_id')
            ->whereIn('status', [
                PaymentRequestStatus::PENDING_DEPARTMENT_HEAD,
                PaymentRequestStatus::PENDING_ACCOUNTANT,
                PaymentRequestStatus::PENDING_CEO,
                PaymentRequestStatus::PENDING_PAYMENT
            ])
            ->with('user.office')
            ->get();
            
        $this->info("Found {$requests->count()} requests with missing approver");
        
        foreach ($requests as $request) {
            $approverId = $this->getCorrectApprover($request);
            
            $this->info("Request #{$request->id} - Status: {$request->status->value}");
            $this->info("  User: {$request->user->name} (Office: {$request->user->office->name})");
            $this->info("  Current approver: " . ($request->current_approver_id ?? 'NULL'));
            $this->info("  Should be: " . ($approverId ?? 'NULL'));
            
            if ($approverId) {
                $approver = User::find($approverId);
                $this->info("  Approver: {$approver->name} ({$approver->email})");
                
                if (!$dryRun) {
                    $request->update(['current_approver_id' => $approverId]);
                    $this->info("  ✅ Fixed!");
                }
            } else {
                $this->error("  ❌ Could not determine approver!");
            }
            
            $this->info('');
        }
        
        if ($dryRun) {
            $this->warn('This was a dry run. Use --no-dry-run to actually fix the data.');
        } else {
            $this->info('✅ All requests fixed!');
        }
        
        return 0;
    }
    
    private function getCorrectApprover(PaymentRequest $request): ?int
    {
        return match($request->status) {
            PaymentRequestStatus::PENDING_DEPARTMENT_HEAD => $this->getDepartmentHead($request),
            PaymentRequestStatus::PENDING_ACCOUNTANT => $this->getAccountant($request),
            PaymentRequestStatus::PENDING_CEO => $this->getCEO(),
            PaymentRequestStatus::PENDING_PAYMENT => $this->getAccountant($request),
            default => null,
        };
    }
    
    private function getDepartmentHead(PaymentRequest $request): ?int
    {
        return User::role('department_head')
            ->where('office_id', $request->user->office_id)
            ->first()?->id;
    }
    
    private function getAccountant(PaymentRequest $request): ?int
    {
        return User::role('accountant')
            ->where('office_id', $request->user->office_id)
            ->first()?->id;
    }
    
    private function getCEO(): ?int
    {
        return User::role('ceo')->first()?->id;
    }
}
