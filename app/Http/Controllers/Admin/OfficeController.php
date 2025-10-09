<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Office;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OfficeController extends Controller
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
        $offices = Office::withCount(['departments', 'users'])
            ->orderBy('name')
            ->paginate(20);

        return Inertia::render('Admin/Offices/Index', [
            'offices' => $offices,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Offices/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string',
        ]);

        Office::create($validated);

        return redirect()->route('admin.offices.index')->with('success', 'Văn phòng đã được tạo');
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
    public function edit(Office $office)
    {
        return Inertia::render('Admin/Offices/Edit', [
            'office' => $office,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Office $office)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string',
        ]);

        $office->update($validated);

        return redirect()->route('admin.offices.index')->with('success', 'Văn phòng đã được cập nhật');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Office $office)
    {
        $office->delete();

        return redirect()->route('admin.offices.index')->with('success', 'Văn phòng đã được xóa');
    }
}
