<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class UserController extends Controller
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
        $users = User::with(['roles', 'office', 'department'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = \Spatie\Permission\Models\Role::all();
        $offices = \App\Models\Office::all();
        $departments = \App\Models\Department::all();

        return Inertia::render('Admin/Users/Create', [
            'roles' => $roles,
            'offices' => $offices,
            'departments' => $departments,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'office_id' => 'nullable|exists:offices,id',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        // Check if department_head role and department already has a head
        if ($validated['role'] === 'department_head' && $validated['department_id']) {
            $existingHead = \App\Models\Department::find($validated['department_id'])->head;
            if ($existingHead) {
                return redirect()->back()->withErrors([
                    'department_id' => 'Bộ phận này đã có trưởng bộ phận: ' . $existingHead->name,
                ]);
            }
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'office_id' => $validated['office_id'],
            'department_id' => $validated['department_id'],
        ]);

        $user->assignRole($validated['role']);

        // If department_head, set as head of department
        if ($validated['role'] === 'department_head' && $validated['department_id']) {
            \App\Models\Department::find($validated['department_id'])->update([
                'head_user_id' => $user->id
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Người dùng đã được tạo');
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
    public function edit(User $user)
    {
        $user->load(['roles', 'office', 'department']);
        $roles = \Spatie\Permission\Models\Role::all();
        $offices = \App\Models\Office::all();
        $departments = \App\Models\Department::all();

        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
            'roles' => $roles,
            'offices' => $offices,
            'departments' => $departments,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'office_id' => 'nullable|exists:offices,id',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $oldRole = $user->roles->first()?->name;
        $oldDepartmentId = $user->department_id;

        // Check if changing to department_head and department already has a head
        if ($validated['role'] === 'department_head' && $validated['department_id']) {
            $department = \App\Models\Department::find($validated['department_id']);
            $existingHead = $department->head;

            // If there's an existing head and it's not this user
            if ($existingHead && $existingHead->id !== $user->id) {
                return redirect()->back()->withErrors([
                    'department_id' => 'Bộ phận này đã có trưởng bộ phận: ' . $existingHead->name,
                ]);
            }
        }

        // If user was department_head and changing role or department, remove from head position
        if ($oldRole === 'department_head' && $oldDepartmentId) {
            if ($validated['role'] !== 'department_head' || $validated['department_id'] != $oldDepartmentId) {
                \App\Models\Department::find($oldDepartmentId)->update(['head_user_id' => null]);
            }
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'office_id' => $validated['office_id'],
            'department_id' => $validated['department_id'],
        ]);

        if ($validated['password']) {
            $user->update(['password' => bcrypt($validated['password'])]);
        }

        $user->syncRoles([$validated['role']]);

        // If department_head, set as head of department
        if ($validated['role'] === 'department_head' && $validated['department_id']) {
            \App\Models\Department::find($validated['department_id'])->update([
                'head_user_id' => $user->id
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Người dùng đã được cập nhật');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Không thể xóa chính mình');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Người dùng đã được xóa');
    }
}
