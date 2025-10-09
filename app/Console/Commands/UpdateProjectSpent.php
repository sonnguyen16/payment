<?php

namespace App\Console\Commands;

use App\Models\PaymentRequest;
use App\Models\Project;
use Illuminate\Console\Command;

class UpdateProjectSpent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:update-spent {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update project spent amounts based on paid payment requests';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
        }
        
        // Reset all project spent amounts to 0
        $projects = Project::all();
        $this->info("Found {$projects->count()} projects");
        
        foreach ($projects as $project) {
            $paidRequests = PaymentRequest::where('project_id', $project->id)
                ->where('status', 'paid')
                ->get();
                
            $totalSpent = $paidRequests->sum('amount');
            
            $this->info("Project: {$project->name} (ID: {$project->id})");
            $this->info("  Current spent: {$project->spent}");
            $this->info("  Calculated spent: {$totalSpent}");
            $this->info("  Paid requests: {$paidRequests->count()}");
            
            if (!$dryRun) {
                $project->update(['spent' => $totalSpent]);
                $this->info("  ✅ Updated spent to {$totalSpent}");
            }
            
            $this->info('');
        }
        
        if ($dryRun) {
            $this->warn('This was a dry run. Use --no-dry-run to actually update the data.');
        } else {
            $this->info('✅ All projects updated successfully!');
        }
        
        return 0;
    }
}
