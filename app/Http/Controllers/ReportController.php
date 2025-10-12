<?php

namespace App\Http\Controllers;

use App\Models\PaymentRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    /**
     * Display payment requests report
     */
    public function paymentRequests(Request $request)
    {
        // Check authorization
        $user = auth()->user();
        if (!$user->hasAnyRole(['accountant', 'ceo'])) {
            abort(403, 'Bạn không có quyền truy cập báo cáo này');
        }

        // Get date range from request or default to current month
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!$startDate || !$endDate) {
            $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        // Build query
        $query = PaymentRequest::with(['user', 'category', 'project', 'details'])
            ->where('status', 'paid')
            ->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);

        // Filter by office for accountant
        if ($user->hasRole('accountant')) {
            $officeId = $user->office_id;
            $query->whereHas('user', function ($q) use ($officeId) {
                $q->where('office_id', $officeId);
            });
        }

        // Get results
        $paymentRequests = $query->orderBy('created_at', 'desc')->get();

        // Calculate total
        $totalAmount = $paymentRequests->sum('amount');

        return Inertia::render('Reports/PaymentRequests', [
            'paymentRequests' => $paymentRequests,
            'totalAmount' => $totalAmount,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'userRole' => $user->roles->pluck('name')->first(),
        ]);
    }
}
