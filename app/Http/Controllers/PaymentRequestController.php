<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequestRequest;
use App\Http\Requests\UpdatePaymentRequestRequest;
use App\Models\PaymentRequest;
use App\Models\Project;
use App\Services\PaymentRequestService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentRequestController extends Controller
{
    public function __construct(
        protected PaymentRequestService $paymentRequestService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PaymentRequest::with(['user', 'project', 'currentApprover', 'category'])
            ->where(function($q) {
                $user = auth()->user();

                if ($user->hasRole('ceo')) {
                    // CEO sees all
                    return;
                } elseif ($user->hasRole('accountant')) {
                    // Accountant sees requests from their offices
                    $q->whereHas('user', function($query) use ($user) {
                        $query->where('office_id', $user->office_id);
                    });
                } elseif ($user->hasRole('department_head')) {
                    // Department head sees requests from their department
                    $q->whereHas('user', function($query) use ($user) {
                        $query->where('department_id', $user->department_id);
                    });
                } else {
                    // Employee sees own requests
                    $q->where('user_id', $user->id);
                }
            });

        // Filters
        if ($request->status) {
            $query->where('status', $request->status);
        } else {
            // Mặc định không hiển thị phiếu cancelled và deleted
            $query->whereNotIn('status', ['cancelled', 'deleted']);
        }

        if ($request->priority) {
            $query->where('priority', $request->priority);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('description', 'like', "%{$request->search}%")
                  ->orWhere('reason', 'like', "%{$request->search}%");
            });
        }

        $requests = $query->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('PaymentRequests/Index', [
            'requests' => $requests,
            'filters' => $request->only(['status', 'priority', 'type', 'search']),
            'can' => [
                'create' => auth()->user()->can('create', PaymentRequest::class),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', PaymentRequest::class);

        return Inertia::render('PaymentRequests/Create', [
            'projects' => Project::active()->get(),
            'categories' => \App\Models\Category::active()->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentRequestRequest $request)
    {
        $paymentRequest = $this->paymentRequestService->create($request->validated());

        return redirect()->route('payment-requests.show', $paymentRequest)
            ->with('success', 'Phiếu đề xuất đã được tạo thành công');
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentRequest $paymentRequest)
    {
        $this->authorize('view', $paymentRequest);

        $paymentRequest->load([
            'user',
            'project',
            'currentApprover',
            'category',
            'details',
            'documents.uploader',
            'approvalHistories.user',
            'updateHistories.user'
        ]);

        return Inertia::render('PaymentRequests/Show', [
            'request' => $paymentRequest,
            'can' => [
                'update' => auth()->user()->can('update', $paymentRequest),
                'cancel' => auth()->user()->can('cancel', $paymentRequest),
                'delete' => auth()->user()->can('delete', $paymentRequest),
                'approve' => auth()->user()->can('approve', $paymentRequest),
                'reject' => auth()->user()->can('reject', $paymentRequest),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentRequest $paymentRequest)
    {
        $this->authorize('update', $paymentRequest);

        $paymentRequest->load('details');

        return Inertia::render('PaymentRequests/Edit', [
            'request' => $paymentRequest,
            'projects' => Project::active()->get(),
            'categories' => \App\Models\Category::active()->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentRequestRequest $request, PaymentRequest $paymentRequest)
    {
        $validated = $request->validated();

        $this->paymentRequestService->update(
            $paymentRequest,
            $validated,
            $validated['update_reason']
        );

        return redirect()->route('payment-requests.show', $paymentRequest)
            ->with('success', 'Phiếu đã được cập nhật');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentRequest $paymentRequest)
    {
        $this->authorize('delete', $paymentRequest);

        $this->paymentRequestService->delete($paymentRequest);

        return redirect()->route('payment-requests.index')
            ->with('success', 'Phiếu đã được xóa');
    }

    public function submit(PaymentRequest $paymentRequest)
    {
        $this->authorize('update', $paymentRequest);

        $this->paymentRequestService->submit($paymentRequest);

        return redirect()->route('payment-requests.show', $paymentRequest)
            ->with('success', 'Phiếu đã được gửi để phê duyệt');
    }

    public function cancel(Request $request, PaymentRequest $paymentRequest)
    {
        $this->authorize('cancel', $paymentRequest);

        $this->paymentRequestService->cancel($paymentRequest, $request->reason);

        return redirect()->route('payment-requests.index')
            ->with('success', 'Phiếu đã được hủy thành công!');
    }

    /**
     * Export payment request to PDF
     */
    public function exportPdf(PaymentRequest $paymentRequest)
    {
        $this->authorize('view', $paymentRequest);

        // Load relationships
        $paymentRequest->load(['user.department', 'user.office', 'category', 'project']);

        // Get approvers for signatures
        $ceo = \App\Models\User::role('ceo')->first();
        $accountant = \App\Models\User::role('accountant')
            ->where('office_id', $paymentRequest->user->office_id)
            ->first();
        $departmentHead = \App\Models\User::role('department_head')
            ->where('department_id', $paymentRequest->user->department_id)
            ->first();

        // Convert amount to words
        $amountInWords = \App\Helpers\NumberToWords::convert($paymentRequest->amount);

        $pdf = \PDF::loadView('pdf.payment-request', [
            'request' => $paymentRequest,
            'amountInWords' => $amountInWords,
            'ceo' => $ceo,
            'accountant' => $accountant,
            'departmentHead' => $departmentHead,
        ]);

        $filename = 'phieu-de-xuat-' . $paymentRequest->id . '.pdf';

        return $pdf->stream($filename);
    }
}
