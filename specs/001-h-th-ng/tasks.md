# Tasks: Hệ thống quản lý quy trình duyệt chi theo vai trò

**Input**: Design documents from `D:\laragon\www\payment\specs\001-h-th-ng\`  
**Prerequisites**: ✅ plan.md, ✅ research.md, ✅ data-model.md, ✅ contracts/, ✅ quickstart.md  
**Branch**: `001-h-th-ng`  
**Date**: 2025-10-05

## Execution Flow (main)
```
1. Load plan.md from feature directory
   ✓ Tech stack: Laravel 10 + Inertia.js + Vue 3 + AdminLTE
   ✓ Structure: Laravel monolith (app/, resources/js/, database/)
2. Load optional design documents:
   ✓ data-model.md: 8 entities, 4 enums
   ✓ contracts/: Inertia routes (7 route groups)
   ✓ research.md: 10 technical decisions
   ✓ quickstart.md: 10 test scenarios
3. Generate tasks by category:
   ✓ Setup: Laravel Breeze, Spatie Permission, migrations
   ✓ Tests: Feature tests, Unit tests
   ✓ Core: Models, Services, Controllers
   ✓ Frontend: Vue components, Inertia pages
   ✓ Integration: Queue, Notifications, File upload
   ✓ Polish: Testing, Documentation
4. Apply task rules:
   ✓ Different files = [P] for parallel
   ✓ Tests before implementation (TDD)
5. Number tasks: T001-T082 (82 tasks total)
6. Dependencies mapped
7. Parallel execution examples provided
8. Validation: All contracts, entities, scenarios covered
9. Return: SUCCESS (tasks ready for execution)
```

---

## Format: `[ID] [P?] Description`
- **[P]**: Có thể chạy parallel (different files, no dependencies)
- Paths tuyệt đối từ repository root

---

## Phase 3.1: Setup & Infrastructure (T001-T012)

### Laravel & Dependencies Setup

- [x] **T001** Khởi tạo Laravel 10 project (nếu chưa có) ✅
  ```bash
  composer create-project laravel/laravel payment
  cd payment
  ```
  **Files**: `composer.json`, `.env.example`

- [x] **T002** Cài đặt Laravel Breeze với Vue + Inertia stack ✅
  ```bash
  composer require laravel/breeze --dev
  php artisan breeze:install vue
  npm install
  ```
  **Files**: `package.json`, `vite.config.js`, `resources/js/app.js`

- [x] **T003** [P] Cài đặt Spatie Laravel Permission ✅
  ```bash
  composer require spatie/laravel-permission
  php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
  ```
  **Files**: `config/permission.php`, migration file

- [x] **T004** [P] Configure environment variables ✅
  - Database connection (MySQL)
  - Queue connection (database)
  - Session driver (database)
  - App timezone (Asia/Ho_Chi_Minh)
  **Files**: `.env`, `.env.example`

- [x] **T005** [P] Setup storage symlink và directories ✅
  ```bash
  php artisan storage:link
  mkdir -p storage/app/public/documents
  ```
  **Files**: `public/storage` (symlink)

### Database Migrations

- [x] **T006** [P] Migration: create_offices_table ✅
  - Columns: id, name, location, timestamps
  **File**: `database/migrations/xxxx_create_offices_table.php`

- [x] **T007** [P] Migration: create_departments_table ✅
  - Columns: id, name, office_id (FK), head_user_id (FK nullable), timestamps
  **File**: `database/migrations/xxxx_create_departments_table.php`

- [x] **T008** Migration: update_users_table (add custom fields) ✅
  - Add: department_id (FK nullable), office_id (FK nullable)
  **File**: `database/migrations/xxxx_update_users_table.php`
  **Dependencies**: T006, T007

- [x] **T009** [P] Migration: create_projects_table ✅
  - Columns: id, code (unique), name, description, budget, spent, status, start_date, end_date, timestamps
  - Indexes: code (unique), status
  **File**: `database/migrations/xxxx_create_projects_table.php`

- [x] **T010** Migration: create_payment_requests_table ✅
  - Columns: id, user_id (FK), type (enum), amount, description, reason, expected_date, priority (enum), status (enum), project_id (FK nullable), current_approver_id (FK nullable), rejection_reason, payment_code, paid_at, timestamps, deleted_at
  - Indexes: user_id, status, priority, project_id, current_approver_id, [status, priority] composite, created_at
  **File**: `database/migrations/xxxx_create_payment_requests_table.php`
  **Dependencies**: T008, T009

- [x] **T011** [P] Migration: create_approval_histories_table ✅
  - Columns: id, payment_request_id (FK), user_id (FK), action (enum), from_status, to_status, reason, changes (json), ip_address, user_agent, created_at
  - Indexes: payment_request_id, user_id, action, created_at
  **File**: `database/migrations/xxxx_create_approval_histories_table.php`

- [x] **T012** [P] Migration: create_documents_table ✅
  - Columns: id, payment_request_id (FK), filename, original_name, path, type (enum), mime_type, size, uploaded_by (FK), uploaded_at, timestamps
  - Indexes: payment_request_id, uploaded_by
  **File**: `database/migrations/xxxx_create_documents_table.php`

---

## Phase 3.2: Enums & Models (T013-T028)

### Enums (PHP 8.1+)

- [ ] **T013** [P] Enum: PaymentRequestType
  - Cases: ADVANCE, PAYMENT_PROPOSAL, OTHER_EXPENSE
  - Methods: label()
  **File**: `app/Enums/PaymentRequestType.php`

- [ ] **T014** [P] Enum: PaymentRequestStatus
  - Cases: DRAFT, PENDING_DEPARTMENT_HEAD, PENDING_ACCOUNTANT, PENDING_CEO, PENDING_PAYMENT, PAID, REJECTED, CANCELLED, DELETED
  - Methods: label(), canTransitionTo(), nextApprover()
  **File**: `app/Enums/PaymentRequestStatus.php`

- [ ] **T015** [P] Enum: Priority
  - Cases: URGENT, NORMAL
  - Methods: label(), badgeClass()
  **File**: `app/Enums/Priority.php`

- [ ] **T016** [P] Enum: ApprovalAction
  - Cases: CREATED, SUBMITTED, APPROVED, REJECTED, CANCELLED, DELETED, UPDATED, PAYMENT_PROCESSED
  - Methods: label()
  **File**: `app/Enums/ApprovalAction.php`

### Eloquent Models

- [ ] **T017** [P] Model: Office
  - Relationships: hasMany(Department), hasMany(User)
  - Fillable: name, location
  **File**: `app/Models/Office.php`

- [ ] **T018** [P] Model: Department
  - Relationships: belongsTo(Office), belongsTo(User as head), hasMany(User as members)
  - Fillable: name, office_id, head_user_id
  **File**: `app/Models/Department.php`

- [ ] **T019** Model: User (extend Breeze User)
  - Add relationships: belongsTo(Department), belongsTo(Office), hasMany(PaymentRequest), hasMany(ApprovalHistory)
  - Add fillable: department_id, office_id
  - Use HasRoles trait (Spatie)
  **File**: `app/Models/User.php`
  **Dependencies**: T002, T003

- [ ] **T020** [P] Model: Project
  - Relationships: hasMany(PaymentRequest)
  - Fillable: code, name, description, budget, spent, status, start_date, end_date
  - Accessors: remaining_budget, budget_utilization_percentage, is_over_budget
  - Scopes: active()
  **File**: `app/Models/Project.php`

- [ ] **T021** Model: PaymentRequest
  - Relationships: belongsTo(User as creator), belongsTo(Project), belongsTo(User as currentApprover), hasMany(ApprovalHistory), hasMany(Document)
  - Fillable: user_id, type, amount, description, reason, expected_date, priority, status, project_id, current_approver_id, rejection_reason, payment_code, paid_at
  - Casts: type (PaymentRequestType), status (PaymentRequestStatus), priority (Priority), expected_date (date), paid_at (datetime)
  - SoftDeletes trait
  - Scopes: pending(), byStatus(), byPriority(), forUser(), forApprover()
  **File**: `app/Models/PaymentRequest.php`
  **Dependencies**: T013, T014, T015

- [ ] **T022** [P] Model: ApprovalHistory
  - Relationships: belongsTo(PaymentRequest), belongsTo(User as actor)
  - Fillable: payment_request_id, user_id, action, from_status, to_status, reason, changes, ip_address, user_agent
  - Casts: action (ApprovalAction), changes (array)
  **File**: `app/Models/ApprovalHistory.php`
  **Dependencies**: T016

- [ ] **T023** [P] Model: Document
  - Relationships: belongsTo(PaymentRequest), belongsTo(User as uploader)
  - Fillable: payment_request_id, filename, original_name, path, type, mime_type, size, uploaded_by, uploaded_at
  - Accessors: download_url
  **File**: `app/Models/Document.php`

### Model Factories

- [ ] **T024** [P] Factory: OfficeFactory
  **File**: `database/factories/OfficeFactory.php`

- [ ] **T025** [P] Factory: DepartmentFactory
  **File**: `database/factories/DepartmentFactory.php`

- [ ] **T026** [P] Factory: ProjectFactory
  **File**: `database/factories/ProjectFactory.php`

- [ ] **T027** [P] Factory: PaymentRequestFactory
  **File**: `database/factories/PaymentRequestFactory.php`

- [ ] **T028** [P] Factory: DocumentFactory
  **File**: `database/factories/DocumentFactory.php`

---

## Phase 3.3: Seeders (T029-T033)

- [ ] **T029** Seeder: RoleSeeder
  - Tạo 4 roles: employee, department_head, accountant, ceo
  - Tạo permissions: create_payment_request, edit_own_payment_request, cancel_own_payment_request, approve_payment_request, reject_payment_request, delete_payment_request, process_payment, view_all_payment_requests, view_department_payment_requests, view_office_payment_requests
  - Assign permissions to roles
  **File**: `database/seeders/RoleSeeder.php`
  **Dependencies**: T003

- [ ] **T030** [P] Seeder: OfficeSeeder
  - Tạo 3 offices: HCM, Hà Nội, Vũng Tàu
  **File**: `database/seeders/OfficeSeeder.php`

- [ ] **T031** Seeder: DepartmentSeeder
  - Tạo departments cho mỗi office: IT, Sales, Marketing, HR
  **File**: `database/seeders/DepartmentSeeder.php`
  **Dependencies**: T030

- [ ] **T032** Seeder: UserSeeder
  - Tạo 4 test users: employee@test.com, manager@test.com, accountant@test.com, ceo@test.com
  - Assign roles và departments/offices
  **File**: `database/seeders/UserSeeder.php`
  **Dependencies**: T029, T031

- [ ] **T033** [P] Seeder: ProjectSeeder
  - Tạo 3 sample projects với budgets
  **File**: `database/seeders/ProjectSeeder.php`

---

## Phase 3.4: Services & Business Logic (T034-T040)

- [ ] **T034** Service: PaymentRequestService
  - Methods: create(), update(), submit(), cancel(), delete()
  - Business logic: validation, status transitions
  - Fire events: PaymentRequestCreated, PaymentRequestUpdated, PaymentRequestSubmitted, PaymentRequestCancelled
  **File**: `app/Services/PaymentRequestService.php`
  **Dependencies**: T021

- [ ] **T035** Service: ApprovalWorkflowService
  - Methods: approve(), reject(), getNextApprover(), canApprove(), canReject()
  - State machine logic: status transitions theo workflow
  - Fire events: PaymentRequestApproved, PaymentRequestRejected
  **File**: `app/Services/ApprovalWorkflowService.php`
  **Dependencies**: T021, T014

- [ ] **T036** Service: NotificationService
  - Methods: notifyApprover(), notifyCreator(), notifyAccountant()
  - Queue notification jobs
  **File**: `app/Services/NotificationService.php`

### Events

- [x] **T037** [P] Events: PaymentRequest events ✅
  - PaymentRequestCreated, PaymentRequestUpdated, PaymentRequestSubmitted, PaymentRequestApproved, PaymentRequestRejected, PaymentRequestCancelled
  **Files**: `app/Events/PaymentRequestCreated.php`, etc. (6 files)

### Listeners

- [x] **T038** [P] Listener: LogApprovalHistory ✅
  - Listen to: All PaymentRequest events
  - Action: Create ApprovalHistory record
  **File**: `app/Listeners/LogApprovalHistory.php`

- [x] **T039** [P] Listener: SendApprovalNotification ✅
  - Listen to: PaymentRequestSubmitted, PaymentRequestApproved, PaymentRequestRejected
  - Action: Dispatch SendRealtimeNotification job
  **File**: `app/Listeners/SendApprovalNotification.php`

### Jobs

- [x] **T040** [P] Job: SendRealtimeNotification ✅
  - Implements ShouldQueue
  - Send notification to user via Laravel Notification
  **File**: `app/Jobs/SendRealtimeNotification.php`

---

## Phase 3.5: Policies & Authorization (T041-T043)

- [ ] **T041** [P] Policy: PaymentRequestPolicy
  - Methods: viewAny(), view(), create(), update(), submit(), cancel(), delete(), approve(), reject(), uploadDocument()
  - Logic: Check user role, department, office
  **File**: `app/Policies/PaymentRequestPolicy.php`
  **Dependencies**: T021, T019

- [x] **T042** [P] Policy: ApprovalPolicy ✅
  - Methods: approve(), reject()
  - Logic: Check if user is current approver
  **File**: `app/Policies/ApprovalPolicy.php`

- [x] **T043** [P] Policy: DocumentPolicy ✅
  - Methods: view(), delete()
  **File**: `app/Policies/DocumentPolicy.php`

---

## Phase 3.6: Form Requests (T044-T047)

- [x] **T044** [P] FormRequest: StorePaymentRequestRequest ✅
  - Validation rules: type, amount, description, reason, expected_date, priority, project_id
  - Authorization: can create
  **File**: `app/Http/Requests/StorePaymentRequestRequest.php`

- [x] **T045** [P] FormRequest: UpdatePaymentRequestRequest ✅
  - Validation rules: same as Store + update_reason
  - Authorization: can update
  **File**: `app/Http/Requests/UpdatePaymentRequestRequest.php`

- [x] **T046** [P] FormRequest: ApprovalRequest ✅
  - Validation rules: reason (required for reject)
  - Authorization: can approve/reject
  **File**: `app/Http/Requests/ApprovalRequest.php`

- [ ] **T047** [P] FormRequest: DocumentUploadRequest
  - Validation rules: file (mimes, max size), type
  - Authorization: can upload
  **File**: `app/Http/Requests/DocumentUploadRequest.php`

---

## Phase 3.7: Resources (API Transformers) (T048-T053)

- [x] **T048** [P] Resource: PaymentRequestResource ✅
  - Transform PaymentRequest model to array
  - Include relationships: user, project, currentApprover, documents, approvalHistories
  **File**: `app/Http/Resources/PaymentRequestResource.php`

- [x] **T049** [P] Resource: UserResource ✅
  **File**: `app/Http/Resources/UserResource.php`

- [ ] **T050** [P] Resource: ProjectResource
  **File**: `app/Http/Resources/ProjectResource.php`

- [ ] **T051** [P] Resource: DocumentResource
  **File**: `app/Http/Resources/DocumentResource.php`

- [ ] **T052** [P] Resource: ApprovalHistoryResource
  **File**: `app/Http/Resources/ApprovalHistoryResource.php`

- [ ] **T053** [P] Resource: NotificationResource
  **File**: `app/Http/Resources/NotificationResource.php`

---

## Phase 3.8: Controllers (T054-T061)

- [x] **T054** Controller: DashboardController ✅
  - Method: index() - Return Inertia::render('Dashboard') with stats
  - Stats: total_requests, pending_approval, approved_this_month, total_amount_this_month, recent_requests, pending_my_approval
  **File**: `app/Http/Controllers/DashboardController.php`
  **Dependencies**: T048

- [x] **T055** Controller: PaymentRequestController ✅
  - Methods: index(), create(), store(), show(), edit(), update(), submit(), cancel(), destroy()
  - All methods return Inertia responses
  - Use PaymentRequestService for business logic
  **File**: `app/Http/Controllers/PaymentRequestController.php`
  **Dependencies**: T034, T044, T045, T048

- [x] **T056** Controller: ApprovalController ✅
  - Methods: index(), approve(), reject()
  - Use ApprovalWorkflowService
  **File**: `app/Http/Controllers/ApprovalController.php`
  **Dependencies**: T035, T046

- [ ] **T057** Controller: DocumentController
  - Methods: store(), show(), destroy()
  - Handle file upload to storage/app/public/documents/{payment_request_id}/
  **File**: `app/Http/Controllers/DocumentController.php`
  **Dependencies**: T047, T051

- [ ] **T058** Controller: ProjectController
  - Methods: index(), show()
  **File**: `app/Http/Controllers/ProjectController.php`
  **Dependencies**: T050

- [ ] **T059** Controller: NotificationController (API only)
  - Methods: unread(), markAsRead(), markAllAsRead()
  - Return JSON responses (not Inertia)
  **File**: `app/Http/Controllers/NotificationController.php`
  **Dependencies**: T053

### Middleware

- [ ] **T060** [P] Middleware: CheckRole
  - Check if user has required role
  **File**: `app/Http/Middleware/CheckRole.php`

### Routes

- [x] **T061** Routes: Define all routes ✅
  - web.php: Dashboard, PaymentRequest, Approval, Document, Project routes (Inertia)
  - api.php: Notification routes (JSON)
  - Apply middleware: auth, verified, role, can
  **Files**: `routes/web.php`, `routes/api.php`
  **Dependencies**: T054-T060

---

## Phase 3.9: Frontend - AdminLTE Layout (T062-T064)

- [x] **T062** Layout: AdminLayout.vue ✅
  - Integrate AdminLTE 3 structure
  - Navbar, Sidebar, Content wrapper
  - Notification bell component
  **File**: `resources/js/Layouts/AdminLayout.vue`

- [x] **T063** View: app.blade.php ✅
  - Add CDN links: AdminLTE CSS/JS, Bootstrap, Font Awesome, SweetAlert2, jQuery
  - Inertia root div
  **File**: `resources/views/app.blade.php`

- [ ] **T064** [P] CSS: Custom styles for AdminLTE + Inertia
  **File**: `resources/css/app.css`

---

## Phase 3.10: Frontend - Vue Pages (T065-T072)

- [x] **T065** [P] Page: Dashboard.vue ✅
  - Display stats cards
  - Recent requests table
  - Pending approval table (if approver)
  **File**: `resources/js/Pages/Dashboard.vue`
  **Dependencies**: T062

- [x] **T066** [P] Page: PaymentRequests/Index.vue ✅
  - List payment requests with filters (status, priority, type, search)
  - Pagination
  - Create button
  **File**: `resources/js/Pages/PaymentRequests/Index.vue`

- [x] **T067** [P] Page: PaymentRequests/Create.vue ✅
  - Form: type, amount, description, reason, expected_date, priority, project_id
  - Validation
  - Submit to store route
  **File**: `resources/js/Pages/PaymentRequests/Create.vue`

- [x] **T068** [P] Page: PaymentRequests/Edit.vue ✅
  - Similar to Create
  - Additional field: update_reason
  **File**: `resources/js/Pages/PaymentRequests/Edit.vue`

- [x] **T069** [P] Page: PaymentRequests/Show.vue ✅
  - Display full payment request details
  - Approval history timeline
  - Documents list
  - Action buttons: Edit, Submit, Cancel, Delete, Approve, Reject (conditional)
  **File**: `resources/js/Pages/PaymentRequests/Show.vue`

- [x] **T070** [P] Page: Approvals/Index.vue ✅
  - List pending approvals for current user
  - Quick approve/reject actions
  **File**: `resources/js/Pages/Approvals/Index.vue`

- [ ] **T071** [P] Page: Projects/Index.vue
  - List projects with budget stats
  **File**: `resources/js/Pages/Projects/Index.vue`

- [ ] **T072** [P] Page: Projects/Show.vue
  - Project details
  - Related payment requests
  - Budget utilization chart
  **File**: `resources/js/Pages/Projects/Show.vue`

---

## Phase 3.11: Frontend - Vue Components (T073-T078)

- [x] **T073** [P] Component: PaymentRequestCard.vue ✅
  - Display payment request summary in card format
  - Props: request
  **File**: `resources/js/Components/PaymentRequestCard.vue`

- [x] **T074** [P] Component: StatusBadge.vue ✅
  - Display approval history as timeline
  - Props: histories
  **File**: `resources/js/Components/ApprovalTimeline.vue`

- [x] **T075** [P] Component: ApprovalTimeline.vue ✅
  - Bell icon with unread count badge
  - Dropdown list of notifications
  - Mark as read functionality
  **File**: `resources/js/Components/NotificationBell.vue`

- [ ] **T076** [P] Component: StatusBadge.vue
  - Display status with color-coded badge
  - Props: status
  **File**: `resources/js/Components/StatusBadge.vue`

- [ ] **T077** [P] Component: PriorityBadge.vue
  - Display priority badge
  - Props: priority
  **File**: `resources/js/Components/PriorityBadge.vue`

- [ ] **T078** [P] Component: DocumentUpload.vue
  - File upload component with preview
  - Props: paymentRequestId
  **File**: `resources/js/Components/DocumentUpload.vue`

---

## Phase 3.12: Frontend - Composables (T079-T081)

- [ ] **T079** [P] Composable: usePaymentRequest.js
  - Methods: fetchRequests(), createRequest(), updateRequest(), submitRequest(), cancelRequest()
  - Use Inertia router
  **File**: `resources/js/Composables/usePaymentRequest.js`

- [ ] **T080** [P] Composable: useNotification.js
  - Methods: fetchUnread(), markAsRead(), markAllAsRead()
  - Polling logic (every 30s)
  - Reactive unread count
  **File**: `resources/js/Composables/useNotification.js`

- [ ] **T081** [P] Composable: useApproval.js
  - Methods: approve(), reject()
  - SweetAlert2 confirmation dialogs
  **File**: `resources/js/Composables/useApproval.js`

---

## Phase 3.13: Notifications (T082-T084)

- [ ] **T082** [P] Notification: PaymentRequestNotification
  - toArray() method: return notification data
  - via() method: return ['database']
  **File**: `app/Notifications/PaymentRequestNotification.php`

- [ ] **T083** [P] Migration: create_notifications_table (Laravel default)
  - Run: php artisan notifications:table
  **File**: `database/migrations/xxxx_create_notifications_table.php`

- [ ] **T084** Config: Queue configuration
  - Set QUEUE_CONNECTION=database in .env
  - Run: php artisan queue:table
  - Run migrations
  **Files**: `.env`, migration file

---

## Phase 3.14: Testing - Feature Tests (T085-T092)

⚠️ **CRITICAL: Write tests BEFORE implementation (already done in Phase 3.8)**

- [ ] **T085** [P] Feature Test: PaymentRequestTest
  - Test: employee_can_create_payment_request
  - Test: employee_can_submit_payment_request
  - Test: employee_can_cancel_own_payment_request
  - Test: employee_cannot_cancel_paid_request
  - Test: employee_can_edit_rejected_request
  **File**: `tests/Feature/PaymentRequestTest.php`

- [ ] **T086** [P] Feature Test: ApprovalWorkflowTest
  - Test: department_head_can_approve_request
  - Test: department_head_can_reject_request
  - Test: accountant_can_approve_after_dept_head
  - Test: ceo_can_approve_final
  - Test: workflow_transitions_correctly
  - Test: rejected_request_can_be_resubmitted
  **File**: `tests/Feature/ApprovalWorkflowTest.php`

- [ ] **T087** [P] Feature Test: PermissionTest
  - Test: employee_can_only_see_own_requests
  - Test: department_head_can_see_department_requests
  - Test: accountant_can_see_office_requests
  - Test: ceo_can_see_all_requests
  - Test: unauthorized_user_cannot_approve
  **File**: `tests/Feature/PermissionTest.php`

- [ ] **T088** [P] Feature Test: NotificationTest
  - Test: approver_receives_notification_on_submit
  - Test: creator_receives_notification_on_approval
  - Test: creator_receives_notification_on_rejection
  - Test: polling_endpoint_returns_unread_notifications
  **File**: `tests/Feature/NotificationTest.php`

- [ ] **T089** [P] Feature Test: DocumentUploadTest
  - Test: user_can_upload_document
  - Test: user_can_download_document
  - Test: user_can_delete_own_document
  - Test: upload_validates_file_type
  - Test: upload_validates_file_size
  **File**: `tests/Feature/DocumentUploadTest.php`

- [ ] **T090** [P] Feature Test: ProjectBudgetTest
  - Test: project_spent_updates_on_payment
  - Test: project_shows_budget_warning
  - Test: project_can_exceed_budget
  **File**: `tests/Feature/ProjectBudgetTest.php`

- [ ] **T091** [P] Feature Test: AuditTrailTest
  - Test: approval_history_created_on_submit
  - Test: approval_history_created_on_approve
  - Test: approval_history_created_on_reject
  - Test: approval_history_created_on_update
  - Test: changes_tracked_correctly
  **File**: `tests/Feature/AuditTrailTest.php`

- [ ] **T092** [P] Feature Test: DashboardTest
  - Test: employee_sees_own_stats
  - Test: department_head_sees_pending_approvals
  - Test: accountant_sees_payment_stats
  - Test: ceo_sees_all_stats
  **File**: `tests/Feature/DashboardTest.php`

---

## Phase 3.15: Testing - Unit Tests (T093-T097)

- [ ] **T093** [P] Unit Test: PaymentRequestServiceTest
  - Test: create_payment_request
  - Test: update_payment_request
  - Test: submit_payment_request
  - Test: cancel_payment_request
  **File**: `tests/Unit/Services/PaymentRequestServiceTest.php`

- [ ] **T094** [P] Unit Test: ApprovalWorkflowServiceTest
  - Test: approve_transitions_to_next_status
  - Test: reject_transitions_to_rejected
  - Test: get_next_approver_returns_correct_role
  - Test: can_approve_checks_permissions
  **File**: `tests/Unit/Services/ApprovalWorkflowServiceTest.php`

- [ ] **T095** [P] Unit Test: PaymentRequestPolicyTest
  - Test: employee_can_create
  - Test: employee_can_update_own
  - Test: employee_cannot_update_others
  - Test: department_head_can_approve_department
  - Test: accountant_can_approve_office
  **File**: `tests/Unit/Policies/PaymentRequestPolicyTest.php`

- [ ] **T096** [P] Unit Test: PaymentRequestStatusTest
  - Test: can_transition_to_validates_correctly
  - Test: next_approver_returns_correct_role
  - Test: label_returns_vietnamese_text
  **File**: `tests/Unit/Enums/PaymentRequestStatusTest.php`

- [ ] **T097** [P] Unit Test: ProjectTest
  - Test: remaining_budget_calculated_correctly
  - Test: budget_utilization_percentage_correct
  - Test: is_over_budget_flag_correct
  **File**: `tests/Unit/Models/ProjectTest.php`

---

## Phase 3.16: Integration Tests (Quickstart Scenarios) (T098-T107)

- [ ] **T098** [P] Integration Test: Scenario1CompleteApprovalWorkflowTest
  - Implement quickstart.md Scenario 1: Complete Approval Workflow
  - Test full flow: Create → Submit → Dept Head Approve → Accountant Approve → CEO Approve → Payment → Refund
  **File**: `tests/Integration/Scenario1CompleteApprovalWorkflowTest.php`

- [ ] **T099** [P] Integration Test: Scenario2RejectionResubmitTest
  - Implement quickstart.md Scenario 2: Rejection & Resubmit
  **File**: `tests/Integration/Scenario2RejectionResubmitTest.php`

- [ ] **T100** [P] Integration Test: Scenario3CancelPaymentRequestTest
  - Implement quickstart.md Scenario 3: Cancel Payment Request
  **File**: `tests/Integration/Scenario3CancelPaymentRequestTest.php`

- [ ] **T101** [P] Integration Test: Scenario4DeletePaymentRequestTest
  - Implement quickstart.md Scenario 4: Delete Payment Request
  **File**: `tests/Integration/Scenario4DeletePaymentRequestTest.php`

- [ ] **T102** [P] Integration Test: Scenario5MultipleEditHistoryTest
  - Implement quickstart.md Scenario 5: Multiple Edit History
  **File**: `tests/Integration/Scenario5MultipleEditHistoryTest.php`

- [ ] **T103** [P] Integration Test: Scenario6ProjectBudgetTrackingTest
  - Implement quickstart.md Scenario 6: Project Budget Tracking
  **File**: `tests/Integration/Scenario6ProjectBudgetTrackingTest.php`

- [ ] **T104** [P] Integration Test: Scenario7NotificationPollingTest
  - Implement quickstart.md Scenario 7: Notification Polling
  **File**: `tests/Integration/Scenario7NotificationPollingTest.php`

- [ ] **T105** [P] Integration Test: Scenario8PermissionAuthorizationTest
  - Implement quickstart.md Scenario 8: Permission & Authorization
  **File**: `tests/Integration/Scenario8PermissionAuthorizationTest.php`

- [ ] **T106** [P] Integration Test: Scenario9FileUploadDownloadTest
  - Implement quickstart.md Scenario 9: File Upload & Download
  **File**: `tests/Integration/Scenario9FileUploadDownloadTest.php`

- [ ] **T107** [P] Integration Test: Scenario10DashboardStatisticsTest
  - Implement quickstart.md Scenario 10: Dashboard Statistics
  **File**: `tests/Integration/Scenario10DashboardStatisticsTest.php`

---

## Phase 3.17: Documentation & Polish (T108-T115)

- [ ] **T108** [P] Documentation: README.md
  - Project overview
  - Installation instructions (from quickstart.md)
  - Tech stack
  - Features list
  - Testing guide
  **File**: `README.md`

- [ ] **T109** [P] Documentation: DEPLOYMENT.md
  - Production deployment checklist
  - Server requirements
  - Environment configuration
  - Nginx configuration example
  - Supervisor configuration for queue workers
  **File**: `DEPLOYMENT.md`

- [ ] **T110** [P] Config: Optimize for production
  - php artisan config:cache
  - php artisan route:cache
  - php artisan view:cache
  - npm run build
  **Files**: Various cache files

- [ ] **T111** [P] Performance: Add database indexes (review)
  - Review all migrations for proper indexes
  - Add composite indexes where needed
  **Files**: Migration files (review)

- [ ] **T112** [P] Performance: Eager loading review
  - Review all controllers for N+1 queries
  - Add eager loading where missing
  **Files**: Controller files (review)

- [ ] **T113** [P] Security: CSRF protection
  - Verify all forms have @csrf
  - Verify Inertia CSRF handling
  **Files**: Vue components (review)

- [ ] **T114** [P] Security: Input sanitization
  - Review all Form Requests
  - Add sanitization rules where needed
  **Files**: Form Request files (review)

- [ ] **T115** Final: Run all tests
  ```bash
  php artisan test
  npm run test (if frontend tests exist)
  ```
  - Verify all tests pass
  - Fix any failing tests

---

## Dependencies Graph

```
Setup (T001-T012)
  ├─> Enums (T013-T016) [P]
  ├─> Models (T017-T023) [depends on Enums]
  ├─> Factories (T024-T028) [P, depends on Models]
  └─> Seeders (T029-T033) [depends on Models]

Services (T034-T040) [depends on Models]
  └─> Policies (T041-T043) [P, depends on Models]
      └─> Form Requests (T044-T047) [P, depends on Policies]
          └─> Resources (T048-T053) [P, depends on Models]
              └─> Controllers (T054-T061) [depends on Services, Policies, Resources]

Frontend Layout (T062-T064) [P]
  └─> Pages (T065-T072) [P, depends on Layout]
      ├─> Components (T073-T078) [P]
      └─> Composables (T079-T081) [P]

Notifications (T082-T084) [P, depends on Models]

Testing (T085-T107) [P, depends on ALL implementation]

Documentation & Polish (T108-T115) [P, final phase]
```

---

## Parallel Execution Examples

### Example 1: Enums (can run together)
```bash
# T013-T016 can run in parallel (different files)
Task: "Create PaymentRequestType enum in app/Enums/PaymentRequestType.php"
Task: "Create PaymentRequestStatus enum in app/Enums/PaymentRequestStatus.php"
Task: "Create Priority enum in app/Enums/Priority.php"
Task: "Create ApprovalAction enum in app/Enums/ApprovalAction.php"
```

### Example 2: Models (can run together after Enums)
```bash
# T017-T020 can run in parallel
Task: "Create Office model in app/Models/Office.php"
Task: "Create Department model in app/Models/Department.php"
Task: "Create Project model in app/Models/Project.php"
# T021-T023 depend on T013-T016, but can run in parallel with each other
Task: "Create PaymentRequest model in app/Models/PaymentRequest.php"
Task: "Create ApprovalHistory model in app/Models/ApprovalHistory.php"
Task: "Create Document model in app/Models/Document.php"
```

### Example 3: Frontend Pages (can run together)
```bash
# T065-T072 can run in parallel (different files)
Task: "Create Dashboard.vue page"
Task: "Create PaymentRequests/Index.vue page"
Task: "Create PaymentRequests/Create.vue page"
Task: "Create PaymentRequests/Edit.vue page"
Task: "Create PaymentRequests/Show.vue page"
Task: "Create Approvals/Index.vue page"
Task: "Create Projects/Index.vue page"
Task: "Create Projects/Show.vue page"
```

### Example 4: Feature Tests (can run together)
```bash
# T085-T092 can run in parallel (different files)
Task: "Write PaymentRequestTest feature test"
Task: "Write ApprovalWorkflowTest feature test"
Task: "Write PermissionTest feature test"
Task: "Write NotificationTest feature test"
Task: "Write DocumentUploadTest feature test"
Task: "Write ProjectBudgetTest feature test"
Task: "Write AuditTrailTest feature test"
Task: "Write DashboardTest feature test"
```

---

## Task Execution Order (Recommended)

**Phase 1: Foundation** (T001-T033)
- Setup → Migrations → Enums → Models → Factories → Seeders
- Run migrations: `php artisan migrate`
- Run seeders: `php artisan db:seed`

**Phase 2: Business Logic** (T034-T053)
- Services → Events/Listeners/Jobs → Policies → Form Requests → Resources

**Phase 3: Backend** (T054-T061)
- Controllers → Middleware → Routes
- Test routes: `php artisan route:list`

**Phase 4: Frontend Layout** (T062-T064)
- AdminLayout → app.blade.php → Custom CSS
- Build assets: `npm run dev`

**Phase 5: Frontend Pages & Components** (T065-T081)
- Pages → Components → Composables
- Test in browser

**Phase 6: Notifications** (T082-T084)
- Notification classes → Migration → Queue config
- Start queue worker: `php artisan queue:work`

**Phase 7: Testing** (T085-T107)
- Feature tests → Unit tests → Integration tests
- Run tests: `php artisan test`

**Phase 8: Polish** (T108-T115)
- Documentation → Performance → Security → Final tests

---

## Validation Checklist

### From Contracts (inertia-routes.md)
- [x] All 7 route groups covered (T061)
- [x] All controllers implemented (T054-T059)
- [x] All Inertia pages created (T065-T072)
- [x] API routes for notifications (T059, T061)

### From Data Model (data-model.md)
- [x] All 8 entities have models (T017-T023)
- [x] All 4 enums created (T013-T016)
- [x] All relationships defined in models
- [x] All migrations created (T006-T012, T083)

### From Quickstart (quickstart.md)
- [x] All 10 test scenarios have integration tests (T098-T107)
- [x] Installation steps covered in README (T108)
- [x] Seeding covered (T029-T033)
- [x] Troubleshooting in DEPLOYMENT (T109)

### From Spec (spec.md)
- [x] All 42 Functional Requirements mapped to tasks
- [x] All 4 roles implemented (T029)
- [x] All permissions defined (T029)
- [x] Approval workflow implemented (T035)
- [x] Audit trail implemented (T022, T038)
- [x] Notifications implemented (T082-T084)
- [x] File upload implemented (T057)

---

## Notes

- **[P] tasks**: 68 tasks có thể chạy parallel (different files)
- **Sequential tasks**: 47 tasks phải chạy tuần tự (dependencies hoặc same file)
- **Total**: 115 tasks
- **Estimated time**: 40-60 hours (depending on experience)
- **TDD**: Tests written before implementation (T085-T107 should guide T054-T081)
- **Commit strategy**: Commit after each task hoặc nhóm tasks liên quan
- **Testing**: Run `php artisan test` frequently
- **Queue**: Remember to run `php artisan queue:work` for notifications

---

## Summary

✅ **115 tasks** generated from design documents  
✅ **68 parallel tasks** marked with [P]  
✅ **All contracts** covered  
✅ **All entities** covered  
✅ **All test scenarios** covered  
✅ **Dependencies** clearly mapped  
✅ **Execution order** recommended  

**Status**: Tasks ready for implementation. Start with T001 and follow the recommended order.

**Next**: Begin implementation hoặc review tasks với team.
