<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExpenseCategoryController extends Controller
{
    public function __construct()
    {
        // Chỉ admin mới có quyền truy cập
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasRole('admin')) {
                abort(403, 'Bạn không có quyền truy cập chức năng này');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ExpenseCategory::withCount('expenseVouchers');

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'active');
        }

        $categories = $query->orderBy('name')->paginate(20);

        return Inertia::render('ExpenseCategories/Index', [
            'categories' => $categories,
            'filters' => [
                'search' => $request->search,
                'is_active' => $request->is_active,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('ExpenseCategories/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name',
            'description' => 'nullable|string|max:500',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục',
            'name.unique' => 'Tên danh mục đã tồn tại',
            'color.required' => 'Vui lòng chọn màu sắc',
            'color.regex' => 'Màu sắc không hợp lệ',
        ]);

        ExpenseCategory::create($validated);

        return redirect()->route('admin.expense-categories.index')
            ->with('success', 'Danh mục chi đã được tạo thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(ExpenseCategory $expenseCategory)
    {
        $expenseCategory->load(['expenseVouchers' => function($query) {
            $query->with(['user', 'project'])->latest()->limit(10);
        }]);

        return Inertia::render('ExpenseCategories/Show', [
            'category' => $expenseCategory,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExpenseCategory $expenseCategory)
    {
        $expenseCategory->loadCount('expenseVouchers');

        return Inertia::render('ExpenseCategories/Edit', [
            'category' => $expenseCategory,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name,' . $expenseCategory->id,
            'description' => 'nullable|string|max:500',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục',
            'name.unique' => 'Tên danh mục đã tồn tại',
            'color.required' => 'Vui lòng chọn màu sắc',
            'color.regex' => 'Màu sắc không hợp lệ',
        ]);

        $expenseCategory->update($validated);

        return redirect()->route('admin.expense-categories.index')
            ->with('success', 'Danh mục chi đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExpenseCategory $expenseCategory)
    {
        // Kiểm tra xem có phiếu chi nào đang sử dụng category này không
        if ($expenseCategory->expenseVouchers()->count() > 0) {
            return back()->with('error', 'Không thể xóa danh mục này vì đang có phiếu chi sử dụng!');
        }

        $expenseCategory->delete();

        return redirect()->route('admin.expense-categories.index')
            ->with('success', 'Danh mục chi đã được xóa thành công!');
    }
}
