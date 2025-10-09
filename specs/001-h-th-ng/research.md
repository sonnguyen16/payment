# Research: Hệ thống quản lý quy trình duyệt chi theo vai trò

**Feature**: 001-h-th-ng  
**Date**: 2025-10-05  
**Status**: Complete

## Overview

Research findings cho việc triển khai hệ thống quản lý quy trình phê duyệt chi phí đa cấp sử dụng Laravel 10 + Inertia.js + Vue 3.

---

## 1. Laravel Breeze với Vue + Inertia Setup

### Decision
Sử dụng Laravel Breeze starter kit với Vue 3 + Inertia.js stack.

### Rationale
- **Official support**: Laravel Breeze là starter kit chính thức từ Laravel team
- **Pre-configured**: Đã setup sẵn authentication, Inertia.js, Vue 3, Vite
- **Lightweight**: Không bloated như Jetstream, dễ customize
- **Modern stack**: Sử dụng Composition API, TypeScript support
- **Best practices**: Follow Laravel conventions và Vue 3 best practices

### Installation Command
```bash
composer create-project laravel/laravel payment
cd payment
composer require laravel/breeze --dev
php artisan breeze:install vue
npm install && npm run dev
php artisan migrate
```

### Alternatives Considered
- **Laravel Jetstream**: Quá phức tạp, có nhiều features không cần thiết (teams, 2FA)
- **Manual setup**: Mất thời gian, dễ miss configuration
- **Rejected**: Breeze là lựa chọn tối ưu cho admin panel

---

## 2. Spatie Laravel Permission vs Policy + Gate

### Decision
Sử dụng **Spatie Laravel Permission** cho role-based access control.

### Rationale
- **Complex permissions**: Hệ thống có 4 roles với permissions phức tạp theo văn phòng/bộ phận
- **Dynamic roles**: Kế toán quản lý nhiều văn phòng, cần assign permissions động
- **Database-driven**: Roles và permissions lưu trong DB, dễ quản lý qua UI
- **Proven solution**: Package phổ biến nhất (10k+ stars), well-maintained
- **Blade directives**: `@role`, `@can` helpers tiện lợi
- **Middleware**: `role:admin`, `permission:approve-payment` middleware sẵn có

### Installation
```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

### Roles Structure
```php
// Roles
- employee (Nhân viên)
- department_head (Trưởng bộ phận)
- accountant (Kế toán)
- ceo (Tổng giám đốc)

// Permissions
- create_payment_request
- edit_own_payment_request
- cancel_own_payment_request
- approve_payment_request
- reject_payment_request
- delete_payment_request
- process_payment
- view_all_payment_requests
- view_department_payment_requests
- view_office_payment_requests
```

### Alternatives Considered
- **Policy + Gate**: Tốt cho simple authorization, nhưng không linh hoạt với dynamic roles
- **Custom implementation**: Reinventing the wheel, maintenance overhead
- **Rejected**: Spatie Permission phù hợp hơn cho requirements phức tạp

---

## 3. Realtime Notifications Architecture

### Decision
Sử dụng **Laravel Queue (database driver)** + **Polling** cho thông báo realtime.

### Rationale
- **Simplicity**: Không cần setup WebSocket server (Pusher, Laravel Echo Server)
- **Cost-effective**: Không phụ thuộc third-party service
- **Sufficient performance**: Polling mỗi 30s-60s đủ cho use case (không cần instant messaging)
- **Laravel native**: Sử dụng Queue + Notification system có sẵn
- **Easy deployment**: Không cần infrastructure phức tạp

### Implementation Approach
```php
// Backend: Queue job
class SendRealtimeNotification implements ShouldQueue
{
    public function handle()
    {
        Notification::send($users, new PaymentRequestNotification($request));
    }
}

// Frontend: Polling với Composable
// useNotification.js
const { data: notifications } = usePolling('/api/notifications/unread', {
    interval: 30000 // 30 seconds
});
```

### Upgrade Path (Future)
Nếu cần realtime thực sự:
1. Thêm Laravel Reverb (official WebSocket server từ Laravel 11)
2. Hoặc sử dụng Pusher/Ably
3. Code notification logic không cần thay đổi nhiều

### Alternatives Considered
- **Laravel Reverb**: Chỉ có từ Laravel 11, project dùng Laravel 10
- **Pusher**: Tốn phí, phụ thuộc third-party
- **Laravel Echo Server**: Deprecated, không maintain
- **Socket.io**: Cần setup Node.js server riêng, phức tạp
- **Rejected**: Polling đủ đơn giản và hiệu quả cho requirements hiện tại

---

## 4. AdminLTE Integration với Inertia.js

### Decision
Sử dụng **AdminLTE 3** qua CDN, tích hợp vào Inertia layout.

### Rationale
- **Requirement**: Dùng CDN thay vì npm (theo yêu cầu)
- **Proven UI**: AdminLTE là admin template phổ biến, nhiều components
- **Bootstrap-based**: Tương thích với Bootstrap 5
- **Free & Open Source**: MIT license
- **Responsive**: Mobile-friendly out of the box

### Integration Approach
```vue
<!-- resources/js/Layouts/AdminLayout.vue -->
<template>
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Navbar -->
        </nav>
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Sidebar -->
        </aside>
        <div class="content-wrapper">
            <slot /> <!-- Inertia page content -->
        </div>
    </div>
</template>

<script setup>
// Import AdminLTE JS từ CDN trong app.blade.php
</script>
```

### CDN Links (app.blade.php)
```html
<!-- AdminLTE CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
<!-- Bootstrap 5 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- jQuery (required by AdminLTE) -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE JS -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
```

### Alternatives Considered
- **Tailwind Admin Templates**: Breeze mặc định dùng Tailwind, nhưng requirement yêu cầu AdminLTE
- **Vuetify/Element Plus**: Cần npm install, không dùng CDN
- **Custom UI**: Mất thời gian, không cần thiết
- **Rejected**: AdminLTE đáp ứng đủ requirements

---

## 5. File Upload Strategy

### Decision
Sử dụng **Laravel Storage** với `storage/app/public` và symbolic link.

### Rationale
- **Laravel native**: Sử dụng Storage facade, không cần package thêm
- **Secure**: Files không public trực tiếp, access qua controller
- **Organized**: Lưu theo structure `documents/{payment_request_id}/{filename}`
- **Easy backup**: Tất cả files trong 1 folder, dễ backup/restore

### Implementation
```bash
# Tạo symlink
php artisan storage:link
```

```php
// Controller
public function uploadDocument(Request $request, PaymentRequest $paymentRequest)
{
    $path = $request->file('document')->store(
        "documents/{$paymentRequest->id}",
        'public'
    );
    
    $paymentRequest->documents()->create([
        'filename' => $request->file('document')->getClientOriginalName(),
        'path' => $path,
        'type' => $request->type,
    ]);
}

// Access file
Route::get('/documents/{document}', function (Document $document) {
    return Storage::disk('public')->download($document->path);
})->middleware('auth');
```

### File Structure
```
storage/app/public/
└── documents/
    ├── 1/  # payment_request_id
    │   ├── invoice-001.pdf
    │   └── receipt-002.jpg
    └── 2/
        └── contract-003.pdf
```

### Alternatives Considered
- **AWS S3**: Overkill cho scale hiện tại, tốn phí
- **Local public folder**: Không secure, không control access
- **Rejected**: Laravel Storage đủ đơn giản và hiệu quả

---

## 6. Audit Trail Implementation

### Decision
Sử dụng custom **ApprovalHistory** model + **Event/Listener** pattern.

### Rationale
- **Full control**: Track chính xác những gì cần (old value, new value, reason)
- **Performance**: Không overhead như Spatie Activity Log (track mọi thứ)
- **Business logic**: Tích hợp với approval workflow
- **Queryable**: Dễ query lịch sử theo phiếu, người dùng, hành động

### Database Schema
```php
// approval_histories table
Schema::create('approval_histories', function (Blueprint $table) {
    $table->id();
    $table->foreignId('payment_request_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained(); // Người thực hiện
    $table->string('action'); // created, submitted, approved, rejected, cancelled, deleted, updated
    $table->string('from_status')->nullable();
    $table->string('to_status')->nullable();
    $table->text('reason')->nullable(); // Lý do từ chối, hủy, chỉnh sửa
    $table->json('changes')->nullable(); // Old/new values cho updates
    $table->timestamps();
});
```

### Event/Listener Pattern
```php
// Event
class PaymentRequestUpdated
{
    public function __construct(
        public PaymentRequest $paymentRequest,
        public array $changes,
        public string $reason
    ) {}
}

// Listener
class LogApprovalHistory
{
    public function handle(PaymentRequestUpdated $event)
    {
        ApprovalHistory::create([
            'payment_request_id' => $event->paymentRequest->id,
            'user_id' => auth()->id(),
            'action' => 'updated',
            'from_status' => $event->paymentRequest->getOriginal('status'),
            'to_status' => $event->paymentRequest->status,
            'reason' => $event->reason,
            'changes' => $event->changes,
        ]);
    }
}
```

### Alternatives Considered
- **Spatie Laravel Activitylog**: Track tất cả models, overhead không cần thiết
- **Laravel Auditing**: Tương tự, quá general-purpose
- **Database triggers**: Khó maintain, không flexible
- **Rejected**: Custom solution phù hợp nhất với business requirements

---

## 7. State Machine for Approval Workflow

### Decision
Sử dụng **Enum-based state management** với **Service layer** xử lý transitions.

### Rationale
- **Type safety**: PHP 8.1+ Enums provide type safety
- **Explicit states**: Rõ ràng các trạng thái có thể có
- **Centralized logic**: ApprovalWorkflowService quản lý tất cả transitions
- **Validation**: Dễ validate transitions hợp lệ

### PaymentRequestStatus Enum
```php
enum PaymentRequestStatus: string
{
    case DRAFT = 'draft';
    case PENDING_DEPARTMENT_HEAD = 'pending_department_head';
    case PENDING_ACCOUNTANT = 'pending_accountant';
    case PENDING_CEO = 'pending_ceo';
    case PENDING_PAYMENT = 'pending_payment';
    case PAID = 'paid';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
    case DELETED = 'deleted';
    
    public function canTransitionTo(self $newStatus): bool
    {
        return match($this) {
            self::DRAFT => in_array($newStatus, [self::PENDING_DEPARTMENT_HEAD, self::CANCELLED]),
            self::PENDING_DEPARTMENT_HEAD => in_array($newStatus, [
                self::PENDING_ACCOUNTANT, 
                self::REJECTED, 
                self::DELETED
            ]),
            // ... other transitions
        };
    }
}
```

### ApprovalWorkflowService
```php
class ApprovalWorkflowService
{
    public function approve(PaymentRequest $request): void
    {
        $nextStatus = match($request->status) {
            PaymentRequestStatus::PENDING_DEPARTMENT_HEAD => PaymentRequestStatus::PENDING_ACCOUNTANT,
            PaymentRequestStatus::PENDING_ACCOUNTANT => PaymentRequestStatus::PENDING_CEO,
            PaymentRequestStatus::PENDING_CEO => PaymentRequestStatus::PENDING_PAYMENT,
            default => throw new InvalidStateTransitionException(),
        };
        
        if (!$request->status->canTransitionTo($nextStatus)) {
            throw new InvalidStateTransitionException();
        }
        
        $request->update(['status' => $nextStatus]);
        event(new PaymentRequestApproved($request));
    }
}
```

### Alternatives Considered
- **State Machine packages** (asantibanez/laravel-eloquent-state-machines): Overkill, thêm dependency
- **Simple string status**: Không type-safe, dễ lỗi typo
- **Rejected**: Enum + Service layer đủ mạnh và đơn giản

---

## 8. Testing Strategy

### Decision
Sử dụng **Feature tests** cho workflows + **Unit tests** cho Services/Policies.

### Rationale
- **Business-critical**: Approval workflow là core feature, cần test kỹ
- **Regression prevention**: Tránh break workflow khi refactor
- **Documentation**: Tests serve as living documentation
- **CI/CD ready**: Dễ integrate vào pipeline

### Test Structure
```php
// Feature test
class ApprovalWorkflowTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function employee_can_create_payment_request()
    {
        $employee = User::factory()->role('employee')->create();
        
        $this->actingAs($employee)
            ->post('/payment-requests', [
                'type' => 'advance',
                'amount' => 1000000,
                'description' => 'Tạm ứng công tác',
                'project_id' => Project::factory()->create()->id,
            ])
            ->assertRedirect();
            
        $this->assertDatabaseHas('payment_requests', [
            'user_id' => $employee->id,
            'status' => PaymentRequestStatus::PENDING_DEPARTMENT_HEAD,
        ]);
    }
    
    /** @test */
    public function department_head_can_approve_request()
    {
        // ... test approval flow
    }
}

// Unit test
class ApprovalWorkflowServiceTest extends TestCase
{
    /** @test */
    public function it_transitions_to_correct_next_status()
    {
        $service = new ApprovalWorkflowService();
        $request = PaymentRequest::factory()->create([
            'status' => PaymentRequestStatus::PENDING_DEPARTMENT_HEAD
        ]);
        
        $service->approve($request);
        
        $this->assertEquals(
            PaymentRequestStatus::PENDING_ACCOUNTANT, 
            $request->fresh()->status
        );
    }
}
```

### Coverage Goals
- **Feature tests**: 100% coverage cho approval workflows
- **Unit tests**: 80%+ coverage cho Services, Policies
- **Integration tests**: Key scenarios từ spec.md

### Alternatives Considered
- **E2E tests (Dusk)**: Quá chậm cho CI, maintenance overhead
- **Only unit tests**: Không catch integration issues
- **Rejected**: Feature + Unit tests balance tốt nhất

---

## 9. Performance Optimization

### Decision
Sử dụng **Eager loading**, **Database indexing**, và **Query optimization**.

### Rationale
- **N+1 prevention**: Eager load relationships (user, approver, project)
- **Fast queries**: Index foreign keys và status columns
- **Pagination**: Limit results, không load toàn bộ
- **Caching**: Cache roles/permissions (Spatie built-in)

### Database Indexes
```php
Schema::create('payment_requests', function (Blueprint $table) {
    $table->id();
    // ... columns
    
    // Indexes
    $table->index('status'); // Filter by status
    $table->index('user_id'); // Filter by creator
    $table->index('created_at'); // Sort by date
    $table->index(['status', 'priority']); // Composite for dashboard
});
```

### Query Optimization
```php
// Bad: N+1 query
$requests = PaymentRequest::all(); // 1 query
foreach ($requests as $request) {
    echo $request->user->name; // N queries
}

// Good: Eager loading
$requests = PaymentRequest::with(['user', 'project', 'currentApprover'])
    ->where('status', PaymentRequestStatus::PENDING_ACCOUNTANT)
    ->orderBy('priority', 'desc')
    ->orderBy('created_at', 'asc')
    ->paginate(20); // 1 query + 3 eager loads
```

### Alternatives Considered
- **Redis caching**: Premature optimization, không cần thiết ở scale hiện tại
- **Database replication**: Overkill
- **Rejected**: Simple optimizations đủ cho 100+ concurrent users

---

## 10. Deployment & Environment

### Decision
Development trên **Laragon** (Windows), production deploy lên **Linux server** (Ubuntu/CentOS).

### Rationale
- **Laragon**: Lightweight, fast, PHP 8.1+ support, easy MySQL management
- **Production**: Standard LEMP stack (Linux, Nginx, MySQL, PHP-FPM)
- **Compatibility**: Laravel runs identically on Windows/Linux

### Development Setup (Laragon)
```bash
# Laragon đã có sẵn:
- PHP 8.1+
- MySQL/MariaDB
- Composer
- Node.js/npm

# Chỉ cần:
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run dev
```

### Production Checklist
```bash
# Server requirements
- PHP 8.1+ (with extensions: mbstring, xml, pdo, openssl, tokenizer, json, bcmath)
- MySQL 8.0+ / MariaDB 10.6+
- Nginx
- Redis (optional, for queue)
- Supervisor (for queue workers)

# Deployment
composer install --optimize-autoloader --no-dev
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
php artisan migrate --force
```

### Alternatives Considered
- **Docker**: Thêm complexity, không cần thiết cho monolith
- **XAMPP**: Outdated, Laragon tốt hơn
- **Rejected**: Laragon + LEMP là setup chuẩn cho Laravel

---

## Summary

Tất cả technical decisions đã được research và finalize. Không còn `NEEDS CLARIFICATION` nào. Stack:

- **Backend**: Laravel 10 + PHP 8.1+ + MySQL
- **Frontend**: Vue 3 Composition API + Inertia.js + AdminLTE (CDN)
- **Auth**: Laravel Breeze
- **Authorization**: Spatie Laravel Permission
- **Notifications**: Queue + Polling (upgrade path: Laravel Reverb)
- **File Upload**: Laravel Storage (local disk)
- **Audit Trail**: Custom ApprovalHistory model
- **State Management**: Enum-based với Service layer
- **Testing**: Feature + Unit tests (PHPUnit)
- **Development**: Laragon
- **Production**: LEMP stack

**Status**: ✅ Research complete, ready for Phase 1 (Design & Contracts)
