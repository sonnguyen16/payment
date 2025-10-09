# Data Model: Hệ thống quản lý quy trình duyệt chi

**Feature**: 001-h-th-ng  
**Date**: 2025-10-05  
**Status**: Complete

## Overview

Định nghĩa data model cho hệ thống quản lý quy trình phê duyệt chi phí, bao gồm entities, relationships, validation rules và state transitions.

---

## Entity Relationship Diagram

```
┌─────────────────┐
│     User        │
│─────────────────│
│ id              │
│ name            │
│ email           │
│ department_id   │◄──────┐
│ office_id       │       │
│ roles (Spatie)  │       │
└─────────────────┘       │
        │                 │
        │ created_by      │
        │                 │
        ▼                 │
┌──────────────────────┐  │
│  PaymentRequest      │  │
│──────────────────────│  │
│ id                   │  │
│ user_id (creator)    │──┘
│ type                 │
│ amount               │
│ description          │
│ reason               │
│ expected_date        │
│ priority             │
│ status               │
│ project_id           │──┐
│ current_approver_id  │  │
│ paid_at              │  │
│ payment_code         │  │
│ timestamps           │  │
└──────────────────────┘  │
        │                 │
        │                 ▼
        │         ┌──────────────┐
        │         │   Project    │
        │         │──────────────│
        │         │ id           │
        │         │ code         │
        │         │ name         │
        │         │ budget       │
        │         │ spent        │
        │         │ status       │
        │         └──────────────┘
        │
        ├──────────────────────┐
        │                      │
        ▼                      ▼
┌──────────────────┐   ┌──────────────────┐
│ ApprovalHistory  │   │    Document      │
│──────────────────│   │──────────────────│
│ id               │   │ id               │
│ payment_req_id   │   │ payment_req_id   │
│ user_id          │   │ filename         │
│ action           │   │ original_name    │
│ from_status      │   │ path             │
│ to_status        │   │ type             │
│ reason           │   │ size             │
│ changes (JSON)   │   │ uploaded_at      │
│ timestamps       │   │ timestamps       │
└──────────────────┘   └──────────────────┘

┌──────────────────┐
│  Notification    │
│──────────────────│
│ id               │
│ user_id          │
│ type             │
│ data (JSON)      │
│ read_at          │
│ timestamps       │
└──────────────────┘

┌──────────────────┐
│   Department     │
│──────────────────│
│ id               │
│ name             │
│ office_id        │
│ head_user_id     │
└──────────────────┘

┌──────────────────┐
│     Office       │
│──────────────────│
│ id               │
│ name             │
│ location         │
└──────────────────┘
```

---

## Entities

### 1. User (Người dùng)

**Purpose**: Quản lý thông tin người dùng và vai trò trong hệ thống.

**Attributes**:
```php
id: bigint (PK)
name: string(255)
email: string(255) UNIQUE
email_verified_at: timestamp NULLABLE
password: string(255)
department_id: bigint (FK -> departments.id) NULLABLE
office_id: bigint (FK -> offices.id) NULLABLE
remember_token: string(100) NULLABLE
created_at: timestamp
updated_at: timestamp
```

**Relationships**:
- `hasMany` PaymentRequest (as creator)
- `hasMany` ApprovalHistory (as approver)
- `hasMany` Notification
- `belongsTo` Department
- `belongsTo` Office
- `belongsToMany` Role (Spatie)
- `belongsToMany` Permission (Spatie)

**Validation Rules**:
```php
'name' => 'required|string|max:255',
'email' => 'required|email|unique:users,email',
'password' => 'required|min:8|confirmed',
'department_id' => 'nullable|exists:departments,id',
'office_id' => 'nullable|exists:offices,id',
```

**Business Rules**:
- Email phải unique
- Mỗi user phải có ít nhất 1 role
- Kế toán phải được assign office_id
- Trưởng bộ phận phải có department_id

---

### 2. PaymentRequest (Phiếu đề xuất chi)

**Purpose**: Lưu trữ thông tin phiếu đề xuất chi phí và trạng thái workflow.

**Attributes**:
```php
id: bigint (PK)
user_id: bigint (FK -> users.id) // Người tạo phiếu
type: enum('advance', 'payment_proposal', 'other_expense')
amount: decimal(15,2)
description: text
reason: text
expected_date: date
priority: enum('urgent', 'normal')
status: enum(
    'draft',
    'pending_department_head',
    'pending_accountant',
    'pending_ceo',
    'pending_payment',
    'paid',
    'rejected',
    'cancelled',
    'deleted'
)
project_id: bigint (FK -> projects.id) NULLABLE
current_approver_id: bigint (FK -> users.id) NULLABLE
rejection_reason: text NULLABLE
payment_code: string(50) NULLABLE
paid_at: timestamp NULLABLE
created_at: timestamp
updated_at: timestamp
deleted_at: timestamp NULLABLE (soft delete)
```

**Relationships**:
- `belongsTo` User (creator)
- `belongsTo` Project
- `belongsTo` User (currentApprover)
- `hasMany` ApprovalHistory
- `hasMany` Document
- `morphMany` Notification

**Validation Rules**:
```php
'type' => 'required|in:advance,payment_proposal,other_expense',
'amount' => 'required|numeric|min:0',
'description' => 'required|string|max:1000',
'reason' => 'required|string|max:500',
'expected_date' => 'required|date|after_or_equal:today',
'priority' => 'required|in:urgent,normal',
'project_id' => 'nullable|exists:projects,id',
```

**Business Rules**:
- Amount không được âm
- Expected date không được trong quá khứ
- Khi status = 'deleted' hoặc 'cancelled', không được chỉnh sửa
- Khi status = 'paid', không được xóa
- Chỉ creator mới được cancel phiếu (khi status != 'paid')
- Chỉ approver hiện tại mới được approve/reject

**State Transitions**:
```
draft -> pending_department_head (submit)
pending_department_head -> pending_accountant (approve by dept head)
pending_department_head -> rejected (reject by dept head)
pending_department_head -> deleted (delete by dept head)
pending_accountant -> pending_ceo (approve by accountant)
pending_accountant -> rejected (reject by accountant)
pending_accountant -> deleted (delete by accountant)
pending_ceo -> pending_payment (approve by CEO)
pending_ceo -> rejected (reject by CEO)
pending_ceo -> deleted (delete by CEO)
pending_payment -> paid (payment processed)
rejected -> pending_department_head (edit & resubmit)
* -> cancelled (cancel by creator, except paid/deleted)
```

**Indexes**:
```php
index('user_id')
index('status')
index('priority')
index('project_id')
index('current_approver_id')
index(['status', 'priority']) // Composite for dashboard queries
index('created_at')
```

---

### 3. ApprovalHistory (Lịch sử phê duyệt)

**Purpose**: Audit trail cho mọi thay đổi trạng thái và chỉnh sửa phiếu.

**Attributes**:
```php
id: bigint (PK)
payment_request_id: bigint (FK -> payment_requests.id)
user_id: bigint (FK -> users.id) // Người thực hiện action
action: enum(
    'created',
    'submitted',
    'approved',
    'rejected',
    'cancelled',
    'deleted',
    'updated',
    'payment_processed'
)
from_status: string(50) NULLABLE
to_status: string(50) NULLABLE
reason: text NULLABLE // Lý do reject/cancel/update
changes: json NULLABLE // Old/new values cho updates
ip_address: string(45) NULLABLE
user_agent: string(255) NULLABLE
created_at: timestamp
```

**Relationships**:
- `belongsTo` PaymentRequest
- `belongsTo` User (actor)

**Validation Rules**:
```php
'payment_request_id' => 'required|exists:payment_requests,id',
'user_id' => 'required|exists:users,id',
'action' => 'required|in:created,submitted,approved,rejected,cancelled,deleted,updated,payment_processed',
'reason' => 'required_if:action,rejected,cancelled,updated',
```

**Business Rules**:
- Không được xóa history records (immutable)
- Mỗi action phải ghi nhận user_id và timestamp
- Reason bắt buộc khi action = rejected/cancelled/updated
- Changes (JSON) lưu diff khi action = updated

**JSON Structure for Changes**:
```json
{
    "field": "amount",
    "old_value": "1000000",
    "new_value": "1500000"
}
```

**Indexes**:
```php
index('payment_request_id')
index('user_id')
index('action')
index('created_at')
```

---

### 4. Project (Dự án)

**Purpose**: Quản lý dự án và tracking chi phí theo dự án.

**Attributes**:
```php
id: bigint (PK)
code: string(50) UNIQUE
name: string(255)
description: text NULLABLE
budget: decimal(15,2)
spent: decimal(15,2) DEFAULT 0 // Tính từ payment_requests
status: enum('active', 'completed', 'cancelled')
start_date: date NULLABLE
end_date: date NULLABLE
created_at: timestamp
updated_at: timestamp
```

**Relationships**:
- `hasMany` PaymentRequest

**Validation Rules**:
```php
'code' => 'required|string|max:50|unique:projects,code',
'name' => 'required|string|max:255',
'budget' => 'required|numeric|min:0',
'status' => 'required|in:active,completed,cancelled',
'start_date' => 'nullable|date',
'end_date' => 'nullable|date|after_or_equal:start_date',
```

**Business Rules**:
- Code phải unique
- Spent được tính tự động từ tổng amount của payment_requests có status = 'paid'
- Không được xóa project có payment_requests liên quan
- Budget warning khi spent > 80% budget

**Computed Attributes**:
```php
remaining_budget: budget - spent
budget_utilization_percentage: (spent / budget) * 100
is_over_budget: spent > budget
```

**Indexes**:
```php
unique('code')
index('status')
```

---

### 5. Document (Chứng từ)

**Purpose**: Lưu trữ file đính kèm (hóa đơn, biên lai, chứng từ).

**Attributes**:
```php
id: bigint (PK)
payment_request_id: bigint (FK -> payment_requests.id)
filename: string(255) // Generated filename
original_name: string(255) // User's original filename
path: string(500) // storage/app/public/documents/{payment_request_id}/{filename}
type: enum('invoice', 'receipt', 'contract', 'other')
mime_type: string(100)
size: integer // bytes
uploaded_by: bigint (FK -> users.id)
uploaded_at: timestamp
created_at: timestamp
updated_at: timestamp
```

**Relationships**:
- `belongsTo` PaymentRequest
- `belongsTo` User (uploader)

**Validation Rules**:
```php
'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB
'type' => 'required|in:invoice,receipt,contract,other',
```

**Business Rules**:
- Max file size: 10MB
- Allowed types: PDF, JPG, JPEG, PNG
- Files lưu trong `storage/app/public/documents/{payment_request_id}/`
- Filename được hash để tránh conflict: `{timestamp}_{hash}.{ext}`
- Không được xóa document sau khi payment_request đã paid

**File Naming Convention**:
```php
// Example: 1704067200_a1b2c3d4e5f6.pdf
$filename = time() . '_' . Str::random(12) . '.' . $extension;
```

**Indexes**:
```php
index('payment_request_id')
index('uploaded_by')
```

---

### 6. Notification (Thông báo)

**Purpose**: Lưu trữ thông báo realtime cho users.

**Attributes**:
```php
id: uuid (PK)
type: string(255) // Notification class name
notifiable_type: string(255) // User
notifiable_id: bigint // user_id
data: json // Notification payload
read_at: timestamp NULLABLE
created_at: timestamp
updated_at: timestamp
```

**Relationships**:
- `morphTo` Notifiable (User)

**Data JSON Structure**:
```json
{
    "payment_request_id": 123,
    "action": "approved",
    "actor_name": "Nguyễn Văn A",
    "message": "Phiếu #123 đã được Trưởng bộ phận phê duyệt",
    "url": "/payment-requests/123"
}
```

**Business Rules**:
- Notifications tự động tạo qua Event/Listener
- Polling API: `/api/notifications/unread` (mỗi 30s)
- Mark as read: PATCH `/api/notifications/{id}/read`
- Auto-delete notifications > 30 days (scheduled job)

**Indexes**:
```php
index(['notifiable_type', 'notifiable_id'])
index('read_at')
index('created_at')
```

---

### 7. Department (Bộ phận)

**Purpose**: Quản lý bộ phận/phòng ban.

**Attributes**:
```php
id: bigint (PK)
name: string(255)
office_id: bigint (FK -> offices.id)
head_user_id: bigint (FK -> users.id) NULLABLE // Trưởng bộ phận
created_at: timestamp
updated_at: timestamp
```

**Relationships**:
- `belongsTo` Office
- `belongsTo` User (head)
- `hasMany` User (members)

**Validation Rules**:
```php
'name' => 'required|string|max:255',
'office_id' => 'required|exists:offices,id',
'head_user_id' => 'nullable|exists:users,id',
```

**Business Rules**:
- Mỗi department thuộc 1 office
- Head user phải có role 'department_head'

---

### 8. Office (Văn phòng)

**Purpose**: Quản lý văn phòng (Vũng Tàu, HCM, Hà Nội, etc.).

**Attributes**:
```php
id: bigint (PK)
name: string(255)
location: string(255) NULLABLE
created_at: timestamp
updated_at: timestamp
```

**Relationships**:
- `hasMany` Department
- `hasMany` User

**Validation Rules**:
```php
'name' => 'required|string|max:255|unique:offices,name',
'location' => 'nullable|string|max:255',
```

**Business Rules**:
- Name phải unique
- Kế toán được assign offices để quản lý phiếu của các departments thuộc offices đó

---

## Enums

### PaymentRequestType
```php
enum PaymentRequestType: string
{
    case ADVANCE = 'advance'; // Tạm ứng
    case PAYMENT_PROPOSAL = 'payment_proposal'; // Đề xuất thanh toán
    case OTHER_EXPENSE = 'other_expense'; // Chi phí khác
    
    public function label(): string
    {
        return match($this) {
            self::ADVANCE => 'Tạm ứng',
            self::PAYMENT_PROPOSAL => 'Đề xuất thanh toán',
            self::OTHER_EXPENSE => 'Chi phí khác',
        };
    }
}
```

### PaymentRequestStatus
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
    
    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Nháp',
            self::PENDING_DEPARTMENT_HEAD => 'Chờ Trưởng bộ phận',
            self::PENDING_ACCOUNTANT => 'Chờ Kế toán',
            self::PENDING_CEO => 'Chờ Tổng giám đốc',
            self::PENDING_PAYMENT => 'Chờ thanh toán',
            self::PAID => 'Đã thanh toán',
            self::REJECTED => 'Bị từ chối',
            self::CANCELLED => 'Đã hủy',
            self::DELETED => 'Đã xóa',
        };
    }
    
    public function canTransitionTo(self $newStatus): bool
    {
        return match($this) {
            self::DRAFT => in_array($newStatus, [
                self::PENDING_DEPARTMENT_HEAD,
                self::CANCELLED
            ]),
            self::PENDING_DEPARTMENT_HEAD => in_array($newStatus, [
                self::PENDING_ACCOUNTANT,
                self::REJECTED,
                self::DELETED
            ]),
            self::PENDING_ACCOUNTANT => in_array($newStatus, [
                self::PENDING_CEO,
                self::REJECTED,
                self::DELETED
            ]),
            self::PENDING_CEO => in_array($newStatus, [
                self::PENDING_PAYMENT,
                self::REJECTED,
                self::DELETED
            ]),
            self::PENDING_PAYMENT => in_array($newStatus, [
                self::PAID
            ]),
            self::REJECTED => in_array($newStatus, [
                self::PENDING_DEPARTMENT_HEAD, // Re-submit after edit
                self::CANCELLED
            ]),
            default => false,
        };
    }
    
    public function nextApprover(): ?string
    {
        return match($this) {
            self::PENDING_DEPARTMENT_HEAD => 'department_head',
            self::PENDING_ACCOUNTANT => 'accountant',
            self::PENDING_CEO => 'ceo',
            default => null,
        };
    }
}
```

### Priority
```php
enum Priority: string
{
    case URGENT = 'urgent';
    case NORMAL = 'normal';
    
    public function label(): string
    {
        return match($this) {
            self::URGENT => 'Gấp',
            self::NORMAL => 'Bình thường',
        };
    }
    
    public function badgeClass(): string
    {
        return match($this) {
            self::URGENT => 'badge-danger',
            self::NORMAL => 'badge-secondary',
        };
    }
}
```

### ApprovalAction
```php
enum ApprovalAction: string
{
    case CREATED = 'created';
    case SUBMITTED = 'submitted';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
    case DELETED = 'deleted';
    case UPDATED = 'updated';
    case PAYMENT_PROCESSED = 'payment_processed';
    
    public function label(): string
    {
        return match($this) {
            self::CREATED => 'Tạo phiếu',
            self::SUBMITTED => 'Gửi duyệt',
            self::APPROVED => 'Phê duyệt',
            self::REJECTED => 'Từ chối',
            self::CANCELLED => 'Hủy',
            self::DELETED => 'Xóa',
            self::UPDATED => 'Chỉnh sửa',
            self::PAYMENT_PROCESSED => 'Đã thanh toán',
        };
    }
}
```

---

## Database Migrations Order

1. `create_offices_table`
2. `create_departments_table`
3. `create_users_table` (Laravel default + custom fields)
4. `create_password_reset_tokens_table` (Laravel default)
5. `create_failed_jobs_table` (Laravel default)
6. `create_personal_access_tokens_table` (Laravel Sanctum, if needed)
7. `create_permission_tables` (Spatie Permission)
8. `create_projects_table`
9. `create_payment_requests_table`
10. `create_approval_histories_table`
11. `create_documents_table`
12. `create_notifications_table` (Laravel default)

---

## Summary

Data model hoàn chỉnh với:
- ✅ 8 core entities
- ✅ 4 enums với business logic
- ✅ Relationships rõ ràng
- ✅ Validation rules chi tiết
- ✅ Business rules documented
- ✅ State machine cho approval workflow
- ✅ Audit trail đầy đủ
- ✅ Database indexes cho performance

**Status**: Ready for contract generation (Phase 1 next step)
