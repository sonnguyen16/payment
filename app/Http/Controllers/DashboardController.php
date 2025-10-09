<?php

namespace App\Http\Controllers;

use App\Models\PaymentRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Redirect admin to users management
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.users.index');
        }
        
        $stats = [
            'total_requests' => PaymentRequest::where('user_id', $user->id)
                ->whereNotIn('status', ['cancelled', 'deleted'])
                ->count(),
            'pending_approval' => PaymentRequest::where('user_id', $user->id)->pending()->count(),
            'approved_this_month' => PaymentRequest::where('user_id', $user->id)
                ->where('status', 'paid')
                ->whereMonth('paid_at', now()->month)
                ->count(),
            'total_amount_this_month' => PaymentRequest::where('user_id', $user->id)
                ->where('status', 'paid')
                ->whereMonth('paid_at', now()->month)
                ->sum('amount'),
        ];

        $recentRequests = PaymentRequest::with(['project'])
            ->where('user_id', $user->id)
            ->whereNotIn('status', ['cancelled', 'deleted'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $pendingMyApproval = null;
        if ($user->hasAnyRole(['department_head', 'accountant', 'ceo'])) {
            $pendingMyApproval = PaymentRequest::with(['user', 'project'])
                ->where('current_approver_id', $user->id)
                ->whereNotIn('status', ['cancelled', 'rejected', 'paid', 'deleted'])
                ->orderBy('priority', 'desc')
                ->limit(5)
                ->get();
        }

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recent_requests' => $recentRequests,
            'pending_my_approval' => $pendingMyApproval,
        ]);
    }
}
