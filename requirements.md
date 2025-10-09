---
# Quy trình nghiệp vụ duyệt chi theo vai trò
---

## **1. Nhân viên**

### **1.1. Tạo và gửi phiếu đề xuất**

Nhân viên có quyền khởi tạo các loại phiếu đề xuất liên quan đến chi phí, bao gồm:

- Phiếu **tạm ứng**
- Phiếu **đề xuất thanh toán**
- Phiếu **chi phí khác**

**Quy trình thực hiện:**

1. **Khởi tạo phiếu:**
   Nhân viên điền đầy đủ thông tin chi tiết:

   - Nội dung chi phí
   - Số tiền dự kiến
   - Lý do chi
   - Ngày phát sinh dự kiến

2. **Phiếu liên quan dự án:**
   Nếu chi phí thuộc dự án, nhân viên **bắt buộc nhập mã dự án** để đối chiếu và quản lý.
3. **Gửi đề xuất:**
   Sau khi hoàn tất, phiếu sẽ được gửi đến **cấp quản lý trực tiếp** để phê duyệt.
   Phiếu có thể được đánh dấu **“Gấp”** hoặc **“Bình thường”** để sắp xếp mức độ ưu tiên xử lý.

### **1.2. Theo dõi kết quả**

- Nhân viên được **thông báo** khi phiếu được **phê duyệt hoặc từ chối**, kèm **lý do cụ thể**.
- Hệ thống hiển thị rõ **trạng thái phiếu** và **bộ phận đang xử lý**.

### **1.3. Thanh toán & hoàn ứng**

- Sau khi phiếu được duyệt, hồ sơ được chuyển sang **bộ phận kế toán** để thanh toán.
- Sau khi thanh toán xong, nhân viên có **nghĩa vụ hoàn ứng** và **nộp chứng từ** phục vụ lưu trữ, kiểm soát kế toán.

### **1.4. Hủy phiếu**

- Nhân viên có quyền **hủy phiếu** khi cần thiết, phải **ghi rõ lý do**.
- Khi bị hủy, phiếu chuyển sang trạng thái **“Đã hủy”**, **dừng quy trình**, **khóa chỉnh sửa**.

### **1.5. Chỉnh sửa phiếu**

- Nếu phiếu đang ở trạng thái **chờ duyệt**, nhân viên vẫn có thể **yêu cầu chỉnh sửa**.
- Khi chỉnh sửa, phải ghi rõ **nội dung thay đổi**.
- Sau khi cập nhật, phiếu **quay lại quy trình phê duyệt từ đầu**, bắt đầu lại từ **cấp quản lý trực tiếp**.

---

## **2. Trưởng bộ phận**

### **2.1. Phê duyệt phiếu**

- Xem xét và **phê duyệt các phiếu đề xuất** chi phí do **nhân viên thuộc quyền** gửi lên.
- Khi phiếu được phê duyệt, hệ thống **tự động chuyển** sang **bộ phận kế toán**.

### **2.2. Từ chối phiếu**

- Khi từ chối, trưởng bộ phận **ghi rõ lý do**.
- Phiếu được **chuyển trả lại cho nhân viên** để điều chỉnh hoặc hủy bỏ.

### **2.3. Xóa phiếu**

- Có quyền **xóa phiếu** trong trường hợp cần hủy bỏ hoàn toàn.
- Khi xóa:

  - Phiếu chuyển sang trạng thái **“Đã xóa”**
  - Bị **khóa chỉnh sửa** và không thể tiếp tục xử lý.

---

## **3. Kế toán**

### **3.1. Phê duyệt phiếu**

- **Xem xét và phê duyệt** các phiếu do **trưởng bộ phận** gửi lên.
- Khi duyệt, hệ thống **chuyển tiếp phiếu** sang **Tổng giám đốc** để phê duyệt cuối.

### **3.2. Từ chối phiếu**

- Khi từ chối, kế toán ghi rõ **lý do từ chối**.
- Phiếu **trả về nhân viên đề xuất** để chỉnh sửa hoặc hủy.

### **3.3. Xóa phiếu**

- Có thể **xóa phiếu** nếu cần hủy bỏ hoàn toàn.
- Khi xóa:

  - Phiếu chuyển trạng thái **“Đã xóa”**
  - Bị **khóa hoàn toàn**, không chỉnh sửa hoặc xử lý tiếp.

### **3.4. Thanh toán & đối soát**

- Sau khi **Tổng giám đốc phê duyệt**, kế toán thực hiện **thanh toán**.
- Ghi nhận **mã thanh toán, ngày chi, chứng từ đính kèm**.
- Theo dõi quá trình **hoàn ứng** và **bổ sung chứng từ** từ nhân viên.

---

## **4. Tổng giám đốc**

### **4.1. Phê duyệt cuối cùng**

- **Phê duyệt** các phiếu do kế toán chuyển lên.
- Khi duyệt, hệ thống **chuyển phiếu sang phòng kế toán** để thực hiện thanh toán.

### **4.2. Từ chối phiếu**

- Khi không đồng ý, Tổng giám đốc **ghi rõ lý do từ chối**, hệ thống chuyển trả về **kế toán**.

### **4.3. Xóa phiếu**

- Có quyền **xóa phiếu** trong trường hợp đặc biệt.
- Phiếu bị **khóa chỉnh sửa** và dừng toàn bộ quy trình xử lý.

---

## **5. Phân quyền truy cập hệ thống**

| Vai trò            | Quyền hạn chính                                                                                                                                                                                       |
| ------------------ | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Nhân viên**      | - Xem, sửa, xóa các phiếu do **chính mình tạo**.- Theo dõi trạng thái và phản hồi từ cấp duyệt.                                                                                                       |
| **Trưởng bộ phận** | - Xem các phiếu **do mình tạo** và **của nhân viên trong bộ phận**.- Phê duyệt hoặc từ chối phiếu của cấp dưới.                                                                                       |
| **Kế toán**        | - Xem và xử lý phiếu từ các **văn phòng được phân công**, mỗi văn phòng sẽ bao gồm nhiều phòng ban (ví dụ: kế toán phụ trách văn phòng Vũng Tàu, HCM...).- Thanh toán và ghi nhận chứng từ sau duyệt. |
| **Tổng giám đốc**  | - Quyền phê duyệt **toàn bộ phiếu trong hệ thống**.- Phê duyệt cuối cùng trước khi thanh toán.                                                                                                        |
