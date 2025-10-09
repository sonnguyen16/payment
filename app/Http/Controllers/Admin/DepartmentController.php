<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::with(['office', 'head'])
            ->withCount('users')
            ->orderBy('name')
            ->paginate(20);

        return Inertia::render('Admin/Departments/Index', [
            'departments' => $departments,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $offices = \App\Models\Office::all();

        return Inertia::render('Admin/Departments/Create', [
            'offices' => $offices,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'office_id' => 'required|exists:offices,id',
        ]);

        Department::create($validated);

        return redirect()->route('admin.departments.index')->with('success', 'Bộ phận đã được tạo');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        $offices = \App\Models\Office::all();

        return Inertia::render('Admin/Departments/Edit', [
            'department' => $department,
            'offices' => $offices,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'office_id' => 'required|exists:offices,id',
        ]);

        $department->update($validated);

        return redirect()->route('admin.departments.index')->with('success', 'Bộ phận đã được cập nhật');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()->route('admin.departments.index')->with('success', 'Bộ phận đã được xóa');
    }
}
