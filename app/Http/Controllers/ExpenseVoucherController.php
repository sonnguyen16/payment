<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseVoucherRequest;
use App\Http\Requests\UpdateExpenseVoucherRequest;
use App\Models\Category;
use App\Models\ExpenseVoucher;
use App\Models\Project;
use App\Services\ExpenseVoucherService;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ExpenseVoucherController extends Controller
{
    protected $expenseVoucherService;

    public function __construct(ExpenseVoucherService $expenseVoucherService)
    {
        $this->expenseVoucherService = $expenseVoucherService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', ExpenseVoucher::class);

        $query = ExpenseVoucher::with(['user', 'category', 'project']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('recipient', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Project filter
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('expense_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('expense_date', '<=', $request->end_date);
        }

        $vouchers = $query->latest('expense_date')->paginate(15)->withQueryString();

        return Inertia::render('ExpenseVouchers/Index', [
            'vouchers' => $vouchers,
            'filters' => $request->only(['search', 'category_id', 'project_id', 'start_date', 'end_date']),
            'categories' => Category::where('is_active', true)->get(),
            'projects' => Project::whereIn('status', ['active', 'planning'])->get(),
            'can' => [
                'create' => auth()->user()->can('create', ExpenseVoucher::class),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', ExpenseVoucher::class);

        return Inertia::render('ExpenseVouchers/Create', [
            'categories' => Category::where('is_active', true)->get(),
            'projects' => Project::whereIn('status', ['active', 'planning'])->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExpenseVoucherRequest $request)
    {
        $validated = $request->validated();

        $this->expenseVoucherService->create($validated);

        return redirect()->route('expense-vouchers.index')
            ->with('success', 'Phiếu chi đã được tạo thành công');
    }

    /**
     * Display the specified resource.
     */
    public function show(ExpenseVoucher $expenseVoucher)
    {
        $this->authorize('view', $expenseVoucher);

        $expenseVoucher->load([
            'user',
            'category',
            'project',
            'updateHistories.user'
        ]);

        return Inertia::render('ExpenseVouchers/Show', [
            'voucher' => $expenseVoucher,
            'can' => [
                'update' => auth()->user()->can('update', $expenseVoucher),
                'delete' => auth()->user()->can('delete', $expenseVoucher),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExpenseVoucher $expenseVoucher)
    {
        $this->authorize('update', $expenseVoucher);

        $expenseVoucher->load(['category', 'project']);

        return Inertia::render('ExpenseVouchers/Edit', [
            'voucher' => $expenseVoucher,
            'categories' => Category::where('is_active', true)->get(),
            'projects' => Project::whereIn('status', ['active', 'planning'])->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExpenseVoucherRequest $request, ExpenseVoucher $expenseVoucher)
    {
        $validated = $request->validated();
        $updateReason = $validated['update_reason'];
        unset($validated['update_reason']);

        $this->expenseVoucherService->update(
            $expenseVoucher,
            $validated,
            $updateReason
        );

        return redirect()->route('expense-vouchers.show', $expenseVoucher)
            ->with('success', 'Phiếu chi đã được cập nhật');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExpenseVoucher $expenseVoucher)
    {
        $this->authorize('delete', $expenseVoucher);

        $this->expenseVoucherService->delete($expenseVoucher);

        return redirect()->route('expense-vouchers.index')
            ->with('success', 'Phiếu chi đã được xóa');
    }
}
