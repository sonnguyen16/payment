# Payment Approval System - Documentation

## Tổng quan

Hệ thống quản lý quy trình duyệt chi theo vai trò với workflow nhiều cấp.

## Tính năng chính

### 1. Quản lý phiếu đề xuất
- Tạo, sửa, xóa phiếu đề xuất
- Gửi phiếu để phê duyệt
- Hủy phiếu đã gửi
- Upload tài liệu đính kèm

### 2. Quy trình phê duyệt
- **Cấp 1**: Trưởng phòng/ban
- **Cấp 2**: Kế toán
- **Cấp 3**: Giám đốc (nếu > 50 triệu)
- **Cấp 4**: Thanh toán

### 3. Quản lý dự án
- Theo dõi ngân sách dự án
- Cảnh báo vượt ngân sách
- Danh sách phiếu theo dự án

### 4. Lịch sử & Audit Trail
- Ghi lại mọi thay đổi
- Theo dõi người thực hiện
- Timeline trực quan

## Vai trò người dùng

### Employee (Nhân viên)
- Tạo phiếu đề xuất
- Xem phiếu của mình
- Upload tài liệu

### Department Head (Trưởng phòng)
- Phê duyệt cấp 1
- Xem phiếu trong phòng

### Accountant (Kế toán)
- Phê duyệt cấp 2
- Xem phiếu trong văn phòng
- Thanh toán

### CEO (Giám đốc)
- Phê duyệt cấp 3 (> 50 triệu)
- Xem tất cả phiếu

## Cài đặt

```bash
# Clone repository
git clone <repo-url>
cd payment

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate
php artisan db:seed

# Storage
php artisan storage:link

# Build assets
npm run build

# Start server
php artisan serve
```

## Sử dụng

### Tạo phiếu đề xuất

1. Đăng nhập với tài khoản nhân viên
2. Click "Tạo phiếu mới"
3. Điền thông tin:
   - Loại phiếu
   - Số tiền
   - Mô tả
   - Lý do
   - Ngày dự kiến
   - Mức ưu tiên
4. Click "Lưu nháp" hoặc "Gửi duyệt"

### Phê duyệt phiếu

1. Đăng nhập với tài khoản có quyền duyệt
2. Vào menu "Phê duyệt"
3. Xem danh sách phiếu chờ duyệt
4. Click "Xem chi tiết"
5. Click "Phê duyệt" hoặc "Từ chối"

### Upload tài liệu

1. Vào chi tiết phiếu
2. Kéo thả file hoặc click "Chọn file"
3. Chọn loại tài liệu
4. Click "Upload"

## API Endpoints

### Payment Requests
- `GET /payment-requests` - Danh sách
- `POST /payment-requests` - Tạo mới
- `GET /payment-requests/{id}` - Chi tiết
- `PUT /payment-requests/{id}` - Cập nhật
- `DELETE /payment-requests/{id}` - Xóa
- `POST /payment-requests/{id}/submit` - Gửi duyệt
- `POST /payment-requests/{id}/cancel` - Hủy

### Approvals
- `GET /approvals` - Danh sách chờ duyệt
- `POST /approvals/{id}/approve` - Phê duyệt
- `POST /approvals/{id}/reject` - Từ chối

### Documents
- `POST /payment-requests/{id}/documents` - Upload
- `GET /documents/{id}` - Download
- `DELETE /documents/{id}` - Xóa

### Projects
- `GET /projects` - Danh sách dự án
- `GET /projects/{id}` - Chi tiết dự án

### Notifications
- `GET /notifications` - Danh sách thông báo
- `GET /notifications/unread` - Chưa đọc
- `POST /notifications/{id}/read` - Đánh dấu đã đọc

## Troubleshooting

### Lỗi 500 khi upload file
- Kiểm tra `php.ini`: `upload_max_filesize`, `post_max_size`
- Kiểm tra quyền thư mục `storage/`

### Không nhận được notification
- Chạy queue worker: `php artisan queue:work`
- Kiểm tra bảng `notifications`

### Lỗi permission
- Chạy lại seeder: `php artisan db:seed --class=RoleSeeder`
- Clear cache: `php artisan cache:clear`

## Bảo trì

### Backup database
```bash
php artisan backup:run
```

### Clear cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Update dependencies
```bash
composer update
npm update
```

## Liên hệ hỗ trợ

- Email: support@example.com
- Hotline: 1900-xxxx
