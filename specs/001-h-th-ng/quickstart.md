# Quickstart Guide: Hệ thống quản lý quy trình duyệt chi

**Feature**: 001-h-th-ng  
**Date**: 2025-10-05  
**Purpose**: Hướng dẫn setup và test scenarios để validate implementation

---

## Prerequisites

- PHP 8.1+
- Composer
- Node.js 18+ & npm
- MySQL 8.0+ / MariaDB 10.6+
- Laragon (hoặc LEMP stack)

---

## Installation Steps

### 1. Clone & Install Dependencies

```bash
# Clone repository (hoặc tạo mới)
cd d:\laragon\www\payment

# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 2. Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=payment
DB_USERNAME=root
DB_PASSWORD=

# Queue configuration (database driver)
QUEUE_CONNECTION=database

# Session & cache
SESSION_DRIVER=database
CACHE_DRIVER=file
```

### 3. Database Setup

```bash
# Create database
mysql -u root -e "CREATE DATABASE payment CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php artisan migrate

# Seed initial data (roles, permissions, test users)
php artisan db:seed
```

### 4. Storage Setup

```bash
# Create symbolic link for file uploads
php artisan storage:link

# Set permissions (Linux/Mac)
chmod -R 775 storage bootstrap/cache
```

### 5. Build Frontend Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 6. Start Development Server

```bash
# Terminal 1: Laravel dev server
php artisan serve

# Terminal 2: Vite dev server (for HMR)
npm run dev

# Terminal 3: Queue worker (for notifications)
php artisan queue:work
```

### 7. Access Application

- URL: http://localhost:8000
- Login với seeded users (xem Database Seeding section)

---

## Database Seeding

### Roles & Permissions

```php
// RoleSeeder.php tạo:
- employee (Nhân viên)
- department_head (Trưởng bộ phận)
- accountant (Kế toán)
- ceo (Tổng giám đốc)

// Permissions:
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

### Test Users

```php
// UserSeeder.php tạo test accounts:

1. Nhân viên
   Email: employee@test.com
   Password: password
   Role: employee
   Department: IT
   Office: HCM

2. Trưởng bộ phận
   Email: manager@test.com
   Password: password
   Role: department_head
   Department: IT (head)
   Office: HCM

3. Kế toán
   Email: accountant@test.com
   Password: password
   Role: accountant
   Office: HCM (assigned)

4. Tổng giám đốc
   Email: ceo@test.com
   Password: password
   Role: ceo
```

### Sample Data

```php
// ProjectSeeder.php tạo:
- Project "Website Redesign" (code: WEB-2025-001, budget: 50,000,000)
- Project "Mobile App Development" (code: APP-2025-002, budget: 100,000,000)
- Project "Infrastructure Upgrade" (code: INF-2025-003, budget: 200,000,000)
```

---

## Test Scenarios

### Scenario 1: Complete Approval Workflow (Happy Path)

**Objective**: Test toàn bộ quy trình từ tạo phiếu đến thanh toán

**Steps**:

1. **Login as Employee** (employee@test.com)
   ```
   - Navigate to Dashboard
   - Click "Tạo phiếu mới"
   - Fill form:
     * Type: Tạm ứng
     * Amount: 5,000,000
     * Description: Tạm ứng công tác Hà Nội
     * Reason: Tham dự hội nghị khách hàng
     * Expected Date: [tomorrow]
     * Priority: Gấp
     * Project: Website Redesign
   - Click "Lưu nháp"
   - Verify: Phiếu có status "Nháp"
   - Click "Gửi duyệt"
   - Verify: Status chuyển sang "Chờ Trưởng bộ phận"
   ```

2. **Login as Department Head** (manager@test.com)
   ```
   - Navigate to "Phiếu cần duyệt"
   - Verify: Thấy phiếu vừa tạo (priority "Gấp" hiển thị trước)
   - Verify: Có notification bell với badge "1"
   - Click vào phiếu để xem chi tiết
   - Verify: Thông tin đầy đủ, có nút "Phê duyệt" và "Từ chối"
   - Click "Phê duyệt"
   - Verify: Status chuyển sang "Chờ Kế toán"
   - Verify: Lịch sử phê duyệt có record "Đã phê duyệt bởi [Manager Name]"
   ```

3. **Login as Accountant** (accountant@test.com)
   ```
   - Navigate to "Phiếu cần duyệt"
   - Verify: Thấy phiếu (chỉ phiếu của office HCM)
   - Verify: Có notification
   - Click vào phiếu
   - Click "Phê duyệt"
   - Verify: Status chuyển sang "Chờ Tổng giám đốc"
   ```

4. **Login as CEO** (ceo@test.com)
   ```
   - Navigate to "Phiếu cần duyệt"
   - Verify: Thấy phiếu
   - Verify: Có notification
   - Click vào phiếu
   - Click "Phê duyệt"
   - Verify: Status chuyển sang "Chờ thanh toán"
   ```

5. **Login as Accountant** (accountant@test.com)
   ```
   - Navigate to "Phiếu chờ thanh toán"
   - Click vào phiếu
   - Click "Xử lý thanh toán"
   - Fill form:
     * Payment Code: PAY-2025-001
     * Payment Date: [today]
     * Upload chứng từ: [PDF file]
   - Click "Xác nhận thanh toán"
   - Verify: Status chuyển sang "Đã thanh toán"
   - Verify: Paid_at được set
   ```

6. **Login as Employee** (employee@test.com)
   ```
   - Verify: Có notification "Phiếu đã được thanh toán"
   - Navigate to "Phiếu của tôi"
   - Click vào phiếu
   - Verify: Status "Đã thanh toán"
   - Verify: Có payment_code và paid_at
   - Click "Hoàn ứng"
   - Upload chứng từ hoàn ứng
   - Verify: Document được lưu
   ```

**Expected Results**:
- ✅ Workflow chuyển đúng trạng thái
- ✅ Notifications gửi đúng người
- ✅ Lịch sử phê duyệt đầy đủ
- ✅ Permissions được enforce đúng
- ✅ Files upload thành công

---

### Scenario 2: Rejection & Resubmit

**Objective**: Test quy trình từ chối và gửi lại

**Steps**:

1. **Employee tạo phiếu và gửi duyệt**
   ```
   - Login as employee@test.com
   - Tạo phiếu mới (Amount: 10,000,000)
   - Gửi duyệt
   ```

2. **Department Head từ chối**
   ```
   - Login as manager@test.com
   - Navigate to phiếu cần duyệt
   - Click "Từ chối"
   - Fill reason: "Thiếu thông tin chi tiết về mục đích sử dụng"
   - Verify: Status chuyển sang "Bị từ chối"
   - Verify: rejection_reason được lưu
   ```

3. **Employee chỉnh sửa và gửi lại**
   ```
   - Login as employee@test.com
   - Verify: Có notification "Phiếu bị từ chối"
   - Navigate to phiếu
   - Verify: Thấy lý do từ chối
   - Click "Chỉnh sửa"
   - Update description với thông tin chi tiết hơn
   - Fill "Lý do chỉnh sửa": "Bổ sung thông tin theo yêu cầu"
   - Click "Lưu và gửi lại"
   - Verify: Status quay về "Chờ Trưởng bộ phận"
   - Verify: Lịch sử có record "Đã chỉnh sửa" với changes (old/new values)
   ```

4. **Department Head duyệt lần 2**
   ```
   - Login as manager@test.com
   - Verify: Phiếu xuất hiện lại trong danh sách cần duyệt
   - Verify: Thấy lịch sử chỉnh sửa
   - Click "Phê duyệt"
   - Verify: Workflow tiếp tục bình thường
   ```

**Expected Results**:
- ✅ Từ chối với lý do rõ ràng
- ✅ Employee nhận notification
- ✅ Chỉnh sửa lưu lại changes
- ✅ Workflow reset về đầu
- ✅ Lịch sử đầy đủ

---

### Scenario 3: Cancel Payment Request

**Objective**: Test hủy phiếu

**Steps**:

1. **Employee tạo và gửi phiếu**
   ```
   - Login as employee@test.com
   - Tạo phiếu mới
   - Gửi duyệt
   - Verify: Status "Chờ Trưởng bộ phận"
   ```

2. **Employee hủy phiếu**
   ```
   - Navigate to phiếu
   - Click "Hủy phiếu"
   - Fill reason: "Đã thanh toán bằng nguồn khác"
   - Confirm
   - Verify: Status chuyển sang "Đã hủy"
   - Verify: Không thể chỉnh sửa hoặc gửi lại
   - Verify: Lịch sử có record "Đã hủy" với reason
   ```

3. **Department Head verify**
   ```
   - Login as manager@test.com
   - Verify: Phiếu không còn trong danh sách cần duyệt
   - Navigate to "Tất cả phiếu"
   - Filter by status "Đã hủy"
   - Verify: Thấy phiếu với lý do hủy
   ```

**Expected Results**:
- ✅ Hủy thành công với lý do
- ✅ Phiếu bị lock (không edit được)
- ✅ Không còn trong pending list
- ✅ Lịch sử ghi nhận

---

### Scenario 4: Delete Payment Request (by Approver)

**Objective**: Test xóa phiếu bởi người duyệt

**Steps**:

1. **Employee tạo phiếu sai**
   ```
   - Login as employee@test.com
   - Tạo phiếu với thông tin sai (duplicate, wrong amount, etc.)
   - Gửi duyệt
   ```

2. **Department Head xóa phiếu**
   ```
   - Login as manager@test.com
   - Navigate to phiếu
   - Click "Xóa phiếu"
   - Confirm
   - Verify: Status chuyển sang "Đã xóa"
   - Verify: Soft delete (deleted_at được set)
   - Verify: Lịch sử có record "Đã xóa"
   ```

3. **Employee verify**
   ```
   - Login as employee@test.com
   - Verify: Phiếu không còn trong danh sách active
   - Navigate to "Phiếu đã xóa" (nếu có filter)
   - Verify: Thấy phiếu với status "Đã xóa"
   ```

**Expected Results**:
- ✅ Xóa thành công (soft delete)
- ✅ Không hiển thị trong active list
- ✅ Lịch sử được giữ lại
- ✅ Có thể restore nếu cần (admin feature)

---

### Scenario 5: Multiple Edit History

**Objective**: Test lưu lịch sử chỉnh sửa không giới hạn

**Steps**:

1. **Employee tạo phiếu**
   ```
   - Login as employee@test.com
   - Tạo phiếu (Amount: 1,000,000)
   - Gửi duyệt
   ```

2. **Department Head từ chối lần 1**
   ```
   - Login as manager@test.com
   - Từ chối với reason: "Thiếu chứng từ báo giá"
   ```

3. **Employee chỉnh sửa lần 1**
   ```
   - Login as employee@test.com
   - Edit: Upload document báo giá
   - Update reason: "Bổ sung báo giá"
   - Gửi lại
   ```

4. **Accountant từ chối lần 2**
   ```
   - Login as accountant@test.com
   - (Giả sử đã qua dept head)
   - Từ chối với reason: "Số tiền không khớp với báo giá"
   ```

5. **Employee chỉnh sửa lần 2**
   ```
   - Edit: Update amount từ 1,000,000 -> 1,200,000
   - Update reason: "Điều chỉnh theo báo giá chính xác"
   - Gửi lại
   ```

6. **CEO từ chối lần 3**
   ```
   - Login as ceo@test.com
   - (Giả sử đã qua dept head + accountant)
   - Từ chối với reason: "Vượt ngân sách dự án"
   ```

7. **Employee chỉnh sửa lần 3**
   ```
   - Edit: Change project, reduce amount to 1,000,000
   - Update reason: "Chuyển sang dự án khác, giảm số tiền"
   - Gửi lại
   ```

8. **Verify lịch sử**
   ```
   - Navigate to phiếu detail
   - Click "Xem lịch sử"
   - Verify: Có đầy đủ 3 lần chỉnh sửa
   - Verify: Mỗi lần có:
     * Người chỉnh sửa
     * Thời gian
     * Lý do
     * Changes (old/new values)
   - Verify: Có thể xem diff cho từng field
   ```

**Expected Results**:
- ✅ Không giới hạn số lần chỉnh sửa
- ✅ Mỗi lần đều lưu đầy đủ thông tin
- ✅ Changes được track chính xác
- ✅ Timeline hiển thị rõ ràng

---

### Scenario 6: Project Budget Tracking

**Objective**: Test tracking chi phí theo dự án

**Steps**:

1. **Check initial project budget**
   ```
   - Login as any user
   - Navigate to Projects
   - Click "Website Redesign"
   - Verify: Budget = 50,000,000, Spent = 0
   ```

2. **Create và approve 3 phiếu cho cùng project**
   ```
   - Phiếu 1: 10,000,000 (approved & paid)
   - Phiếu 2: 15,000,000 (approved & paid)
   - Phiếu 3: 20,000,000 (approved & paid)
   ```

3. **Verify project spent**
   ```
   - Navigate to "Website Redesign" project
   - Verify: Spent = 45,000,000
   - Verify: Remaining = 5,000,000
   - Verify: Utilization = 90%
   - Verify: Warning badge "Gần hết ngân sách"
   ```

4. **Create phiếu vượt budget**
   ```
   - Tạo phiếu mới: 10,000,000 (total would be 55M)
   - Verify: Warning message "Vượt ngân sách dự án"
   - Verify: Vẫn có thể tạo (không block, chỉ warning)
   ```

5. **Approve phiếu vượt budget**
   ```
   - Workflow approval bình thường
   - Verify: CEO thấy warning "Phiếu này làm vượt ngân sách dự án"
   - CEO vẫn có thể approve (business decision)
   - After paid: Verify project.spent = 55,000,000
   - Verify: is_over_budget = true
   - Verify: Badge "Vượt ngân sách" màu đỏ
   ```

**Expected Results**:
- ✅ Spent tính chính xác từ paid requests
- ✅ Warning khi gần hết budget (>80%)
- ✅ Không block tạo phiếu vượt budget
- ✅ Approver thấy warning rõ ràng
- ✅ Project stats update realtime

---

### Scenario 7: Notification Polling

**Objective**: Test thông báo realtime qua polling

**Steps**:

1. **Setup**
   ```
   - Ensure queue worker đang chạy: php artisan queue:work
   - Login as employee@test.com
   - Open browser DevTools > Network tab
   ```

2. **Monitor polling**
   ```
   - Verify: Mỗi 30s có request đến /api/notifications/unread
   - Verify: Response trả về unread_count = 0
   ```

3. **Trigger notification**
   ```
   - Trong tab khác, login as manager@test.com
   - Approve một phiếu của employee
   ```

4. **Verify notification received**
   ```
   - Quay lại tab employee
   - Wait tối đa 30s (polling interval)
   - Verify: Notification bell badge hiển thị "1"
   - Verify: Dropdown có notification mới
   - Verify: Message: "Phiếu #XXX đã được phê duyệt"
   - Click notification
   - Verify: Navigate đến phiếu detail
   - Verify: Notification marked as read
   - Verify: Badge count giảm xuống 0
   ```

5. **Test multiple notifications**
   ```
   - Trigger 5 notifications liên tiếp
   - Verify: Badge hiển thị "5"
   - Verify: Dropdown list 5 notifications
   - Click "Đánh dấu tất cả đã đọc"
   - Verify: Badge về 0
   - Verify: Tất cả notifications có read_at
   ```

**Expected Results**:
- ✅ Polling hoạt động mỗi 30s
- ✅ Notifications hiển thị < 2s sau event
- ✅ Badge count chính xác
- ✅ Mark as read hoạt động
- ✅ Click notification navigate đúng

---

### Scenario 8: Permission & Authorization

**Objective**: Test phân quyền chặt chẽ

**Steps**:

1. **Employee permissions**
   ```
   - Login as employee@test.com
   - Verify: Có thể tạo phiếu
   - Verify: Chỉ thấy phiếu của mình
   - Verify: Không thấy menu "Phiếu cần duyệt"
   - Verify: Không thể approve phiếu
   - Try: Truy cập /approvals trực tiếp
   - Verify: 403 Forbidden
   ```

2. **Department Head permissions**
   ```
   - Login as manager@test.com
   - Verify: Thấy phiếu của mình + nhân viên trong bộ phận
   - Verify: KHÔNG thấy phiếu của bộ phận khác
   - Verify: Có thể approve phiếu của nhân viên thuộc quyền
   - Try: Approve phiếu của bộ phận khác (via API)
   - Verify: 403 Forbidden
   ```

3. **Accountant permissions**
   ```
   - Login as accountant@test.com
   - Verify: Chỉ thấy phiếu của offices được assign (HCM)
   - Verify: KHÔNG thấy phiếu của office khác (Hà Nội)
   - Create accountant2 assigned to Hà Nội office
   - Verify: accountant2 chỉ thấy phiếu Hà Nội
   ```

4. **CEO permissions**
   ```
   - Login as ceo@test.com
   - Verify: Thấy TẤT CẢ phiếu trong hệ thống
   - Verify: Có thể approve mọi phiếu pending_ceo
   - Verify: Không thể approve phiếu đang pending_department_head
   ```

5. **Cross-role actions**
   ```
   - Employee try to delete phiếu của người khác
   - Verify: 403 Forbidden
   - Department Head try to process payment
   - Verify: 403 Forbidden (chỉ accountant mới được)
   ```

**Expected Results**:
- ✅ Mỗi role chỉ thấy phiếu thuộc quyền
- ✅ Actions bị block nếu không có permission
- ✅ Direct URL access cũng bị check
- ✅ API calls bị validate authorization
- ✅ No data leakage giữa offices/departments

---

### Scenario 9: File Upload & Download

**Objective**: Test upload/download chứng từ

**Steps**:

1. **Upload during creation**
   ```
   - Login as employee@test.com
   - Tạo phiếu mới
   - Upload file: invoice.pdf (5MB)
   - Verify: File uploaded successfully
   - Verify: File lưu trong storage/app/public/documents/{payment_request_id}/
   - Verify: Filename được hash (timestamp_random.pdf)
   ```

2. **Upload multiple files**
   ```
   - Upload thêm: receipt.jpg (2MB)
   - Upload thêm: contract.pdf (8MB)
   - Verify: Tất cả 3 files hiển thị trong danh sách
   - Verify: Mỗi file có icon theo type (PDF/image)
   - Verify: Hiển thị file size
   ```

3. **Download file**
   ```
   - Click download invoice.pdf
   - Verify: File download với original filename
   - Verify: Content chính xác (không corrupt)
   ```

4. **Delete file**
   ```
   - Click delete receipt.jpg
   - Confirm
   - Verify: File bị xóa khỏi storage
   - Verify: Document record bị xóa khỏi DB
   ```

5. **Upload validation**
   ```
   - Try upload file .exe
   - Verify: Error "File type not allowed"
   - Try upload file 15MB
   - Verify: Error "File too large (max 10MB)"
   ```

6. **Permission check**
   ```
   - Login as manager@test.com
   - Navigate to phiếu của employee
   - Try download file
   - Verify: Download thành công (có quyền view)
   - Try delete file
   - Verify: 403 Forbidden (chỉ creator mới delete được)
   ```

**Expected Results**:
- ✅ Upload multiple files thành công
- ✅ File validation hoạt động
- ✅ Download với original filename
- ✅ Delete file xóa cả storage & DB
- ✅ Permissions được enforce

---

### Scenario 10: Dashboard Statistics

**Objective**: Test dashboard hiển thị stats chính xác

**Steps**:

1. **Employee dashboard**
   ```
   - Login as employee@test.com
   - Navigate to Dashboard
   - Verify stats:
     * Tổng phiếu của tôi: [count]
     * Đang chờ duyệt: [count pending]
     * Đã duyệt tháng này: [count approved this month]
     * Tổng số tiền tháng này: [sum amount]
   - Verify: Recent requests (5 phiếu gần nhất)
   - Verify: Không có section "Phiếu cần tôi duyệt"
   ```

2. **Department Head dashboard**
   ```
   - Login as manager@test.com
   - Verify stats:
     * Phiếu cần tôi duyệt: [count pending_department_head]
     * Đã duyệt hôm nay: [count approved today]
   - Verify: Pending requests list (urgent trước)
   - Verify: Recent approved requests
   ```

3. **Accountant dashboard**
   ```
   - Login as accountant@test.com
   - Verify stats:
     * Phiếu cần duyệt: [count pending_accountant]
     * Phiếu cần thanh toán: [count pending_payment]
     * Đã thanh toán tháng này: [count paid this month]
     * Tổng thanh toán tháng này: [sum paid amount]
   ```

4. **CEO dashboard**
   ```
   - Login as ceo@test.com
   - Verify stats:
     * Tổng phiếu trong hệ thống: [total count]
     * Phiếu cần tôi duyệt: [count pending_ceo]
     * Tổng chi phí tháng này: [sum all paid]
   - Verify: Charts (nếu có):
     * Phiếu theo trạng thái (pie chart)
     * Chi phí theo tháng (line chart)
     * Top projects by spending (bar chart)
   ```

**Expected Results**:
- ✅ Stats tính chính xác theo role
- ✅ Filters theo thời gian hoạt động
- ✅ Charts render đúng data
- ✅ Performance < 500ms

---

## Performance Testing

### Load Test Setup

```bash
# Install Apache Bench (nếu chưa có)
# Hoặc dùng tools: wrk, k6, JMeter

# Test dashboard endpoint
ab -n 1000 -c 10 http://localhost:8000/dashboard

# Test payment requests list
ab -n 1000 -c 10 http://localhost:8000/payment-requests

# Expected:
# - Requests per second: > 100
# - Mean response time: < 500ms
# - 99th percentile: < 1000ms
```

### Database Query Optimization

```bash
# Enable query logging
# config/database.php: 'log_queries' => true

# Run typical user journey
# Check laravel.log for N+1 queries

# Expected:
# - Dashboard: < 10 queries
# - Payment requests list: < 5 queries (with eager loading)
# - Payment request detail: < 8 queries
```

---

## Troubleshooting

### Queue not processing

```bash
# Check queue worker
php artisan queue:work --tries=3

# Clear failed jobs
php artisan queue:flush

# Restart queue
php artisan queue:restart
```

### Notifications not showing

```bash
# Check queue_jobs table
SELECT * FROM jobs;

# Check notifications table
SELECT * FROM notifications WHERE notifiable_id = [user_id];

# Test notification manually
php artisan tinker
>>> $user = User::find(1);
>>> $user->notify(new \App\Notifications\TestNotification());
```

### File upload errors

```bash
# Check storage permissions
ls -la storage/app/public

# Recreate symlink
php artisan storage:link

# Check upload_max_filesize in php.ini
php -i | grep upload_max_filesize
```

### Inertia not loading

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild assets
npm run build

# Check Vite server
npm run dev
```

---

## Validation Checklist

### Functional Requirements (from spec.md)

- [ ] FR-001: Tạo 3 loại phiếu
- [ ] FR-002: Nhập đầy đủ thông tin
- [ ] FR-003: Đánh dấu ưu tiên
- [ ] FR-004: Bắt buộc mã dự án (nếu liên quan)
- [ ] FR-005: Chỉnh sửa phiếu (chờ duyệt/bị từ chối)
- [ ] FR-006: Ghi lý do chỉnh sửa
- [ ] FR-006a: Lưu lịch sử chỉnh sửa đầy đủ
- [ ] FR-006b: Không giới hạn số lần chỉnh sửa
- [ ] FR-007: Hủy phiếu với lý do
- [ ] FR-008: Khóa phiếu khi hủy
- [ ] FR-009: Reset workflow khi chỉnh sửa
- [ ] FR-010: Workflow đúng thứ tự
- [ ] FR-011: Tự động chuyển cấp tiếp theo
- [ ] FR-012: Approve/reject
- [ ] FR-013: Ghi lý do từ chối
- [ ] FR-014: Thông báo khi từ chối
- [ ] FR-015: Xóa phiếu (approvers)
- [ ] FR-016: Khóa phiếu khi xóa
- [ ] FR-017: Hiển thị trạng thái rõ ràng
- [ ] FR-018: Chuyển sang chờ thanh toán
- [ ] FR-019: Ghi nhận thông tin thanh toán
- [ ] FR-020: Chuyển sang đã thanh toán
- [ ] FR-021: Ghi nhận hoàn ứng
- [ ] FR-022: Nộp chứng từ
- [ ] FR-023: Nhân viên xem/sửa/hủy phiếu của mình
- [ ] FR-024: Trưởng BP xem phiếu bộ phận
- [ ] FR-025: Trưởng BP duyệt/từ chối/xóa
- [ ] FR-026: Kế toán xem phiếu theo văn phòng
- [ ] FR-027: Phân biệt phiếu theo văn phòng
- [ ] FR-028: TGĐ xem toàn bộ
- [ ] FR-029-032: Thông báo realtime
- [ ] FR-033: Hiển thị theo ưu tiên
- [ ] FR-033a: Không quy định thời hạn
- [ ] FR-034: Liên kết với dự án
- [ ] FR-035: Xem phiếu theo dự án
- [ ] FR-036-039: Lưu lịch sử đầy đủ

### Non-Functional Requirements

- [ ] Performance: Response < 500ms
- [ ] Notifications: Delay < 2s
- [ ] Concurrent users: 100+
- [ ] File upload: Max 10MB
- [ ] Security: Authorization enforced
- [ ] Audit trail: Complete history
- [ ] UI: AdminLTE responsive
- [ ] Browser: Chrome, Firefox, Edge

---

## Summary

✅ **Installation guide** hoàn chỉnh  
✅ **10 test scenarios** cover tất cả workflows  
✅ **Performance testing** guidelines  
✅ **Troubleshooting** common issues  
✅ **Validation checklist** map với 42 FRs  

**Status**: Quickstart complete, ready for implementation
