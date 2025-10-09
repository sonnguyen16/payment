<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApprovalRequest;
use App\Models\PaymentRequest;
use App\Services\ApprovalWorkflowService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ApprovalController extends Controller
{
    public function __construct(
        protected ApprovalWorkflowService $approvalWorkflowService
    ) {}

    public function index()
    {
        $user = auth()->user();
        
        $requests = PaymentRequest::with(['user', 'project'])
            ->where('current_approver_id', $user->id)
            ->whereNotIn('status', ['cancelled', 'rejected', 'paid', 'deleted'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        $stats = [
            'pending_count' => PaymentRequest::where('current_approver_id', $user->id)
                ->whereNotIn('status', ['cancelled', 'rejected', 'paid', 'deleted'])
                ->count(),
            'approved_today' => PaymentRequest::whereHas('approvalHistories', function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('action', 'approved')
                  ->whereDate('created_at', today());
            })->count(),
        ];

        return Inertia::render('Approvals/Index', [
            'requests' => $requests,
            'stats' => $stats,
        ]);
    }

    public function approve(ApprovalRequest $request, PaymentRequest $paymentRequest)
    {
        $validated = $request->validated();
        
        $this->approvalWorkflowService->approve($paymentRequest, $validated['note'] ?? null);

        return redirect()->route('approvals.index')
            ->with('success', 'Phiếu đã được phê duyệt');
    }

    public function reject(ApprovalRequest $request, PaymentRequest $paymentRequest)
    {
        $validated = $request->validated();
        
        $this->approvalWorkflowService->reject($paymentRequest, $validated['reason']);

        return redirect()->route('approvals.index')
            ->with('success', 'Phiếu đã bị từ chối');
    }
}
