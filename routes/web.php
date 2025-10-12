<?php

use App\Http\Controllers\Admin\DepartmentController as AdminDepartmentController;
use App\Http\Controllers\Admin\OfficeController as AdminOfficeController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ExpenseVoucherController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentRequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReportController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Payment Requests
    Route::resource('payment-requests', PaymentRequestController::class);
    Route::post('payment-requests/{paymentRequest}/submit', [PaymentRequestController::class, 'submit'])
        ->name('payment-requests.submit');
    Route::post('payment-requests/{paymentRequest}/cancel', [PaymentRequestController::class, 'cancel'])
        ->name('payment-requests.cancel');
    Route::get('payment-requests/{paymentRequest}/pdf', [PaymentRequestController::class, 'exportPdf'])
        ->name('payment-requests.pdf');
    
    // Approvals
    Route::get('approvals', [ApprovalController::class, 'index'])
        ->name('approvals.index')
        ->middleware('role:department_head|accountant|ceo');
    Route::post('approvals/{paymentRequest}/approve', [ApprovalController::class, 'approve'])
        ->name('approvals.approve');
    Route::post('approvals/{paymentRequest}/reject', [ApprovalController::class, 'reject'])
        ->name('approvals.reject');
    
    // Expense Vouchers
    Route::resource('expense-vouchers', ExpenseVoucherController::class);
    
    // Documents
    Route::post('payment-requests/{paymentRequest}/documents', [DocumentController::class, 'store'])
        ->name('documents.store');
    Route::get('documents/{document}', [DocumentController::class, 'show'])
        ->name('documents.show');
    Route::delete('documents/{document}', [DocumentController::class, 'destroy'])
        ->name('documents.destroy');
    
    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    
    // Reports (only for accountant and ceo)
    Route::get('reports/payment-requests', [ReportController::class, 'paymentRequests'])
        ->name('reports.payment-requests')
        ->middleware('role:accountant|ceo');
    
    // Admin Routes (only for admin role)
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::resource('users', AdminUserController::class);
        Route::resource('offices', AdminOfficeController::class);
        Route::resource('departments', AdminDepartmentController::class);
        Route::resource('projects', ProjectController::class);
        Route::resource('categories', CategoryController::class);
    });
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
