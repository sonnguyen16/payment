# Hướng dẫn sử dụng - Payment Approval System

## Đăng nhập

1. Truy cập: `http://payment.example.com`
2. Nhập email và mật khẩu
3. Click "Đăng nhập"

### Tài khoản mẫu:
- **Nhân viên**: employee@test.com / password
- **Trưởng phòng**: manager@test.com / password
- **Kế toán**: accountant@test.com / password
- **Giám đốc**: ceo@test.com / password

## Dành cho Nhân viên

### Tạo phiếu đề xuất mới

1. Click menu "Phiếu đề xuất"
2. Click nút "Tạo phiếu mới"
3. Điền thông tin:
   - **Loại phiếu**: Tạm ứng / Đề xuất thanh toán / Chi phí khác
   - **Số tiền**: Nhập số tiền cần chi
   - **Mô tả**: Mô tả chi tiết nội dung chi
   - **Lý do**: Lý do cần chi tiền này
   - **Ngày dự kiến**: Ngày cần có tiền
   - **Mức ưu tiên**: Bình thường / Khẩn cấp
   - **Dự án**: Chọn dự án (nếu có)
4. Click "Lưu nháp" hoặc "Gửi duyệt"

### Chỉnh sửa phiếu

1. Vào "Phiếu đề xuất" của tôi
2. Click vào phiếu cần sửa
3. Click nút "Chỉnh sửa"
4. Sửa thông tin
5. Nhập "Lý do chỉnh sửa"
6. Click "Cập nhật"

**Lưu ý**: Chỉ sửa được phiếu ở trạng thái "Nháp" hoặc "Bị từ chối"

### Upload tài liệu

1. Vào chi tiết phiếu
2. Cuộn xuống phần "Tài liệu đính kèm"
3. Kéo thả file hoặc click "Chọn file"
4. Chọn loại tài liệu:
   - Hóa đơn
   - Biên lai
   - Hợp đồng
   - Khác
5. Click "Upload"

**Định dạng hỗ trợ**: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG  
**Kích thước tối đa**: 10MB

### Gửi phiếu duyệt

1. Vào chi tiết phiếu (trạng thái "Nháp")
2. Kiểm tra lại thông tin
3. Click nút "Gửi duyệt"
4. Xác nhận

**Lưu ý**: Sau khi gửi duyệt, không thể chỉnh sửa phiếu

### Hủy phiếu

1. Vào chi tiết phiếu
2. Click nút "Hủy phiếu"
3. Nhập lý do hủy
4. Xác nhận

### Xem lịch sử

1. Vào chi tiết phiếu
2. Cuộn xuống phần "Lịch sử phê duyệt"
3. Xem timeline các hành động

## Dành cho Trưởng phòng/ban

### Xem phiếu chờ duyệt

1. Click menu "Phê duyệt"
2. Xem danh sách phiếu chờ duyệt
3. Click vào phiếu để xem chi tiết

### Phê duyệt phiếu

1. Vào chi tiết phiếu
2. Kiểm tra thông tin:
   - Số tiền
   - Lý do
   - Tài liệu đính kèm
3. Click nút "Phê duyệt"
4. Nhập ghi chú (không bắt buộc)
5. Xác nhận

### Từ chối phiếu

1. Vào chi tiết phiếu
2. Click nút "Từ chối"
3. Nhập lý do từ chối (bắt buộc)
4. Xác nhận

**Lưu ý**: Phiếu bị từ chối sẽ trả về người tạo để chỉnh sửa

## Dành cho Kế toán

### Phê duyệt cấp 2

1. Vào menu "Phê duyệt"
2. Xem phiếu đã được Trưởng phòng duyệt
3. Kiểm tra:
   - Ngân sách dự án
   - Tài liệu hợp lệ
   - Thông tin chính xác
4. Phê duyệt hoặc từ chối

### Thanh toán

1. Vào phiếu trạng thái "Chờ thanh toán"
2. Thực hiện thanh toán
3. Click "Đánh dấu đã thanh toán"
4. Nhập thông tin thanh toán
5. Xác nhận

## Dành cho Giám đốc

### Phê duyệt phiếu lớn

1. Vào menu "Phê duyệt"
2. Xem phiếu > 50 triệu chờ duyệt
3. Kiểm tra kỹ thông tin
4. Phê duyệt hoặc từ chối

### Xem tổng quan

1. Vào "Dashboard"
2. Xem các chỉ số:
   - Tổng số phiếu
   - Tổng tiền đã chi
   - Phiếu chờ duyệt
   - Xu hướng chi tiêu

## Quản lý dự án

### Xem danh sách dự án

1. Click menu "Dự án"
2. Xem danh sách với:
   - Ngân sách
   - Đã chi
   - Còn lại
   - Tiến độ

### Xem chi tiết dự án

1. Click vào dự án
2. Xem:
   - Thông tin dự án
   - Ngân sách chi tiết
   - Cảnh báo vượt ngân sách
   - Danh sách phiếu liên quan

## Thông báo

### Xem thông báo

1. Click icon chuông ở góc phải
2. Xem danh sách thông báo
3. Click vào thông báo để xem chi tiết

### Đánh dấu đã đọc

1. Click vào thông báo
2. Tự động đánh dấu đã đọc

## Các trạng thái phiếu

| Trạng thái | Ý nghĩa | Hành động |
|------------|---------|-----------|
| Nháp | Chưa gửi duyệt | Có thể sửa, xóa |
| Chờ Trưởng BP | Chờ trưởng phòng duyệt | Chờ phê duyệt |
| Chờ Kế toán | Chờ kế toán duyệt | Chờ phê duyệt |
| Chờ Giám đốc | Chờ giám đốc duyệt | Chờ phê duyệt |
| Chờ thanh toán | Đã duyệt, chờ thanh toán | Kế toán thanh toán |
| Đã thanh toán | Hoàn tất | Không thao tác |
| Bị từ chối | Không được duyệt | Có thể sửa lại |
| Đã hủy | Người tạo hủy | Không thao tác |

## Câu hỏi thường gặp

### Tôi quên mật khẩu?
- Click "Quên mật khẩu" ở trang đăng nhập
- Nhập email
- Kiểm tra email để reset

### Tôi không thể upload file?
- Kiểm tra định dạng file (PDF, DOC, XLS, JPG, PNG)
- Kiểm tra kích thước < 10MB
- Thử lại sau vài phút

### Phiếu của tôi bị từ chối?
- Xem lý do từ chối trong lịch sử
- Chỉnh sửa phiếu theo yêu cầu
- Gửi lại duyệt

### Tôi muốn hủy phiếu đã gửi?
- Vào chi tiết phiếu
- Click "Hủy phiếu"
- Nhập lý do

### Làm sao biết phiếu đã được duyệt?
- Nhận thông báo
- Kiểm tra trạng thái phiếu
- Xem lịch sử phê duyệt

## Liên hệ hỗ trợ

- **Email**: support@example.com
- **Hotline**: 1900-xxxx
- **Giờ làm việc**: 8:00 - 17:00 (T2-T6)
