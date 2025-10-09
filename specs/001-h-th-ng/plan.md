
# Implementation Plan: Hệ thống quản lý quy trình duyệt chi theo vai trò

**Branch**: `001-h-th-ng` | **Date**: 2025-10-05 | **Spec**: [spec.md](./spec.md)
**Input**: Feature specification from `D:\laragon\www\payment\specs\001-h-th-ng\spec.md`

## Execution Flow (/plan command scope)
```
1. Load feature spec from Input path
   → If not found: ERROR "No feature spec at {path}"
2. Fill Technical Context (scan for NEEDS CLARIFICATION)
   → Detect Project Type from file system structure or context (web=frontend+backend, mobile=app+api)
   → Set Structure Decision based on project type
3. Fill the Constitution Check section based on the content of the constitution document.
4. Evaluate Constitution Check section below
   → If violations exist: Document in Complexity Tracking
   → If no justification possible: ERROR "Simplify approach first"
   → Update Progress Tracking: Initial Constitution Check
5. Execute Phase 0 → research.md
   → If NEEDS CLARIFICATION remain: ERROR "Resolve unknowns"
6. Execute Phase 1 → contracts, data-model.md, quickstart.md, agent-specific template file (e.g., `CLAUDE.md` for Claude Code, `.github/copilot-instructions.md` for GitHub Copilot, `GEMINI.md` for Gemini CLI, `QWEN.md` for Qwen Code, or `AGENTS.md` for all other agents).
7. Re-evaluate Constitution Check section
   → If new violations: Refactor design, return to Phase 1
   → Update Progress Tracking: Post-Design Constitution Check
8. Plan Phase 2 → Describe task generation approach (DO NOT create tasks.md)
9. STOP - Ready for /tasks command
```

**IMPORTANT**: The /plan command STOPS at step 7. Phases 2-4 are executed by other commands:
- Phase 2: /tasks command creates tasks.md
- Phase 3-4: Implementation execution (manual or via tools)

## Summary

Hệ thống quản lý quy trình phê duyệt chi phí đa cấp với 4 vai trò chính (Nhân viên, Trưởng bộ phận, Kế toán, Tổng giám đốc). Workflow tuân theo: Nhân viên tạo phiếu → Trưởng BP duyệt → Kế toán duyệt → TGĐ duyệt → Thanh toán. Hệ thống hỗ trợ 3 loại phiếu (Tạm ứng, Đề xuất thanh toán, Chi phí khác), thông báo realtime trong app, lưu đầy đủ lịch sử thay đổi và không giới hạn số lần chỉnh sửa.

**Technical Approach**: Laravel 10 backend với Inertia.js + Vue 3 Composition API cho admin frontend. Sử dụng Spatie Permission cho phân quyền, Queue (database/redis) cho thông báo realtime, và MySQL cho database.

## Technical Context

**Language/Version**: PHP 8.1+  
**Framework**: Laravel 10  
**Frontend**: Vue 3 (Composition API) + Inertia.js + AdminLTE + Bootstrap + SweetAlert2  
**Primary Dependencies**:  
- Backend: Laravel 10, Spatie Laravel Permission, Laravel Queue
- Frontend: Vue 3, Inertia.js, AdminLTE (CDN), Bootstrap (CDN), SweetAlert2 (CDN)
- Starter: Laravel Breeze với Vue + Inertia template

**Storage**: MySQL/MariaDB  
**File Upload**: `storage/app/public` với symlink  
**Queue System**: Database hoặc Redis cho xử lý thông báo realtime  
**Authentication**: Laravel Breeze  
**Authorization**: Spatie Laravel Permission hoặc Policy + Gate  
**Testing**: PHPUnit (backend), Vitest/Jest (frontend)  
**Target Platform**: Web application (admin panel)  
**Project Type**: Web (backend + frontend)  

**Performance Goals**:  
- Response time < 500ms cho các thao tác CRUD
- Thông báo realtime delay < 2s
- Hỗ trợ đồng thời 100+ users

**Constraints**:  
- Không viết API riêng cho Inertia (xử lý trong Controller trả về data + view)
- Chỉ viết API cho các thao tác đặc biệt không thể reload trang
- AdminLTE, Bootstrap, SweetAlert2 dùng CDN thay vì npm
- Phải lưu đầy đủ lịch sử chỉnh sửa phiếu (audit trail)

**Scale/Scope**:  
- ~50-200 users (nhân viên + quản lý)
- ~1000-5000 phiếu/tháng
- 4 vai trò chính, phân quyền theo văn phòng/bộ phận

## Constitution Check
*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

**Status**: Constitution file chưa được cấu hình (template placeholders). Bỏ qua gate check này và tiếp tục với best practices chuẩn Laravel.

**Laravel Best Practices Applied**:
- ✓ Sử dụng Eloquent ORM cho database operations
- ✓ Service layer cho business logic phức tạp
- ✓ Policy/Gate cho authorization
- ✓ Queue cho background jobs (notifications)
- ✓ Events & Listeners cho decoupling
- ✓ Form Requests cho validation
- ✓ Resources cho API responses (nếu cần)
- ✓ Migrations cho database versioning
- ✓ Seeders cho test data

## Project Structure

### Documentation (this feature)
```
specs/[###-feature]/
├── plan.md              # This file (/plan command output)
├── research.md          # Phase 0 output (/plan command)
├── data-model.md        # Phase 1 output (/plan command)
├── quickstart.md        # Phase 1 output (/plan command)
├── contracts/           # Phase 1 output (/plan command)
└── tasks.md             # Phase 2 output (/tasks command - NOT created by /plan)
```

### Source Code (repository root)
```
app/
├── Models/
│   ├── PaymentRequest.php       # Phiếu đề xuất chi
│   ├── ApprovalHistory.php      # Lịch sử phê duyệt
│   ├── Project.php              # Dự án
│   ├── Document.php             # Chứng từ
│   ├── Notification.php         # Thông báo (extend Laravel Notification)
│   └── User.php                 # Người dùng (extend Breeze)
│
├── Http/
│   ├── Controllers/
│   │   ├── PaymentRequestController.php
│   │   ├── ApprovalController.php
│   │   ├── ProjectController.php
│   │   ├── NotificationController.php
│   │   └── DashboardController.php
│   │
│   ├── Requests/
│   │   ├── StorePaymentRequestRequest.php
│   │   ├── UpdatePaymentRequestRequest.php
│   │   └── ApprovalRequest.php
│   │
│   └── Middleware/
│       └── CheckRole.php
│
├── Services/
│   ├── PaymentRequestService.php    # Business logic cho phiếu
│   ├── ApprovalWorkflowService.php  # Logic workflow phê duyệt
│   └── NotificationService.php      # Logic gửi thông báo
│
├── Policies/
│   ├── PaymentRequestPolicy.php
│   └── ApprovalPolicy.php
│
├── Events/
│   ├── PaymentRequestCreated.php
│   ├── PaymentRequestApproved.php
│   ├── PaymentRequestRejected.php
│   └── PaymentRequestUpdated.php
│
├── Listeners/
│   ├── SendApprovalNotification.php
│   └── LogApprovalHistory.php
│
├── Jobs/
│   └── SendRealtimeNotification.php
│
└── Enums/
    ├── PaymentRequestType.php       # Tạm ứng, Đề xuất thanh toán, Chi phí khác
    ├── PaymentRequestStatus.php     # Chờ duyệt, Bị từ chối, Đã hủy, etc.
    ├── Priority.php                 # Gấp, Bình thường
    └── UserRole.php                 # Nhân viên, Trưởng BP, Kế toán, TGĐ

database/
├── migrations/
│   ├── xxxx_create_payment_requests_table.php
│   ├── xxxx_create_approval_histories_table.php
│   ├── xxxx_create_projects_table.php
│   ├── xxxx_create_documents_table.php
│   └── xxxx_create_notifications_table.php
│
├── seeders/
│   ├── RoleSeeder.php
│   ├── UserSeeder.php
│   └── ProjectSeeder.php
│
└── factories/
    ├── PaymentRequestFactory.php
    └── UserFactory.php

resources/
├── js/
│   ├── Pages/
│   │   ├── Dashboard.vue
│   │   ├── PaymentRequests/
│   │   │   ├── Index.vue
│   │   │   ├── Create.vue
│   │   │   ├── Edit.vue
│   │   │   └── Show.vue
│   │   ├── Approvals/
│   │   │   └── Index.vue
│   │   └── Projects/
│   │       └── Index.vue
│   │
│   ├── Components/
│   │   ├── PaymentRequestCard.vue
│   │   ├── ApprovalTimeline.vue
│   │   ├── NotificationBell.vue
│   │   └── StatusBadge.vue
│   │
│   ├── Composables/
│   │   ├── usePaymentRequest.js
│   │   ├── useNotification.js
│   │   └── useApproval.js
│   │
│   └── Layouts/
│       └── AdminLayout.vue          # AdminLTE layout
│
└── views/
    └── app.blade.php                # Inertia root

routes/
├── web.php                          # Inertia routes
└── api.php                          # API cho realtime actions (nếu cần)

tests/
├── Feature/
│   ├── PaymentRequestTest.php
│   ├── ApprovalWorkflowTest.php
│   └── NotificationTest.php
│
└── Unit/
    ├── Services/
    │   ├── PaymentRequestServiceTest.php
    │   └── ApprovalWorkflowServiceTest.php
    │
    └── Policies/
        └── PaymentRequestPolicyTest.php

storage/
└── app/
    └── public/
        └── documents/               # Upload chứng từ
```

**Structure Decision**: Sử dụng cấu trúc Laravel 10 chuẩn với Inertia.js. Backend xử lý business logic trong Services, Controllers trả về Inertia responses. Frontend Vue 3 Composition API với AdminLTE layout. Không tách riêng backend/frontend folders vì Inertia.js tích hợp chặt chẽ trong Laravel monolith.

## Phase 0: Outline & Research

✅ **COMPLETED**

**Research Topics Covered**:
1. Laravel Breeze với Vue + Inertia setup
2. Spatie Laravel Permission vs Policy + Gate
3. Realtime Notifications architecture (Queue + Polling)
4. AdminLTE integration với Inertia.js
5. File Upload strategy (Laravel Storage)
6. Audit Trail implementation
7. State Machine cho Approval Workflow
8. Testing strategy (Feature + Unit tests)
9. Performance optimization
10. Deployment & Environment setup

**Output**: ✅ `research.md` - 10 technical decisions documented with rationale

## Phase 1: Design & Contracts

✅ **COMPLETED**

**Deliverables**:

1. ✅ **data-model.md**:
   - 8 core entities (User, PaymentRequest, ApprovalHistory, Project, Document, Notification, Department, Office)
   - 4 enums với business logic
   - ERD diagram
   - Relationships, validation rules, business rules
   - State machine cho PaymentRequestStatus
   - Database indexes cho performance

2. ✅ **contracts/inertia-routes.md**:
   - 7 route groups (Auth, Dashboard, PaymentRequest, Approval, Document, Project, Notification API)
   - Controller contracts với Inertia responses
   - Request/Response structures
   - Authorization middleware
   - Resource transformers

3. ✅ **quickstart.md**:
   - Installation guide (10 steps)
   - Database seeding (roles, users, projects)
   - 10 test scenarios covering all workflows:
     * Complete approval workflow
     * Rejection & resubmit
     * Cancel payment request
     * Delete by approver
     * Multiple edit history
     * Project budget tracking
     * Notification polling
     * Permission & authorization
     * File upload & download
     * Dashboard statistics
   - Performance testing guidelines
   - Troubleshooting guide
   - Validation checklist (42 FRs)

4. ✅ **Agent context file**: Updated via update-agent-context.ps1

**Output**: All design artifacts complete, ready for task generation

## Phase 2: Task Planning Approach
*This section describes what the /tasks command will do - DO NOT execute during /plan*

**Task Generation Strategy**:
1. **Setup & Infrastructure** (Tasks 1-10):
   - Laravel Breeze installation
   - Spatie Permission setup
   - Database migrations (8 tables)
   - Enums creation (4 enums)
   - Seeders (roles, permissions, test users, projects)

2. **Models & Relationships** (Tasks 11-18) [P]:
   - 8 models với relationships
   - Eloquent scopes
   - Accessors/Mutators
   - Model factories

3. **Services & Business Logic** (Tasks 19-25):
   - PaymentRequestService
   - ApprovalWorkflowService
   - NotificationService
   - Event/Listener setup

4. **Policies & Authorization** (Tasks 26-28) [P]:
   - PaymentRequestPolicy
   - ApprovalPolicy
   - Gate definitions

5. **Controllers & Routes** (Tasks 29-40):
   - 7 controllers với Inertia responses
   - Form Requests validation
   - Resource transformers
   - Route definitions

6. **Frontend Components** (Tasks 41-55) [P]:
   - AdminLTE layout integration
   - Vue pages (Dashboard, PaymentRequests, Approvals, Projects)
   - Vue components (Cards, Timeline, Notifications, Badges)
   - Composables (usePaymentRequest, useNotification, useApproval)

7. **File Upload & Storage** (Tasks 56-58):
   - DocumentController
   - Storage configuration
   - Upload validation

8. **Notifications & Queue** (Tasks 59-63):
   - Notification classes
   - Queue jobs
   - Polling API endpoints
   - Frontend notification bell

9. **Testing** (Tasks 64-75):
   - Feature tests (approval workflow, permissions)
   - Unit tests (services, policies)
   - Integration tests (quickstart scenarios)

10. **Documentation & Deployment** (Tasks 76-80):
    - README update
    - Environment setup guide
    - Deployment checklist
    - Performance optimization

**Ordering Strategy**:
- Infrastructure first (migrations, seeders)
- Models parallel after migrations
- Services depend on models
- Controllers depend on services
- Frontend parallel with backend
- Tests after implementation

**Estimated Output**: 75-80 numbered, ordered tasks in tasks.md

**IMPORTANT**: This phase is executed by the /tasks command, NOT by /plan

## Phase 3+: Future Implementation
*These phases are beyond the scope of the /plan command*

**Phase 3**: Task execution (/tasks command creates tasks.md)  
**Phase 4**: Implementation (execute tasks.md following constitutional principles)  
**Phase 5**: Validation (run tests, execute quickstart.md, performance validation)

## Complexity Tracking
*Fill ONLY if Constitution Check has violations that must be justified*

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|-------------------------------------|
| [e.g., 4th project] | [current need] | [why 3 projects insufficient] |
| [e.g., Repository pattern] | [specific problem] | [why direct DB access insufficient] |


## Progress Tracking
*This checklist is updated during execution flow*

**Phase Status**:
- [x] Phase 0: Research complete (/plan command) ✅
- [x] Phase 1: Design complete (/plan command) ✅
- [x] Phase 2: Task planning complete (/plan command - describe approach only) ✅
- [ ] Phase 3: Tasks generated (/tasks command) - READY
- [ ] Phase 4: Implementation complete
- [ ] Phase 5: Validation passed

**Gate Status**:
- [x] Initial Constitution Check: PASS (skipped - template not configured)
- [x] Post-Design Constitution Check: PASS (Laravel best practices applied)
- [x] All NEEDS CLARIFICATION resolved (all tech stack specified)
- [x] Complexity deviations documented (N/A - standard Laravel architecture)

**Artifacts Generated**:
- ✅ `plan.md` - This file
- ✅ `research.md` - 10 technical decisions
- ✅ `data-model.md` - 8 entities, 4 enums, ERD
- ✅ `contracts/inertia-routes.md` - Routes & controller contracts
- ✅ `quickstart.md` - Installation & 10 test scenarios
- ✅ Agent context file - Updated (Windsurf)

**Next Command**: `/tasks` to generate tasks.md

---
*Based on Constitution v2.1.1 - See `/memory/constitution.md`*
